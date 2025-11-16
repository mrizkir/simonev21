import 'package:dio/dio.dart';
import 'package:flutter/foundation.dart';
import 'storage_service.dart';
import '../utils/app_config.dart';

class ApiService {
  late Dio _dio;

  ApiService() {
    _dio = Dio(
      BaseOptions(
        baseUrl: AppConfig.baseUrl,
        connectTimeout: AppConfig.connectionTimeout,
        receiveTimeout: AppConfig.receiveTimeout,
        headers: {
          'Content-Type': 'application/json',
          'Accept': 'application/json',
        },
      ),
    );

    // Interceptor untuk menambahkan token ke header dan logging
    _dio.interceptors.add(
      InterceptorsWrapper(
        onRequest: (options, handler) async {
          // Log request untuk debugging
          debugPrint('📤 Request: ${options.method} ${options.path}');
          debugPrint('📤 Data: ${options.data}');
          
          // Cek apakah sudah ada Authorization header (dari getCurrentUser)
          if (!options.headers.containsKey('Authorization')) {
            final tokenType = StorageService.getTokenType();
            final accessToken = StorageService.getAccessToken();
            
            // Gunakan format "token_type access_token" jika ada
            if (tokenType != null && accessToken != null) {
              options.headers['Authorization'] = '$tokenType $accessToken';
              debugPrint('📤 Auth: $tokenType [token hidden]');
            } else {
              // Fallback ke token biasa
              final token = StorageService.getToken();
              if (token != null) {
                options.headers['Authorization'] = 'Bearer $token';
                debugPrint('📤 Auth: Bearer [token hidden]');
              }
            }
          }
          return handler.next(options);
        },
        onResponse: (response, handler) {
          // Log response untuk debugging
          debugPrint('📥 Response: ${response.statusCode} ${response.requestOptions.path}');
          if (kDebugMode) {
            debugPrint('📥 Response Data: ${response.data}');
          }
          return handler.next(response);
        },
        onError: (error, handler) {
          // Log error untuk debugging
          debugPrint('❌ API Error: ${error.requestOptions.method} ${error.requestOptions.path}');
          debugPrint('❌ Error Type: ${error.type}');
          debugPrint('❌ Error Message: ${error.message}');
          if (error.response != null) {
            debugPrint('❌ Status Code: ${error.response?.statusCode}');
            debugPrint('❌ Response Data: ${error.response?.data}');
          }
          
          if (error.response?.statusCode == 401) {
            // Token expired, perlu logout
            debugPrint('⚠️ 401 Unauthorized - Removing token');
            StorageService.removeToken();
            StorageService.removeUserData();
          }
          return handler.next(error);
        },
      ),
    );
    
    // Tambahkan LogInterceptor untuk melihat semua HTTP request/response
    if (kDebugMode) {
      _dio.interceptors.add(
        LogInterceptor(
          requestBody: true,
          responseBody: true,
          error: true,
          logPrint: (obj) => debugPrint('🌐 HTTP: $obj'),
        ),
      );
    }
  }

  // Auth endpoints
  Future<Response> login(String username, String password) async {
    try {
      final response = await _dio.post(
        '/auth/login',
        data: {
          'username': username,
          'password': password,
        },
      );
      return response;
    } catch (e) {
      rethrow;
    }
  }

  Future<Response> getCurrentUser({String? tokenType, String? accessToken}) async {
    try {
      final options = Options();
      
      // Jika token type dan access token disediakan, gunakan untuk Authorization header
      if (tokenType != null && accessToken != null) {
        options.headers = {
          'Authorization': '$tokenType $accessToken',
        };
      }
      
      final response = await _dio.get('/auth/me', options: options);
      return response;
    } catch (e) {
      rethrow;
    }
  }
  
  // Get UI Frontend settings (untuk mendapatkan daftar tahun anggaran)
  Future<Response> getUIFrontSettings() async {
    try {
      final response = await _dio.get('/system/setting/uifront');
      return response;
    } catch (e) {
      rethrow;
    }
  }

  Future<Response> logout() async {
    try {
      final response = await _dio.post('/auth/logout');
      return response;
    } catch (e) {
      rethrow;
    }
  }

  Future<Response> refreshToken() async {
    try {
      final response = await _dio.get('/auth/refresh');
      return response;
    } catch (e) {
      rethrow;
    }
  }

  // Dashboard endpoints
  Future<Response> getDashboardFront(Map<String, dynamic> data) async {
    try {
      final response = await _dio.post('/dashboard/front', data: data);
      return response;
    } catch (e) {
      rethrow;
    }
  }

  Future<Response> getPelaporanOPD(Map<String, dynamic> data) async {
    try {
      final response = await _dio.post('/dashboard/pelaporanopd', data: data);
      return response;
    } catch (e) {
      rethrow;
    }
  }

  Future<Response> getEvaluasiMurniRealisasiTA(Map<String, dynamic> data) async {
    try {
      final response = await _dio.post('/evaluasimurni/realisasita', data: data);
      return response;
    } catch (e) {
      rethrow;
    }
  }

  Future<Response> getEvaluasiMurniRealisasiTW(Map<String, dynamic> data) async {
    try {
      final response = await _dio.post('/evaluasimurni/realisasitw', data: data);
      return response;
    } catch (e) {
      rethrow;
    }
  }

  // DMaster endpoints
  Future<Response> getDMaster(Map<String, dynamic> data) async {
    try {
      final response = await _dio.post('/dmaster', data: data);
      return response;
    } catch (e) {
      rethrow;
    }
  }

  // RenjaMurni endpoints
  Future<Response> getRenjaMurni(Map<String, dynamic> data) async {
    try {
      final response = await _dio.post('/renjamurni', data: data);
      return response;
    } catch (e) {
      rethrow;
    }
  }

  Future<Response> reloadRenjaMurniStatistik1(Map<String, dynamic> data) async {
    try {
      final response = await _dio.post('/renjamurni/reloadstatistik1', data: data);
      return response;
    } catch (e) {
      rethrow;
    }
  }

  Future<Response> reloadRenjaMurniStatistik2(Map<String, dynamic> data) async {
    try {
      final response = await _dio.post('/renjamurni/reloadstatistik2', data: data);
      return response;
    } catch (e) {
      rethrow;
    }
  }

  Future<Response> getTahunAnggaran() async {
    try {
      final response = await _dio.get('/dmaster/ta');
      return response;
    } catch (e) {
      rethrow;
    }
  }

  // RKA Murni endpoints
  Future<Response> getOPDList(Map<String, dynamic> data) async {
    try {
      final response = await _dio.post('/dmaster/opd', data: data);
      return response;
    } catch (e) {
      rethrow;
    }
  }

  Future<Response> getUnitKerjaList(String orgID) async {
    try {
      final response = await _dio.get('/dmaster/opd/$orgID/unitkerja');
      return response;
    } catch (e) {
      rethrow;
    }
  }

  Future<Response> getRKAMurniList(Map<String, dynamic> data) async {
    try {
      final response = await _dio.post('/renja/rkamurni', data: data);
      return response;
    } catch (e) {
      rethrow;
    }
  }

  Future<Response> loadDataKegiatanFirstTime(Map<String, dynamic> data) async {
    try {
      final response = await _dio.post('/renja/rkamurni/loaddatakegiatanfirsttime', data: data);
      return response;
    } catch (e) {
      rethrow;
    }
  }

  Future<Response> loadDataUraianFirstTime(Map<String, dynamic> data) async {
    try {
      final response = await _dio.post('/renja/rkamurni/loaddatauraianfirsttime', data: data);
      return response;
    } catch (e) {
      rethrow;
    }
  }

  Future<Response> storeKegiatan(Map<String, dynamic> data) async {
    try {
      final response = await _dio.post('/renja/rkamurni/storekegiatan', data: data);
      return response;
    } catch (e) {
      rethrow;
    }
  }

  Future<Response> deleteRKA(String rkaID) async {
    try {
      final response = await _dio.post(
        '/renja/rkamurni/$rkaID',
        data: {
          '_method': 'DELETE',
          'pid': 'datarka',
        },
      );
      return response;
    } catch (e) {
      rethrow;
    }
  }

  Future<Response> resetDataKegiatan(String rkaID) async {
    try {
      final response = await _dio.post(
        '/renja/rkamurni/resetdatakegiatan/$rkaID',
        data: {
          '_method': 'PUT',
        },
      );
      return response;
    } catch (e) {
      rethrow;
    }
  }

  Future<Response> getProgramList(String bidangID, Map<String, dynamic> data) async {
    try {
      final response = await _dio.post('/dmaster/kodefikasi/bidangurusan/$bidangID/program', data: data);
      return response;
    } catch (e) {
      rethrow;
    }
  }

  Future<Response> getKegiatanList(String prgID) async {
    try {
      final response = await _dio.get('/dmaster/kodefikasi/program/$prgID/kegiatan');
      return response;
    } catch (e) {
      rethrow;
    }
  }

  Future<Response> getSubKegiatanList(String kgtID, Map<String, dynamic> data) async {
    try {
      final response = await _dio.post('/dmaster/kodefikasi/kegiatan/$kgtID/subkegiatanrka', data: data);
      return response;
    } catch (e) {
      rethrow;
    }
  }

  // Gallery endpoints
  Future<Response> getGalleryPembangunan(Map<String, dynamic> data) async {
    try {
      final response = await _dio.post('/gallerypembangunan', data: data);
      return response;
    } catch (e) {
      rethrow;
    }
  }

  // RPJMD endpoints
  Future<Response> getRPJMDStatistik(Map<String, dynamic> data) async {
    try {
      final response = await _dio.post('/rpjmd/dashboard/statistik', data: data);
      return response;
    } catch (e) {
      rethrow;
    }
  }

  Future<Response> getRPJMDMisi(Map<String, dynamic> data) async {
    try {
      final response = await _dio.post('/rpjmd/dashboard/misi', data: data);
      return response;
    } catch (e) {
      rethrow;
    }
  }

  // Generic GET request
  Future<Response> get(String path, {Map<String, dynamic>? queryParameters}) async {
    try {
      final response = await _dio.get(path, queryParameters: queryParameters);
      return response;
    } catch (e) {
      rethrow;
    }
  }

  // Generic POST request
  Future<Response> post(String path, {dynamic data}) async {
    try {
      final response = await _dio.post(path, data: data);
      return response;
    } catch (e) {
      rethrow;
    }
  }
}

