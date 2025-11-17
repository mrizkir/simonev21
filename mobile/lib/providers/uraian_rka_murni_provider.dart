import 'package:flutter/material.dart';
import '../models/rka_murni_model.dart';
import '../services/api_service.dart';

class UraianRKAMurniProvider with ChangeNotifier {
  final ApiService apiService;

  // Data Kegiatan
  RKADetailModel? _dataKegiatan;
  
  // Data Table Uraian
  List<RKAUraianItem> _dataTable = [];
  bool _dataTableLoading = false;
  bool _dataTableLoaded = false;
  String _searchQuery = '';

  // Summary/Footer
  RKAUraianSummary _summary = RKAUraianSummary(
    paguuraian: 0,
    realisasi: 0,
    sisa: 0,
    persen_keuangan: 0,
    fisik: 0,
  );

  // Dialog Form
  bool _btnLoading = false;
  String? _errorMessage;

  UraianRKAMurniProvider({required this.apiService});

  // Getters
  RKADetailModel? get dataKegiatan => _dataKegiatan;
  List<RKAUraianItem> get dataTable => _dataTable;
  bool get dataTableLoading => _dataTableLoading;
  bool get dataTableLoaded => _dataTableLoaded;
  String get searchQuery => _searchQuery;
  RKAUraianSummary get summary => _summary;
  bool get btnLoading => _btnLoading;
  String? get errorMessage => _errorMessage;

  // Filtered data table based on search
  List<RKAUraianItem> get filteredDataTable {
    if (_searchQuery.isEmpty) {
      return _dataTable;
    }
    final query = _searchQuery.toLowerCase();
    return _dataTable.where((item) {
      return item.kode_uraian.toLowerCase().contains(query) ||
          item.nama_uraian.toLowerCase().contains(query);
    }).toList();
  }

  // Check if can show load data button
  bool get showBtnLoadDataUraian {
    if (!_dataTableLoaded) {
      return true;
    }
    return _dataTable.isEmpty;
  }

  // Set Data Kegiatan from RKAItem (temporary data while loading from API)
  void setDataKegiatanFromRKAItem(RKAItem item) {
    // Create a temporary RKADetailModel from RKAItem
    // Note: Some fields will be empty until loaded from API
    _dataKegiatan = RKADetailModel(
      RKAID: item.RKAID,
      kode_program: '',
      Nm_Program: '',
      kode_kegiatan: '',
      Nm_Kegiatan: '',
      kode_sub_kegiatan: item.kode_sub_kegiatan,
      Nm_Sub_Kegiatan: item.Nm_Sub_Kegiatan,
      kode_organisasi: '',
      Nm_Organisasi: '',
      kode_sub_organisasi: null,
      Nm_Sub_Organisasi: '',
      Nm_Bidang: null,
      PaguDana1: item.PaguDana1,
      Locked: item.Locked,
      created_at: item.created_at,
      updated_at: item.updated_at,
    );
    notifyListeners();
  }

  // Load Data Kegiatan and Uraian
  Future<void> loadData(String rkaID) async {
    try {
      _dataTableLoading = true;
      _errorMessage = null;
      notifyListeners();

      final response = await apiService.getRKAUraian(rkaID);

      if (response.statusCode == 200) {
        final data = response.data;
        // Update data kegiatan jika ada di response, jika tidak tetap gunakan yang sudah ada
        // Backend mengembalikan 'datakegiatan' bukan 'rka'
        if (data['datakegiatan'] != null) {
          debugPrint('RKADetailModel fromJson data: ${data['datakegiatan']}');
          _dataKegiatan = RKADetailModel.fromJson(data['datakegiatan']);
          debugPrint('RKADetailModel kode_program: ${_dataKegiatan?.kode_program}');
          debugPrint('RKADetailModel Nm_Program: ${_dataKegiatan?.Nm_Program}');
          debugPrint('RKADetailModel kode_kegiatan: ${_dataKegiatan?.kode_kegiatan}');
          debugPrint('RKADetailModel Nm_Kegiatan: ${_dataKegiatan?.Nm_Kegiatan}');
        } else if (data['rka'] != null) {
          // Fallback untuk kompatibilitas jika ada yang menggunakan 'rka'
          debugPrint('RKADetailModel fromJson data (rka): ${data['rka']}');
          _dataKegiatan = RKADetailModel.fromJson(data['rka']);
        } else if (_dataKegiatan == null) {
          // Jika tidak ada data kegiatan di response dan belum ada sebelumnya, buat dari RKAID
          debugPrint('Warning: Response tidak mengandung data datakegiatan/rka, menggunakan data sementara');
        } else {
          debugPrint('Warning: Response tidak mengandung data datakegiatan/rka, tetap menggunakan data sementara yang sudah ada');
        }
        if (data['uraian'] != null) {
          _dataTable = (data['uraian'] as List)
              .map((item) => RKAUraianItem.fromJson(item))
              .toList();
        }
        _dataTableLoaded = true;
        _calculateSummary();
        _errorMessage = null;
      } else {
        _errorMessage = 'Gagal memuat data uraian';
      }
    } catch (e) {
      _errorMessage = _getErrorMessage(e);
      debugPrint('UraianRKAMurni loadData error: $e');
    } finally {
      _dataTableLoading = false;
      notifyListeners();
    }
  }

  // Load Data Uraian First Time
  Future<void> loadDataUraianFirstTime(String rkaID) async {
    try {
      _btnLoading = true;
      _errorMessage = null;
      notifyListeners();

      final response = await apiService.loadDataUraianFirstTime({
        'RKAID': rkaID,
      });

      if (response.statusCode == 200) {
        final data = response.data;
        // Update data kegiatan jika ada di response
        // Backend mengembalikan 'datakegiatan' bukan 'rka'
        if (data['datakegiatan'] != null) {
          _dataKegiatan = RKADetailModel.fromJson(data['datakegiatan']);
        } else if (data['rka'] != null) {
          // Fallback untuk kompatibilitas jika ada yang menggunakan 'rka'
          _dataKegiatan = RKADetailModel.fromJson(data['rka']);
        }
        if (data['uraian'] != null) {
          _dataTable = (data['uraian'] as List)
              .map((item) => RKAUraianItem.fromJson(item))
              .toList();
        }
        _dataTableLoaded = true;
        _calculateSummary();
        _errorMessage = null;
      } else {
        _errorMessage = 'Gagal memuat data uraian';
      }
    } catch (e) {
      _errorMessage = _getErrorMessage(e);
      debugPrint('UraianRKAMurni loadDataUraianFirstTime error: $e');
    } finally {
      _btnLoading = false;
      notifyListeners();
    }
  }

  // Update Uraian
  Future<bool> updateUraian(String rkarincID, Map<String, dynamic> data) async {
    try {
      _btnLoading = true;
      _errorMessage = null;
      notifyListeners();

      final response = await apiService.updateUraian(rkarincID, data);

      if (response.statusCode == 200) {
        final responseData = response.data;
        // Backend mengembalikan 'datakegiatan' bukan 'rka'
        if (responseData['datakegiatan'] != null) {
          _dataKegiatan = RKADetailModel.fromJson(responseData['datakegiatan']);
        } else if (responseData['rka'] != null) {
          // Fallback untuk kompatibilitas
          _dataKegiatan = RKADetailModel.fromJson(responseData['rka']);
        }
        await loadData(_dataKegiatan!.RKAID);
        _errorMessage = null;
        return true;
      } else {
        _errorMessage = 'Gagal mengupdate uraian';
        return false;
      }
    } catch (e) {
      _errorMessage = _getErrorMessage(e);
      debugPrint('UraianRKAMurni updateUraian error: $e');
      return false;
    } finally {
      _btnLoading = false;
      notifyListeners();
    }
  }

  // Delete Uraian
  Future<bool> deleteUraian(String rkarincID) async {
    try {
      _btnLoading = true;
      _errorMessage = null;
      notifyListeners();

      final response = await apiService.deleteUraian(rkarincID);

      if (response.statusCode == 200) {
        if (_dataKegiatan != null) {
          await loadData(_dataKegiatan!.RKAID);
        }
        _errorMessage = null;
        return true;
      } else {
        _errorMessage = 'Gagal menghapus uraian';
        return false;
      }
    } catch (e) {
      _errorMessage = _getErrorMessage(e);
      debugPrint('UraianRKAMurni deleteUraian error: $e');
      return false;
    } finally {
      _btnLoading = false;
      notifyListeners();
    }
  }

  // Reset Data Kegiatan
  Future<bool> resetDataKegiatan(String rkaID) async {
    try {
      _btnLoading = true;
      _errorMessage = null;
      notifyListeners();

      final response = await apiService.resetDataKegiatan(rkaID);

      if (response.statusCode == 200) {
        await loadData(rkaID);
        _errorMessage = null;
        return true;
      } else {
        _errorMessage = 'Gagal reset data kegiatan';
        return false;
      }
    } catch (e) {
      _errorMessage = _getErrorMessage(e);
      debugPrint('UraianRKAMurni resetDataKegiatan error: $e');
      return false;
    } finally {
      _btnLoading = false;
      notifyListeners();
    }
  }

  // Set Search Query
  void setSearchQuery(String query) {
    _searchQuery = query;
    notifyListeners();
  }

  // Calculate Summary
  void _calculateSummary() {
    if (_dataTable.isEmpty) {
      _summary = RKAUraianSummary(
        paguuraian: 0,
        realisasi: 0,
        sisa: 0,
        persen_keuangan: 0,
        fisik: 0,
      );
      return;
    }

    double totalPaguUraian = _dataTable.fold(0.0, (sum, item) => sum + item.PaguUraian1);
    double totalRealisasi = _dataTable.fold(0.0, (sum, item) => sum + item.realisasi1);
    double totalSisa = totalPaguUraian - totalRealisasi;
    double persenKeuangan = totalPaguUraian > 0
        ? (totalRealisasi / totalPaguUraian) * 100
        : 0;
    double totalFisik = _dataTable.fold(0.0, (sum, item) => sum + item.fisik1);
    double avgFisik = _dataTable.isNotEmpty ? totalFisik / _dataTable.length : 0;

    _summary = RKAUraianSummary(
      paguuraian: totalPaguUraian,
      realisasi: totalRealisasi,
      sisa: totalSisa,
      persen_keuangan: persenKeuangan,
      fisik: avgFisik,
    );
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

  void clearError() {
    _errorMessage = null;
    notifyListeners();
  }
}

