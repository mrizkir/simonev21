# Fix Device Detection - HP Tidak Muncul di Cursor

## 🔍 Masalah: HP Tidak Muncul di Device List

Saat menjalankan Flutter dari Cursor, HP Android tidak muncul di list device.

## ✅ Langkah-langkah Troubleshooting

### 1. Cek HP Terdeteksi di ADB

```bash
# Cek apakah HP terdeteksi di ADB
adb devices

# Output yang diharapkan:
# List of devices attached
# ABC123XYZ    device    ← Status harus "device", bukan "unauthorized" atau "offline"
```

**Jika kosong atau "unauthorized":**

#### A. Restart ADB Server
```bash
adb kill-server
adb start-server
adb devices
```

#### B. Di HP Android:
1. **Revoke USB debugging authorizations:**
   - Settings → Developer options → **Revoke USB debugging authorizations**
   - Disconnect dan reconnect HP
   - Popup "Allow USB debugging?" akan muncul lagi

2. **Allow USB debugging:**
   - Saat popup muncul, centang **"Always allow from this computer"**
   - Tap **"Allow"** atau **"OK"**

3. **Cek USB Debugging aktif:**
   - Settings → Developer options → **USB debugging** ✅ (harus ON)

#### C. Cek Mode USB Connection
- Di HP, tarik notification bar saat terhubung USB
- Pilih **"File Transfer"** atau **"MTP"**
- Bukan **"Charge only"** atau **"PTP"**

### 2. Cek HP Terdeteksi di Flutter

```bash
# Cek dengan Flutter
cd mobile
flutter devices

# Output yang diharapkan:
# Found 1 connected device:
#   <device_name> • <device_id> • android-arm64 • Android <version>
```

**Jika Flutter tidak detect tapi ADB detect:**

#### A. Restart Flutter Daemon
```bash
# Stop semua Flutter process
pkill -f flutter

# Cek lagi
flutter devices
```

#### B. Restart Cursor/IDE
- Tutup Cursor sepenuhnya
- Buka lagi
- Cek device list

### 3. Cek PATH ADB

Flutter perlu akses ke ADB. Pastikan ADB ada di PATH:

```bash
# Cek ADB
which adb

# Output: /Users/username/Library/Android/sdk/platform-tools/adb
# Jika "command not found", perlu setup PATH
```

**Setup PATH jika belum:**

```bash
# Tambahkan ke ~/.zshrc
echo 'export ANDROID_HOME=$HOME/Library/Android/sdk' >> ~/.zshrc
echo 'export PATH=$PATH:$ANDROID_HOME/platform-tools' >> ~/.zshrc
echo 'export PATH=$PATH:$ANDROID_HOME/tools' >> ~/.zshrc
echo 'export PATH=$PATH:$ANDROID_HOME/tools/bin' >> ~/.zshrc

# Reload
source ~/.zshrc

# Verify
which adb
adb devices
```

### 4. Test Kabel USB

**Ganti kabel USB:**
- Pastikan kabel support **data transfer**, bukan charge-only
- Coba port USB lain di komputer
- Beberapa HP perlu kabel USB OTG tertentu

### 5. Cek di Cursor Device Selector

**Cara 1: Dari Status Bar**
1. Lihat status bar di bawah Cursor
2. Klik device name (atau "No device" jika tidak ada)
3. Pilih device dari list

**Cara 2: Dari Command Palette**
1. Cmd+Shift+P → **"Flutter: Select Device"**
2. Pilih device dari list

**Cara 3: Dari Run Configuration**
1. Klik **Run and Debug** di sidebar
2. Dropdown "Device" di atas Run button
3. Pilih device

### 6. Troubleshooting Specific

#### HP Terdeteksi di ADB tapi Tidak di Flutter

```bash
# 1. Restart Flutter daemon
pkill -f flutter
flutter doctor
flutter devices

# 2. Restart Cursor
# Tutup dan buka lagi

# 3. Check Flutter doctor
flutter doctor -v
# Pastikan Android toolchain OK
```

#### HP Status "unauthorized"

**Solusi:**
1. Di HP: Settings → Developer options → **Revoke USB debugging authorizations**
2. Disconnect HP
3. Reconnect HP
4. Allow USB debugging di popup HP
5. Centang "Always allow from this computer"

#### HP Status "offline"

**Solusi:**
```bash
# Restart ADB
adb kill-server
adb start-server
adb devices

# Di HP, disable lalu enable USB debugging lagi
# Settings → Developer options → USB debugging (OFF lalu ON)
```

#### HP Tidak Terdeteksi Sama Sekali

**Checklist:**
- [ ] Developer options enabled?
- [ ] USB debugging enabled?
- [ ] HP terhubung via USB?
- [ ] Kabel USB support data transfer?
- [ ] Mode USB = File Transfer (bukan Charge only)?
- [ ] HP tidak dalam lock screen?
- [ ] ADB ada di PATH?
- [ ] Coba kabel USB lain?
- [ ] Coba port USB lain?

## 🔧 Quick Fix Script

Saya sudah membuat script helper:

```bash
cd mobile
./quick_setup_device.sh
```

Script ini akan:
- ✅ Cek ADB setup
- ✅ Cek devices yang terhubung
- ✅ Berikan checklist troubleshooting

## 📱 Step-by-Step Fix

### Step 1: Enable Developer Options & USB Debugging

**Di HP Android:**
1. Settings → About phone
2. Tap **Build number** 7x
3. Kembali → Developer options
4. Enable **USB debugging** ✅
5. Enable **Stay awake** (opsional) ✅

### Step 2: Connect HP ke Komputer

1. Sambungkan HP ke komputer via USB
2. Di HP, akan muncul popup: **"Allow USB debugging?"**
3. Centang **"Always allow from this computer"** ✅
4. Tap **"Allow"** atau **"OK"**

### Step 3: Set USB Mode

**Di HP:**
- Tarik notification bar
- Tap **"USB"** atau **"USB charging this device"**
- Pilih **"File Transfer"** atau **"MTP"**
- Bukan "Charge only"!

### Step 4: Verify di Terminal

```bash
# Cek ADB
adb devices

# Harus muncul:
# List of devices attached
# ABC123XYZ    device    ← Status "device"

# Cek Flutter
flutter devices

# Harus muncul:
# Found 1 connected device:
#   <device_name> • <device_id> • android-arm64
```

### Step 5: Verify di Cursor

1. **Cek Status Bar:**
   - Lihat status bar bawah Cursor
   - Harus muncul device name (bukan "No device")

2. **Cek Device Selector:**
   - Cmd+Shift+P → **"Flutter: Select Device"**
   - Device HP harus muncul di list

3. **Cek Run & Debug:**
   - Sidebar → Run and Debug
   - Dropdown "Device" harus menampilkan HP Anda

## 🎯 Quick Checklist

- [ ] Developer options enabled di HP
- [ ] USB debugging enabled di HP
- [ ] HP terhubung via USB ke komputer
- [ ] Popup "Allow USB debugging" sudah di-allow
- [ ] Mode USB = File Transfer (bukan Charge only)
- [ ] `adb devices` menampilkan HP dengan status "device"
- [ ] `flutter devices` menampilkan HP
- [ ] Restart Cursor (tutup dan buka lagi)
- [ ] HP tidak dalam lock screen

## 💡 Tips

1. **Selalu unlock HP** saat development
2. **Gunakan kabel USB berkualitas** (support data transfer)
3. **Keep screen on** (enable "Stay awake" di Developer options)
4. **Restart Cursor** setelah connect HP (terkadang perlu refresh)
5. **Cek status bar** di Cursor untuk melihat device yang terpilih

## 🚀 After Fix

Setelah HP terdeteksi:

1. **Run dari Cursor:**
   - Tekan F5 atau klik Run button
   - Pilih device (HP Anda)
   - App akan jalan di HP!

2. **Hot Reload:**
   - Edit code → Save (Cmd+S) = auto hot reload
   - Perubahan langsung muncul di HP

---

**Jika masih tidak muncul, jalankan script helper dan share output untuk dibantu troubleshoot lebih lanjut! 🐛**

