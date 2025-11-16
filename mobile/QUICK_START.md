# Quick Start - Jalankan Aplikasi Flutter SIMONEV 21

## ✅ Status

Sekarang project sudah siap untuk dijalankan! Folder `android/` sudah dibuat.

## 🚀 Langkah-langkah Menjalankan

### 1. Pastikan Emulator Berjalan

```bash
# Cek emulator yang berjalan
flutter devices

# Output yang diharapkan:
# sdk gphone64 x86 64 (mobile) • emulator-5554 • android-x64 • Android 15 (API 35) (emulator)
```

Jika emulator belum berjalan:
```bash
# List AVD
emulator -list-avds

# Jalankan emulator
emulator -avd <NAMA_AVD> &
```

### 2. Konfigurasi API URL (Penting!)

Edit file `lib/utils/app_config.dart`:

```dart
static const String baseUrl = 'https://simonev21-api.bintankab.go.id/v1';
```

**Catatan:** Pastikan URL backend dapat diakses dari emulator.

### 3. Install Dependencies (Jika Belum)

```bash
cd mobile
flutter pub get
```

### 4. Jalankan Aplikasi

```bash
# Dari folder mobile/
flutter run

# Atau dengan device spesifik
flutter run -d emulator-5554
```

### 5. Build APK (Optional)

```bash
# Build debug APK
flutter build apk --debug

# Build release APK
flutter build apk --release
```

APK akan berada di: `build/app/outputs/flutter-apk/app-release.apk`

## 🔧 Troubleshooting

### Jika "No supported devices connected"

1. **Pastikan emulator sudah boot sempurna:**
   ```bash
   flutter devices
   ```
   Tunggu sampai emulator benar-benar boot (home screen muncul).

2. **Restart adb:**
   ```bash
   adb kill-server
   adb start-server
   flutter devices
   ```

### Jika "Waiting for another flutter command"

1. **Tutup proses Flutter yang sedang berjalan:**
   ```bash
   pkill -f flutter
   flutter run
   ```

### Jika Error Build

1. **Clean project:**
   ```bash
   flutter clean
   flutter pub get
   flutter run
   ```

### Jika Error Network/API

1. **Pastikan emulator bisa akses internet:**
   ```bash
   # Test dari emulator
   adb shell ping -c 4 8.8.8.8
   ```

2. **Untuk localhost API di Mac:**
   - Gunakan `10.0.2.2` sebagai pengganti `localhost`
   - Atau gunakan IP address Mac Anda di jaringan lokal

## 📱 Testing

1. **Login Screen:**
   - Masukkan username dan password
   - Pilih tahun anggaran dari dropdown
   - Klik tombol MASUK

2. **Dashboard:**
   - Setelah login berhasil, akan muncul dashboard
   - Statistik akan dimuat dari API

## 📝 Catatan

- **Package Name:** `com.example.simonev21_mobile`
- **App Label:** SIMONEV 21
- **Min SDK:** Sesuai dengan Flutter default
- **Target SDK:** Android 15 (API 35)

## 🎯 Next Steps

Setelah aplikasi berjalan:
1. Test semua fitur login
2. Test dashboard dan API calls
3. Test navigasi antar screen
4. Build APK untuk testing di device fisik

