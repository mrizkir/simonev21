# Setup Android Emulator untuk Flutter

## Masalah: "No supported devices connected"

Ini berarti Flutter tidak mendeteksi emulator atau device fisik. Solusinya adalah membuat dan menjalankan Android Virtual Device (AVD).

## Langkah-langkah:

### 1. Cek Flutter Doctor (Pastikan setup sudah benar)

```bash
flutter doctor
```

Pastikan:
- ✅ Flutter SDK sudah terinstall
- ✅ Android toolchain sudah terinstall
- ✅ Android Studio sudah terinstall (opsional tapi disarankan)

### 2. Cek Emulator yang Tersedia

```bash
# Cek AVD yang sudah dibuat
emulator -list-avds
```

Jika kosong, lanjut ke step 3.

### 3. Buat AVD (Android Virtual Device)

#### Opsi A: Menggunakan Android Studio (Recommended)

1. Buka **Android Studio**
2. Klik **More Actions** → **Virtual Device Manager**
3. Klik **Create Device**
4. Pilih device (misalnya **Pixel 5**)
5. Klik **Next**
6. Pilih system image (misalnya **API 34 - Android 14**)
7. Klik **Next** → **Finish**

#### Opsi B: Menggunakan Command Line

**Untuk Apple Silicon Mac (M1/M2/M3):**
```bash
avdmanager create avd \
  -n Pixel_5_API_34 \
  -k "system-images;android-34;google_apis;arm64-v8a" \
  -d "pixel_5"
```

**Untuk Intel Mac:**
```bash
avdmanager create avd \
  -n Pixel_5_API_34 \
  -k "system-images;android-34;google_apis;x86_64" \
  -d "pixel_5"
```

### 4. Jalankan Emulator

#### Opsi A: Dari Android Studio
1. Buka **Virtual Device Manager**
2. Klik tombol **Play** pada emulator yang ingin dijalankan

#### Opsi B: Dari Command Line
```bash
# List emulator yang tersedia
emulator -list-avds

# Jalankan emulator (ganti dengan nama AVD Anda)
emulator -avd Pixel_5_API_34

# Atau jalankan di background
emulator -avd Pixel_5_API_34 &
```

### 5. Verifikasi Emulator Berjalan

```bash
# Cek device yang terhubung
flutter devices

# Atau menggunakan adb
adb devices
```

Output yang diharapkan:
```
List of devices attached
emulator-5554   device
```

### 6. Jalankan Flutter App

Setelah emulator berjalan:

```bash
cd mobile
flutter run
```

### 7. Troubleshooting

#### Jika `flutter devices` tidak menampilkan emulator:

1. **Pastikan emulator sudah boot sempurna**
   - Tunggu sampai lock screen muncul di emulator

2. **Restart adb server:**
   ```bash
   adb kill-server
   adb start-server
   adb devices
   ```

3. **Pastikan ANDROID_HOME sudah diset:**
   ```bash
   # Cek
   echo $ANDROID_HOME
   
   # Set jika belum (ganti path sesuai lokasi Anda)
   export ANDROID_HOME=$HOME/Library/Android/sdk
   export PATH=$PATH:$ANDROID_HOME/emulator
   export PATH=$PATH:$ANDROID_HOME/platform-tools
   export PATH=$PATH:$ANDROID_HOME/tools
   export PATH=$PATH:$ANDROID_HOME/tools/bin
   ```

   Tambahkan ke `~/.zshrc` agar permanent:
   ```bash
   echo 'export ANDROID_HOME=$HOME/Library/Android/sdk' >> ~/.zshrc
   echo 'export PATH=$PATH:$ANDROID_HOME/emulator' >> ~/.zshrc
   echo 'export PATH=$PATH:$ANDROID_HOME/platform-tools' >> ~/.zshrc
   echo 'export PATH=$PATH:$ANDROID_HOME/tools' >> ~/.zshrc
   echo 'export PATH=$PATH:$ANDROID_HOME/tools/bin' >> ~/.zshrc
   source ~/.zshrc
   ```

4. **Cek Flutter PATH:**
   ```bash
   # Cek
   which flutter
   
   # Set jika belum (ganti path sesuai lokasi Flutter Anda)
   export PATH=$PATH:$HOME/flutter/bin
   
   # Tambahkan ke ~/.zshrc
   echo 'export PATH=$PATH:$HOME/flutter/bin' >> ~/.zshrc
   source ~/.zshrc
   ```

#### Jika AVD tidak bisa dibuat:

1. **Pastikan system image sudah terinstall:**
   ```bash
   sdkmanager --list | grep system-images
   ```

2. **Install system image jika belum:**
   ```bash
   # Apple Silicon Mac
   sdkmanager "system-images;android-34;google_apis;arm64-v8a"
   
   # Intel Mac
   sdkmanager "system-images;android-34;google_apis;x86_64"
   ```

3. **Accept licenses:**
   ```bash
   sdkmanager --licenses
   ```

#### Jika emulator lambat:

1. **Enable hardware acceleration:**
   - Di Android Studio: Tools → SDK Manager → SDK Tools
   - Centang "Intel x86 Emulator Accelerator (HAXM installer)"

2. **Gunakan emulator dengan lebih sedikit RAM:**
   - Edit AVD → Advanced Settings
   - Kurangi RAM allocation

### Quick Reference Commands

```bash
# Cek Flutter setup
flutter doctor

# List AVD
emulator -list-avds

# Jalankan emulator
emulator -avd <AVD_NAME>

# Cek devices
flutter devices
adb devices

# Run Flutter app
flutter run

# Restart adb
adb kill-server && adb start-server
```

## Catatan Penting

1. **Pastikan emulator sudah boot sempurna** sebelum menjalankan `flutter run`
2. **Tunggu sampai home screen muncul** di emulator (bukan hanya lock screen)
3. **Pastikan ANDROID_HOME dan PATH sudah benar** di terminal yang digunakan
4. **Gunakan terminal baru** setelah mengubah PATH di `.zshrc`

