// CONTOH: Implementasi caching di RKAMurniProvider
// File ini adalah contoh, bukan file yang digunakan langsung
// Copy logic caching ke provider yang sebenarnya

import 'package:flutter/material.dart';
import '../models/rka_murni_model.dart';
import '../services/api_service.dart';
import '../services/cache_service.dart';

class RKAMurniProviderCachedExample with ChangeNotifier {
  final ApiService apiService;

  // ... existing fields ...

  // Fetch OPD List dengan caching
  Future<void> fetchOPD(String tahun) async {
    try {
      _errorMessage = null;
      notifyListeners();

      // 1. Cek cache dulu
      final cacheKey = '/dmaster/opd';
      final cachedData = await CacheService.getCache(
        cacheKey,
        params: {'tahun': tahun},
      );

      if (cachedData != null) {
        // Gunakan data dari cache
        if (cachedData['opd'] != null) {
          _daftarOPD = (cachedData['opd'] as List)
              .map((item) => OPDModel.fromJson(item))
              .toList();
        }
        _dataTableLoaded = false;
        _errorMessage = null;
        notifyListeners();
        
        // Load fresh data di background (optional)
        _fetchOPDFresh(tahun);
        return;
      }

      // 2. Jika tidak ada cache, fetch dari API
      await _fetchOPDFresh(tahun);
    } catch (e) {
      _errorMessage = _getErrorMessage(e);
      debugPrint('RKAMurni fetchOPD error: $e');
      notifyListeners();
    }
  }

  // Fetch fresh data dari API
  Future<void> _fetchOPDFresh(String tahun) async {
    try {
      final response = await apiService.getOPDList({'tahun': tahun});

      if (response.statusCode == 200) {
        final data = response.data;
        if (data['opd'] != null) {
          _daftarOPD = (data['opd'] as List)
              .map((item) => OPDModel.fromJson(item))
              .toList();
        }
        _dataTableLoaded = false;
        _errorMessage = null;

        // 3. Simpan ke cache dengan TTL 24 jam (data master)
        await CacheService.setCache(
          '/dmaster/opd',
          data,
          params: {'tahun': tahun},
          ttlSeconds: CacheTTL.masterData, // 24 jam
        );
      } else {
        _errorMessage = 'Gagal memuat daftar OPD';
      }
    } catch (e) {
      _errorMessage = _getErrorMessage(e);
      debugPrint('RKAMurni _fetchOPDFresh error: $e');
    }
    notifyListeners();
  }

  // Load Data Kegiatan dengan caching
  Future<void> loadDataKegiatan(String tahun, String bulan) async {
    if (_sOrgIDSelected == null || _sOrgIDSelected!.isEmpty) {
      return;
    }

    try {
      _dataTableLoading = true;
      _errorMessage = null;
      notifyListeners();

      // 1. Cek cache
      final cacheKey = '/renja/rkamurni';
      final cachedData = await CacheService.getCache(
        cacheKey,
        params: {
          'tahun': tahun,
          'bulan': bulan,
          'SOrgID': _sOrgIDSelected!,
        },
      );

      if (cachedData != null) {
        // Gunakan data dari cache
        if (cachedData['unitkerja'] != null) {
          _dataUnitKerja = UnitKerjaModel.fromJson(cachedData['unitkerja']);
        }
        if (cachedData['rka'] != null) {
          _dataTable = (cachedData['rka'] as List)
              .map((item) => RKAItem.fromJson(item))
              .toList();
        }
        _dataTableLoaded = true;
        _calculateSummary();
        _errorMessage = null;
        _dataTableLoading = false;
        notifyListeners();

        // Load fresh data di background
        _loadDataKegiatanFresh(tahun, bulan);
        return;
      }

      // 2. Fetch dari API
      await _loadDataKegiatanFresh(tahun, bulan);
    } catch (e) {
      _errorMessage = _getErrorMessage(e);
      debugPrint('RKAMurni loadDataKegiatan error: $e');
      _dataTableLoading = false;
      notifyListeners();
    }
  }

  // Fetch fresh data dari API
  Future<void> _loadDataKegiatanFresh(String tahun, String bulan) async {
    try {
      final response = await apiService.getRKAMurniList({
        'tahun': tahun,
        'bulan': bulan,
        'SOrgID': _sOrgIDSelected!,
      });

      if (response.statusCode == 200) {
        final data = response.data;
        if (data['unitkerja'] != null) {
          _dataUnitKerja = UnitKerjaModel.fromJson(data['unitkerja']);
        }
        if (data['rka'] != null) {
          _dataTable = (data['rka'] as List)
              .map((item) => RKAItem.fromJson(item))
              .toList();
        }
        _dataTableLoaded = true;
        _calculateSummary();
        _errorMessage = null;

        // Simpan ke cache dengan TTL 5 menit (data transaksi)
        await CacheService.setCache(
          '/renja/rkamurni',
          data,
          params: {
            'tahun': tahun,
            'bulan': bulan,
            'SOrgID': _sOrgIDSelected!,
          },
          ttlSeconds: CacheTTL.transaction, // 5 menit
        );
      } else {
        _errorMessage = 'Gagal memuat data RKA';
      }
    } catch (e) {
      _errorMessage = _getErrorMessage(e);
      debugPrint('RKAMurni _loadDataKegiatanFresh error: $e');
    }
    _dataTableLoading = false;
    notifyListeners();
  }

  // Invalidate cache saat data diubah (save, delete, update)
  Future<void> invalidateCache() async {
    // Hapus cache untuk data yang berubah
    await CacheService.removeCache('/renja/rkamurni');
    // Bisa juga clear semua cache jika perlu
    // await CacheService.clearAllCache();
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

