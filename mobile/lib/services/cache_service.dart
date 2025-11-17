import 'dart:convert';
import 'package:shared_preferences/shared_preferences.dart';
import 'package:flutter/foundation.dart';

/// Service untuk caching data API responses
class CacheService {
  static SharedPreferences? _prefs;
  static const String _cachePrefix = 'api_cache_';
  static const String _cacheTimestampPrefix = 'cache_timestamp_';

  static Future<void> init() async {
    _prefs ??= await SharedPreferences.getInstance();
  }

  /// Generate cache key dari URL dan parameters
  static String _generateCacheKey(String url, Map<String, dynamic>? params) {
    final paramsString = params != null ? jsonEncode(params) : '';
    return '$_cachePrefix${url}_$paramsString';
  }

  /// Generate timestamp key
  static String _generateTimestampKey(String cacheKey) {
    return '$_cacheTimestampPrefix$cacheKey';
  }

  /// Simpan response ke cache
  /// 
  /// [url] - API endpoint URL
  /// [params] - Request parameters (optional)
  /// [data] - Response data yang akan di-cache
  /// [ttlSeconds] - Time to live dalam detik (default: 300 = 5 menit)
  static Future<bool> setCache(
    String url,
    dynamic data, {
    Map<String, dynamic>? params,
    int ttlSeconds = 300,
  }) async {
    try {
      await init();
      final cacheKey = _generateCacheKey(url, params);
      final timestampKey = _generateTimestampKey(cacheKey);
      
      final dataString = jsonEncode(data);
      final timestamp = DateTime.now().millisecondsSinceEpoch;
      
      final saved = await _prefs?.setString(cacheKey, dataString) ?? false;
      await _prefs?.setInt(timestampKey, timestamp);
      await _prefs?.setInt('${timestampKey}_ttl', ttlSeconds);
      
      if (kDebugMode) {
        debugPrint('💾 Cached: $url (TTL: ${ttlSeconds}s)');
      }
      
      return saved;
    } catch (e) {
      debugPrint('❌ Cache save error: $e');
      return false;
    }
  }

  /// Ambil data dari cache
  /// 
  /// Returns null jika cache tidak ada atau sudah expired
  static Future<dynamic> getCache(
    String url, {
    Map<String, dynamic>? params,
  }) async {
    try {
      await init();
      final cacheKey = _generateCacheKey(url, params);
      final timestampKey = _generateTimestampKey(cacheKey);
      
      final dataString = _prefs?.getString(cacheKey);
      final timestamp = _prefs?.getInt(timestampKey);
      final ttl = _prefs?.getInt('${timestampKey}_ttl') ?? 300;
      
      if (dataString == null || timestamp == null) {
        return null;
      }
      
      // Check if cache is expired
      final now = DateTime.now().millisecondsSinceEpoch;
      final age = (now - timestamp) ~/ 1000; // age in seconds
      
      if (age > ttl) {
        // Cache expired, remove it
        await removeCache(url, params: params);
        if (kDebugMode) {
          debugPrint('⏰ Cache expired: $url (age: ${age}s, TTL: ${ttl}s)');
        }
        return null;
      }
      
      final data = jsonDecode(dataString);
      if (kDebugMode) {
        debugPrint('📦 Cache hit: $url (age: ${age}s)');
      }
      
      return data;
    } catch (e) {
      debugPrint('❌ Cache get error: $e');
      return null;
    }
  }

  /// Hapus cache untuk URL tertentu
  static Future<bool> removeCache(
    String url, {
    Map<String, dynamic>? params,
  }) async {
    try {
      await init();
      final cacheKey = _generateCacheKey(url, params);
      final timestampKey = _generateTimestampKey(cacheKey);
      
      await _prefs?.remove(cacheKey);
      await _prefs?.remove(timestampKey);
      await _prefs?.remove('${timestampKey}_ttl');
      
      return true;
    } catch (e) {
      debugPrint('❌ Cache remove error: $e');
      return false;
    }
  }

  /// Clear semua cache
  static Future<bool> clearAllCache() async {
    try {
      await init();
      final keys = _prefs?.getKeys() ?? {};
      
      for (final key in keys) {
        if (key.startsWith(_cachePrefix) || 
            key.startsWith(_cacheTimestampPrefix)) {
          await _prefs?.remove(key);
        }
      }
      
      if (kDebugMode) {
        debugPrint('🗑️ All cache cleared');
      }
      
      return true;
    } catch (e) {
      debugPrint('❌ Clear cache error: $e');
      return false;
    }
  }

  /// Get cache age dalam detik
  static int? getCacheAge(
    String url, {
    Map<String, dynamic>? params,
  }) {
    try {
      final cacheKey = _generateCacheKey(url, params);
      final timestampKey = _generateTimestampKey(cacheKey);
      
      final timestamp = _prefs?.getInt(timestampKey);
      if (timestamp == null) return null;
      
      final now = DateTime.now().millisecondsSinceEpoch;
      return (now - timestamp) ~/ 1000;
    } catch (e) {
      return null;
    }
  }

  /// Check if cache exists and is valid
  static Future<bool> hasValidCache(
    String url, {
    Map<String, dynamic>? params,
  }) async {
    final cache = await getCache(url, params: params);
    return cache != null;
  }
}

/// Cache TTL constants untuk berbagai jenis data
class CacheTTL {
  // Data master (jarang berubah)
  static const int masterData = 86400; // 24 jam
  
  // Data statistik (berubah periodik)
  static const int statistics = 3600; // 1 jam
  
  // Data transaksi (berubah sering)
  static const int transaction = 300; // 5 menit
  
  // Data real-time (tidak di-cache)
  static const int realTime = 0; // No cache
  
  // Data user preferences
  static const int userPreferences = 604800; // 7 hari
}

