#!/bin/bash

echo "📱 Checking Device Detection..."
echo ""

# Check ADB
if ! command -v adb &> /dev/null; then
    echo "⚠️  ADB tidak ditemukan di PATH"
    export PATH=$PATH:$HOME/Library/Android/sdk/platform-tools
    
    if ! command -v adb &> /dev/null; then
        echo "❌ ADB masih tidak ditemukan"
        echo ""
        echo "🔧 Fix: Tambahkan ke ~/.zshrc:"
        echo "   export ANDROID_HOME=\$HOME/Library/Android/sdk"
        echo "   export PATH=\$PATH:\$ANDROID_HOME/platform-tools"
        exit 1
    fi
fi

echo "✅ ADB ditemukan: $(which adb)"
echo ""

# Check devices dengan ADB
echo "🔍 ADB Devices:"
adb devices
echo ""

# Check Flutter devices
echo "🔍 Flutter Devices:"
cd "$(dirname "$0")" || exit
flutter devices
echo ""

# Check device status
DEVICE_COUNT=$(adb devices | grep -v "List" | grep -v "^$" | wc -l | tr -d ' ')
UNAUTHORIZED=$(adb devices | grep "unauthorized" | wc -l | tr -d ' ')
OFFLINE=$(adb devices | grep "offline" | wc -l | tr -d ' ')

if [ "$DEVICE_COUNT" -eq 0 ]; then
    echo "❌ Tidak ada device terdeteksi"
    echo ""
    echo "📋 Checklist:"
    echo "  [ ] HP terhubung via USB"
    echo "  [ ] USB Debugging aktif di HP"
    echo "  [ ] Allow USB debugging di popup HP"
    echo "  [ ] Mode USB = File Transfer (bukan Charge only)"
    echo "  [ ] Kabel USB support data transfer"
elif [ "$UNAUTHORIZED" -gt 0 ]; then
    echo "⚠️  Device detected tapi 'unauthorized'"
    echo ""
    echo "🔧 Fix:"
    echo "  1. Di HP: Settings → Developer options → Revoke USB debugging authorizations"
    echo "  2. Disconnect dan reconnect HP"
    echo "  3. Allow USB debugging di popup HP"
    echo "  4. Centang 'Always allow from this computer'"
elif [ "$OFFLINE" -gt 0 ]; then
    echo "⚠️  Device detected tapi 'offline'"
    echo ""
    echo "🔧 Fix:"
    echo "  1. Restart ADB: adb kill-server && adb start-server"
    echo "  2. Di HP: Disable lalu enable USB debugging lagi"
    echo "  3. Unlock HP (tidak dalam lock screen)"
else
    echo "✅ Device terdeteksi dengan baik!"
    echo ""
    echo "💡 Tips:"
    echo "  - Restart Cursor jika device tidak muncul di device selector"
    echo "  - Cmd+Shift+P → 'Flutter: Select Device' untuk pilih device"
fi

