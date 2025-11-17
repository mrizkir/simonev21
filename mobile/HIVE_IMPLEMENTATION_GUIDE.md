# Panduan Implementasi Hive untuk Data Master

## 📋 Overview

Semua data master yang diakses melalui endpoint `/dmaster/` akan disimpan di database lokal Hive untuk:
- **Akses cepat** - Data langsung dari HP, tidak perlu request ke backend
- **Offline support** - Bisa digunakan tanpa internet
- **Menghemat bandwidth** - Hanya sync saat diperlukan

## 🏗️ Struktur yang Sudah Dibuat

### 1. **Hive Models** (`lib/models/hive_models.dart`)
   - `HiveBoxNames` - Nama-nama box untuk setiap jenis data master
   - `SyncMetadata` - Metadata untuk tracking sync status

### 2. **Hive Service** (`lib/services/hive_service.dart`)
   - Service untuk CRUD operations ke Hive
   - Methods: `saveMasterData`, `getMasterData`, `getAllMasterData`, dll

### 3. **Data Master Sync Service** (`lib/services/data_master_sync_service.dart`)
   - Service untuk sync data dari API ke Hive
   - Methods: `syncOPD`, `syncUnitKerja`, `syncProgram`, dll

### 4. **Data Master Provider** (`lib/providers/data_master_provider.dart`)
   - Provider untuk akses data master dari Hive
   - Auto-sync jika data expired atau belum ada

## 📦 Data Master yang Disimpan

Berdasarkan endpoint `/dmaster/` di `api.php`:

1. **OPD/SKPD** - `/dmaster/opd`
2. **Unit Kerja** - `/dmaster/opd/{id}/unitkerja`
3. **Urusan** - `/dmaster/kodefikasi/urusan`
4. **Bidang Urusan** - `/dmaster/kodefikasi/bidangurusan`
5. **Program** - `/dmaster/kodefikasi/bidangurusan/{id}/program`
6. **Kegiatan** - `/dmaster/kodefikasi/program/{id}/kegiatan`
7. **Sub Kegiatan** - `/dmaster/kodefikasi/kegiatan/{id}/subkegiatanrka`
8. **Rekening** (Akun, Kelompok, Jenis, Objek, Rincian Objek, Sub Rincian Objek)
9. **ASN** - `/dmaster/asn`
10. **Pejabat** - `/dmaster/pejabat`
11. **Sumber Dana** - `/dmaster/sumberdana`
12. **Tahun Anggaran** - `/dmaster/ta`
13. **Jenis Pelaksanaan** - `/dmaster/jenispelaksanaan`
14. **Jenis Pembangunan** - `/dmaster/jenispembangunan`
15. **Provinsi, Kabupaten, Kecamatan, Desa**

## 🚀 Cara Menggunakan

### 1. Initialize Hive (Sudah di main.dart)
```dart
await HiveService.init();
```

### 2. Di Provider, Gunakan DataMasterProvider

```dart
// Di RKAMurniProvider
final dataMasterProvider = Provider.of<DataMasterProvider>(context, listen: false);

// Get OPD dari Hive (instant, no network)
final opdList = dataMasterProvider.getOPDFromHive(tahun: tahun);

// Auto-sync jika perlu
await dataMasterProvider.syncOPDIfNeeded(tahun);
```

### 3. Pattern: Hive-First, Background Sync

```dart
// 1. Cek Hive dulu (instant)
final data = dataMasterProvider.getOPDFromHive();

// 2. Jika ada, gunakan langsung
if (data.isNotEmpty) {
  // Tampilkan data dari Hive
  // Sync di background untuk update
  dataMasterProvider.syncOPDIfNeeded(tahun);
}

// 3. Jika tidak ada, sync dari API
if (data.isEmpty) {
  await dataMasterProvider.syncOPDIfNeeded(tahun);
  final freshData = dataMasterProvider.getOPDFromHive();
}
```

## 🔄 Sync Strategy

### Auto Sync (Background)
- Data di-sync otomatis jika:
  - Belum pernah di-sync
  - Data sudah expired (> 24 jam untuk data master)
- Sync dilakukan di background, tidak blocking UI

### Manual Sync
- User bisa trigger manual sync
- Full sync semua data master

### Smart Sync
- Hanya sync data yang diperlukan
- Sync berdasarkan context (tahun, orgID, dll)

## 📊 TTL (Time To Live)

| Data Type | TTL | Reason |
|-----------|-----|--------|
| OPD | 24 jam | Jarang berubah |
| Unit Kerja | 24 jam | Jarang berubah |
| Program | 24 jam | Jarang berubah |
| Kegiatan | 24 jam | Jarang berubah |
| Sub Kegiatan | 24 jam | Jarang berubah |
| Tahun Anggaran | 7 hari | Sangat jarang berubah |

## 🔧 Update Provider yang Ada

### Contoh: Update RKAMurniProvider

**Sebelum:**
```dart
Future<void> fetchOPD(String tahun) async {
  final response = await apiService.getOPDList({'tahun': tahun});
  // Parse response...
}
```

**Sesudah:**
```dart
Future<void> fetchOPD(String tahun) async {
  // 1. Get dari Hive (instant)
  _daftarOPD = dataMasterProvider.getOPDFromHive(tahun: tahun);
  notifyListeners();
  
  // 2. Sync di background jika perlu
  await dataMasterProvider.syncOPDIfNeeded(tahun);
  
  // 3. Update jika ada data baru
  _daftarOPD = dataMasterProvider.getOPDFromHive(tahun: tahun);
  notifyListeners();
}
```

## 📝 Langkah Implementasi

### Phase 1: Setup (Selesai ✅)
- [x] Tambah Hive dependencies
- [x] Buat HiveService
- [x] Buat DataMasterSyncService
- [x] Buat DataMasterProvider
- [x] Initialize di main.dart

### Phase 2: Integrate ke Providers
- [ ] Update RKAMurniProvider untuk menggunakan Hive
- [ ] Update RKAMurniProvider untuk menggunakan Hive
- [ ] Update providers lain yang menggunakan data master

### Phase 3: Add More Endpoints
- [ ] Sync semua endpoint `/dmaster/` yang ada
- [ ] Test setiap endpoint

### Phase 4: Optimize
- [ ] Implement delta sync (hanya sync data yang berubah)
- [ ] Add compression untuk data besar
- [ ] Add migration untuk schema changes

## 🎯 Expected Benefits

| Metric | Before | After | Improvement |
|--------|--------|-------|-------------|
| OPD Load Time | 2-3s | < 100ms | 95% faster |
| Unit Kerja Load | 2-3s | < 100ms | 95% faster |
| Offline Support | 0% | 100% | New feature |
| Network | High | Low | Reduced |

## ⚠️ Notes

1. **Hive Adapters**: Untuk production, generate Hive adapters dengan `build_runner` untuk performa lebih baik
2. **Data Size**: Monitor ukuran database, implement cleanup jika perlu
3. **Sync Frequency**: TTL bisa disesuaikan berdasarkan kebutuhan
4. **Error Handling**: Pastikan fallback ke API jika Hive error

## 🔍 Testing

```dart
// Test sync
final dataMasterProvider = Provider.of<DataMasterProvider>(context, listen: false);
await dataMasterProvider.syncAll('2024');

// Test get from Hive
final opd = dataMasterProvider.getOPDFromHive(tahun: '2024');
print('OPD count: ${opd.length}');

// Test storage stats
final stats = dataMasterProvider.getStorageStats();
print('Storage: $stats');
```

