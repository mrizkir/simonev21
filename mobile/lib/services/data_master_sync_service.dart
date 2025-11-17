import 'package:flutter/foundation.dart';
import '../services/api_service.dart';
import '../services/hive_service.dart';
import '../models/hive_models.dart';

/// Service untuk sync data master dari API ke Hive
class DataMasterSyncService {
  final ApiService apiService;

  DataMasterSyncService({required this.apiService});

  /// Sync OPD/SKPD dengan version support
  /// 
  /// [tahun] - Tahun anggaran
  /// [clientVersion] - Version dari client (optional, untuk version-based sync)
  /// 
  /// Returns:
  /// - true jika sync berhasil atau data tidak berubah (304)
  /// - false jika error
  Future<bool> syncOPD(String tahun, {String? clientVersion}) async {
    try {
      debugPrint('🔄 Syncing OPD for tahun: $tahun${clientVersion != null ? ' (version: $clientVersion)' : ''}');
      
      // Prepare request data
      final requestData = <String, dynamic>{'tahun': tahun};
      if (clientVersion != null) {
        requestData['version'] = clientVersion;
      }
      
      final response = await apiService.getOPDList(requestData);
      
      // Handle 304 Not Modified (data tidak berubah)
      if (response.statusCode == 304) {
        debugPrint('✅ OPD data tidak berubah (304), skip sync');
        // Update lastSync tapi keep version yang sama
        await HiveService.saveSyncMetadata(SyncMetadata(
          endpoint: '/dmaster/opd',
          tahun: tahun,
          lastSync: DateTime.now(),
          version: clientVersion, // Keep existing version
        ));
        return true;
      }
      
      // Handle 200 OK dengan data baru
      if (response.statusCode == 200 && response.data['opd'] != null) {
        final opdList = response.data['opd'] as List;
        final dataMap = <String, Map<String, dynamic>>{};
        
        for (var opd in opdList) {
          final orgId = opd['OrgID']?.toString() ?? '';
          if (orgId.isNotEmpty) {
            dataMap[orgId] = Map<String, dynamic>.from(opd);
          }
        }
        
        await HiveService.saveMasterDataBatch(HiveBoxNames.opd, dataMap);
        
        // Get version dari response (jika ada) atau generate dari timestamp
        final serverVersion = response.data['version']?.toString() ?? 
                             DateTime.now().toIso8601String();
        
        // Save sync metadata dengan version baru
        await HiveService.saveSyncMetadata(SyncMetadata(
          endpoint: '/dmaster/opd',
          tahun: tahun,
          lastSync: DateTime.now(),
          version: serverVersion,
        ));
        
        debugPrint('✅ Synced ${dataMap.length} OPD (version: $serverVersion)');
        return true;
      }
      
      return false;
    } catch (e) {
      debugPrint('❌ Error syncing OPD: $e');
      return false;
    }
  }

  /// Sync Unit Kerja untuk OPD tertentu
  Future<bool> syncUnitKerja(String orgID) async {
    try {
      debugPrint('🔄 Syncing Unit Kerja for OrgID: $orgID');
      
      final response = await apiService.getUnitKerjaList(orgID);
      
      if (response.statusCode == 200 && response.data['unitkerja'] != null) {
        final unitList = response.data['unitkerja'] as List;
        final dataMap = <String, Map<String, dynamic>>{};
        
        for (var unit in unitList) {
          final sOrgId = unit['SOrgID']?.toString() ?? '';
          if (sOrgId.isNotEmpty) {
            dataMap[sOrgId] = Map<String, dynamic>.from(unit);
          }
        }
        
        // Save dengan prefix orgID untuk memudahkan query
        final prefixedMap = <String, Map<String, dynamic>>{};
        for (var entry in dataMap.entries) {
          prefixedMap['${orgID}_${entry.key}'] = entry.value;
        }
        
        await HiveService.saveMasterDataBatch(HiveBoxNames.unitKerja, prefixedMap);
        
        // Save sync metadata
        await HiveService.saveSyncMetadata(SyncMetadata(
          endpoint: '/dmaster/opd/$orgID/unitkerja',
          orgId: orgID,
          lastSync: DateTime.now(),
        ));
        
        debugPrint('✅ Synced ${dataMap.length} Unit Kerja');
        return true;
      }
      
      return false;
    } catch (e) {
      debugPrint('❌ Error syncing Unit Kerja: $e');
      return false;
    }
  }

  /// Sync Program berdasarkan Bidang Urusan
  Future<bool> syncProgram(String bidangID, String tahun) async {
    try {
      debugPrint('🔄 Syncing Program for BidangID: $bidangID, Tahun: $tahun');
      
      final response = await apiService.getProgramList(bidangID, {'TA': tahun});
      
      if (response.statusCode == 200 && response.data['program'] != null) {
        final programList = response.data['program'] as List;
        final dataMap = <String, Map<String, dynamic>>{};
        
        for (var program in programList) {
          final prgId = program['PrgID']?.toString() ?? '';
          if (prgId.isNotEmpty) {
            dataMap[prgId] = Map<String, dynamic>.from(program);
          }
        }
        
        // Save dengan prefix bidangID
        final prefixedMap = <String, Map<String, dynamic>>{};
        for (var entry in dataMap.entries) {
          prefixedMap['${bidangID}_${entry.key}'] = entry.value;
        }
        
        await HiveService.saveMasterDataBatch(HiveBoxNames.program, prefixedMap);
        
        await HiveService.saveSyncMetadata(SyncMetadata(
          endpoint: '/dmaster/kodefikasi/bidangurusan/$bidangID/program',
          tahun: tahun,
          lastSync: DateTime.now(),
        ));
        
        debugPrint('✅ Synced ${dataMap.length} Program');
        return true;
      }
      
      return false;
    } catch (e) {
      debugPrint('❌ Error syncing Program: $e');
      return false;
    }
  }

  /// Sync Kegiatan berdasarkan Program
  Future<bool> syncKegiatan(String prgID) async {
    try {
      debugPrint('🔄 Syncing Kegiatan for PrgID: $prgID');
      
      final response = await apiService.getKegiatanList(prgID);
      
      if (response.statusCode == 200 && response.data['programkegiatan'] != null) {
        final kegiatanList = response.data['programkegiatan'] as List;
        final dataMap = <String, Map<String, dynamic>>{};
        
        for (var kegiatan in kegiatanList) {
          final kgtId = kegiatan['KgtID']?.toString() ?? '';
          if (kgtId.isNotEmpty) {
            dataMap[kgtId] = Map<String, dynamic>.from(kegiatan);
          }
        }
        
        // Save dengan prefix prgID
        final prefixedMap = <String, Map<String, dynamic>>{};
        for (var entry in dataMap.entries) {
          prefixedMap['${prgID}_${entry.key}'] = entry.value;
        }
        
        await HiveService.saveMasterDataBatch(HiveBoxNames.kegiatan, prefixedMap);
        
        await HiveService.saveSyncMetadata(SyncMetadata(
          endpoint: '/dmaster/kodefikasi/program/$prgID/kegiatan',
          lastSync: DateTime.now(),
        ));
        
        debugPrint('✅ Synced ${dataMap.length} Kegiatan');
        return true;
      }
      
      return false;
    } catch (e) {
      debugPrint('❌ Error syncing Kegiatan: $e');
      return false;
    }
  }

  /// Sync Sub Kegiatan berdasarkan Kegiatan
  Future<bool> syncSubKegiatan(String kgtID, String sOrgID) async {
    try {
      debugPrint('🔄 Syncing Sub Kegiatan for KgtID: $kgtID, SOrgID: $sOrgID');
      
      final response = await apiService.getSubKegiatanList(kgtID, {'SOrgID': sOrgID});
      
      if (response.statusCode == 200 && response.data['subkegiatanrka'] != null) {
        final subKegiatanList = response.data['subkegiatanrka'] as List;
        final dataMap = <String, Map<String, dynamic>>{};
        
        for (var subKgt in subKegiatanList) {
          final subKgtId = subKgt['SubKgtID']?.toString() ?? '';
          if (subKgtId.isNotEmpty) {
            dataMap[subKgtId] = Map<String, dynamic>.from(subKgt);
          }
        }
        
        // Save dengan prefix kgtID_sOrgID
        final prefixedMap = <String, Map<String, dynamic>>{};
        for (var entry in dataMap.entries) {
          prefixedMap['${kgtID}_${sOrgID}_${entry.key}'] = entry.value;
        }
        
        await HiveService.saveMasterDataBatch(HiveBoxNames.subKegiatan, prefixedMap);
        
        await HiveService.saveSyncMetadata(SyncMetadata(
          endpoint: '/dmaster/kodefikasi/kegiatan/$kgtID/subkegiatanrka',
          orgId: sOrgID,
          lastSync: DateTime.now(),
        ));
        
        debugPrint('✅ Synced ${dataMap.length} Sub Kegiatan');
        return true;
      }
      
      return false;
    } catch (e) {
      debugPrint('❌ Error syncing Sub Kegiatan: $e');
      return false;
    }
  }

  /// Sync Tahun Anggaran
  Future<bool> syncTahunAnggaran() async {
    try {
      debugPrint('🔄 Syncing Tahun Anggaran');
      
      final response = await apiService.getTahunAnggaran();
      
      if (response.statusCode == 200 && response.data != null) {
        final taList = response.data is List ? response.data as List : [response.data];
        final dataMap = <String, Map<String, dynamic>>{};
        
        for (var ta in taList) {
          final taId = ta['TA']?.toString() ?? ta['id']?.toString() ?? '';
          if (taId.isNotEmpty) {
            dataMap[taId] = Map<String, dynamic>.from(ta);
          }
        }
        
        await HiveService.saveMasterDataBatch(HiveBoxNames.tahunAnggaran, dataMap);
        
        await HiveService.saveSyncMetadata(SyncMetadata(
          endpoint: '/dmaster/ta',
          lastSync: DateTime.now(),
        ));
        
        debugPrint('✅ Synced ${dataMap.length} Tahun Anggaran');
        return true;
      }
      
      return false;
    } catch (e) {
      debugPrint('❌ Error syncing Tahun Anggaran: $e');
      return false;
    }
  }

  /// Sync semua data master yang diperlukan
  /// Biasanya dipanggil saat pertama kali login atau manual sync
  Future<Map<String, bool>> syncAllMasterData(String tahun) async {
    final results = <String, bool>{};
    
    debugPrint('🚀 Starting full sync for tahun: $tahun');
    
    // Sync Tahun Anggaran
    results['tahunAnggaran'] = await syncTahunAnggaran();
    
    // Sync OPD
    results['opd'] = await syncOPD(tahun);
    
    // Sync lainnya bisa ditambahkan sesuai kebutuhan
    
    debugPrint('✅ Full sync completed: $results');
    return results;
  }

  /// Sync data master yang diperlukan untuk RKA Murni
  Future<bool> syncForRKAMurni(String tahun, String orgID) async {
    try {
      // Sync OPD
      await syncOPD(tahun);
      
      // Sync Unit Kerja untuk OPD ini
      await syncUnitKerja(orgID);
      
      return true;
    } catch (e) {
      debugPrint('❌ Error syncing for RKA Murni: $e');
      return false;
    }
  }
}

