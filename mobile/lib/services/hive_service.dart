import 'package:flutter/foundation.dart';
import 'package:hive_flutter/hive_flutter.dart';
import '../models/hive_models.dart';

/// Service untuk manage Hive database
class HiveService {
  static bool _initialized = false;

  /// Initialize Hive
  static Future<void> init() async {
    if (_initialized) return;

    try {
      await Hive.initFlutter();
      
      // Register adapters
      // Note: Untuk production, perlu generate adapters dengan build_runner
      // Hive.registerAdapter(SyncMetadataAdapter());
      // Hive.registerAdapter(MasterDataItemAdapter());
      
      // Open boxes
      await _openBoxes();
      
      _initialized = true;
      debugPrint('✅ Hive initialized successfully');
    } catch (e) {
      debugPrint('❌ Hive initialization error: $e');
      rethrow;
    }
  }

  /// Open semua boxes yang diperlukan
  static Future<void> _openBoxes() async {
    final boxes = [
      HiveBoxNames.opd,
      HiveBoxNames.unitKerja,
      HiveBoxNames.urusan,
      HiveBoxNames.bidangUrusan,
      HiveBoxNames.program,
      HiveBoxNames.kegiatan,
      HiveBoxNames.subKegiatan,
      HiveBoxNames.rekeningAkun,
      HiveBoxNames.rekeningKelompok,
      HiveBoxNames.rekeningJenis,
      HiveBoxNames.rekeningObjek,
      HiveBoxNames.rekeningRincianObjek,
      HiveBoxNames.rekeningSubRincianObjek,
      HiveBoxNames.asn,
      HiveBoxNames.pejabat,
      HiveBoxNames.sumberDana,
      HiveBoxNames.tahunAnggaran,
      HiveBoxNames.jenisPelaksanaan,
      HiveBoxNames.jenisPembangunan,
      HiveBoxNames.provinsi,
      HiveBoxNames.kabupaten,
      HiveBoxNames.kecamatan,
      HiveBoxNames.desa,
      HiveBoxNames.syncMetadata,
    ];

    for (final boxName in boxes) {
      try {
        await Hive.openBox(boxName);
        debugPrint('📦 Opened box: $boxName');
      } catch (e) {
        debugPrint('⚠️ Error opening box $boxName: $e');
      }
    }
  }

  /// Get box by name
  static Box getBox(String boxName) {
    if (!Hive.isBoxOpen(boxName)) {
      throw Exception('Box $boxName is not open');
    }
    return Hive.box(boxName);
  }

  /// Save data master ke Hive
  /// 
  /// [boxName] - Nama box (dari HiveBoxNames)
  /// [key] - Key untuk menyimpan (biasanya ID)
  /// [data] - Data yang akan disimpan
  static Future<void> saveMasterData(
    String boxName,
    String key,
    Map<String, dynamic> data,
  ) async {
    try {
      final box = getBox(boxName);
      await box.put(key, data);
      debugPrint('💾 Saved to $boxName: $key');
    } catch (e) {
      debugPrint('❌ Error saving to $boxName: $e');
      rethrow;
    }
  }

  /// Save multiple data master sekaligus
  static Future<void> saveMasterDataBatch(
    String boxName,
    Map<String, Map<String, dynamic>> dataMap,
  ) async {
    try {
      final box = getBox(boxName);
      await box.putAll(dataMap);
      debugPrint('💾 Saved ${dataMap.length} items to $boxName');
    } catch (e) {
      debugPrint('❌ Error batch saving to $boxName: $e');
      rethrow;
    }
  }

  /// Get data master dari Hive
  static Map<String, dynamic>? getMasterData(String boxName, String key) {
    try {
      final box = getBox(boxName);
      final data = box.get(key);
      if (data == null) return null;
      
      // Convert ke Map jika perlu
      if (data is Map) {
        return Map<String, dynamic>.from(data);
      }
      return null;
    } catch (e) {
      debugPrint('❌ Error getting from $boxName: $e');
      return null;
    }
  }

  /// Get all data dari box
  static List<Map<String, dynamic>> getAllMasterData(String boxName) {
    try {
      final box = getBox(boxName);
      final allData = <Map<String, dynamic>>[];
      
      for (var key in box.keys) {
        final data = box.get(key);
        if (data is Map) {
          allData.add(Map<String, dynamic>.from(data));
        }
      }
      
      return allData;
    } catch (e) {
      debugPrint('❌ Error getting all from $boxName: $e');
      return [];
    }
  }

  /// Get data dengan filter
  static List<Map<String, dynamic>> getMasterDataFiltered(
    String boxName,
    bool Function(Map<String, dynamic>) filter,
  ) {
    try {
      final allData = getAllMasterData(boxName);
      return allData.where(filter).toList();
    } catch (e) {
      debugPrint('❌ Error filtering $boxName: $e');
      return [];
    }
  }

  /// Delete data master
  static Future<void> deleteMasterData(String boxName, String key) async {
    try {
      final box = getBox(boxName);
      await box.delete(key);
      debugPrint('🗑️ Deleted from $boxName: $key');
    } catch (e) {
      debugPrint('❌ Error deleting from $boxName: $e');
      rethrow;
    }
  }

  /// Clear semua data di box
  static Future<void> clearBox(String boxName) async {
    try {
      final box = getBox(boxName);
      await box.clear();
      debugPrint('🗑️ Cleared box: $boxName');
    } catch (e) {
      debugPrint('❌ Error clearing $boxName: $e');
      rethrow;
    }
  }

  /// Save sync metadata
  static Future<void> saveSyncMetadata(SyncMetadata metadata) async {
    try {
      final box = getBox(HiveBoxNames.syncMetadata);
      await box.put(metadata.cacheKey, metadata.toMap());
      debugPrint('💾 Saved sync metadata: ${metadata.cacheKey}');
    } catch (e) {
      debugPrint('❌ Error saving sync metadata: $e');
      rethrow;
    }
  }

  /// Get sync metadata
  static SyncMetadata? getSyncMetadata(String endpoint, {String? tahun, String? orgId}) {
    try {
      final box = getBox(HiveBoxNames.syncMetadata);
      String cacheKey;
      if (orgId != null) {
        cacheKey = '${endpoint}_${tahun}_$orgId';
      } else if (tahun != null) {
        cacheKey = '${endpoint}_$tahun';
      } else {
        cacheKey = endpoint;
      }
      
      final data = box.get(cacheKey);
      if (data == null) return null;
      
      if (data is Map) {
        return SyncMetadata.fromMap(Map<String, dynamic>.from(data));
      }
      return null;
    } catch (e) {
      debugPrint('❌ Error getting sync metadata: $e');
      return null;
    }
  }

  /// Check if data perlu di-sync
  /// Returns true jika data belum pernah di-sync atau sudah expired
  static bool needsSync(
    String endpoint, {
    String? tahun,
    String? orgId,
    Duration maxAge = const Duration(hours: 24),
  }) {
    final metadata = getSyncMetadata(endpoint, tahun: tahun, orgId: orgId);
    if (metadata == null) return true;
    
    final age = DateTime.now().difference(metadata.lastSync);
    return age > maxAge;
  }

  /// Get box size (jumlah item)
  static int getBoxSize(String boxName) {
    try {
      final box = getBox(boxName);
      return box.length;
    } catch (e) {
      return 0;
    }
  }

  /// Get total storage size (approximate)
  static int getTotalSize() {
    int total = 0;
    final boxes = [
      HiveBoxNames.opd,
      HiveBoxNames.unitKerja,
      HiveBoxNames.urusan,
      HiveBoxNames.bidangUrusan,
      HiveBoxNames.program,
      HiveBoxNames.kegiatan,
      HiveBoxNames.subKegiatan,
      // ... other boxes
    ];
    
    for (final boxName in boxes) {
      if (Hive.isBoxOpen(boxName)) {
        total += getBoxSize(boxName);
      }
    }
    
    return total;
  }

  /// Close semua boxes
  static Future<void> closeAll() async {
    await Hive.close();
    _initialized = false;
    debugPrint('🔒 All Hive boxes closed');
  }
}

