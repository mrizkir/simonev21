#!/bin/bash

echo "🔧 Fixing Flutter Doctor Issues..."
echo ""

# Check Flutter
if ! command -v flutter &> /dev/null; then
    echo "❌ Flutter tidak ditemukan"
    exit 1
fi

echo "📋 Current Flutter version:"
flutter --version
echo ""

echo "📋 Current Flutter channel:"
flutter channel
echo ""

echo "🔧 Switching to stable channel..."
flutter channel stable
flutter upgrade

echo ""
echo "📋 Updated Flutter version:"
flutter --version
echo ""

echo "✅ Please run 'flutter doctor' to verify"
echo ""
echo "If still error, try:"
echo "  flutter doctor --android-licenses"
echo "  sdkmanager 'platforms;android-34'"
echo "  sdkmanager 'build-tools;34.0.0'"
