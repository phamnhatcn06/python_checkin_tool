@echo off
echo Building CheckInApp...
pyinstaller --noconfirm --onedir --windowed --add-data "assets;assets" --name "CheckInApp" main.py
echo Build complete. Executable is in dist\CheckInApp\CheckInApp.exe
pause
