# Fix Flutter Doctor Error: Android SDK 36 Required

## Masalah

```
Android toolchain - develop for Android devices (Android SDK version 35.0.0)
    ✗ Flutter requires Android SDK 36 and the Android BuildTools 28.0.3
```

## Penyebab

Flutter versi yang digunakan (kemungkinan versi development/preview) membutuhkan Android SDK 36 yang belum tersedia. Android SDK terbaru adalah 35.

## Solusi

### Opsi 1: Update Flutter ke Versi Stable (Recommended)

```bash
# Cek versi Flutter saat ini
flutter --version

# Update ke versi stable terbaru
flutter channel stable
flutter upgrade

# Verifikasi
flutter doctor
```

### Opsi 2: Install Android SDK yang Diperlukan

Jika Anda memang perlu menggunakan Flutter versi development:

```bash
# Install Android SDK 36 (jika tersedia)
sdkmanager "platforms;android-36"

# Install Android BuildTools 28.0.3
sdkmanager "build-tools;28.0.3"

# Verify
sdkmanager --list_installed
```

**Catatan:** Android SDK 36 mungkin belum tersedia. Lebih baik gunakan Opsi 1.

### Opsi 3: Switch ke Flutter Version Lain

```bash
# List Flutter versions
flutter version

# Install Flutter version spesifik (contoh)
flutter version 3.24.0

# Atau gunakan FVM (Flutter Version Management)
# Install FVM
dart pub global activate fvm

# Install Flutter stable
fvm install stable

# Use stable version
fvm use stable

# Run dengan FVM
fvm flutter run
```

### Opsi 4: Fix Manual (Jika Flutter Versi Lama)

Jika Flutter Anda terlalu lama dan membutuhkan update:

```bash
# 1. Update Flutter
flutter upgrade

# 2. Accept licenses
flutter doctor --android-licenses

# 3. Install Android SDK yang disarankan
sdkmanager "platforms;android-34"
sdkmanager "build-tools;34.0.0"

# 4. Verify
flutter doctor
```

## Langkah-langkah Detail

### 1. Cek Flutter Channel dan Version

```bash
flutter channel
flutter --version
```

### 2. Switch ke Stable Channel

```bash
flutter channel stable
flutter upgrade
flutter doctor
```

### 3. Set Android SDK Path (Jika Belum)

```bash
# Set ANDROID_HOME (ganti path sesuai lokasi Anda)
export ANDROID_HOME=$HOME/Library/Android/sdk

# Tambahkan ke ~/.zshrc agar permanent
echo 'export ANDROID_HOME=$HOME/Library/Android/sdk' >> ~/.zshrc
source ~/.zshrc
```

### 4. Install Android SDK yang Diperlukan

```bash
# Install Android SDK 34 (versi terbaru yang tersedia)
sdkmanager "platforms;android-34"

# Install Build Tools 34.0.0
sdkmanager "build-tools;34.0.0"

# Install Platform Tools
sdkmanager "platform-tools"

# Accept licenses
sdkmanager --licenses
```

### 5. Accept Flutter Licenses

```bash
flutter doctor --android-licenses
# Tekan 'y' untuk semua license yang ditanyakan
```

### 6. Verifikasi Setup

```bash
flutter doctor -v
```

## Troubleshooting

### Jika `flutter upgrade` error:

```bash
# Hapus cache Flutter
flutter clean

# Update Flutter
flutter channel stable
flutter upgrade --force

# Verify
flutter doctor
```

### Jika Android SDK tidak ditemukan:

1. Install Android Studio
2. Open Android Studio → More Actions → SDK Manager
3. Install Android SDK Platform 34
4. Install Android SDK Build-Tools 34.0.0
5. Set ANDROID_HOME:

```bash
export ANDROID_HOME=$HOME/Library/Android/sdk
export PATH=$PATH:$ANDROID_HOME/platform-tools
export PATH=$PATH:$ANDROID_HOME/tools
export PATH=$PATH:$ANDROID_HOME/tools/bin
```

### Jika masih error setelah update:

```bash
# Reinstall Flutter (backup dulu jika perlu)
cd ~
rm -rf flutter
git clone https://github.com/flutter/flutter.git -b stable
export PATH=$PATH:$HOME/flutter/bin
flutter doctor
```

## Catatan Penting

1. **Android SDK 36 belum tersedia** - Gunakan Flutter stable yang kompatibel dengan SDK 34/35
2. **BuildTools 28.0.3 terdengar aneh** - Versi build tools biasanya lebih tinggi (34.0.0, 33.0.0, dst)
3. **Gunakan Flutter Stable Channel** untuk development yang stabil
4. **Pastikan ANDROID_HOME sudah diset** sebelum menjalankan Flutter

## Recommended Setup

```bash
# 1. Flutter Stable
flutter channel stable
flutter upgrade

# 2. Android SDK 34
sdkmanager "platforms;android-34"
sdkmanager "build-tools;34.0.0"
sdkmanager "platform-tools"

# 3. Accept licenses
flutter doctor --android-licenses

# 4. Verify
flutter doctor
```

Hasil yang diharapkan:
```
✓ Flutter (Channel stable, ...)
✓ Android toolchain - develop for Android devices (Android SDK version 34.0.0)
✓ Android Studio (version ...)
```

