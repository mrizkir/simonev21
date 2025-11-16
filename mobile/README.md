# SIMONEV 21 Mobile App

Aplikasi mobile Android untuk SIMONEV 21 - Sistem Informasi Monitoring dan Evaluasi Pembangunan versi 21.

## Fitur

- ✅ Autentikasi dengan JWT
- ✅ Dashboard dengan statistik utama
- ✅ Data Master
- ✅ Monitoring Renja (Murni & Perubahan)
- ✅ RPJMD
- ✅ Gallery Pembangunan
- ✅ Laporan & Statistik

## Setup

### Prerequisites

- Flutter SDK (>=3.0.0)
- Android Studio / VS Code
- Android SDK (untuk Android development)

### Installation

1. Install dependencies:
```bash
cd mobile
flutter pub get
```

2. Konfigurasi API URL:
   - Buka file `lib/utils/app_config.dart`
   - Ubah `baseUrl` sesuai dengan URL backend Anda
   - Contoh: `static const String baseUrl = 'http://192.168.1.100:8000/api/v1';`

3. Jalankan aplikasi:
```bash
flutter run
```

## Struktur Project

```
mobile/
├── lib/
│   ├── main.dart                 # Entry point
│   ├── models/                   # Data models
│   │   ├── user_model.dart
│   │   └── dashboard_model.dart
│   ├── providers/                # State management
│   │   ├── auth_provider.dart
│   │   └── dashboard_provider.dart
│   ├── screens/                  # UI Screens
│   │   ├── auth/
│   │   │   └── login_screen.dart
│   │   ├── home/
│   │   │   └── dashboard_screen.dart
│   │   └── splash_screen.dart
│   ├── services/                 # API & Storage services
│   │   ├── api_service.dart
│   │   └── storage_service.dart
│   ├── utils/                    # Utilities
│   │   └── app_config.dart
│   └── widgets/                  # Reusable widgets
│       └── stat_card.dart
├── pubspec.yaml
└── README.md
```

## API Integration

Aplikasi ini menggunakan API dari backend Laravel/Lumen SIMONEV 21. Pastikan backend sudah berjalan dan dapat diakses dari device/emulator Anda.

### Endpoints yang digunakan:

- `POST /api/v1/auth/login` - Login
- `GET /api/v1/auth/me` - Get current user
- `POST /api/v1/auth/logout` - Logout
- `POST /api/v1/dashboard/front` - Get dashboard data
- Dan endpoint lainnya sesuai kebutuhan

## Development

### Menambahkan fitur baru:

1. Buat model di folder `lib/models/`
2. Buat provider di folder `lib/providers/` jika perlu state management
3. Buat service method di `lib/services/api_service.dart` jika perlu API call baru
4. Buat screen di folder `lib/screens/`
5. Tambahkan route di `lib/main.dart` jika perlu

## Build APK

Untuk build APK release:

```bash
flutter build apk --release
```

APK akan berada di `build/app/outputs/flutter-apk/app-release.apk`

## Catatan

- Pastikan URL backend dapat diakses dari device/emulator
- Untuk testing di emulator Android, gunakan `10.0.2.2` sebagai pengganti `localhost`
- Untuk testing di device fisik, gunakan IP address komputer Anda di jaringan yang sama

