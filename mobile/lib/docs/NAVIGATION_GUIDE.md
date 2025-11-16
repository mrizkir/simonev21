# Panduan Navigasi di Flutter - SIMONEV 21

## Istilah di Flutter

### ❌ Bukan Activity
Di Flutter, **TIDAK menggunakan istilah "Activity"** karena itu adalah konsep Android Native.

### ✅ Gunakan istilah ini:
- **Screen** - Istilah yang paling umum digunakan untuk halaman di Flutter
- **Page** - Alternatif istilah, tapi kurang umum
- **Route** - Untuk navigasi/routing (seperti URL di web)

## Perbandingan dengan Platform Lain

| Platform | Istilah | Contoh File |
|----------|---------|-------------|
| **Web** | Page | `login.html`, `login.vue` |
| **Android Native** | Activity | `LoginActivity.kt` |
| **iOS Native** | ViewController | `LoginViewController.swift` |
| **Flutter** | **Screen** ✅ | `login_screen.dart` |

## Cara Menambahkan Screen Baru

### 1. Buat File Screen Baru

Buat file di folder `lib/screens/[nama_folder]/[nama]_screen.dart`

**Contoh:**
```
lib/screens/profile/profile_screen.dart
```

**Template dasar:**
```dart
import 'package:flutter/material.dart';

class ProfileScreen extends StatefulWidget {
  const ProfileScreen({super.key});

  @override
  State<ProfileScreen> createState() => _ProfileScreenState();
}

class _ProfileScreenState extends State<ProfileScreen> {
  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
        title: const Text('Profile'),
      ),
      body: const Center(
        child: Text('Profile Screen'),
      ),
    );
  }
}
```

### 2. Import Screen di main.dart

```dart
import 'screens/profile/profile_screen.dart';
```

### 3. Tambahkan Route (Opsi 1: Named Routes)

Di `MaterialApp`, tambahkan route:

```dart
MaterialApp(
  routes: {
    '/login': (context) => const LoginScreen(),
    '/dashboard': (context) => const DashboardScreen(),
    '/profile': (context) => const ProfileScreen(), // ✅ Tambahkan di sini
  },
)
```

**Navigasi ke screen:**
```dart
Navigator.pushNamed(context, '/profile');
```

### 4. Atau Gunakan MaterialPageRoute (Opsi 2: Direct Navigation)

```dart
Navigator.push(
  context,
  MaterialPageRoute(
    builder: (context) => const ProfileScreen(),
  ),
);
```

## Jenis Navigasi di Flutter

### 1. Push (Menambah ke stack)
```dart
Navigator.push(
  context,
  MaterialPageRoute(builder: (context) => const ProfileScreen()),
);
```
- Menambah screen baru di atas screen saat ini
- Bisa kembali dengan tombol back

### 2. Push Replacement (Mengganti screen)
```dart
Navigator.pushReplacement(
  context,
  MaterialPageRoute(builder: (context) => const DashboardScreen()),
);
```
- Mengganti screen saat ini
- Tidak bisa kembali ke screen sebelumnya

### 3. Push Named (Named Route)
```dart
Navigator.pushNamed(context, '/profile');
```
- Menggunakan named route yang sudah didefinisikan di `main.dart`

### 4. Push and Remove Until (Clear stack)
```dart
Navigator.pushAndRemoveUntil(
  context,
  MaterialPageRoute(builder: (context) => const LoginScreen()),
  (route) => false, // Hapus semua route sebelumnya
);
```
- Menghapus semua screen di stack dan menambah screen baru
- Biasanya untuk logout

### 5. Pop (Kembali)
```dart
Navigator.pop(context);
// Atau dengan return value
Navigator.pop(context, 'data yang dikembalikan');
```

## Contoh Lengkap: Menambahkan Profile Screen

### Step 1: Buat Screen
File: `lib/screens/profile/profile_screen.dart` (sudah dibuat)

### Step 2: Import di main.dart
```dart
import 'screens/profile/profile_screen.dart';
```

### Step 3: Tambahkan Route
```dart
routes: {
  '/profile': (context) => const ProfileScreen(),
}
```

### Step 4: Navigasi ke Profile Screen
Dari Dashboard, tambahkan button:

```dart
ElevatedButton(
  onPressed: () {
    Navigator.pushNamed(context, '/profile');
    // Atau:
    // Navigator.push(
    //   context,
    //   MaterialPageRoute(builder: (context) => const ProfileScreen()),
    // );
  },
  child: const Text('Buka Profile'),
)
```

## Struktur Folder Screen

```
lib/screens/
├── splash_screen.dart          # Splash screen
├── auth/
│   └── login_screen.dart       # Login screen
├── home/
│   └── dashboard_screen.dart   # Dashboard screen
├── profile/
│   └── profile_screen.dart     # Profile screen (contoh baru)
├── renja/
│   ├── renja_murni_screen.dart
│   └── renja_perubahan_screen.dart
└── rpjmd/
    └── rpjmd_screen.dart
```

## Tips

1. **Gunakan istilah "Screen"** bukan "Activity" atau "Page"
2. **Organisir screen** dalam folder sesuai kategori
3. **Gunakan named routes** untuk navigasi yang sederhana
4. **Gunakan MaterialPageRoute** untuk navigasi dengan parameter

## Kesimpulan

- ✅ Di Flutter: **Screen** (bukan Activity!)
- ✅ Navigasi: **Route** atau **MaterialPageRoute**
- ✅ Activity = Android Native, bukan Flutter

