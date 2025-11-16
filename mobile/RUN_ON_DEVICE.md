# Menjalankan Aplikasi Flutter di HP Android (Device Fisik)

## 🎯 Keuntungan Menggunakan Device Fisik

- ⚡ Lebih cepat dari emulator
- 📱 Testing lebih realistis
- 🔋 Tidak memakan resource komputer
- 📷 Akses kamera, GPS, sensor, dll

## 📋 Persiapan

### 1. Enable Developer Options di HP Android

1. Buka **Settings** → **About phone**
2. Cari **Build number** (bisa juga **MIUI version** untuk Xiaomi)
3. **Tap 7 kali** pada Build number sampai muncul "You are now a developer!"
4. Kembali ke **Settings** → **System** → **Developer options** (atau **Additional settings** → **Developer options**)

### 2. Enable USB Debugging

1. Buka **Developer options**
2. Aktifkan **USB debugging**
3. Aktifkan **Stay awake** (opsional, agar HP tidak sleep saat charging)
4. Aktifkan **Install via USB** (opsional, untuk install APK via USB)

### 3. Connect HP ke Komputer

1. Sambungkan HP ke komputer dengan **USB cable**
2. Di HP akan muncul popup **"Allow USB debugging?"**
3. Centang **"Always allow from this computer"**
4. Tap **"Allow"** atau **"OK"**

### 4. Setup di Komputer (Mac)

#### Cek ADB (Android Debug Bridge)

```bash
# Cek apakah adb sudah terinstall
adb version

# Jika belum ada, adb biasanya sudah termasuk di Android SDK
export PATH=$PATH:$HOME/Library/Android/sdk/platform-tools

# Verify di ~/.zshrc
echo 'export PATH=$PATH:$HOME/Library/Android/sdk/platform-tools' >> ~/.zshrc
source ~/.zshrc
```

#### Cek Device Terdeteksi

```bash
# List devices yang terhubung
adb devices

# Output yang diharapkan:
# List of devices attached
# ABC123XYZ    device
```

Jika muncul **"unauthorized"**, coba:
```bash
# Restart adb server
adb kill-server
adb start-server
adb devices
```

Lalu di HP, **allow USB debugging** lagi.

#### Cek dengan Flutter

```bash
cd mobile
flutter devices

# Output yang diharapkan:
# Found 1 connected device:
#   <device_name> • <device_id> • android-arm64 • Android <version> (API <number>)
```

## 🚀 Menjalankan Aplikasi

### Cara 1: Run Langsung

```bash
cd mobile
flutter run
```

Flutter akan otomatis mendeteksi device fisik dan menjalankan di sana.

### Cara 2: Run dengan Device Spesifik

```bash
# List devices dulu
flutter devices

# Run dengan device ID spesifik
flutter run -d <device_id>

# Contoh:
flutter run -d ABC123XYZ
```

### Cara 3: Build APK dan Install Manual

```bash
# Build APK debug
flutter build apk --debug

# File APK akan berada di:
# build/app/outputs/flutter-apk/app-debug.apk

# Install via ADB
adb install build/app/outputs/flutter-apk/app-debug.apk

# Atau copy file APK ke HP dan install manual
```

## 🔧 Troubleshooting

### 1. Device Tidak Terdeteksi

**Problem:** `adb devices` kosong atau device muncul sebagai "unauthorized"

**Solusi:**
```bash
# 1. Restart adb
adb kill-server
adb start-server

# 2. Cek kabel USB (coba ganti kabel)
# 3. Cek mode USB connection di HP:
#    - Pilih "File Transfer" atau "MTP"
#    - Bukan "Charge only"

# 4. Re-enable USB Debugging di HP
# 5. Trust computer lagi di popup HP
```

### 2. Device Terdeteksi tapi Flutter Tidak Bisa Run

**Problem:** `flutter devices` menampilkan device tapi `flutter run` error

**Solusi:**
```bash
# 1. Pastikan device bukan "unauthorized"
adb devices

# 2. Cek USB debugging masih aktif
# 3. Unlock HP (tidak dalam lock screen)
# 4. Cek developer options masih aktif

# 5. Restart Flutter/ADB
flutter clean
adb kill-server && adb start-server
flutter devices
flutter run
```

### 3. "No devices found" di Flutter

**Solusi:**
```bash
# 1. Pastikan HP terdeteksi di adb
adb devices

# 2. Jika adb tidak ada device, fix masalah ADB dulu

# 3. Jika adb ada device tapi Flutter tidak, cek:
flutter doctor

# 4. Pastikan Android toolchain OK
```

### 4. HP Tidak Muncul Popup "Allow USB Debugging"

**Solusi:**
1. **Revoke USB debugging authorizations:**
   - Settings → Developer options → **Revoke USB debugging authorizations**
   - Disconnect dan reconnect HP
   - Popup akan muncul lagi

2. **Temporary Workaround:**
   ```bash
   # Install APK via ADB tanpa popup
   adb install -r build/app/outputs/flutter-apk/app-debug.apk
   ```

### 5. Kabel USB Tidak Terdeteksi

**Solusi:**
- Ganti kabel USB (pastikan kabel support data transfer, bukan charge-only)
- Coba port USB lain di komputer
- Untuk Mac, coba USB port yang berbeda
- Beberapa HP perlu **USB OTG** atau **USB-C** kabel tertentu

### 6. Install APK Gagal

**Problem:** `adb install` error atau "INSTALL_FAILED"

**Solusi:**
```bash
# Uninstall app lama dulu (jika ada)
adb uninstall com.example.simonev21_mobile

# Install lagi
adb install build/app/outputs/flutter-apk/app-debug.apk

# Atau force reinstall
adb install -r build/app/outputs/flutter-apk/app-debug.apk

# Atau downgrade (jika versi lebih rendah)
adb install -d -r build/app/outputs/flutter-apk/app-debug.apk
```

## 📱 Setup HP Android Berbagai Merek

### Samsung
1. Settings → About phone → Build number (tap 7x)
2. Settings → Developer options → USB debugging ✅

### Xiaomi/Redmi/Poco
1. Settings → About phone → Tap MIUI version 7x
2. Settings → Additional settings → Developer options → USB debugging ✅
3. Settings → Additional settings → Developer options → USB debugging (Security settings) ✅

### Oppo/Realme/OnePlus
1. Settings → About phone → Build number (tap 7x)
2. Settings → Additional settings → Developer options → USB debugging ✅

### Vivo
1. Settings → About phone → Software version (tap 7x pada "Build number")
2. Settings → More settings → Developer options → USB debugging ✅

## 🎯 Quick Start Checklist

- [ ] Enable Developer Options di HP
- [ ] Enable USB Debugging
- [ ] Connect HP ke komputer via USB
- [ ] Allow USB debugging di popup HP
- [ ] `adb devices` menampilkan device
- [ ] `flutter devices` menampilkan device
- [ ] HP tidak dalam lock screen
- [ ] Run `flutter run`

## 📝 Tips & Best Practices

1. **Selalu unlock HP** saat menjalankan app
2. **Gunakan kabel USB berkualitas** yang support data transfer
3. **Keep screen on** saat development (enable "Stay awake" di Developer options)
4. **Hot reload** akan lebih cepat di device fisik
5. **Build release APK** untuk test performa final:
   ```bash
   flutter build apk --release
   ```

## 🚀 Commands Summary

```bash
# Setup
export PATH=$PATH:$HOME/Library/Android/sdk/platform-tools

# Check device
adb devices
flutter devices

# Run app
cd mobile
flutter run

# Build APK
flutter build apk --debug
flutter build apk --release

# Install APK
adb install build/app/outputs/flutter-apk/app-debug.apk
```

## 🔐 Security Note

**USB Debugging** memberikan akses penuh ke device Anda. Hanya enable saat development dan disable setelah selesai untuk keamanan.

---

**Selamat! App akan berjalan lebih cepat di device fisik! 🎉**

