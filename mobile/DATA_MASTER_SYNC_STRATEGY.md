# Strategi Sync Data Master - Update Mechanism

## 🎯 Masalah
Ketika terjadi perubahan data master di backend, bagaimana cara update data di Hive di masing-masing HP pengguna?

## 🔄 Strategi Sync (Multi-Layer)

### 1. **TTL-Based Sync** (Sudah diimplementasi) ⭐⭐⭐
**Cara kerja:**
- Data di-sync ulang setelah TTL (Time To Live) expired
- Default: 24 jam untuk data master
- Otomatis check saat app start atau saat data diakses

**Kelebihan:**
- Simple, tidak perlu perubahan backend
- Otomatis update

**Kekurangan:**
- Bisa delay sampai 24 jam
- Tidak real-time

**Implementasi:**
```dart
// Di DataMasterProvider
bool needsSync(String endpoint, {String? tahun, String? orgId}) {
  return HiveService.needsSync(
    endpoint,
    tahun: tahun,
    orgId: orgId,
    maxAge: Duration(hours: 24), // TTL 24 jam
  );
}
```

---

### 2. **Version-Based Sync** ⭐⭐⭐⭐⭐ (RECOMMENDED)
**Cara kerja:**
- Backend mengirim `version` atau `last_updated` timestamp
- Client simpan version di Hive
- Saat sync, client kirim version terakhir
- Backend cek: jika berbeda, kirim data baru; jika sama, kirim 304 Not Modified

**Kelebihan:**
- Real-time update
- Efficient (hanya sync jika ada perubahan)
- Menghemat bandwidth

**Kekurangan:**
- Perlu modifikasi backend untuk support version

**Implementasi Backend (Contoh):**
```php
// Di Controller
public function index(Request $request) {
    $tahun = $request->input('tahun');
    $clientVersion = $request->input('version'); // Version dari client
    
    // Get data dengan version
    $data = OPDModel::where('TA', $tahun)->get();
    $serverVersion = $this->getDataVersion('opd', $tahun); // Hash atau timestamp
    
    // Jika version sama, return 304
    if ($clientVersion === $serverVersion) {
        return response()->json([
            'status' => 304,
            'message' => 'Data tidak berubah'
        ], 304);
    }
    
    // Jika berbeda, return data + version baru
    return response()->json([
        'status' => 200,
        'opd' => $data,
        'version' => $serverVersion,
        'last_updated' => now()->toIso8601String(),
    ]);
}
```

**Implementasi Client:**
```dart
// Di DataMasterSyncService
Future<bool> syncOPD(String tahun, {String? clientVersion}) async {
  try {
    final response = await apiService.getOPDList({
      'tahun': tahun,
      'version': clientVersion, // Kirim version dari Hive
    });
    
    // Jika 304, data tidak berubah
    if (response.statusCode == 304) {
      debugPrint('✅ OPD data tidak berubah, skip sync');
      // Update lastSync tapi tidak update data
      await HiveService.saveSyncMetadata(SyncMetadata(
        endpoint: '/dmaster/opd',
        tahun: tahun,
        lastSync: DateTime.now(),
        version: clientVersion, // Keep version yang sama
      ));
      return true;
    }
    
    // Jika 200, ada data baru
    if (response.statusCode == 200 && response.data['opd'] != null) {
      final opdList = response.data['opd'] as List;
      final newVersion = response.data['version'] ?? DateTime.now().toIso8601String();
      
      // Save data
      final dataMap = <String, Map<String, dynamic>>{};
      for (var opd in opdList) {
        final orgId = opd['OrgID']?.toString() ?? '';
        if (orgId.isNotEmpty) {
          dataMap[orgId] = Map<String, dynamic>.from(opd);
        }
      }
      await HiveService.saveMasterDataBatch(HiveBoxNames.opd, dataMap);
      
      // Save dengan version baru
      await HiveService.saveSyncMetadata(SyncMetadata(
        endpoint: '/dmaster/opd',
        tahun: tahun,
        lastSync: DateTime.now(),
        version: newVersion,
      ));
      
      return true;
    }
    
    return false;
  } catch (e) {
    debugPrint('❌ Error syncing OPD: $e');
    return false;
  }
}
```

---

### 3. **On-Demand Sync** (Sudah diimplementasi) ⭐⭐⭐⭐
**Cara kerja:**
- Sync saat user buka screen tertentu
- Sync saat user pull-to-refresh
- Sync saat user pilih filter baru

**Kelebihan:**
- User control
- Fresh data saat diperlukan

**Implementasi:**
```dart
// Di screen
ElevatedButton(
  onPressed: () async {
    // Manual sync
    await dataMasterProvider.syncOPDIfNeeded(tahun);
    // Refresh UI
    setState(() {});
  },
  child: Text('Refresh Data'),
)
```

---

### 4. **Background Sync** ⭐⭐⭐
**Cara kerja:**
- Sync periodik di background (misalnya setiap 6 jam)
- Menggunakan WorkManager atau background task

**Kelebihan:**
- Data selalu fresh
- Tidak perlu user action

**Kekurangan:**
- Konsumsi battery
- Perlu permission background

**Implementasi (Future):**
```dart
// Menggunakan workmanager package
await Workmanager().registerPeriodicTask(
  "sync-master-data",
  "syncMasterData",
  frequency: Duration(hours: 6),
);
```

---

### 5. **Push Notification Sync** ⭐⭐⭐⭐⭐ (IDEAL)
**Cara kerja:**
- Backend kirim push notification saat data master berubah
- Client terima notifikasi, trigger sync

**Kelebihan:**
- Real-time update
- Efficient (hanya sync saat ada perubahan)

**Kekurangan:**
- Perlu setup push notification
- Perlu backend support

**Implementasi (Future):**
```dart
// Setup Firebase Cloud Messaging
FirebaseMessaging.onMessage.listen((message) {
  if (message.data['type'] == 'master_data_updated') {
    final endpoint = message.data['endpoint'];
    // Trigger sync untuk endpoint tersebut
    dataMasterProvider.syncSpecificEndpoint(endpoint);
  }
});
```

---

### 6. **Force Sync on App Start** ⭐⭐⭐
**Cara kerja:**
- Saat app start, check semua data master
- Sync jika TTL expired atau version berbeda

**Implementasi:**
```dart
// Di SplashScreen atau main
Future<void> checkAndSyncMasterData() async {
  final dataMasterProvider = Provider.of<DataMasterProvider>(context, listen: false);
  final authProvider = Provider.of<AuthProvider>(context, listen: false);
  final tahun = authProvider.user?.tahunSelected;
  
  if (tahun != null) {
    // Check dan sync jika perlu
    await dataMasterProvider.syncOPDIfNeeded(tahun);
  }
}
```

---

## 🎯 Recommended Strategy (Kombinasi)

### **Strategy Hybrid: TTL + Version + On-Demand**

1. **TTL sebagai fallback** (24 jam)
   - Jika version check gagal, gunakan TTL

2. **Version check saat sync**
   - Setiap kali sync, kirim version
   - Backend return 304 jika tidak ada perubahan

3. **On-demand sync**
   - User bisa manual refresh
   - Auto-sync saat buka screen

4. **Background sync** (optional)
   - Sync periodik untuk critical data

---

## 📋 Implementasi Step-by-Step

### Step 1: Update Backend (Jika memungkinkan)

Tambahkan version support di response:

```php
// Di OrganisasiController@index
public function index(Request $request) {
    $tahun = $request->input('tahun');
    $clientVersion = $request->input('version');
    
    // Calculate version (bisa dari last_updated atau hash)
    $lastUpdated = \DB::table('tmOrg')
        ->where('TA', $tahun)
        ->max('updated_at');
    
    $serverVersion = md5($lastUpdated . $tahun);
    
    // Check version
    if ($clientVersion === $serverVersion) {
        return response()->json([
            'status' => 304,
            'message' => 'Data tidak berubah'
        ], 304);
    }
    
    // Get data
    $data = // ... existing code ...
    
    return response()->json([
        'status' => 200,
        'opd' => $data,
        'version' => $serverVersion,
        'last_updated' => $lastUpdated,
    ]);
}
```

### Step 2: Update Client Sync Service

```dart
// Di DataMasterSyncService
Future<bool> syncOPD(String tahun, {String? clientVersion}) async {
  try {
    // Get version dari Hive jika ada
    final metadata = HiveService.getSyncMetadata('/dmaster/opd', tahun: tahun);
    final version = clientVersion ?? metadata?.version;
    
    final response = await apiService.getOPDList({
      'tahun': tahun,
      if (version != null) 'version': version,
    });
    
    // Handle 304 Not Modified
    if (response.statusCode == 304) {
      // Update lastSync tapi keep version
      await HiveService.saveSyncMetadata(SyncMetadata(
        endpoint: '/dmaster/opd',
        tahun: tahun,
        lastSync: DateTime.now(),
        version: version,
      ));
      return true;
    }
    
    // Handle 200 OK dengan data baru
    if (response.statusCode == 200 && response.data['opd'] != null) {
      // ... save data ...
      
      // Save dengan version baru
      final newVersion = response.data['version'] ?? 
                         DateTime.now().toIso8601String();
      await HiveService.saveSyncMetadata(SyncMetadata(
        endpoint: '/dmaster/opd',
        tahun: tahun,
        lastSync: DateTime.now(),
        version: newVersion,
      ));
      
      return true;
    }
    
    return false;
  } catch (e) {
    debugPrint('❌ Error syncing OPD: $e');
    return false;
  }
}
```

### Step 3: Update Provider untuk Version Check

```dart
// Di DataMasterProvider
Future<void> syncOPDIfNeeded(String tahun) async {
  // Check TTL dulu
  if (!needsSync('/dmaster/opd', tahun: tahun)) {
    return;
  }

  // Get version dari Hive
  final metadata = getSyncStatus('/dmaster/opd', tahun: tahun);
  final version = metadata?.version;

  _isSyncing = true;
  notifyListeners();

  try {
    // Sync dengan version
    final success = await syncService.syncOPD(tahun, clientVersion: version);
    if (!success) {
      _syncError = 'Gagal sync OPD';
    }
  } catch (e) {
    _syncError = 'Error: $e';
  } finally {
    _isSyncing = false;
    notifyListeners();
  }
}
```

---

## 🔍 Monitoring & Debugging

### Check Sync Status
```dart
final status = dataMasterProvider.getSyncStatus('/dmaster/opd', tahun: '2024');
print('Version: ${status?.version}');
print('Last sync: ${status?.lastSync}');
print('Needs sync: ${dataMasterProvider.needsSync('/dmaster/opd', tahun: '2024')}');
```

### Force Sync
```dart
// Force sync tanpa check TTL
await dataMasterProvider.syncAll('2024');
```

### Clear Cache
```dart
// Clear semua data master (untuk testing)
await dataMasterProvider.clearAllData();
```

---

## 📊 Comparison Table

| Strategy | Real-time | Efficiency | Complexity | Recommended |
|----------|-----------|------------|------------|-------------|
| TTL Only | ❌ | ⭐⭐ | ⭐ | ✅ Fallback |
| Version Check | ✅ | ⭐⭐⭐⭐⭐ | ⭐⭐⭐ | ✅ **BEST** |
| On-Demand | ⚠️ | ⭐⭐⭐⭐ | ⭐⭐ | ✅ User Control |
| Background | ✅ | ⭐⭐⭐ | ⭐⭐⭐⭐ | ⚠️ Optional |
| Push Notif | ✅✅ | ⭐⭐⭐⭐⭐ | ⭐⭐⭐⭐⭐ | ✅✅ Ideal |

---

## 🎯 Recommended Implementation

### **Phase 1: TTL + On-Demand (Current)**
- ✅ TTL 24 jam sebagai fallback
- ✅ On-demand sync saat user action
- ✅ Manual refresh button

### **Phase 2: Add Version Support**
- ⏳ Backend support version
- ⏳ Client check version saat sync
- ⏳ Skip sync jika version sama

### **Phase 3: Background Sync (Optional)**
- ⏳ Periodic sync untuk critical data
- ⏳ Smart sync (hanya data yang diperlukan)

### **Phase 4: Push Notification (Future)**
- ⏳ Backend push notif saat data berubah
- ⏳ Client auto-sync saat terima notif

---

## 💡 Best Practices

1. **Always check version** sebelum sync
2. **Handle 304 Not Modified** untuk efisiensi
3. **Fallback ke TTL** jika version check gagal
4. **User control** - beri opsi manual refresh
5. **Background sync** untuk critical data saja
6. **Monitor sync frequency** - jangan terlalu sering
7. **Error handling** - fallback ke cached data jika sync gagal

---

## 🔧 Quick Implementation (Tanpa Ubah Backend)

Jika backend belum support version, gunakan kombinasi:

1. **TTL lebih pendek** untuk data yang sering berubah (misalnya 6 jam)
2. **On-demand sync** saat user buka screen
3. **Manual refresh** button
4. **Force sync** saat app start (optional)

```dart
// Di DataMasterProvider
Future<void> syncOPDIfNeeded(String tahun) async {
  // TTL lebih pendek: 6 jam untuk data yang mungkin berubah
  if (!HiveService.needsSync(
    '/dmaster/opd',
    tahun: tahun,
    maxAge: Duration(hours: 6), // TTL 6 jam
  )) {
    return;
  }
  
  // Sync dari API
  await syncService.syncOPD(tahun);
}
```

