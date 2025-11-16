# Fix Error: INSTALL_FAILED_VERSION_DOWNGRADE

## 🔍 Masalah
Error `INSTALL_FAILED_VERSION_DOWNGRADE` terjadi ketika mencoba install APK dengan versi yang lebih rendah dari yang sudah terinstall di device.

## ✅ Solusi

### Opsi 1: Uninstall Aplikasi Lama (Paling Cepat)
```bash
# Uninstall aplikasi yang sudah terinstall
adb uninstall com.example.simonev21_mobile

# Lalu install ulang
flutter run
```

### Opsi 2: Install dengan Flag --force-reinstall
```bash
# Install dengan force (akan replace versi lama)
adb install -r build/app/outputs/flutter-apk/app-debug.apk
```

### Opsi 3: Tingkatkan Version Code
Edit `pubspec.yaml`:
```yaml
version: 1.0.0+2  # Tingkatkan angka setelah + (versionCode)
```

Lalu rebuild:
```bash
flutter clean
flutter pub get
flutter run
```

### Opsi 4: Gunakan Flutter Run dengan Flag
```bash
# Flutter run akan otomatis handle version conflict
flutter run --release  # Atau --debug
```

## 🚀 Solusi Cepat (Recommended)

```bash
# 1. Uninstall aplikasi lama
adb uninstall com.example.simonev21_mobile

# 2. Run aplikasi baru
flutter run
```

## 📝 Catatan

- **Version Code** (angka setelah `+`): Harus selalu naik untuk setiap build baru
- **Version Name** (sebelum `+`): Bisa sama atau berbeda
- Format: `version: 1.0.0+1` = versionName `1.0.0`, versionCode `1`

## 🔧 Troubleshooting

### Jika masih error setelah uninstall:
```bash
# Clear app data juga
adb shell pm clear com.example.simonev21_mobile

# Lalu uninstall
adb uninstall com.example.simonev21_mobile

# Install ulang
flutter run
```

### Cek versi yang terinstall:
```bash
# Cek versionName
adb shell dumpsys package com.example.simonev21_mobile | grep versionName

# Cek versionCode
adb shell dumpsys package com.example.simonev21_mobile | grep versionCode
```

