// CONTOH: RKAMurniProvider yang menggunakan Hive untuk data master
// Copy logic ini ke RKAMurniProvider yang sebenarnya

import 'package:flutter/material.dart';
import '../models/rka_murni_model.dart';
import '../services/api_service.dart';
import '../services/cache_service.dart';
import '../providers/data_master_provider.dart';

class RKAMurniProviderWithHive with ChangeNotifier {
  final ApiService apiService;
  final DataMasterProvider dataMasterProvider;

  // ... existing fields ...

  RKAMurniProviderWithHive({
    required this.apiService,
    required this.dataMasterProvider,
  });

  // Fetch OPD List - menggunakan Hive
  Future<void> fetchOPD(String tahun) async {
    try {
      _errorMessage = null;
      notifyListeners();

      // 1. Cek Hive dulu
      final opdFromHive = dataMasterProvider.getOPDFromHive(tahun: tahun);
      
      if (opdFromHive.isNotEmpty) {
        // Data ada di Hive, gunakan langsung
        _daftarOPD = opdFromHive;
        _dataTableLoaded = false;
        _errorMessage = null;
        notifyListeners();
        
        // 2. Sync di background jika perlu update
        dataMasterProvider.syncOPDIfNeeded(tahun).then((_) {
          // Update jika ada data baru
          final updated = dataMasterProvider.getOPDFromHive(tahun: tahun);
          if (updated.length != _daftarOPD.length) {
            _daftarOPD = updated;
            notifyListeners();
          }
        });
        
        return;
      }

      // 3. Jika Hive kosong, sync dari API
      await dataMasterProvider.syncOPDIfNeeded(tahun);
      _daftarOPD = dataMasterProvider.getOPDFromHive(tahun: tahun);
      _dataTableLoaded = false;
      _errorMessage = null;
    } catch (e) {
      _errorMessage = _getErrorMessage(e);
      debugPrint('RKAMurni fetchOPD error: $e');
    }
    notifyListeners();
  }

  // Load Unit Kerja - menggunakan Hive
  Future<void> loadUnitKerja(String orgID) async {
    try {
      _errorMessage = null;
      _orgIDSelected = orgID;
      
      // Clear Unit Kerja data first
      _daftarUnitKerja = [];
      _sOrgIDSelected = null;
      _dataUnitKerja = null;
      _dataTable = [];
      _dataTableLoaded = false;
      notifyListeners();

      // 1. Cek Hive dulu
      final unitFromHive = dataMasterProvider.getUnitKerjaFromHive(orgID);
      
      if (unitFromHive.isNotEmpty) {
        // Data ada di Hive - Remove duplicates by SOrgID
        final seen = <String>{};
        _daftarUnitKerja = unitFromHive.where((unit) {
          if (seen.contains(unit.SOrgID)) {
            return false;
          }
          seen.add(unit.SOrgID);
          return true;
        }).toList();
        
        // Get OPD data dari Hive juga
        final opdData = dataMasterProvider.getOPDFromHive().firstWhere(
          (opd) => opd.OrgID == orgID,
          orElse: () => _daftarOPD.firstWhere((opd) => opd.OrgID == orgID),
        );
        _dataOPD = opdData;
        
        _dataTableLoaded = false;
        _errorMessage = null;
        notifyListeners();
        
        // 2. Sync di background
        dataMasterProvider.syncUnitKerjaIfNeeded(orgID).then((_) {
          final updated = dataMasterProvider.getUnitKerjaFromHive(orgID);
          // Remove duplicates from updated list too
          final seenUpdated = <String>{};
          final updatedDeduped = updated.where((unit) {
            if (seenUpdated.contains(unit.SOrgID)) {
              return false;
            }
            seenUpdated.add(unit.SOrgID);
            return true;
          }).toList();
          
          if (updatedDeduped.length != _daftarUnitKerja.length) {
            _daftarUnitKerja = updatedDeduped;
            notifyListeners();
          }
        });
        
        return;
      }

      // 3. Jika Hive kosong, sync dari API
      await dataMasterProvider.syncUnitKerjaIfNeeded(orgID);
      final unitFromHiveAfterSync = dataMasterProvider.getUnitKerjaFromHive(orgID);
      
      // Remove duplicates by SOrgID
      final seen = <String>{};
      _daftarUnitKerja = unitFromHiveAfterSync.where((unit) {
        if (seen.contains(unit.SOrgID)) {
          return false;
        }
        seen.add(unit.SOrgID);
        return true;
      }).toList();
      
      // Get OPD data
      final opdData = dataMasterProvider.getOPDFromHive().firstWhere(
        (opd) => opd.OrgID == orgID,
        orElse: () => _daftarOPD.firstWhere((opd) => opd.OrgID == orgID),
      );
      _dataOPD = opdData;
      
      _dataTableLoaded = false;
      _errorMessage = null;
    } catch (e) {
      _errorMessage = _getErrorMessage(e);
      debugPrint('RKAMurni loadUnitKerja error: $e');
    }
    notifyListeners();
  }

  // Load Program List - menggunakan Hive
  Future<void> loadProgramList(String bidangID, String tahun) async {
    try {
      _daftarProgram = [];
      _formPrgID = null;
      _daftarKegiatan = [];
      _formKgtID = null;
      _daftarSubKegiatan = [];
      _formSubKgtID = null;
      notifyListeners();

      // 1. Cek Hive dulu
      final programFromHive = dataMasterProvider.getProgramFromHive(bidangID);
      
      if (programFromHive.isNotEmpty) {
        _daftarProgram = programFromHive;
        notifyListeners();
        
        // Sync di background
        dataMasterProvider.syncProgramIfNeeded(bidangID, tahun).then((_) {
          final updated = dataMasterProvider.getProgramFromHive(bidangID);
          if (updated.length != _daftarProgram.length) {
            _daftarProgram = updated;
            notifyListeners();
          }
        });
        
        return;
      }

      // 2. Sync dari API
      await dataMasterProvider.syncProgramIfNeeded(bidangID, tahun);
      _daftarProgram = dataMasterProvider.getProgramFromHive(bidangID);
    } catch (e) {
      debugPrint('RKAMurni loadProgramList error: $e');
    }
    notifyListeners();
  }

  // Load Kegiatan List - menggunakan Hive
  Future<void> loadKegiatanList(String prgID) async {
    try {
      _daftarKegiatan = [];
      _formKgtID = null;
      _daftarSubKegiatan = [];
      _formSubKgtID = null;
      notifyListeners();

      // 1. Cek Hive dulu
      final kegiatanFromHive = dataMasterProvider.getKegiatanFromHive(prgID);
      
      if (kegiatanFromHive.isNotEmpty) {
        _daftarKegiatan = kegiatanFromHive;
        notifyListeners();
        
        // Sync di background
        dataMasterProvider.syncKegiatanIfNeeded(prgID).then((_) {
          final updated = dataMasterProvider.getKegiatanFromHive(prgID);
          if (updated.length != _daftarKegiatan.length) {
            _daftarKegiatan = updated;
            notifyListeners();
          }
        });
        
        return;
      }

      // 2. Sync dari API
      await dataMasterProvider.syncKegiatanIfNeeded(prgID);
      _daftarKegiatan = dataMasterProvider.getKegiatanFromHive(prgID);
    } catch (e) {
      debugPrint('RKAMurni loadKegiatanList error: $e');
    }
    notifyListeners();
  }

  // Load Sub Kegiatan List - menggunakan Hive
  Future<void> loadSubKegiatanList(String kgtID, String sOrgID) async {
    try {
      _daftarSubKegiatan = [];
      _formSubKgtID = null;
      notifyListeners();

      // 1. Cek Hive dulu
      final subKegiatanFromHive = dataMasterProvider.getSubKegiatanFromHive(kgtID, sOrgID);
      
      if (subKegiatanFromHive.isNotEmpty) {
        _daftarSubKegiatan = subKegiatanFromHive;
        notifyListeners();
        
        // Sync di background
        dataMasterProvider.syncSubKegiatanIfNeeded(kgtID, sOrgID).then((_) {
          final updated = dataMasterProvider.getSubKegiatanFromHive(kgtID, sOrgID);
          if (updated.length != _daftarSubKegiatan.length) {
            _daftarSubKegiatan = updated;
            notifyListeners();
          }
        });
        
        return;
      }

      // 2. Sync dari API
      await dataMasterProvider.syncSubKegiatanIfNeeded(kgtID, sOrgID);
      _daftarSubKegiatan = dataMasterProvider.getSubKegiatanFromHive(kgtID, sOrgID);
    } catch (e) {
      debugPrint('RKAMurni loadSubKegiatanList error: $e');
    }
    notifyListeners();
  }

  String _getErrorMessage(dynamic error) {
    if (error.toString().contains('401')) {
      return 'Sesi Anda telah berakhir. Silakan login kembali.';
    } else if (error.toString().contains('SocketException') ||
        error.toString().contains('Connection')) {
      return 'Tidak dapat terhubung ke server';
    } else {
      return 'Gagal memuat data: ${error.toString()}';
    }
  }
}

