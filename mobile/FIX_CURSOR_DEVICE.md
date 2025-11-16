# Fix Device Tidak Muncul di Cursor Device Selector

## ✅ Status: HP Sudah Terdeteksi!

Dari terminal, HP Anda sudah terdeteksi:
- ✅ ADB: `RR8N608YY2F    device`
- ✅ Flutter: `SM A217F (mobile) • RR8N608YY2F`

**Masalah:** Device tidak muncul di Cursor device selector

## 🔧 Solusi: Refresh Flutter Extension di Cursor

### Solusi 1: Reload Window (Recommended)

1. **Command Palette:**
   - Tekan **Cmd+Shift+P** (Mac) atau **Ctrl+Shift+P** (Windows)
   - Ketik: **"Developer: Reload Window"**
   - Enter

2. **Atau restart Cursor:**
   - Tutup Cursor sepenuhnya
   - Buka lagi
   - Device list akan refresh

### Solusi 2: Select Device Manual

1. **Command Palette:**
   - Tekan **Cmd+Shift+P** (Mac) atau **Ctrl+Shift+P** (Windows)
   - Ketik: **"Flutter: Select Device"**
   - Enter
   - Pilih device: **SM A217F** atau **RR8N608YY2F**

2. **Atau dari Status Bar:**
   - Lihat status bar di bawah Cursor
   - Klik device name (atau "No device")
   - Pilih device dari list

### Solusi 3: Run Langsung dengan Device ID

1. **Command Palette:**
   - Cmd+Shift+P → **"Flutter: Run"**
   - Ketik device ID: **RR8N608YY2F**
   - Enter

2. **Atau edit launch.json:**
   ```json
   {
     "name": "simonev21_mobile (Debug)",
     "request": "launch",
     "type": "dart",
     "program": "lib/main.dart",
     "deviceId": "RR8N608YY2F"
   }
   ```

### Solusi 4: Restart Flutter Daemon

Di terminal Cursor:

```bash
# Kill Flutter daemon
pkill -f flutter

# Restart Flutter extension di Cursor:
# Cmd+Shift+P → "Flutter: Restart Daemon"
```

## 🎯 Step-by-Step Fix (Langkah demi Langkah)

### Step 1: Reload Window Cursor

1. Tekan **Cmd+Shift+P** (Mac) atau **Ctrl+Shift+P** (Windows)
2. Ketik: **"Developer: Reload Window"**
3. Enter
4. Tunggu Cursor reload

### Step 2: Select Device

Setelah reload:

1. **Cara 1: Command Palette**
   - Cmd+Shift+P → **"Flutter: Select Device"**
   - Pilih: **SM A217F** atau **RR8N608YY2F**

2. **Cara 2: Status Bar**
   - Lihat status bar bawah Cursor
   - Klik device name
   - Pilih device

3. **Cara 3: Run & Debug Panel**
   - Sidebar kiri → **Run and Debug**
   - Dropdown "Device" di atas Run button
   - Pilih device

### Step 3: Run App

1. Tekan **F5** atau klik **Run** button
2. Atau Cmd+Shift+P → **"Flutter: Run"**
3. Pilih device jika diminta

## 🔍 Verify Device Selection

Setelah select device, cek:

1. **Status Bar:**
   - Bawah Cursor harus muncul: **"SM A217F"** atau device name

2. **Run & Debug Panel:**
   - Dropdown "Device" harus menampilkan HP Anda

3. **Terminal:**
   - Ketik: `flutter devices`
   - Harus muncul HP Anda di list

## 💡 Troubleshooting

### Jika Reload Window Tidak Membantu:

#### 1. Restart Flutter Extension

```bash
# Di terminal Cursor
pkill -f flutter

# Di Cursor, Command Palette:
# Cmd+Shift+P → "Flutter: Restart Daemon"
```

#### 2. Reinstall Flutter Extension

1. Cmd+Shift+X (Extensions)
2. Search "Flutter"
3. Uninstall extension
4. Install lagi
5. Reload Window

#### 3. Manual Select Device via Terminal

Jika device selector di Cursor tidak muncul, run dari terminal:

```bash
cd mobile
flutter run -d RR8N608YY2F
```

#### 4. Edit launch.json

Tambahkan `deviceId` di launch.json:

```json
{
  "version": "0.2.0",
  "configurations": [
    {
      "name": "simonev21_mobile (Debug)",
      "request": "launch",
      "type": "dart",
      "program": "lib/main.dart",
      "deviceId": "RR8N608YY2F"
    }
  ]
}
```

## 🚀 Quick Fix (Paling Cepat)

```bash
# 1. Di Cursor, Command Palette:
Cmd+Shift+P → "Developer: Reload Window"

# 2. Setelah reload:
Cmd+Shift+P → "Flutter: Select Device"
# Pilih: SM A217F atau RR8N608YY2F

# 3. Run app:
F5 atau klik Run button
```

## 📋 Checklist

- [ ] HP terdeteksi di `adb devices` ✅ (sudah OK)
- [ ] HP terdeteksi di `flutter devices` ✅ (sudah OK)
- [ ] Reload Window Cursor
- [ ] Select device manual via Command Palette
- [ ] Device muncul di status bar
- [ ] Run app dengan F5

---

**Device HP Anda sudah terdeteksi dengan baik! Tinggal refresh Cursor dan select device. 🎉**

