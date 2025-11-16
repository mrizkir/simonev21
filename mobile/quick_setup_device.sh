#!/bin/bash

echo "📱 Quick Setup untuk Run Flutter di HP Android"
echo ""

# Check ADB
if ! command -v adb &> /dev/null; then
    echo "⚠️  ADB tidak ditemukan"
    echo "   Setting PATH..."
    export PATH=$PATH:$HOME/Library/Android/sdk/platform-tools
    
    if ! command -v adb &> /dev/null; then
        echo "❌ ADB masih tidak ditemukan"
        echo "   Pastikan Android SDK sudah terinstall"
        echo "   Atau tambahkan ke ~/.zshrc:"
        echo "   export PATH=\$PATH:\$HOME/Library/Android/sdk/platform-tools"
        exit 1
    fi
fi

echo "✅ ADB ditemukan: $(which adb)"
echo ""

# Check devices
echo "🔍 Checking connected devices..."
echo ""
adb devices

echo ""
echo "📋 Checklist:"
echo "  [ ] HP terhubung via USB"
echo "  [ ] USB Debugging aktif di HP"
echo "  [ ] Allow USB debugging di popup HP"
echo "  [ ] Device muncul di list di atas (status: device)"
echo ""

# Check Flutter devices
echo "🔍 Checking Flutter devices..."
echo ""
cd "$(dirname "$0")" || exit
flutter devices

echo ""
echo "✅ Jika device muncul, jalankan:"
echo "   flutter run"
echo ""
