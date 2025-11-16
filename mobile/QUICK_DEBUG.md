# Quick Debug Guide - Error "Terjadi Kesalahan..."

## 🔍 Cara Debug Error di HP Android

### 1. Lihat Log di Terminal

**Saat `flutter run` berjalan, semua log akan muncul di terminal.**

Saat error muncul, lihat terminal dimana `flutter run` berjalan. Akan muncul:

```
📤 Request: POST /auth/login
📤 Data: {username: xxx, password: xxx}
❌ API Error: POST /auth/login
❌ Error Type: DioExceptionType.connectionTimeout
❌ Error Message: Connection timeout
❌ Status Code: null
❌ Login Error: DioException...
❌ Stack Trace: ...
```

### 2. Log yang Sudah Ditambahkan

Saya sudah menambahkan logging detail di:

1. **API Service** (`api_service.dart`):
   - ✅ Request method & path
   - ✅ Request data
   - ✅ Response status code & data
   - ✅ Error type, message, status code
   - ✅ Full HTTP logs dengan LogInterceptor

2. **Auth Provider** (`auth_provider.dart`):
   - ✅ Error detail saat login
   - ✅ Stack trace
   - ✅ DioException type & response

### 3. Cara Melihat Log

#### Saat App Running:
```bash
# Logs akan otomatis muncul di terminal dimana 'flutter run' berjalan
flutter run

# Semua debugPrint() akan muncul di terminal
```

#### Lihat Log dari Device:
```bash
# Lihat log real-time
flutter logs

# Filter untuk error saja
flutter logs | grep -i "error\|❌"

# Filter untuk API calls
flutter logs | grep -i "📤\|📥\|🌐"
```

#### Lihat Log dari ADB:
```bash
# Log dari device Android
adb logcat | grep flutter

# Filter error
adb logcat *:E | grep flutter
```

### 4. Contoh Debug Output

**Saat Login:**
```
📤 Request: POST /auth/login
📤 Data: {username: admin, password: ***}
📤 Auth: Bearer [token hidden]
📥 Response: 200 /auth/login
📥 Response Data: {access_token: ..., token_type: ...}
```

**Saat Error:**
```
📤 Request: POST /auth/login
📤 Data: {username: admin, password: ***}
❌ API Error: POST /auth/login
❌ Error Type: DioExceptionType.connectionTimeout
❌ Error Message: Connection timeout
❌ Login Error: DioException [connectionTimeout]: ...
❌ Stack Trace: ...
🔍 Getting error message for: DioException
🔍 Error string: DioException...
⚠️ Waktu koneksi habis. Periksa koneksi internet Anda.
```

### 5. Troubleshooting Error

#### Error: "Tidak dapat terhubung ke server"
**Kemungkinan:**
- ✅ HP tidak ada internet
- ✅ URL backend salah di `app_config.dart`
- ✅ Backend server down

**Debug:**
```bash
# Cek URL di app_config.dart
cat mobile/lib/utils/app_config.dart | grep baseUrl

# Test API dari terminal
curl -X POST https://simonev21-api.bintankab.go.id/v1/auth/login \
  -H "Content-Type: application/json" \
  -d '{"username":"test","password":"test"}'
```

#### Error: "Username atau password salah"
**Kemungkinan:**
- ✅ Username/password salah
- ✅ User tidak aktif di backend

**Debug:**
- Cek log di terminal untuk response dari API
- Cek apakah response status code 401

#### Error: "Waktu koneksi habis"
**Kemungkinan:**
- ✅ Koneksi internet lambat
- ✅ Timeout terlalu pendek
- ✅ Server tidak merespon

**Debug:**
```bash
# Cek timeout di app_config.dart
cat mobile/lib/utils/app_config.dart | grep Timeout
```

#### Error: "Endpoint tidak ditemukan"
**Kemungkinan:**
- ✅ URL endpoint salah
- ✅ Base URL salah

**Debug:**
- Cek log `📤 Request:` untuk melihat URL yang dipanggil
- Bandingkan dengan endpoint backend yang benar

### 6. Hot Reload untuk Update Code

Saat debugging, jika Anda update code:

```bash
# Di terminal dimana flutter run berjalan, tekan:
r  # Hot reload (quick)
R  # Hot restart (full restart)
q  # Quit
```

### 7. Tambah Debug Print Manual

Jika ingin debug bagian tertentu, tambahkan:

```dart
// Di mana saja di code
debugPrint('🔵 Debug: Ini adalah debug message');
debugPrint('🔵 Debug: Username: ${username}');
debugPrint('🔵 Debug: Response: ${response.data}');
```

### 8. Check API Response Manual

Untuk test API langsung tanpa app:

```bash
# Test login endpoint
curl -X POST https://simonev21-api.bintankab.go.id/v1/auth/login \
  -H "Content-Type: application/json" \
  -d '{
    "username": "YOUR_USERNAME",
    "password": "YOUR_PASSWORD"
  }'
```

### 9. Check URL Backend

```bash
# Cek URL yang digunakan
cat mobile/lib/utils/app_config.dart

# Atau lihat di code:
# static const String baseUrl = 'https://simonev21-api.bintankab.go.id/v1';
```

## 🎯 Quick Debug Checklist

Saat error "Terjadi kesalahan..." muncul:

- [ ] Lihat terminal dimana `flutter run` berjalan
- [ ] Cari log dengan `❌` atau `Error`
- [ ] Cek `📤 Request:` untuk melihat request yang dikirim
- [ ] Cek `📥 Response:` atau `❌ API Error:` untuk response
- [ ] Cek `❌ Error Type:` untuk jenis error (connectionTimeout, badResponse, dll)
- [ ] Cek `❌ Status Code:` untuk HTTP status code
- [ ] Cek stack trace untuk line number yang error
- [ ] Test API endpoint dengan curl/Postman

## 📋 Common Error Types

| Error Type | Penyebab | Solusi |
|------------|----------|--------|
| `connectionTimeout` | Koneksi lambat/timeout | Cek internet, naikkan timeout |
| `connectionError` | Tidak bisa connect ke server | Cek URL backend, cek internet |
| `badResponse` (401) | Unauthorized | Cek username/password |
| `badResponse` (404) | Endpoint tidak ditemukan | Cek URL endpoint |
| `badResponse` (500) | Server error | Cek backend server |
| `cancel` | Request dibatalkan | - |

---

**Sekarang semua error akan muncul detail di terminal! 🐛**

