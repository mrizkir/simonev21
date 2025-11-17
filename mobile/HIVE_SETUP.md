# Setup Hive untuk Data Master

## 📦 Install Dependencies

Jalankan command berikut untuk install Hive:

```bash
cd mobile
flutter pub get
```

## 🚀 Quick Start

### 1. Hive sudah di-initialize di `main.dart`
Tidak perlu setup tambahan, Hive akan otomatis initialize saat app start.

### 2. Gunakan DataMasterProvider

```dart
// Di screen atau provider
final dataMasterProvider = Provider.of<DataMasterProvider>(context, listen: false);

// Get OPD dari Hive (instant!)
final opdList = dataMasterProvider.getOPDFromHive(tahun: '2024');

// Auto-sync jika perlu
await dataMasterProvider.syncOPDIfNeeded('2024');
```

## 📋 Endpoint yang Sudah Di-Support

✅ **Sudah diimplementasi:**
- `/dmaster/opd` - OPD/SKPD
- `/dmaster/opd/{id}/unitkerja` - Unit Kerja
- `/dmaster/kodefikasi/bidangurusan/{id}/program` - Program
- `/dmaster/kodefikasi/program/{id}/kegiatan` - Kegiatan
- `/dmaster/kodefikasi/kegiatan/{id}/subkegiatanrka` - Sub Kegiatan
- `/dmaster/ta` - Tahun Anggaran

⏳ **Belum diimplementasi (bisa ditambahkan):**
- `/dmaster/kodefikasi/urusan` - Urusan
- `/dmaster/kodefikasi/bidangurusan` - Bidang Urusan
- `/dmaster/rekening/*` - Semua rekening
- `/dmaster/asn` - ASN
- `/dmaster/pejabat` - Pejabat
- `/dmaster/sumberdana` - Sumber Dana
- `/dmaster/jenispelaksanaan` - Jenis Pelaksanaan
- `/dmaster/jenispembangunan` - Jenis Pembangunan
- `/dmaster/provinsi`, `/dmaster/kabupaten`, dll

## 🔄 Cara Menambahkan Endpoint Baru

### 1. Tambahkan method sync di `DataMasterSyncService`

```dart
Future<bool> syncSumberDana(String tahun) async {
  try {
    final response = await apiService.post('/dmaster/sumberdana', data: {'tahun': tahun});
    
    if (response.statusCode == 200 && response.data['sumberdana'] != null) {
      final list = response.data['sumberdana'] as List;
      final dataMap = <String, Map<String, dynamic>>{};
      
      for (var item in list) {
        final id = item['SumberDanaID']?.toString() ?? '';
        if (id.isNotEmpty) {
          dataMap[id] = Map<String, dynamic>.from(item);
        }
      }
      
      await HiveService.saveMasterDataBatch(HiveBoxNames.sumberDana, dataMap);
      await HiveService.saveSyncMetadata(SyncMetadata(
        endpoint: '/dmaster/sumberdana',
        tahun: tahun,
        lastSync: DateTime.now(),
      ));
      
      return true;
    }
    return false;
  } catch (e) {
    debugPrint('❌ Error syncing Sumber Dana: $e');
    return false;
  }
}
```

### 2. Tambahkan method get di `DataMasterProvider`

```dart
List<SumberDanaModel> getSumberDanaFromHive({String? tahun}) {
  try {
    final allData = HiveService.getAllMasterData(HiveBoxNames.sumberDana);
    
    if (tahun != null) {
      final filtered = allData.where((item) => item['TA']?.toString() == tahun).toList();
      return filtered.map((item) => SumberDanaModel.fromJson(item)).toList();
    }
    
    return allData.map((item) => SumberDanaModel.fromJson(item)).toList();
  } catch (e) {
    debugPrint('❌ Error getting Sumber Dana from Hive: $e');
    return [];
  }
}
```

### 3. Tambahkan sync method di `DataMasterProvider`

```dart
Future<void> syncSumberDanaIfNeeded(String tahun) async {
  if (!needsSync('/dmaster/sumberdana', tahun: tahun)) {
    return;
  }

  _isSyncing = true;
  notifyListeners();

  try {
    final success = await syncService.syncSumberDana(tahun);
    if (!success) {
      _syncError = 'Gagal sync Sumber Dana';
    }
  } catch (e) {
    _syncError = 'Error: $e';
  } finally {
    _isSyncing = false;
    notifyListeners();
  }
}
```

## 🎯 Next Steps

1. **Install dependencies**: `flutter pub get`
2. **Test Hive**: Pastikan Hive initialize dengan benar
3. **Integrate ke RKAMurniProvider**: Update provider untuk menggunakan Hive
4. **Test sync**: Test sync data master dari API
5. **Add more endpoints**: Tambahkan endpoint `/dmaster/` lainnya sesuai kebutuhan

## 📊 Monitoring

```dart
// Check storage stats
final stats = dataMasterProvider.getStorageStats();
print('OPD: ${stats['opd']}');
print('Unit Kerja: ${stats['unitKerja']}');

// Check sync status
final status = dataMasterProvider.getSyncStatus('/dmaster/opd', tahun: '2024');
print('Last sync: ${status?.lastSync}');
```

## ⚠️ Troubleshooting

### Hive tidak initialize
- Pastikan `await HiveService.init()` dipanggil di `main()`
- Check error di console

### Data tidak tersimpan
- Check permission untuk write ke storage
- Check error di console

### Sync tidak bekerja
- Check network connection
- Check API response
- Check error di console

