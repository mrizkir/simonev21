# Perintah Check Sebelum Flutter Run

## ❌ `flutter run --dry-run` TIDAK TERSEDIA

Perintah `flutter run --dry-run` **tidak ada** di Flutter. Ini adalah kesalahan informasi sebelumnya.

## ✅ Alternatif Perintah untuk Check Sebelum Run

### 1. **Analyze (Recommended - Cepat)**
```bash
# Check error, warning, dan info tanpa compile penuh
flutter analyze
```

**Output:**
- ✅ No issues found = Siap untuk run
- ⚠️ Warning = Bisa dijalankan, tapi ada perbaikan disarankan
- ❌ Error = Perlu diperbaiki sebelum run

### 2. **Build APK (Test Compile - Lama)**
```bash
# Compile penuh ke APK (tidak run)
flutter build apk --debug
```

**Catatan:** Ini akan compile penuh, jadi memakan waktu lebih lama. Berguna untuk memastikan compile benar-benar berhasil.

### 3. **Format Code (Optional)**
```bash
# Format semua file Dart
flutter format .
```

### 4. **Get Dependencies**
```bash
# Install dependencies
flutter pub get
```

## 🚀 Workflow yang Direkomendasikan

### Sebelum Run (Quick Check):
```bash
cd mobile

# 1. Format code (optional)
flutter format .

# 2. Analyze (check errors)
flutter analyze

# 3. Get dependencies (jika ada perubahan)
flutter pub get

# 4. Jika analyze OK, langsung run
flutter run
```

### Jika Ingin Test Compile Penuh:
```bash
# Build APK (ini akan compile penuh)
flutter build apk --debug

# Jika build berhasil, baru run
flutter run
```

## 📊 Perbandingan

| Perintah | Waktu | Fungsi |
|----------|-------|--------|
| `flutter analyze` | ⚡ Cepat (~5-10 detik) | Check error/warning |
| `flutter build apk --debug` | 🐌 Lama (~1-2 menit) | Test compile penuh |
| `flutter run` | ⏱️ Medium (~30 detik) | Compile + Run |

## 💡 Tips

1. **Gunakan `flutter analyze`** sebelum commit - cepat dan cukup
2. **Gunakan `flutter build apk`** jika ingin pastikan compile 100% berhasil
3. **Langsung `flutter run`** jika sudah yakin code OK - akan compile otomatis sebelum run
4. **`flutter run`** akan compile dulu sebelum run, jadi jika ada error akan ketahuan juga

## ⚠️ Kesalahan Umum

❌ **SALAH:**
```bash
flutter run --dry-run  # Tidak ada!
```

✅ **BENAR:**
```bash
flutter analyze        # Check errors/warnings
flutter build apk      # Test compile
flutter run            # Compile + Run (akan compile dulu otomatis)
```

## 🎯 Kesimpulan

**Tidak ada perintah `flutter run --dry-run`.**

Gunakan:
- **`flutter analyze`** untuk check cepat (recommended)
- **`flutter build apk`** untuk test compile penuh (optional)
- **`flutter run`** langsung - akan compile dulu sebelum run otomatis

