import sys
import os
from PySide6.QtWidgets import QApplication
from app.api_client import ApiClient
from app.controller import Controller
from app.ui.scan_window import ScanWindow
from app.ui.welcome_window import WelcomeWindow

def main():
    # Prevent Windows from turning off the display or going to sleep
    if os.name == 'nt':
        try:
            import ctypes
            # ES_CONTINUOUS = 0x80000000
            # ES_DISPLAY_REQUIRED = 0x00000002
            # ES_SYSTEM_REQUIRED = 0x00000001
            ctypes.windll.kernel32.SetThreadExecutionState(
                0x80000000 | 0x00000002 | 0x00000001
            )
        except Exception as e:
            print(f"Could not set thread execution state: {e}")

    app = QApplication(sys.argv)
    
    # Global Event Filter to block Escape key on all windows/widgets
    from PySide6.QtCore import QObject, QEvent, Qt
    class EscEventFilter(QObject):
        def eventFilter(self, obj, event):
            if event.type() == QEvent.KeyPress and event.key() == Qt.Key_Escape:
                return True # Event handled, stop propagating
            return super().eventFilter(obj, event)
            
    app_filter = EscEventFilter()
    app.installEventFilter(app_filter)

    # 1. Initialize Components
    api_client = ApiClient()
    controller = Controller(api_client)

    scan_window = ScanWindow()
    welcome_window = WelcomeWindow()

    # Connect Signals
    # Scan successful -> Update Welcome Screen
    controller.attendee_scanned.connect(welcome_window.update_attendee)
    controller.attendee_scanned.connect(lambda: print("Scanned!")) # Debug
    
    # Scan Window Input -> Controller
    scan_window.code_scanned.connect(controller.process_scan)

    # Controller -> Scan Window (Feedback)
    controller.attendee_scanned.connect(lambda data: scan_window.show_success(data.get("name")))
    controller.scan_error.connect(scan_window.show_error)
    
    # Settings change -> Welcome Window
    scan_window.settings_changed.connect(welcome_window.update_settings)

    # --- Multi-Monitor Setup ---
    screens = app.screens()
    print(f"Detected {len(screens)} screens.")
    
    # 1. Scan Window on Primary Screen (Laptop)
    scan_window.show()
    
    # 2. Welcome Window on Secondary Screen (Standee) if available
    if len(screens) > 1:
        print("Secondary screen detected. Moving Welcome Window...")
        second_screen = screens[1]
        
        # Move to the top-left of the second screen
        welcome_window.move(second_screen.geometry().topLeft())
        
        # Explicitly set the window handle to that screen (fixes some OS glitches)
        welcome_window.setScreen(second_screen)
        
        # Fullscreen on that screen
        welcome_window.windowHandle().setScreen(second_screen)
        welcome_window.showFullScreen()
    else:
        print("No secondary screen. Showing on primary.")
        welcome_window.showFullScreen()

    print("Application started...")
    sys.exit(app.exec())

if __name__ == "__main__":
    main()
