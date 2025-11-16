import 'dart:convert';
import 'package:shared_preferences/shared_preferences.dart';

class StorageService {
  static SharedPreferences? _prefs;

  static Future<void> init() async {
    _prefs = await SharedPreferences.getInstance();
  }

  // Token management
  static Future<bool> saveToken(String token) async {
    return await _prefs?.setString('token', token) ?? false;
  }

  static Future<bool> saveTokenData(Map<String, dynamic> tokenData) async {
    // Simpan token_type dan access_token terpisah untuk kemudahan
    if (tokenData['access_token'] != null) {
      await _prefs?.setString('access_token', tokenData['access_token']);
    }
    if (tokenData['token_type'] != null) {
      await _prefs?.setString('token_type', tokenData['token_type']);
    }
    // Simpan juga sebagai string untuk compatibility
    final fullToken = tokenData['token_type'] != null && tokenData['access_token'] != null
        ? '${tokenData['token_type']} ${tokenData['access_token']}'
        : tokenData['access_token'] ?? '';
    return await _prefs?.setString('token', fullToken) ?? false;
  }

  static String? getToken() {
    return _prefs?.getString('token');
  }
  
  static String? getAccessToken() {
    return _prefs?.getString('access_token');
  }
  
  static String? getTokenType() {
    return _prefs?.getString('token_type');
  }

  static Future<bool> removeToken() async {
    await _prefs?.remove('token');
    await _prefs?.remove('access_token');
    await _prefs?.remove('token_type');
    return true;
  }

  // User data management
  static Future<bool> saveUserData(Map<String, dynamic> userData) async {
    return await _prefs?.setString('user_data', jsonEncode(userData)) ?? false;
  }

  static Map<String, dynamic>? getUserData() {
    final userDataString = _prefs?.getString('user_data');
    if (userDataString != null) {
      return jsonDecode(userDataString) as Map<String, dynamic>;
    }
    return null;
  }

  static Future<bool> removeUserData() async {
    return await _prefs?.remove('user_data') ?? false;
  }

  // Generic methods
  static Future<bool> saveString(String key, String value) async {
    return await _prefs?.setString(key, value) ?? false;
  }

  static String? getString(String key) {
    return _prefs?.getString(key);
  }

  static Future<bool> remove(String key) async {
    return await _prefs?.remove(key) ?? false;
  }

  static Future<bool> clearAll() async {
    return await _prefs?.clear() ?? false;
  }
}

