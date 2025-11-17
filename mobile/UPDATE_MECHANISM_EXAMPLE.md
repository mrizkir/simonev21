# Contoh Implementasi Update Mechanism

## 📱 Scenario: Update Data Master di Backend

### **Kasus 1: Backend Support Version (Recommended)**

#### Backend Response Format:
```json
{
  "status": 200,
  "opd": [...],
  "version": "abc123xyz",
  "last_updated": "2024-01-15T10:30:00Z"
}
```

#### Flow Update:

1. **User buka app** → Check TTL
   ```dart
   // Di DataMasterProvider
   if (needsSync('/dmaster/opd', tahun: '2024')) {
     // TTL expired, perlu sync
   }
   ```

2. **Sync dengan version check**
   ```dart
   // Get version dari Hive
   final metadata = getSyncStatus('/dmaster/opd', tahun: '2024');
   final clientVersion = metadata?.version; // "abc123xyz"
   
   // Sync dengan kirim version
   await syncService.syncOPD('2024', clientVersion: clientVersion);
   ```

3. **Backend check version**
   ```php
   // Di Backend
   $clientVersion = $request->input('version');
   $serverVersion = getDataVersion('opd', $tahun);
   
   if ($clientVersion === $serverVersion) {
     // Data tidak berubah
     return response()->json(['status' => 304], 304);
   }
   
   // Data berubah, kirim data baru
   return response()->json([
     'status' => 200,
     'opd' => $data,
     'version' => $serverVersion, // Version baru
   ]);
   ```

4. **Client handle response**
   ```dart
   // Di DataMasterSyncService
   if (response.statusCode == 304) {
     // Data tidak berubah, skip download
     // Update lastSync tapi keep version
     await HiveService.saveSyncMetadata(SyncMetadata(
       lastSync: DateTime.now(),
       version: clientVersion, // Keep version yang sama
     ));
     return true; // Sync "berhasil" tanpa download
   }
   
   if (response.statusCode == 200) {
     // Data berubah, save data baru
     await HiveService.saveMasterDataBatch(...);
     
     // Save version baru
     await HiveService.saveSyncMetadata(SyncMetadata(
       lastSync: DateTime.now(),
       version: response.data['version'], // Version baru
     ));
   }
   ```

**Result:**
- ✅ Jika data tidak berubah → Skip download (hemat bandwidth)
- ✅ Jika data berubah → Download dan update Hive
- ✅ Real-time update (tidak perlu tunggu 24 jam)

---

### **Kasus 2: Backend Belum Support Version (Current)**

#### Flow Update dengan TTL:

1. **User buka app** → Check TTL
   ```dart
   // TTL default: 24 jam
   if (needsSync('/dmaster/opd', tahun: '2024')) {
     // TTL expired (> 24 jam), sync
     await syncOPDIfNeeded('2024');
   }
   ```

2. **Sync dari API**
   ```dart
   // Download data dari API
   final response = await apiService.getOPDList({'tahun': '2024'});
   
   // Save ke Hive
   await HiveService.saveMasterDataBatch(...);
   
   // Update lastSync
   await HiveService.saveSyncMetadata(SyncMetadata(
     lastSync: DateTime.now(),
   ));
   ```

3. **Update akan terjadi:**
   - ✅ Setelah 24 jam (TTL expired)
   - ✅ Saat user manual refresh
   - ✅ Saat user buka screen yang memerlukan data tersebut

**Result:**
- ⚠️ Update bisa delay sampai 24 jam
- ✅ Tetap update otomatis
- ✅ User bisa manual refresh

---

### **Kasus 3: Manual Refresh (User Control)**

#### User tap "Refresh" button:

```dart
// Di Screen
ElevatedButton(
  onPressed: () async {
    // Force sync (ignore TTL dan version)
    await dataMasterProvider.forceSyncOPD('2024');
    
    // Refresh UI
    setState(() {});
  },
  child: Text('Refresh Data'),
)
```

**Flow:**
1. User tap "Refresh"
2. Force sync langsung dari API
3. Update Hive dengan data terbaru
4. UI refresh

**Result:**
- ✅ User control penuh
- ✅ Data selalu fresh saat refresh
- ✅ Tidak perlu tunggu TTL

---

## 🔄 Update Mechanism Comparison

### **Scenario: Admin update OPD di backend**

| Time | Backend | Client A (Version Support) | Client B (TTL Only) |
|------|---------|----------------------------|---------------------|
| 10:00 | Update OPD | Version: `v1` | Last sync: 09:00 |
| 10:05 | - | User buka app → Check version → **304 Not Modified** → Skip download | User buka app → TTL masih fresh → Skip sync |
| 10:30 | - | User buka app → Check version → **304 Not Modified** → Skip download | User buka app → TTL masih fresh → Skip sync |
| 11:00 | - | User buka app → Check version → **200 OK** → Download data baru | User buka app → TTL masih fresh → Skip sync |
| 12:00 | - | User buka app → Check version → **304 Not Modified** → Skip download | User buka app → TTL expired → Sync → Download data baru |

**Kesimpulan:**
- **Client A (Version)**: Update **segera** saat check version (10:30)
- **Client B (TTL)**: Update **setelah TTL expired** (12:00, 3 jam delay)

---

## 🎯 Best Practice Implementation

### **1. Hybrid Strategy (Recommended)**

```dart
// Di DataMasterProvider
Future<void> syncOPDIfNeeded(String tahun) async {
  // Step 1: Check TTL (fallback)
  if (!needsSync('/dmaster/opd', tahun: tahun)) {
    return; // Masih fresh
  }
  
  // Step 2: Get version dari Hive
  final metadata = getSyncStatus('/dmaster/opd', tahun: tahun);
  final clientVersion = metadata?.version;
  
  // Step 3: Sync dengan version (jika backend support)
  final success = await syncService.syncOPD(
    tahun, 
    clientVersion: clientVersion,
  );
  
  // Step 4: Jika gagal, retry tanpa version (fallback)
  if (!success && clientVersion != null) {
    await syncService.syncOPD(tahun); // Retry tanpa version
  }
}
```

### **2. On-Demand Sync**

```dart
// Di Screen (RKA Murni)
@override
void initState() {
  super.initState();
  
  // Sync saat screen dibuka
  WidgetsBinding.instance.addPostFrameCallback((_) {
    final tahun = authProvider.user?.tahunSelected;
    if (tahun != null) {
      dataMasterProvider.syncOPDIfNeeded(tahun);
    }
  });
}
```

### **3. Manual Refresh**

```dart
// Di Screen
RefreshIndicator(
  onRefresh: () async {
    final tahun = authProvider.user?.tahunSelected;
    if (tahun != null) {
      await dataMasterProvider.forceSyncOPD(tahun);
    }
  },
  child: ListView(...),
)
```

---

## 📊 Monitoring Sync Status

### **Check Sync Status:**

```dart
// Get sync status
final status = dataMasterProvider.getSyncStatus('/dmaster/opd', tahun: '2024');

print('Last sync: ${status?.lastSync}');
print('Version: ${status?.version}');
print('Needs sync: ${dataMasterProvider.needsSync('/dmaster/opd', tahun: '2024')}');
```

### **Sync Summary:**

```dart
// Di SyncManager
final syncManager = SyncManager(dataMasterProvider: dataMasterProvider);
final summary = syncManager.getSyncSummary('2024');

summary.forEach((endpoint, status) {
  print('$endpoint: ${status.statusText}');
  // Output:
  // /dmaster/opd: Sync 2 jam lalu
  // /dmaster/ta: Perlu sync
});
```

---

## 🚀 Quick Start: Implementasi di Screen

### **Contoh: RKA Murni Screen**

```dart
class RKAMurniScreen extends StatefulWidget {
  @override
  _RKAMurniScreenState createState() => _RKAMurniScreenState();
}

class _RKAMurniScreenState extends State<RKAMurniScreen> {
  @override
  void initState() {
    super.initState();
    
    // Auto-sync saat screen dibuka
    WidgetsBinding.instance.addPostFrameCallback((_) {
      _syncMasterData();
    });
  }
  
  Future<void> _syncMasterData() async {
    final authProvider = Provider.of<AuthProvider>(context, listen: false);
    final dataMasterProvider = Provider.of<DataMasterProvider>(context, listen: false);
    final tahun = authProvider.user?.tahunSelected;
    
    if (tahun != null) {
      // Sync OPD jika diperlukan
      await dataMasterProvider.syncOPDIfNeeded(tahun);
    }
  }
  
  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
        title: Text('RKA Murni'),
        actions: [
          // Manual refresh button
          IconButton(
            icon: Icon(Icons.refresh),
            onPressed: () async {
              final authProvider = Provider.of<AuthProvider>(context, listen: false);
              final dataMasterProvider = Provider.of<DataMasterProvider>(context, listen: false);
              final tahun = authProvider.user?.tahunSelected;
              
              if (tahun != null) {
                // Force sync
                await dataMasterProvider.forceSyncOPD(tahun);
                ScaffoldMessenger.of(context).showSnackBar(
                  SnackBar(content: Text('Data diperbarui')),
                );
              }
            },
          ),
        ],
      ),
      body: Consumer<DataMasterProvider>(
        builder: (context, provider, child) {
          // Get data dari Hive (instant, no network)
          final opdList = provider.getOPDFromHive(tahun: '2024');
          
          if (opdList.isEmpty && provider.isSyncing) {
            return Center(child: CircularProgressIndicator());
          }
          
          if (opdList.isEmpty) {
            return Center(child: Text('Tidak ada data'));
          }
          
          return ListView.builder(
            itemCount: opdList.length,
            itemBuilder: (context, index) {
              final opd = opdList[index];
              return ListTile(
                title: Text(opd.displayName),
                subtitle: Text(opd.OrgID),
              );
            },
          );
        },
      ),
    );
  }
}
```

---

## ✅ Checklist Implementasi

- [x] TTL-based sync (24 jam)
- [x] Version-based sync (jika backend support)
- [x] On-demand sync (saat screen dibuka)
- [x] Manual refresh (user control)
- [x] Force sync (ignore TTL)
- [x] Error handling & retry
- [x] Sync status monitoring
- [ ] Background sync (optional, future)
- [ ] Push notification sync (optional, future)

---

## 💡 Tips

1. **Gunakan version check** jika backend support (lebih efficient)
2. **TTL sebagai fallback** jika version check gagal
3. **On-demand sync** untuk data yang sering diakses
4. **Manual refresh** untuk user control
5. **Monitor sync status** untuk debugging
6. **Error handling** yang robust (fallback ke cached data)


