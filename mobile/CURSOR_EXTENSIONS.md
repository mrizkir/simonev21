# Extension Cursor/VS Code untuk Flutter Development

## ✅ Extension Flutter untuk Cursor

Cursor menggunakan extension marketplace yang sama dengan VS Code, jadi extension VS Code bisa digunakan di Cursor.

## 🔧 Extension yang Direkomendasikan

### 1. **Flutter** (Official)
**Publisher:** Dart Code Team
**ID:** `Dart-Code.flutter`

**Fitur:**
- ✅ Run Flutter commands (flutter run, flutter build, dll)
- ✅ Hot reload (Ctrl+S atau Cmd+S)
- ✅ Hot restart
- ✅ Debugging dengan breakpoints
- ✅ Flutter DevTools integration
- ✅ Flutter doctor
- ✅ Command palette commands

**Install:**
1. Buka Cursor
2. Klik **Extensions** icon (atau Cmd+Shift+X / Ctrl+Shift+X)
3. Search: **"Flutter"**
4. Install extension **"Flutter"** oleh Dart Code

### 2. **Dart** (Required)
**Publisher:** Dart Code Team
**ID:** `Dart-Code.dart-code`

**Fitur:**
- ✅ Dart language support
- ✅ Code completion
- ✅ Error detection
- ✅ Format on save

**Note:** Biasanya sudah terinstall otomatis saat install Flutter extension.

## 🚀 Cara Menggunakan Flutter Extension

### 1. Run Flutter App

**Cara 1: Command Palette**
1. Tekan **Cmd+Shift+P** (Mac) atau **Ctrl+Shift+P** (Windows/Linux)
2. Ketik: **"Flutter: Run"**
3. Pilih device (HP Anda atau emulator)

**Cara 2: Run Button**
1. Buka file `lib/main.dart`
2. Klik icon **Run** di pojok kanan atas (atau tombol play)
3. Atau klik **"Run and Debug"** di sidebar kiri

**Cara 3: Terminal Panel**
1. Buka terminal di Cursor (Cmd+` atau Ctrl+`)
2. Ketik: `flutter run`
3. Pilih device saat diminta

### 2. Hot Reload

**Cara 1: Keyboard Shortcut**
- **Mac:** Cmd+S (save file = hot reload)
- **Windows/Linux:** Ctrl+S

**Cara 2: Command Palette**
- Cmd+Shift+P → **"Flutter: Hot Reload"**

**Cara 3: Button**
- Klik icon reload di debug toolbar

### 3. Hot Restart

**Cara 1: Command Palette**
- Cmd+Shift+P → **"Flutter: Hot Restart"**

**Cara 2: Keyboard Shortcut**
- Cmd+Shift+F5 atau Ctrl+Shift+F5

### 4. Debug Mode (Dengan Breakpoints)

1. Set breakpoint (klik di sebelah kiri line number)
2. Klik **Run and Debug** (icon bug di sidebar)
3. Pilih **"Flutter"** atau **"Dart & Flutter"**
4. App akan pause di breakpoint
5. Inspect variables, step through code, dll

### 5. Flutter DevTools

**Cara 1: Command Palette**
- Cmd+Shift+P → **"Flutter: Open DevTools"**

**Cara 2: Link di Terminal**
- Saat `flutter run`, akan muncul link untuk DevTools

## 📋 Flutter Commands di Cursor

Dari Command Palette (Cmd+Shift+P), ketikkan:

- **Flutter: Run** - Run app
- **Flutter: Hot Reload** - Hot reload
- **Flutter: Hot Restart** - Hot restart
- **Flutter: Clean** - Clean project
- **Flutter: Get Packages** - flutter pub get
- **Flutter: Analyze** - flutter analyze
- **Flutter: Doctor** - flutter doctor
- **Flutter: Build APK** - Build APK
- **Flutter: Open DevTools** - Open DevTools

## 🎯 Setup Run Configuration

### 1. Buat `.vscode/launch.json`

File ini sudah dibuat otomatis saat `flutter create .`, tapi bisa di-custom:

```json
{
  "version": "0.2.0",
  "configurations": [
    {
      "name": "simonev21_mobile (Debug)",
      "request": "launch",
      "type": "dart",
      "program": "lib/main.dart"
    },
    {
      "name": "simonev21_mobile (Profile)",
      "request": "launch",
      "type": "dart",
      "flutterMode": "profile",
      "program": "lib/main.dart"
    },
    {
      "name": "simonev21_mobile (Release)",
      "request": "launch",
      "type": "dart",
      "flutterMode": "release",
      "program": "lib/main.dart"
    }
  ]
}
```

### 2. Gunakan Run Configuration

1. Klik icon **Run and Debug** di sidebar kiri
2. Pilih configuration dari dropdown (misalnya "simonev21_mobile (Debug)")
3. Klik tombol **Play** (▶️)

## 🔍 Debugging dengan Cursor

### 1. Set Breakpoint
- Klik di sebelah kiri line number (akan muncul titik merah)
- App akan pause saat sampai di breakpoint

### 2. Debug Actions
- **Continue** (F5) - Continue execution
- **Step Over** (F10) - Step over line
- **Step Into** (F11) - Step into function
- **Step Out** (Shift+F11) - Step out function
- **Restart** (Cmd+Shift+F5) - Restart debug
- **Stop** (Shift+F5) - Stop debug

### 3. Inspect Variables
- Saat pause di breakpoint, hover mouse ke variable untuk lihat value
- Atau lihat di **Variables** panel di sidebar debug

## 📱 Memilih Device untuk Run

### Cara 1: Dari Command Palette
1. Cmd+Shift+P → **"Flutter: Select Device"**
2. Pilih device (HP Anda atau emulator)

### Cara 2: Dari Status Bar
- Klik device name di status bar (bawah Cursor)
- Pilih device

### Cara 3: Otomatis
- Saat run pertama kali, Flutter akan list semua device
- Pilih device yang ingin digunakan

## 🛠️ Extension Tambahan yang Berguna

### 1. **Error Lens**
**ID:** `usernamehw.errorlens`

**Fitur:**
- ✅ Tampilkan error/warning inline di editor
- ✅ Lebih mudah melihat error tanpa buka Problems panel

### 2. **Dart Data Class Generator**
**ID:** `BracketLabs.Dart Data Class Generator`

**Fitur:**
- ✅ Generate data class dari JSON
- ✅ Generate fromJson/toJson

### 3. **Flutter Widget Snippets**
**ID:** `alexisvt.flutter-snippets`

**Fitur:**
- ✅ Code snippets untuk Flutter widgets
- ✅ Shortcuts untuk widget umum

### 4. **Flutter Tree**
**ID:** `marcelovelasquez.flutter-tree`

**Fitur:**
- ✅ Visual widget tree
- ✅ Inspect widget hierarchy

## 💡 Tips & Tricks

### 1. Keyboard Shortcuts

**Mac:**
- `Cmd+Shift+P` - Command Palette
- `Cmd+S` - Save + Hot Reload
- `Cmd+Shift+F5` - Hot Restart
- `F5` - Start Debugging
- `Shift+F5` - Stop Debugging
- `Cmd+` ` - Toggle Terminal

**Windows/Linux:**
- `Ctrl+Shift+P` - Command Palette
- `Ctrl+S` - Save + Hot Reload
- `Ctrl+Shift+F5` - Hot Restart
- `F5` - Start Debugging
- `Shift+F5` - Stop Debugging
- `Ctrl+` ` - Toggle Terminal

### 2. Quick Actions
- Hover mouse ke widget → muncul quick actions (Wrap, Extract, dll)
- Cmd+. (Mac) atau Ctrl+. (Win) untuk quick actions menu

### 3. Format Code
- `Cmd+Shift+F` (Mac) atau `Ctrl+Shift+F` (Win) - Format document
- Atau setup format on save (otomatis format saat save)

### 4. Multi-cursor Editing
- `Cmd+D` (Mac) atau `Ctrl+D` (Win) - Select next occurrence
- `Cmd+Shift+L` (Mac) atau `Ctrl+Shift+L` (Win) - Select all occurrences

## 📝 Setup Format on Save

Di `.vscode/settings.json` (atau Cursor settings):

```json
{
  "editor.formatOnSave": true,
  "editor.defaultFormatter": "Dart-Code.dart-code",
  "[dart]": {
    "editor.formatOnSave": true,
    "editor.defaultFormatter": "Dart-Code.dart-code"
  }
}
```

## 🎯 Quick Start

1. **Install Extension:**
   - Buka Extensions (Cmd+Shift+X)
   - Search "Flutter"
   - Install **Flutter** extension

2. **Run App:**
   - Buka `lib/main.dart`
   - Tekan F5 atau klik Run button
   - Pilih device (HP Anda)

3. **Hot Reload:**
   - Edit code
   - Save (Cmd+S) = auto hot reload
   - Atau tekan `r` di terminal

4. **Debug:**
   - Set breakpoint
   - Start debugging (F5)
   - Inspect variables saat pause

## ✅ Checklist Setup

- [ ] Install Flutter extension
- [ ] Install Dart extension (auto dengan Flutter)
- [ ] Buka project folder di Cursor
- [ ] Run `flutter pub get` untuk pertama kali
- [ ] Set device (HP atau emulator)
- [ ] Run app (F5 atau Run button)

---

**Selamat! Sekarang bisa run Flutter dari Cursor dengan mudah! 🎉**

