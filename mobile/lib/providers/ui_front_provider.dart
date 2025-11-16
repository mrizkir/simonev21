import 'package:flutter/material.dart';
import '../models/tahun_anggaran_model.dart';
import '../services/api_service.dart';

class UIFrontProvider with ChangeNotifier {
  final ApiService apiService;

  List<TahunAnggaranModel> _daftarTA = [];
  String? _tahunAnggaran;
  String? _bulanRealisasi;
  String? _masaPelaporan;

  bool _isLoading = false;
  String? _errorMessage;

  UIFrontProvider({required this.apiService});

  List<TahunAnggaranModel> get daftarTA => _daftarTA;
  String? get tahunAnggaran => _tahunAnggaran;
  String? get bulanRealisasi => _bulanRealisasi;
  String? get masaPelaporan => _masaPelaporan;
  bool get isLoading => _isLoading;
  String? get errorMessage => _errorMessage;

  Future<void> init() async {
    _isLoading = true;
    _errorMessage = null;
    notifyListeners();

    try {
      final response = await apiService.getUIFrontSettings();

      if (response.statusCode == 200) {
        final data = response.data;

        // Parse daftar tahun anggaran
        if (data['daftar_ta'] != null) {
          _daftarTA = (data['daftar_ta'] as List)
              .map((item) => TahunAnggaranModel.fromJson(item))
              .toList();
        }

        // Set tahun anggaran default jika ada
        if (data['tahun_anggaran'] != null) {
          _tahunAnggaran = data['tahun_anggaran'].toString();
        } else if (_daftarTA.isNotEmpty) {
          _tahunAnggaran = _daftarTA.first.value;
        }

        // Set bulan realisasi dan masa pelaporan jika ada
        _bulanRealisasi = data['bulan_realisasi']?.toString();
        _masaPelaporan = data['masa_pelaporan']?.toString();

        _isLoading = false;
        _errorMessage = null;
      } else {
        _isLoading = false;
        _errorMessage = 'Gagal memuat data';
      }
    } catch (e) {
      _isLoading = false;
      _errorMessage = 'Error: $e';
      debugPrint('UIFront init error: $e');
    }

    notifyListeners();
  }

  void setTahunAnggaran(String tahun) {
    _tahunAnggaran = tahun;
    notifyListeners();
  }

  // Get nama bulan dari nomor bulan
  String getNamaBulan(String? bulan) {
    if (bulan == null || bulan.isEmpty) {
      return '-';
    }
    
    final bulanMap = {
      '1': 'Januari',
      '2': 'Februari',
      '3': 'Maret',
      '4': 'April',
      '5': 'Mei',
      '6': 'Juni',
      '7': 'Juli',
      '8': 'Agustus',
      '9': 'September',
      '10': 'Oktober',
      '11': 'November',
      '12': 'Desember',
    };
    
    return bulanMap[bulan] ?? bulan;
  }

  // Get nama APP (dari app config atau default)
  String getNamaAPP() {
    return 'SIMONEV 21';
  }

  String getNamaAPPAlias() {
    return 'SIMONEV 21';
  }
}

