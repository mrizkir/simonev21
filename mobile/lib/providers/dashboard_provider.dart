import 'package:flutter/material.dart';
import '../models/dashboard_model.dart';
import '../services/api_service.dart';
import 'auth_provider.dart';

class DashboardProvider with ChangeNotifier {
  final ApiService apiService;

  DashboardModel? _dashboardData;
  bool _isLoading = false;
  String? _errorMessage;

  DashboardProvider({required this.apiService});

  void setAuthProvider(AuthProvider authProvider) {
    // Auth provider can be used here if needed in the future
    // Currently not used but kept for consistency with architecture
  }

  DashboardModel? get dashboardData => _dashboardData;
  bool get isLoading => _isLoading;
  String? get errorMessage => _errorMessage;

  Future<void> loadDashboardData({int? tahunAnggaran}) async {
    _isLoading = true;
    _errorMessage = null;
    notifyListeners();

    try {
      final ta = tahunAnggaran ?? DateTime.now().year;
      
      final response = await apiService.getDashboardFront({
        'ta': ta,
      });

      if (response.statusCode == 200) {
        _dashboardData = DashboardModel.fromJson(response.data);
        _isLoading = false;
        _errorMessage = null;
      } else {
        _isLoading = false;
        _errorMessage = 'Gagal memuat data dashboard';
      }
    } catch (e) {
      _isLoading = false;
      _errorMessage = _getErrorMessage(e);
      debugPrint('Dashboard load error: $e');
    }

    notifyListeners();
  }

  String _getErrorMessage(dynamic error) {
    if (error.toString().contains('401')) {
      return 'Sesi Anda telah berakhir. Silakan login kembali.';
    } else if (error.toString().contains('SocketException') || error.toString().contains('Connection')) {
      return 'Tidak dapat terhubung ke server';
    } else {
      return 'Gagal memuat data';
    }
  }

  void clearError() {
    _errorMessage = null;
    notifyListeners();
  }
}

