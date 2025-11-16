class AppConfig {
  // Ganti dengan URL backend Anda
  static const String baseUrl = 'https://simonev21-api.bintankab.go.id/v1';
  
  // Atau gunakan localhost untuk development (gunakan IP address, bukan localhost)
  // static const String baseUrl = 'http://192.168.1.100:8000/api/v1';
  
  // Timeout untuk request
  static const Duration connectionTimeout = Duration(seconds: 30);
  static const Duration receiveTimeout = Duration(seconds: 30);
  
  // Storage keys
  static const String tokenKey = 'auth_token';
  static const String userKey = 'user_data';
  
  // App info
  static const String appName = 'SIMONEV 21';
  static const String appVersion = '1.0.0';
}

