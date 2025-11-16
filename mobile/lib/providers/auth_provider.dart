import 'package:flutter/material.dart';
import 'package:dio/dio.dart';
import '../models/user_model.dart';
import '../services/api_service.dart';
import '../services/storage_service.dart';

class AuthProvider with ChangeNotifier {
  final ApiService apiService;
  final StorageService storageService;

  UserModel? _user;
  bool _isLoading = false;
  String? _errorMessage;

  AuthProvider({
    required this.apiService,
    required this.storageService,
  }) {
    _loadUserFromStorage();
  }

  UserModel? get user => _user;
  bool get isLoading => _isLoading;
  String? get errorMessage => _errorMessage;
  bool get isAuthenticated => _user != null;

  Future<void> _loadUserFromStorage() async {
    try {
      final userData = StorageService.getUserData();
      final token = StorageService.getToken();

      if (userData != null && token != null) {
        _user = UserModel.fromJson(userData);
        notifyListeners();
      }
    } catch (e) {
      debugPrint('Error loading user from storage: $e');
    }
  }

  // Login dengan tahun anggaran (sesuai versi web)
  Future<bool> login(String username, String password, String tahunAnggaran) async {
    _isLoading = true;
    _errorMessage = null;
    notifyListeners();

    try {
      // Step 1: POST /auth/login
      final response = await apiService.login(username, password);
      
      if (response.statusCode == 200) {
        final tokenData = response.data;
        final tokenType = tokenData['token_type'] ?? 'Bearer';
        final accessToken = tokenData['access_token'];
        
        if (accessToken == null) {
          _isLoading = false;
          _errorMessage = 'Token tidak ditemukan dalam response';
          notifyListeners();
          return false;
        }
        
        // Simpan token data
        await StorageService.saveTokenData(tokenData);
        
        // Step 2: GET /auth/me dengan Authorization header
        // Format: "token_type access_token" (sesuai versi web)
        final userResponse = await apiService.getCurrentUser(
          tokenType: tokenType,
          accessToken: accessToken,
        );
        
        if (userResponse.statusCode == 200) {
          // Handle jika response.data adalah List atau Map
          dynamic responseData = userResponse.data;
          
          debugPrint('🔵 Response data type: ${responseData.runtimeType}');
          debugPrint('🔵 Response data: $responseData');
          
          // Jika response adalah List, ambil elemen pertama
          Map<String, dynamic> userData;
          if (responseData is List) {
            debugPrint('🔵 Response adalah List dengan ${responseData.length} elemen');
            if (responseData.isNotEmpty) {
              userData = responseData[0] as Map<String, dynamic>;
              debugPrint('🔵 Mengambil elemen pertama dari List');
            } else {
              _isLoading = false;
              _errorMessage = 'Response user kosong';
              notifyListeners();
              return false;
            }
          } else if (responseData is Map<String, dynamic>) {
            debugPrint('🔵 Response adalah Map');
            userData = responseData;
          } else {
            _isLoading = false;
            _errorMessage = 'Format response tidak valid';
            debugPrint('❌ Invalid response format: ${responseData.runtimeType}');
            debugPrint('❌ Response data: $responseData');
            notifyListeners();
            return false;
          }
          
          debugPrint('🔵 User data sebelum parse: $userData');
          
          // Tambahkan tahun_selected ke user data (sesuai versi web)
          userData['tahun_selected'] = tahunAnggaran;
          
          try {
            _user = UserModel.fromJson(userData);
            debugPrint('✅ User model berhasil dibuat: ${_user?.name}');
            await StorageService.saveUserData(_user!.toJson());
          } catch (e, stackTrace) {
            debugPrint('❌ Error parsing UserModel: $e');
            debugPrint('❌ Stack trace: $stackTrace');
            debugPrint('❌ User data yang error: $userData');
            _isLoading = false;
            _errorMessage = 'Gagal memproses data user: ${e.toString()}';
            notifyListeners();
            return false;
          }
          
          _isLoading = false;
          _errorMessage = null;
          notifyListeners();
          return true;
        } else {
          _isLoading = false;
          _errorMessage = 'Gagal mengambil data user';
          notifyListeners();
          return false;
        }
      } else {
        _isLoading = false;
        _errorMessage = response.data['message'] ?? 'Login gagal';
        notifyListeners();
        return false;
      }
    } catch (e, stackTrace) {
      _isLoading = false;
      // Log error detail untuk debugging
      debugPrint('❌ Login Error: $e');
      debugPrint('❌ Stack Trace: $stackTrace');
      if (e is DioException) {
        debugPrint('❌ Dio Error Type: ${e.type}');
        debugPrint('❌ Dio Error Response: ${e.response?.data}');
        debugPrint('❌ Dio Error Status Code: ${e.response?.statusCode}');
      }
      _errorMessage = _getErrorMessage(e);
      notifyListeners();
      return false;
    }
  }

  Future<void> logout() async {
    try {
      await apiService.logout();
    } catch (e) {
      debugPrint('Logout error: $e');
    } finally {
      _user = null;
      await StorageService.removeToken();
      await StorageService.removeUserData();
      notifyListeners();
    }
  }

  Future<void> refreshUser() async {
    try {
      final response = await apiService.getCurrentUser();
      if (response.statusCode == 200) {
        _user = UserModel.fromJson(response.data);
        await StorageService.saveUserData(_user!.toJson());
        notifyListeners();
      }
    } catch (e) {
      debugPrint('Error refreshing user: $e');
    }
  }

  String _getErrorMessage(dynamic error) {
    // Log error untuk debugging
    debugPrint('🔍 Getting error message for: ${error.runtimeType}');
    debugPrint('🔍 Error string: ${error.toString()}');
    
    if (error is DioException) {
      // Handle DioException (dari Dio package)
      // Gunakan if-else karena DioExceptionType bukan constant untuk switch
      if (error.type == DioExceptionType.connectionTimeout ||
          error.type == DioExceptionType.sendTimeout ||
          error.type == DioExceptionType.receiveTimeout) {
        return 'Waktu koneksi habis. Periksa koneksi internet Anda.';
      }
      
      if (error.type == DioExceptionType.badResponse) {
        final statusCode = error.response?.statusCode;
        if (statusCode == 401) {
          return 'Username atau password salah';
        } else if (statusCode == 404) {
          return 'Endpoint tidak ditemukan. Periksa URL backend.';
        } else if (statusCode == 500) {
          return 'Server error. Silakan coba lagi nanti.';
        } else {
          return 'Error server ($statusCode). Silakan coba lagi.';
        }
      }
      
      if (error.type == DioExceptionType.cancel) {
        return 'Request dibatalkan.';
      }
      
      if (error.type == DioExceptionType.connectionError ||
          error.type == DioExceptionType.unknown) {
        if (error.message?.contains('SocketException') == true ||
            error.message?.contains('Connection') == true) {
          return 'Tidak dapat terhubung ke server. Periksa koneksi internet Anda.';
        }
        return 'Tidak dapat terhubung ke server. Periksa koneksi internet dan URL backend.';
      }
      
      return 'Terjadi kesalahan. Silakan coba lagi.';
    } else if (error.toString().contains('401') || error.toString().contains('Unauthorized')) {
      return 'Username atau password salah';
    } else if (error.toString().contains('SocketException') || error.toString().contains('Connection')) {
      return 'Tidak dapat terhubung ke server. Periksa koneksi internet Anda.';
    } else if (error.toString().contains('TimeoutException') || error.toString().contains('timeout')) {
      return 'Waktu koneksi habis. Silakan coba lagi.';
    } else {
      // Untuk debugging, tampilkan error detail di log
      debugPrint('⚠️ Unknown error type: ${error.runtimeType}');
      debugPrint('⚠️ Error message: ${error.toString()}');
      return 'Terjadi kesalahan. Silakan coba lagi.';
    }
  }

  void clearError() {
    _errorMessage = null;
    notifyListeners();
  }
}

