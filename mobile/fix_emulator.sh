#!/bin/bash

# Script untuk setup dan jalankan Android Emulator untuk Flutter

echo "🔍 Checking Flutter and Android SDK setup..."
echo ""

# 1. Check Flutter
if ! command -v flutter &> /dev/null; then
    echo "❌ Flutter tidak ditemukan di PATH"
    echo "   Pastikan Flutter sudah diinstall dan ditambahkan ke PATH"
    echo "   Contoh: export PATH=\$PATH:\$HOME/flutter/bin"
    exit 1
else
    echo "✅ Flutter ditemukan: $(which flutter)"
fi

# 2. Check Android SDK
if [ -z "$ANDROID_HOME" ]; then
    echo "⚠️  ANDROID_HOME tidak diset"
    export ANDROID_HOME=$HOME/Library/Android/sdk
    echo "   Menggunakan default: $ANDROID_HOME"
else
    echo "✅ ANDROID_HOME: $ANDROID_HOME"
fi

# 3. Add to PATH
export PATH=$PATH:$ANDROID_HOME/emulator
export PATH=$PATH:$ANDROID_HOME/platform-tools
export PATH=$PATH:$ANDROID_HOME/tools
export PATH=$PATH:$ANDROID_HOME/tools/bin

echo ""
echo "📱 Checking available AVDs..."
emulator -list-avds 2>/dev/null || echo "   Tidak ada AVD yang ditemukan"

echo ""
echo "🔧 Available commands:"
echo ""
echo "1. List AVD:"
echo "   emulator -list-avds"
echo ""
echo "2. Create AVD (Apple Silicon Mac):"
echo "   avdmanager create avd -n Pixel_5_API_34 -k \"system-images;android-34;google_apis;arm64-v8a\" -d \"pixel_5\""
echo ""
echo "3. Create AVD (Intel Mac):"
echo "   avdmanager create avd -n Pixel_5_API_34 -k \"system-images;android-34;google_apis;x86_64\" -d \"pixel_5\""
echo ""
echo "4. Start emulator:"
echo "   emulator -avd <AVD_NAME> &"
echo ""
echo "5. Check connected devices:"
echo "   flutter devices"
echo ""
echo "6. Run Flutter app:"
echo "   cd mobile && flutter run"
echo ""

