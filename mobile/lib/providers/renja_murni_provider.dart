import 'package:flutter/material.dart';
import '../models/renja_murni_model.dart';
import '../services/api_service.dart';

class RenjaMurniProvider with ChangeNotifier {
  final ApiService apiService;

  RenjaMurniStatistik1? _statistik1;
  RenjaMurniChart? _chartKeuangan;
  RenjaMurniChart? _chartFisik;
  bool _isLoading = false;
  String? _errorMessage;
  bool _chartLoaded = false;

  RenjaMurniProvider({required this.apiService});

  RenjaMurniStatistik1? get statistik1 => _statistik1;
  RenjaMurniChart? get chartKeuangan => _chartKeuangan;
  RenjaMurniChart? get chartFisik => _chartFisik;
  bool get isLoading => _isLoading;
  String? get errorMessage => _errorMessage;
  bool get chartLoaded => _chartLoaded;

  Future<void> init(String tahunAnggaran, String? bulanRealisasi) async {
    _isLoading = true;
    _errorMessage = null;
    _chartLoaded = false;
    notifyListeners();

    try {
      final response = await apiService.getRenjaMurni({
        'ta': tahunAnggaran,
        'bulan_realisasi': bulanRealisasi ?? '',
      });

      if (response.statusCode == 200) {
        final data = response.data;

        // Parse statistik1
        if (data['statistik1'] != null) {
          _statistik1 = RenjaMurniStatistik1.fromJson(data['statistik1']);
        }

        // Parse chart keuangan
        if (data['chart_keuangan'] != null) {
          _chartKeuangan = RenjaMurniChart.fromJson(data['chart_keuangan']);
        }

        // Parse chart fisik
        if (data['chart_fisik'] != null) {
          _chartFisik = RenjaMurniChart.fromJson(data['chart_fisik']);
        }

        _chartLoaded = true;
        _isLoading = false;
        _errorMessage = null;
      } else {
        _isLoading = false;
        _errorMessage = 'Gagal memuat data';
      }
    } catch (e) {
      _isLoading = false;
      _errorMessage = _getErrorMessage(e);
      debugPrint('RenjaMurni init error: $e');
    }

    notifyListeners();
  }

  Future<void> reloadStatistik1(String tahunAnggaran) async {
    try {
      await apiService.reloadRenjaMurniStatistik1({
        'ta': tahunAnggaran,
      });
      
      // Reload data setelah reload statistik
      final bulanRealisasi = '';
      await init(tahunAnggaran, bulanRealisasi);
    } catch (e) {
      _errorMessage = _getErrorMessage(e);
      notifyListeners();
      debugPrint('RenjaMurni reloadStatistik1 error: $e');
    }
  }

  Future<void> reloadStatistik2(String tahunAnggaran) async {
    try {
      await apiService.reloadRenjaMurniStatistik2({
        'ta': tahunAnggaran,
      });
      
      // Reload data setelah reload statistik
      final bulanRealisasi = '';
      await init(tahunAnggaran, bulanRealisasi);
    } catch (e) {
      _errorMessage = _getErrorMessage(e);
      notifyListeners();
      debugPrint('RenjaMurni reloadStatistik2 error: $e');
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

