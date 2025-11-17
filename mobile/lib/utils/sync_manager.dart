import 'package:flutter/foundation.dart';
import '../providers/data_master_provider.dart';

/// Manager untuk handle sync strategy dan update mechanism
class SyncManager {
  final DataMasterProvider dataMasterProvider;

  SyncManager({required this.dataMasterProvider});

  /// Check dan sync semua data master yang diperlukan
  /// 
  /// Strategy:
  /// 1. Check TTL - jika masih fresh, skip
  /// 2. Check version - jika sama, skip (jika backend support)
  /// 3. Sync jika perlu
  Future<Map<String, bool>> checkAndSyncAll(String tahun) async {
    final results = <String, bool>{};
    
    debugPrint('🔍 Checking sync status for tahun: $tahun');
    
    // Check OPD
    if (dataMasterProvider.needsSync('/dmaster/opd', tahun: tahun)) {
      debugPrint('📥 OPD needs sync');
      results['opd'] = await _syncWithRetry(() => dataMasterProvider.syncOPDIfNeeded(tahun));
    } else {
      debugPrint('✅ OPD masih fresh');
      results['opd'] = true;
    }
    
    // Check Tahun Anggaran
    if (dataMasterProvider.needsSync('/dmaster/ta')) {
      debugPrint('📥 Tahun Anggaran needs sync');
      // Add sync tahun anggaran jika diperlukan
    }
    
    return results;
  }

  /// Sync dengan retry mechanism
  Future<bool> _syncWithRetry(Future<void> Function() syncFunction, {int maxRetries = 2}) async {
    int retries = 0;
    
    while (retries < maxRetries) {
      try {
        await syncFunction();
        return true;
      } catch (e) {
        retries++;
        if (retries >= maxRetries) {
          debugPrint('❌ Sync failed after $maxRetries retries: $e');
          return false;
        }
        debugPrint('⚠️ Sync failed, retrying... ($retries/$maxRetries)');
        await Future.delayed(Duration(seconds: 2));
      }
    }
    
    return false;
  }

  /// Get sync summary untuk semua data master
  Map<String, SyncStatus> getSyncSummary(String tahun) {
    final summary = <String, SyncStatus>{};
    
    final endpoints = [
      '/dmaster/opd',
      '/dmaster/ta',
      // Tambahkan endpoint lain sesuai kebutuhan
    ];
    
    for (final endpoint in endpoints) {
      final metadata = dataMasterProvider.getSyncStatus(endpoint, tahun: tahun);
      final needsSync = dataMasterProvider.needsSync(endpoint, tahun: tahun);
      
      summary[endpoint] = SyncStatus(
        endpoint: endpoint,
        lastSync: metadata?.lastSync,
        version: metadata?.version,
        needsSync: needsSync,
        age: metadata != null 
            ? DateTime.now().difference(metadata.lastSync)
            : null,
      );
    }
    
    return summary;
  }

  /// Force sync semua data master (untuk manual refresh)
  Future<Map<String, bool>> forceSyncAll(String tahun) async {
    debugPrint('🔄 Force syncing all master data for tahun: $tahun');
    return await dataMasterProvider.syncAll(tahun);
  }

  /// Sync specific endpoint (untuk targeted update)
  Future<bool> syncEndpoint(String endpoint, {String? tahun, String? orgId}) async {
    debugPrint('🔄 Syncing specific endpoint: $endpoint');
    
    switch (endpoint) {
      case '/dmaster/opd':
        if (tahun != null) {
          await dataMasterProvider.forceSyncOPD(tahun);
          return true;
        }
        break;
      // Tambahkan case lain sesuai kebutuhan
    }
    
    return false;
  }
}

/// Model untuk sync status
class SyncStatus {
  final String endpoint;
  final DateTime? lastSync;
  final String? version;
  final bool needsSync;
  final Duration? age;

  SyncStatus({
    required this.endpoint,
    this.lastSync,
    this.version,
    required this.needsSync,
    this.age,
  });

  String get statusText {
    if (lastSync == null) return 'Belum pernah sync';
    if (needsSync) return 'Perlu sync';
    if (age != null) {
      if (age!.inHours < 1) return 'Sync ${age!.inMinutes} menit lalu';
      if (age!.inDays < 1) return 'Sync ${age!.inHours} jam lalu';
      return 'Sync ${age!.inDays} hari lalu';
    }
    return 'Up to date';
  }
}

