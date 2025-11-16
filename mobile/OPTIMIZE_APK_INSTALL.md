# Optimasi Instalasi APK - Mengurangi Waktu Install

## 🔍 Masalah
APK debug berukuran **88MB** menyebabkan instalasi memakan waktu **6.5 detik** atau lebih.

## ✅ Solusi yang Diterapkan

### 1. **Split APKs by ABI** (Architecture)
- APK dipecah berdasarkan arsitektur CPU (arm64-v8a, armeabi-v7a, x86_64)
- Menggunakan flag `--split-per-abi` saat build
- Setiap APK hanya berisi library untuk arsitektur tertentu
- **Hasil**: APK menjadi lebih kecil (sekitar 30-40MB per arsitektur)

### 2. **Code Shrinking & Resource Shrinking** (Release Build)
- Menghapus kode dan resource yang tidak digunakan
- Dikonfigurasi di `build.gradle.kts` untuk release build
- Mengoptimalkan ukuran APK release
- **Hasil**: APK release menjadi lebih kecil

## 🚀 Cara Menggunakan

### Opsi 1: Build dengan Split APKs (Recommended - Paling Cepat)

```bash
# Build debug APK dengan split per ABI
flutter build apk --debug --split-per-abi

# Build release APK dengan split per ABI  
flutter build apk --release --split-per-abi
```

**Output:**
- `app-armeabi-v7a-debug.apk` (~54MB) - untuk device ARM 32-bit
- `app-arm64-v8a-debug.apk` (~74MB) - untuk device ARM 64-bit (kebanyakan device modern)
- `app-x86_64-debug.apk` (~60MB) - untuk emulator x86_64

**Catatan:** Ukuran APK tergantung pada dependencies dan assets yang digunakan. Split APK tetap lebih kecil dari universal APK (88MB).

### Opsi 2: Gunakan Flutter Run (Otomatis)

```bash
# Flutter run akan otomatis menggunakan optimasi build
flutter run

# Atau dengan device spesifik
flutter run -d <device-id>
```

**Catatan:** 
- `flutter run` akan otomatis memilih APK yang tepat untuk device Anda
- Untuk split APK, gunakan flag `--split-per-abi` saat build (tidak perlu konfigurasi di build.gradle.kts)
- Untuk development, `flutter run` sudah cukup cepat karena menggunakan hot reload

### Install APK yang Tepat untuk Device

```bash
# Cek arsitektur device
adb shell getprop ro.product.cpu.abi

# Install APK sesuai arsitektur (contoh untuk arm64-v8a)
adb install build/app/outputs/apk/debug/app-arm64-v8a-debug.apk
```

### Atau Gunakan Flutter Run (Otomatis)

```bash
# Flutter akan otomatis memilih APK yang tepat
flutter run
```

## 📊 Perbandingan

| Metode | Ukuran APK | Waktu Install |
|--------|------------|---------------|
| Universal APK (lama) | ~88MB | ~6-8s |
| Split APK ARM64-v8a (baru) | ~74MB | ~4-5s |

**Catatan:** Waktu install bervariasi tergantung device performance. Device entry-level (seperti Samsung A21s) akan lebih lambat dibanding flagship.

## 💡 Tips Tambahan

### 1. Gunakan Hot Reload untuk Development
```bash
flutter run
```
Setelah app berjalan, gunakan `r` untuk hot reload (perubahan cepat tanpa rebuild penuh) atau `R` untuk hot restart.

### 2. Build dengan Target Platform Spesifik
```bash
# Build hanya untuk ARM64 (kebanyakan device modern)
flutter build apk --debug --target-platform android-arm64

# Hasilnya akan lebih kecil karena hanya untuk satu arsitektur
```

### 3. Gunakan USB 3.0 atau Kabel Berkualitas
Kabel USB yang baik akan mempercepat transfer APK ke device.

### 4. Pastikan Device Tidak Terkunci
Device yang terkunci akan memperlambat instalasi.

### 5. Untuk Release Build
```bash
# Build release dengan optimasi penuh
flutter build apk --release --split-per-abi

# Atau gunakan App Bundle (lebih kecil)
flutter build appbundle --release
```

## 🔧 Troubleshooting

### Jika Error "Conflicting configuration: ndk abiFilters cannot be present when splits abi filters are set"
**Penyebab:** Konflik antara konfigurasi `splits` di `build.gradle.kts` dengan `ndk abiFilters` yang diatur oleh Flutter.

**Solusi:** Jangan konfigurasi `splits` di `build.gradle.kts`. Gunakan flag `--split-per-abi` saat build:
```bash
flutter build apk --debug --split-per-abi
```

Flutter sudah memiliki mekanisme built-in untuk split APKs, jadi tidak perlu konfigurasi manual di build.gradle.kts.

### Jika Error "No matching ABI"
Pastikan device Anda menggunakan salah satu arsitektur yang didukung:
- `arm64-v8a` (kebanyakan device modern)
- `armeabi-v7a` (device lama)
- `x86_64` (emulator)

### Jika Ingin Universal APK (Semua Arsitektur)
```bash
# Build tanpa split
flutter build apk --debug
```

## 📝 Catatan

- Split APKs hanya mengurangi ukuran untuk instalasi, bukan untuk distribusi
- Untuk Play Store, gunakan **App Bundle** (`flutter build appbundle`)
- Debug build tetap lebih besar karena tidak di-optimize
- Release build akan lebih kecil dengan code shrinking

