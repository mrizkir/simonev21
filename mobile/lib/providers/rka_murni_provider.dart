import 'package:flutter/material.dart';
import '../models/rka_murni_model.dart';
import '../services/api_service.dart';

class RKAMurniProvider with ChangeNotifier {
  final ApiService apiService;

  // Filter
  List<OPDModel> _daftarOPD = [];
  String? _orgIDSelected;
  OPDModel? _dataOPD;
  
  List<UnitKerjaModel> _daftarUnitKerja = [];
  String? _sOrgIDSelected;
  UnitKerjaModel? _dataUnitKerja;

  // Data Table
  List<RKAItem> _dataTable = [];
  bool _dataTableLoading = false;
  bool _dataTableLoaded = false;
  String _searchQuery = '';

  // Summary/Footer
  RKASummary _summary = RKASummary(
    paguunitkerja: 0,
    pagukegiatan: 0,
    realisasi: 0,
    sisa: 0,
    persen_keuangan: 0,
    fisik: 0,
  );

  // Dialog Form
  bool _dialogFormOpen = false;
  bool _formValid = true;
  bool _btnLoading = false;
  bool _locked = true;

  // Form Data
  List<BidangUrusanModel> _daftarBidang = [];
  List<ProgramModel> _daftarProgram = [];
  List<KegiatanModel> _daftarKegiatan = [];
  List<SubKegiatanModel> _daftarSubKegiatan = [];
  String? _formBidangID;
  String? _formPrgID;
  String? _formKgtID;
  String? _formSubKgtID;

  // Error
  String? _errorMessage;

  RKAMurniProvider({required this.apiService});

  // Getters
  List<OPDModel> get daftarOPD => _daftarOPD;
  String? get orgIDSelected => _orgIDSelected;
  OPDModel? get dataOPD => _dataOPD;
  List<UnitKerjaModel> get daftarUnitKerja => _daftarUnitKerja;
  String? get sOrgIDSelected => _sOrgIDSelected;
  UnitKerjaModel? get dataUnitKerja => _dataUnitKerja;
  List<RKAItem> get dataTable => _dataTable;
  bool get dataTableLoading => _dataTableLoading;
  bool get dataTableLoaded => _dataTableLoaded;
  String get searchQuery => _searchQuery;
  RKASummary get summary => _summary;
  bool get dialogFormOpen => _dialogFormOpen;
  bool get formValid => _formValid;
  bool get btnLoading => _btnLoading;
  bool get locked => _locked;
  List<BidangUrusanModel> get daftarBidang => _daftarBidang;
  List<ProgramModel> get daftarProgram => _daftarProgram;
  List<KegiatanModel> get daftarKegiatan => _daftarKegiatan;
  List<SubKegiatanModel> get daftarSubKegiatan => _daftarSubKegiatan;
  String? get formBidangID => _formBidangID;
  String? get formPrgID => _formPrgID;
  String? get formKgtID => _formKgtID;
  String? get formSubKgtID => _formSubKgtID;
  String? get errorMessage => _errorMessage;

  // Filtered data table based on search
  List<RKAItem> get filteredDataTable {
    if (_searchQuery.isEmpty) {
      return _dataTable;
    }
    final query = _searchQuery.toLowerCase();
    return _dataTable.where((item) {
      return item.kode_sub_kegiatan.toLowerCase().contains(query) ||
          item.Nm_Sub_Kegiatan.toLowerCase().contains(query);
    }).toList();
  }

  // Check if can show load data button
  bool get showBtnLoadDataKegiatan {
    if (_sOrgIDSelected == null || _sOrgIDSelected!.isEmpty) {
      return false;
    }
    if (!_dataTableLoaded) {
      return true;
    }
    return _dataTable.isEmpty;
  }

  // Fetch OPD List
  Future<void> fetchOPD(String tahun) async {
    try {
      _errorMessage = null;
      notifyListeners();

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
      } else {
        _errorMessage = 'Gagal memuat daftar OPD';
      }
    } catch (e) {
      _errorMessage = _getErrorMessage(e);
      debugPrint('RKAMurni fetchOPD error: $e');
    }
    notifyListeners();
  }

  // Load Unit Kerja
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

      final response = await apiService.getUnitKerjaList(orgID);

      if (response.statusCode == 200) {
        final data = response.data;
        if (data['organisasi'] != null) {
          _dataOPD = OPDModel.fromJson(data['organisasi']);
        }
        if (data['unitkerja'] != null) {
          // Remove duplicates by SOrgID
          final unitKerjaList = (data['unitkerja'] as List)
              .map((item) => UnitKerjaModel.fromJson(item))
              .toList();
          
          // Remove duplicates based on SOrgID and filter out invalid items
          final seen = <String>{};
          _daftarUnitKerja = unitKerjaList.where((unit) {
            // Filter out items with empty SOrgID
            if (unit.SOrgID.isEmpty) {
              return false;
            }
            // Remove duplicates
            if (seen.contains(unit.SOrgID)) {
              return false;
            }
            seen.add(unit.SOrgID);
            return true;
          }).toList();
        }
        _dataTableLoaded = false;
        _errorMessage = null;
      } else {
        _errorMessage = 'Gagal memuat daftar Unit Kerja';
      }
    } catch (e) {
      _errorMessage = _getErrorMessage(e);
      debugPrint('RKAMurni loadUnitKerja error: $e');
    }
    notifyListeners();
  }

  // Set Unit Kerja Selected
  void setSOrgIDSelected(String? sOrgID, String tahun, String bulan) {
    _sOrgIDSelected = sOrgID;
    if (sOrgID != null && sOrgID.isNotEmpty) {
      _dataTableLoaded = false;
      _dataTable = [];
      loadDataKegiatan(tahun, bulan);
    }
    notifyListeners();
  }

  // Load Data Kegiatan
  Future<void> loadDataKegiatan(String tahun, String bulan) async {
    if (_sOrgIDSelected == null || _sOrgIDSelected!.isEmpty) {
      return;
    }

    try {
      _dataTableLoading = true;
      _errorMessage = null;
      notifyListeners();

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
      } else {
        _errorMessage = 'Gagal memuat data RKA';
      }
    } catch (e) {
      _errorMessage = _getErrorMessage(e);
      debugPrint('RKAMurni loadDataKegiatan error: $e');
    } finally {
      _dataTableLoading = false;
      notifyListeners();
    }
  }

  // Load Data Kegiatan First Time
  Future<void> loadDataKegiatanFirstTime(String tahun, String bulan) async {
    if (_sOrgIDSelected == null || _sOrgIDSelected!.isEmpty) {
      return;
    }

    try {
      _btnLoading = true;
      _errorMessage = null;
      notifyListeners();

      final response = await apiService.loadDataKegiatanFirstTime({
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
        _calculateSummary();
        _errorMessage = null;
      } else {
        _errorMessage = 'Gagal memuat data kegiatan';
      }
    } catch (e) {
      _errorMessage = _getErrorMessage(e);
      debugPrint('RKAMurni loadDataKegiatanFirstTime error: $e');
    } finally {
      _btnLoading = false;
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
        // Reload data akan dilakukan di screen dengan parameter tahun dan bulan
        _errorMessage = null;
      } else {
        _errorMessage = 'Gagal memuat data uraian';
      }
    } catch (e) {
      _errorMessage = _getErrorMessage(e);
      debugPrint('RKAMurni loadDataUraianFirstTime error: $e');
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
    if (_dataTable.isEmpty || _dataUnitKerja == null) {
      _summary = RKASummary(
        paguunitkerja: 0,
        pagukegiatan: 0,
        realisasi: 0,
        sisa: 0,
        persen_keuangan: 0,
        fisik: 0,
      );
      return;
    }

    double totalPaguKegiatan = _dataTable.fold(0.0, (sum, item) => sum + item.PaguDana1);
    double totalRealisasi = _dataUnitKerja!.RealisasiKeuangan1 ?? 0;
    double totalSisa = totalPaguKegiatan - totalRealisasi;
    double persenKeuangan = totalPaguKegiatan > 0
        ? (totalRealisasi / totalPaguKegiatan) * 100
        : 0;

    _summary = RKASummary(
      paguunitkerja: _dataUnitKerja!.PaguDana1 ?? 0,
      pagukegiatan: totalPaguKegiatan,
      realisasi: totalRealisasi,
      sisa: totalSisa,
      persen_keuangan: persenKeuangan,
      fisik: _dataUnitKerja!.RealisasiFisik1 ?? 0,
    );
  }

  // Open Dialog Form
  void openDialogForm() {
    _dialogFormOpen = true;
    _daftarBidang = [];
    _daftarProgram = [];
    _daftarKegiatan = [];
    _daftarSubKegiatan = [];
    _formBidangID = null;
    _formPrgID = null;
    _formKgtID = null;
    _formSubKgtID = null;
    
    // Add "SEMUA BIDANG URUSAN" option and bidang from OPD
    if (_dataOPD != null) {
      _daftarBidang.add(BidangUrusanModel(
        BidangID: 'all',
        nama_bidang: 'SEMUA BIDANG URUSAN',
      ));
      
      // Add Bidang 1
      if (_dataOPD!.BidangID_1 != null && _dataOPD!.BidangID_1!.isNotEmpty) {
        final kode = _dataOPD!.kode_bidang_1 ?? '';
        final nama = _dataOPD!.Nm_Bidang_1 ?? '';
        _daftarBidang.add(BidangUrusanModel(
          BidangID: _dataOPD!.BidangID_1!,
          nama_bidang: '[$kode] $nama',
        ));
      }
      
      // Add Bidang 2
      if (_dataOPD!.BidangID_2 != null && _dataOPD!.BidangID_2!.isNotEmpty) {
        final kode = _dataOPD!.kode_bidang_2 ?? '';
        final nama = _dataOPD!.Nm_Bidang_2 ?? '';
        _daftarBidang.add(BidangUrusanModel(
          BidangID: _dataOPD!.BidangID_2!,
          nama_bidang: '[$kode] $nama',
        ));
      }
      
      // Add Bidang 3
      if (_dataOPD!.BidangID_3 != null && _dataOPD!.BidangID_3!.isNotEmpty) {
        final kode = _dataOPD!.kode_bidang_3 ?? '';
        final nama = _dataOPD!.Nm_Bidang_3 ?? '';
        _daftarBidang.add(BidangUrusanModel(
          BidangID: _dataOPD!.BidangID_3!,
          nama_bidang: '[$kode] $nama',
        ));
      }
    }
    
    notifyListeners();
  }

  // Close Dialog Form
  void closeDialogForm() {
    _dialogFormOpen = false;
    _btnLoading = false;
    _daftarBidang = [];
    _daftarProgram = [];
    _daftarKegiatan = [];
    _daftarSubKegiatan = [];
    _formBidangID = null;
    _formPrgID = null;
    _formKgtID = null;
    _formSubKgtID = null;
    notifyListeners();
  }

  // Load Program List
  Future<void> loadProgramList(String bidangID, String tahun) async {
    try {
      _daftarProgram = [];
      _formPrgID = null;
      _daftarKegiatan = [];
      _formKgtID = null;
      _daftarSubKegiatan = [];
      _formSubKgtID = null;
      notifyListeners();

      final response = await apiService.getProgramList(bidangID, {'TA': tahun});

      if (response.statusCode == 200) {
        final data = response.data;
        if (data['program'] != null) {
          _daftarProgram = (data['program'] as List)
              .map((item) => ProgramModel.fromJson(item))
              .toList();
        }
      }
    } catch (e) {
      debugPrint('RKAMurni loadProgramList error: $e');
    }
    notifyListeners();
  }

  // Load Kegiatan List
  Future<void> loadKegiatanList(String prgID) async {
    try {
      _daftarKegiatan = [];
      _formKgtID = null;
      _daftarSubKegiatan = [];
      _formSubKgtID = null;
      notifyListeners();

      final response = await apiService.getKegiatanList(prgID);

      if (response.statusCode == 200) {
        final data = response.data;
        if (data['programkegiatan'] != null) {
          _daftarKegiatan = (data['programkegiatan'] as List)
              .map((item) => KegiatanModel.fromJson(item))
              .toList();
        }
      }
    } catch (e) {
      debugPrint('RKAMurni loadKegiatanList error: $e');
    }
    notifyListeners();
  }

  // Load Sub Kegiatan List
  Future<void> loadSubKegiatanList(String kgtID, String sOrgID) async {
    try {
      _daftarSubKegiatan = [];
      _formSubKgtID = null;
      notifyListeners();

      final response = await apiService.getSubKegiatanList(kgtID, {'SOrgID': sOrgID});

      if (response.statusCode == 200) {
        final data = response.data;
        if (data['subkegiatanrka'] != null) {
          _daftarSubKegiatan = (data['subkegiatanrka'] as List)
              .map((item) => SubKegiatanModel.fromJson(item))
              .toList();
        }
      }
    } catch (e) {
      debugPrint('RKAMurni loadSubKegiatanList error: $e');
    }
    notifyListeners();
  }

  // Set Form Fields
  void setFormBidangID(String? bidangID) {
    _formBidangID = bidangID;
    notifyListeners();
  }

  void setFormPrgID(String? prgID) {
    _formPrgID = prgID;
    notifyListeners();
  }

  void setFormKgtID(String? kgtID) {
    _formKgtID = kgtID;
    notifyListeners();
  }

  void setFormSubKgtID(String? subKgtID) {
    _formSubKgtID = subKgtID;
    notifyListeners();
  }

  // Save (Store Kegiatan)
  Future<bool> saveKegiatan() async {
    if (_formSubKgtID == null || _formSubKgtID!.isEmpty) {
      _formValid = false;
      notifyListeners();
      return false;
    }

    if (_dataOPD == null || _dataUnitKerja == null) {
      _errorMessage = 'OPD atau Unit Kerja belum dipilih';
      notifyListeners();
      return false;
    }

    try {
      _btnLoading = true;
      _errorMessage = null;
      notifyListeners();

      final response = await apiService.storeKegiatan({
        'OrgID': _dataOPD!.OrgID,
        'SOrgID': _dataUnitKerja!.SOrgID,
        'SubKgtID': _formSubKgtID!,
      });

      if (response.statusCode == 200) {
        // Reload data akan dilakukan di screen dengan parameter tahun dan bulan
        closeDialogForm();
        _errorMessage = null;
        return true;
      } else {
        _errorMessage = 'Gagal menyimpan data';
        return false;
      }
    } catch (e) {
      _errorMessage = _getErrorMessage(e);
      debugPrint('RKAMurni saveKegiatan error: $e');
      return false;
    } finally {
      _btnLoading = false;
      notifyListeners();
    }
  }

  // Delete RKA
  Future<bool> deleteRKA(String rkaID) async {
    try {
      _btnLoading = true;
      _errorMessage = null;
      notifyListeners();

      final response = await apiService.deleteRKA(rkaID);

      if (response.statusCode == 200) {
        // Reload data akan dilakukan di screen dengan parameter tahun dan bulan
        _errorMessage = null;
        return true;
      } else {
        _errorMessage = 'Gagal menghapus data';
        return false;
      }
    } catch (e) {
      _errorMessage = _getErrorMessage(e);
      debugPrint('RKAMurni deleteRKA error: $e');
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
        // Reload data akan dilakukan di screen dengan parameter tahun dan bulan
        _errorMessage = null;
        return true;
      } else {
        _errorMessage = 'Gagal reset data';
        return false;
      }
    } catch (e) {
      _errorMessage = _getErrorMessage(e);
      debugPrint('RKAMurni resetDataKegiatan error: $e');
      return false;
    } finally {
      _btnLoading = false;
      notifyListeners();
    }
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

