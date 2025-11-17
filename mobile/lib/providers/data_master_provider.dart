import 'package:flutter/material.dart';
import '../services/hive_service.dart';
import '../services/api_service.dart';
import '../services/data_master_sync_service.dart';
import '../models/hive_models.dart';
import '../models/rka_murni_model.dart';

/// Provider untuk manage data master dari Hive
class DataMasterProvider with ChangeNotifier {
  final ApiService apiService;
  final DataMasterSyncService syncService;

  bool _isSyncing = false;
  String? _syncError;
  DateTime? _lastFullSync;

  DataMasterProvider({required this.apiService})
      : syncService = DataMasterSyncService(apiService: apiService);

  bool get isSyncing => _isSyncing;
  String? get syncError => _syncError;
  DateTime? get lastFullSync => _lastFullSync;

  /// Get OPD dari Hive
  List<OPDModel> getOPDFromHive({String? tahun}) {
    try {
      final allData = HiveService.getAllMasterData(HiveBoxNames.opd);
      
      if (tahun != null) {
        // Filter by tahun jika diperlukan
        final filtered = allData.where((item) => item['TA']?.toString() == tahun).toList();
        return filtered.map((item) => OPDModel.fromJson(item)).toList();
      }
      
      return allData.map((item) => OPDModel.fromJson(item)).toList();
    } catch (e) {
      debugPrint('❌ Error getting OPD from Hive: $e');
      return [];
    }
  }

  /// Get Unit Kerja dari Hive untuk OPD tertentu
  List<UnitKerjaModel> getUnitKerjaFromHive(String orgID) {
    try {
      final allData = HiveService.getAllMasterData(HiveBoxNames.unitKerja);
      
      // Filter yang prefix dengan orgID
      final filtered = allData.where((item) {
        // Data disimpan dengan key format: ${orgID}_${sOrgID}
        // Tapi kita perlu cek dari data, bukan key
        return item['OrgID']?.toString() == orgID;
      }).toList();
      
      return filtered.map((item) => UnitKerjaModel.fromJson(item)).toList();
    } catch (e) {
      debugPrint('❌ Error getting Unit Kerja from Hive: $e');
      return [];
    }
  }

  /// Get Program dari Hive untuk Bidang Urusan tertentu
  List<ProgramModel> getProgramFromHive(String bidangID) {
    try {
      final allData = HiveService.getAllMasterData(HiveBoxNames.program);
      
      // Filter yang prefix dengan bidangID
      final filtered = allData.where((item) {
        return item['BidangID']?.toString() == bidangID;
      }).toList();
      
      return filtered.map((item) => ProgramModel.fromJson(item)).toList();
    } catch (e) {
      debugPrint('❌ Error getting Program from Hive: $e');
      return [];
    }
  }

  /// Get Kegiatan dari Hive untuk Program tertentu
  List<KegiatanModel> getKegiatanFromHive(String prgID) {
    try {
      final allData = HiveService.getAllMasterData(HiveBoxNames.kegiatan);
      
      final filtered = allData.where((item) {
        return item['PrgID']?.toString() == prgID;
      }).toList();
      
      return filtered.map((item) => KegiatanModel.fromJson(item)).toList();
    } catch (e) {
      debugPrint('❌ Error getting Kegiatan from Hive: $e');
      return [];
    }
  }

  /// Get Sub Kegiatan dari Hive untuk Kegiatan tertentu
  List<SubKegiatanModel> getSubKegiatanFromHive(String kgtID, String sOrgID) {
    try {
      final allData = HiveService.getAllMasterData(HiveBoxNames.subKegiatan);
      
      final filtered = allData.where((item) {
        return item['KgtID']?.toString() == kgtID &&
               item['SOrgID']?.toString() == sOrgID;
      }).toList();
      
      return filtered.map((item) => SubKegiatanModel.fromJson(item)).toList();
    } catch (e) {
      debugPrint('❌ Error getting Sub Kegiatan from Hive: $e');
      return [];
    }
  }

  /// Check if data perlu di-sync
  bool needsSync(String endpoint, {String? tahun, String? orgId}) {
    return HiveService.needsSync(endpoint, tahun: tahun, orgId: orgId);
  }

  /// Sync OPD jika diperlukan
  /// 
  /// Menggunakan version-based sync jika tersedia, fallback ke TTL
  Future<void> syncOPDIfNeeded(String tahun) async {
    // Check TTL dulu
    if (!needsSync('/dmaster/opd', tahun: tahun)) {
      debugPrint('✅ OPD data masih fresh (TTL), skip sync');
      return;
    }

    _isSyncing = true;
    _syncError = null;
    notifyListeners();

    try {
      // Get version dari Hive (jika ada)
      final metadata = getSyncStatus('/dmaster/opd', tahun: tahun);
      final clientVersion = metadata?.version;
      
      // Sync dengan version (jika backend support)
      final success = await syncService.syncOPD(tahun, clientVersion: clientVersion);
      
      if (!success) {
        _syncError = 'Gagal sync OPD';
      } else {
        debugPrint('✅ OPD sync berhasil');
      }
    } catch (e) {
      _syncError = 'Error: $e';
      debugPrint('❌ Sync OPD error: $e');
    } finally {
      _isSyncing = false;
      notifyListeners();
    }
  }
  
  /// Force sync OPD (ignore TTL dan version)
  /// Berguna untuk manual refresh atau saat yakin ada perubahan
  Future<void> forceSyncOPD(String tahun) async {
    _isSyncing = true;
    _syncError = null;
    notifyListeners();

    try {
      // Get version tapi tetap force sync
      final metadata = getSyncStatus('/dmaster/opd', tahun: tahun);
      final clientVersion = metadata?.version;
      
      final success = await syncService.syncOPD(tahun, clientVersion: clientVersion);
      
      if (!success) {
        _syncError = 'Gagal force sync OPD';
      }
    } catch (e) {
      _syncError = 'Error: $e';
      debugPrint('❌ Force sync OPD error: $e');
    } finally {
      _isSyncing = false;
      notifyListeners();
    }
  }

  /// Sync Unit Kerja jika diperlukan
  Future<void> syncUnitKerjaIfNeeded(String orgID) async {
    if (!needsSync('/dmaster/opd/$orgID/unitkerja', orgId: orgID)) {
      debugPrint('✅ Unit Kerja data masih fresh, skip sync');
      return;
    }

    _isSyncing = true;
    _syncError = null;
    notifyListeners();

    try {
      final success = await syncService.syncUnitKerja(orgID);
      if (!success) {
        _syncError = 'Gagal sync Unit Kerja';
      }
    } catch (e) {
      _syncError = 'Error: $e';
      debugPrint('❌ Sync Unit Kerja error: $e');
    } finally {
      _isSyncing = false;
      notifyListeners();
    }
  }

  /// Sync Program jika diperlukan
  Future<void> syncProgramIfNeeded(String bidangID, String tahun) async {
    if (!needsSync('/dmaster/kodefikasi/bidangurusan/$bidangID/program', tahun: tahun)) {
      debugPrint('✅ Program data masih fresh, skip sync');
      return;
    }

    _isSyncing = true;
    _syncError = null;
    notifyListeners();

    try {
      final success = await syncService.syncProgram(bidangID, tahun);
      if (!success) {
        _syncError = 'Gagal sync Program';
      }
    } catch (e) {
      _syncError = 'Error: $e';
      debugPrint('❌ Sync Program error: $e');
    } finally {
      _isSyncing = false;
      notifyListeners();
    }
  }

  /// Sync Kegiatan jika diperlukan
  Future<void> syncKegiatanIfNeeded(String prgID) async {
    if (!needsSync('/dmaster/kodefikasi/program/$prgID/kegiatan')) {
      debugPrint('✅ Kegiatan data masih fresh, skip sync');
      return;
    }

    _isSyncing = true;
    _syncError = null;
    notifyListeners();

    try {
      final success = await syncService.syncKegiatan(prgID);
      if (!success) {
        _syncError = 'Gagal sync Kegiatan';
      }
    } catch (e) {
      _syncError = 'Error: $e';
      debugPrint('❌ Sync Kegiatan error: $e');
    } finally {
      _isSyncing = false;
      notifyListeners();
    }
  }

  /// Sync Sub Kegiatan jika diperlukan
  Future<void> syncSubKegiatanIfNeeded(String kgtID, String sOrgID) async {
    if (!needsSync('/dmaster/kodefikasi/kegiatan/$kgtID/subkegiatanrka', orgId: sOrgID)) {
      debugPrint('✅ Sub Kegiatan data masih fresh, skip sync');
      return;
    }

    _isSyncing = true;
    _syncError = null;
    notifyListeners();

    try {
      final success = await syncService.syncSubKegiatan(kgtID, sOrgID);
      if (!success) {
        _syncError = 'Gagal sync Sub Kegiatan';
      }
    } catch (e) {
      _syncError = 'Error: $e';
      debugPrint('❌ Sync Sub Kegiatan error: $e');
    } finally {
      _isSyncing = false;
      notifyListeners();
    }
  }

  /// Full sync semua data master
  Future<Map<String, bool>> syncAll(String tahun) async {
    _isSyncing = true;
    _syncError = null;
    notifyListeners();

    try {
      final results = await syncService.syncAllMasterData(tahun);
      _lastFullSync = DateTime.now();
      return results;
    } catch (e) {
      _syncError = 'Error: $e';
      debugPrint('❌ Full sync error: $e');
      return {};
    } finally {
      _isSyncing = false;
      notifyListeners();
    }
  }

  /// Get sync status untuk endpoint tertentu
  SyncMetadata? getSyncStatus(String endpoint, {String? tahun, String? orgId}) {
    return HiveService.getSyncMetadata(endpoint, tahun: tahun, orgId: orgId);
  }

  /// Clear semua data master (untuk testing atau reset)
  Future<void> clearAllData() async {
    try {
      final boxes = [
        HiveBoxNames.opd,
        HiveBoxNames.unitKerja,
        HiveBoxNames.urusan,
        HiveBoxNames.bidangUrusan,
        HiveBoxNames.program,
        HiveBoxNames.kegiatan,
        HiveBoxNames.subKegiatan,
        HiveBoxNames.syncMetadata,
      ];

      for (final boxName in boxes) {
        await HiveService.clearBox(boxName);
      }

      _lastFullSync = null;
      notifyListeners();
      debugPrint('🗑️ All master data cleared');
    } catch (e) {
      debugPrint('❌ Error clearing data: $e');
    }
  }

  /// Get storage statistics
  Map<String, int> getStorageStats() {
    return {
      'opd': HiveService.getBoxSize(HiveBoxNames.opd),
      'unitKerja': HiveService.getBoxSize(HiveBoxNames.unitKerja),
      'program': HiveService.getBoxSize(HiveBoxNames.program),
      'kegiatan': HiveService.getBoxSize(HiveBoxNames.kegiatan),
      'subKegiatan': HiveService.getBoxSize(HiveBoxNames.subKegiatan),
    };
  }
}

