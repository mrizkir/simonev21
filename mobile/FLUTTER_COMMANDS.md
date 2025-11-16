# Flutter Commands - Compile dan Check Sebelum Run

## ✅ Perintah untuk Compile/Check Sebelum Run

### 1. **Analyze Code (Check Errors & Warnings)**
```bash
# Cek semua error dan warning tanpa menjalankan app
flutter analyze

# Output akan menampilkan:
# - Error (jika ada)
# - Warning (jika ada)
# - Info (jika ada)
```

### 2. **Check Compilation (Test Build)**
```bash
# Build APK untuk test compile (tidak run)
flutter build apk --debug

# Atau build untuk device spesifik
flutter build apk --debug --target-platform android-arm64

# Catatan: flutter run --dry-run tidak tersedia
# Alternatif: Gunakan flutter analyze untuk check errors
```

### 3. **Build APK (Compile untuk Android)**
```bash
# Build debug APK (untuk testing)
flutter build apk --debug

# Build release APK (untuk production)
flutter build apk --release

# APK akan berada di: build/app/outputs/flutter-apk/app-release.apk
```

### 4. **Build App Bundle (untuk Play Store)**
```bash
# Build app bundle untuk Google Play Store
flutter build appbundle --release

# File akan berada di: build/app/outputs/bundle/release/app-release.aab
```

### 5. **Format Code**
```bash
# Format semua file Dart sesuai style guide
flutter format .

# Check format tanpa mengubah (dry run)
flutter format --dry-run .
```

### 6. **Get Dependencies (Install Packages)**
```bash
# Install semua dependencies dari pubspec.yaml
flutter pub get

# Upgrade dependencies ke versi terbaru yang compatible
flutter pub upgrade

# Outdated packages check
flutter pub outdated
```

## 🔍 Workflow yang Direkomendasikan

### Sebelum Run (Check Dulu):
```bash
# 1. Format code
flutter format .

# 2. Analyze untuk cek error
flutter analyze

# 3. Get dependencies (jika ada perubahan di pubspec.yaml)
flutter pub get

# 4. Test build untuk cek compile (optional, karena lama)
# flutter build apk --debug

# 5. Jika semua OK, baru jalankan
flutter run
```

### Quick Check Script:
```bash
#!/bin/bash
echo "🔍 Checking Flutter project..."
echo ""

echo "1. Formatting code..."
flutter format . --dry-run

echo ""
echo "2. Analyzing code..."
flutter analyze

echo ""
echo "3. Checking dependencies..."
flutter pub get

echo ""
echo "4. Dry run..."
flutter run --dry-run

echo ""
echo "✅ All checks completed!"
```

## 🚀 Build Commands

### Debug Build (Development):
```bash
# Android APK
flutter build apk --debug

# iOS (hanya di Mac dengan Xcode)
flutter build ios --debug

# Web
flutter build web --debug
```

### Release Build (Production):
```bash
# Android APK
flutter build apk --release

# Android App Bundle (untuk Play Store)
flutter build appbundle --release

# iOS (hanya di Mac dengan Xcode)
flutter build ios --release

# Web
flutter build web --release
```

## 🔧 Troubleshooting Commands

### Clean Build:
```bash
# Hapus semua build artifacts
flutter clean

# Lalu rebuild
flutter pub get
flutter build apk
```

### Check Setup:
```bash
# Cek setup Flutter
flutter doctor

# Cek setup dengan detail
flutter doctor -v
```

### Check Devices:
```bash
# List semua devices yang tersedia
flutter devices

# List emulators
flutter emulators

# Launch emulator
flutter emulators --launch <emulator_id>
```

## 📝 Quick Reference

| Command | Fungsi |
|---------|--------|
| `flutter analyze` | ✅ Check error & warning |
| `flutter format .` | 📝 Format code |
| `flutter pub get` | 📦 Install dependencies |
| `flutter run --dry-run` | 🔍 Check compile tanpa run |
| `flutter build apk` | 📱 Build APK |
| `flutter clean` | 🧹 Clean build artifacts |
| `flutter doctor` | 🩺 Check setup |

## 💡 Tips

1. **Selalu run `flutter analyze`** sebelum commit code
2. **Gunakan `flutter format .`** untuk konsistensi code style
3. **`flutter run --dry-run`** untuk cek compile tanpa launch app
4. **`flutter clean`** jika ada masalah build yang aneh
5. **Check `flutter doctor`** jika ada masalah setup

## 🎯 Contoh Workflow Lengkap

```bash
# 1. Masuk ke folder project
cd mobile

# 2. Format code
flutter format .

# 3. Analyze (check errors)
flutter analyze

# 4. Get dependencies
flutter pub get

# 5. Check compile (dry run)
flutter run --dry-run

# 6. Jika semua OK, jalankan
flutter run

# Atau build APK
flutter build apk --release
```

