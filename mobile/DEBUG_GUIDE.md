# Panduan Debugging Flutter App SIMONEV 21

## 🔍 Cara Mendebug Aplikasi Flutter

### 1. Lihat Log di Terminal

Saat `flutter run` berjalan, semua log akan muncul di terminal:

```bash
flutter run
```

**Di terminal akan muncul:**
- ✅ Print statements (`print()`, `debugPrint()`)
- ❌ Error messages dan stack trace
- 📱 Device logs
- 🔵 Flutter framework logs

### 2. Debug Print Statements

Tambahkan debug print di kode untuk melihat alur eksekusi:

```dart
// Di mana saja di kode
debugPrint('Debug: User clicked login button');
debugPrint('Debug: Username: ${username}');
debugPrint('Debug: Response: ${response.data}');
```

**Catatan:**
- `print()` - untuk development, bisa dihapus
- `debugPrint()` - lebih baik, otomatis dihapus di release build

### 3. Menggunakan Flutter DevTools (Recommended)

DevTools adalah tool visual untuk debugging Flutter:

```bash
# Saat flutter run berjalan, tekan:
# Shift + P atau ketik 'P' untuk open DevTools

# Atau buka manual:
flutter pub global activate devtools
flutter pub global run devtools
```

**Fitur DevTools:**
- 🔍 Inspector - lihat widget tree
- 📊 Performance - analisis performa
- 🐛 Debugger - set breakpoints
- 📝 Logging - lihat semua logs
- 🔵 Network - lihat HTTP requests

### 4. Set Breakpoints (Pause Execution)

Di VS Code atau Android Studio:
1. Klik di sebelah kiri line number untuk set breakpoint (titik merah)
2. Saat app berjalan, execution akan pause di breakpoint
3. Inspect variables, step through code, dll

### 5. Exception Handling & Error Messages

Cek di mana error "terjadi kesalahan" muncul:

```dart
try {
  // Your code
} catch (e, stackTrace) {
  debugPrint('Error: $e');
  debugPrint('Stack trace: $stackTrace');
  // Show error to user
}
```

## 🐛 Debugging Error "Terjadi Kesalahan..."

### Langkah 1: Cek Log di Terminal

Saat error muncul, cek terminal dimana `flutter run` berjalan. Akan muncul:
- Error message
- Stack trace (file dan line number)
- Type error (Exception, DioException, dll)

### Langkah 2: Cari Source Error

```bash
# Filter log untuk error saja
flutter logs | grep -i "error\|exception\|failed"

# Atau lihat semua log
flutter logs
```

### Langkah 3: Tambahkan Debug Print

Tambahkan di file yang dicurigai (misalnya `auth_provider.dart` atau `api_service.dart`):

```dart
try {
  debugPrint('🔵 Debug: Starting login...');
  final response = await apiService.login(username, password);
  debugPrint('🔵 Debug: Response status: ${response.statusCode}');
  debugPrint('🔵 Debug: Response data: ${response.data}');
} catch (e, stackTrace) {
  debugPrint('❌ Error: $e');
  debugPrint('❌ Stack trace: $stackTrace');
  // Error akan muncul di terminal
}
```

### Langkah 4: Cek Network Request

Untuk debug API calls, tambahkan di `api_service.dart`:

```dart
_dio.interceptors.add(
  LogInterceptor(
    requestBody: true,
    responseBody: true,
    error: true,
  ),
);
```

Ini akan print semua HTTP request/response di terminal.

### Langkah 5: Test API Endpoint

Cek apakah API endpoint bisa diakses:

```bash
# Test dari terminal (ganti URL sesuai)
curl -X POST https://simonev21-api.bintankab.go.id/v1/auth/login \
  -H "Content-Type: application/json" \
  -d '{"username":"test","password":"test"}'

# Atau gunakan Postman/Insomnia
```

## 📱 Debugging di Device Fisik

### 1. Enable Verbose Logging

```bash
# Run dengan verbose
flutter run -v

# Atau dengan profile mode
flutter run --profile
```

### 2. Lihat Device Logs

```bash
# Logs dari device
adb logcat

# Filter untuk Flutter
adb logcat | grep flutter

# Filter untuk error
adb logcat *:E
```

### 3. Inspect App State

Tambahkan debug panel di app:

```dart
import 'package:flutter/foundation.dart';

// Di build method
if (kDebugMode) {
  // Show debug info
  Text('Debug: ${authProvider.errorMessage}'),
}
```

## 🔧 Common Error Patterns

### 1. Network Error

**Gejala:** "Terjadi kesalahan" saat login atau API call

**Debug:**
```dart
// Di api_service.dart, tambahkan interceptor
_dio.interceptors.add(
  InterceptorsWrapper(
    onError: (error, handler) {
      debugPrint('❌ API Error: ${error.message}');
      debugPrint('❌ Error Type: ${error.type}');
      debugPrint('❌ Response: ${error.response?.data}');
      return handler.next(error);
    },
  ),
);
```

### 2. Null Error

**Gejala:** "NoSuchMethodError" atau "null check"

**Debug:**
```dart
// Cek null sebelum access
if (user != null) {
  debugPrint('User ID: ${user!.id}');
} else {
  debugPrint('User is null!');
}
```

### 3. Parse Error

**Gejala:** "FormatException" atau "TypeError"

**Debug:**
```dart
try {
  final user = UserModel.fromJson(response.data);
  debugPrint('Parsed user: ${user.toJson()}');
} catch (e) {
  debugPrint('Parse error: $e');
  debugPrint('Data: ${response.data}');
}
```

## 🛠️ Tools Debugging

### 1. Flutter DevTools (Browser-based)
```bash
# Install
flutter pub global activate devtools

# Run
flutter pub global run devtools

# Buka browser: http://localhost:9100
```

### 2. VS Code Debugger

1. Buka `.vscode/launch.json`
2. Pilih "Dart: Flutter" dari Run & Debug panel
3. Set breakpoints
4. Start debugging

### 3. Android Studio Debugger

1. Klik icon "Debug" (bug icon)
2. Set breakpoints
3. Inspect variables saat pause

## 📝 Debug Checklist

Saat ada error "terjadi kesalahan":

- [ ] Cek terminal dimana `flutter run` berjalan
- [ ] Lihat stack trace (file dan line number)
- [ ] Cek log dengan `flutter logs` atau `adb logcat`
- [ ] Tambahkan `debugPrint()` di function yang error
- [ ] Cek network request dengan LogInterceptor
- [ ] Test API endpoint dengan curl/Postman
- [ ] Cek null safety (null check)
- [ ] Cek data parsing (fromJson/toJson)

## 🎯 Quick Debug Commands

```bash
# 1. Lihat logs real-time
flutter logs

# 2. Filter error
flutter logs | grep -i error

# 3. Clear logs
adb logcat -c

# 4. Verbose run
flutter run -v

# 5. Hot restart (clear state)
# Tekan 'R' saat app running

# 6. Stop app
# Tekan 'q' saat app running
```

## 💡 Tips

1. **Gunakan `debugPrint()` bukan `print()`** - lebih efisien
2. **Jangan hardcode debug info** - gunakan `kDebugMode`
3. **Log error dengan stack trace** - `catch (e, stackTrace)`
4. **Test API endpoint terpisah** - pastikan backend OK
5. **Gunakan DevTools** - visual debugging lebih mudah

---

**Selanjutnya: Share error message dan stack trace dari terminal untuk dibantu troubleshoot! 🐛**

