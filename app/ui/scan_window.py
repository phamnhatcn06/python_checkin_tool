from PySide6.QtWidgets import (QMainWindow, QWidget, QVBoxLayout, QHBoxLayout,
                               QLineEdit, QLabel, QPushButton, QDialog, QSpinBox,
                               QFormLayout, QDialogButtonBox, QMessageBox)
from PySide6.QtCore import Qt, Signal
from PySide6.QtGui import QFont
import json
import os

def get_config_path():
    base_dir = os.path.dirname(os.path.dirname(os.path.dirname(os.path.abspath(__file__))))
    return os.path.join(base_dir, "config.json")

class SettingsDialog(QDialog):
    def __init__(self, parent=None):
        super().__init__(parent)
        self.setWindowTitle("Settings")
        self.resize(300, 150)
        
        self.layout = QFormLayout(self)
        
        self.spin_timer = QSpinBox()
        self.spin_timer.setRange(1, 3600)
        self.spin_timer.setSuffix(" giây")
        
        self.layout.addRow("Thời gian chờ:", self.spin_timer)
        
        self.btn_box = QDialogButtonBox(QDialogButtonBox.Save | QDialogButtonBox.Cancel)
        self.btn_box.accepted.connect(self.save_settings)
        self.btn_box.rejected.connect(self.reject)
        
        self.layout.addRow(self.btn_box)
        
        self.load_settings()

    def load_settings(self):
        config_path = get_config_path()
        try:
            if os.path.exists(config_path):
                with open(config_path, 'r', encoding='utf-8') as f:
                    config = json.load(f)
                    val = config.get("auto_reset_seconds", 10)
                    self.spin_timer.setValue(int(val))
            else:
                self.spin_timer.setValue(10)
        except Exception as e:
            print(f"Error loading settings: {e}")
            self.spin_timer.setValue(10)

    def save_settings(self):
        config_path = get_config_path()
        try:
            config = {}
            if os.path.exists(config_path):
                with open(config_path, 'r', encoding='utf-8') as f:
                    config = json.load(f)
            
            config["auto_reset_seconds"] = self.spin_timer.value()
            
            with open(config_path, 'w', encoding='utf-8') as f:
                json.dump(config, f, indent=4)
            
            self.accept()
        except Exception as e:
            QMessageBox.critical(self, "Error", f"Failed to save settings: {e}")

class ScanWindow(QMainWindow):
    # Signal emitted when a code is scanned (Enter pressed)
    code_scanned = Signal(str)
    # Signal emitted when settings are changed
    settings_changed = Signal(dict)

    def __init__(self):
        super().__init__()
        self.setWindowTitle("QR Scanner - Operator")
        self.resize(400, 300)
        self.setStyleSheet("""
            QMainWindow { background-color: #2c3e50; }
            QLabel { color: #ecf0f1; }
            QLineEdit { 
                padding: 10px; 
                border-radius: 5px; 
                border: 2px solid #34495e;
                background-color: #34495e;
                color: white;
                font-size: 14px;
            }
            QPushButton {
                background-color: #d4af37;
                color: #2c3e50;
                border: none;
                padding: 8px;
                border-radius: 4px;
                font-weight: bold;
            }
            QPushButton:hover { background-color: #f1c40f; }
        """)

        # Central Widget
        central_widget = QWidget()
        self.setCentralWidget(central_widget)
        layout = QVBoxLayout(central_widget)

        # Title
        self.lbl_title = QLabel("Quét mã QR")
        self.lbl_title.setAlignment(Qt.AlignCenter)
        self.lbl_title.setFont(QFont("Segoe UI", 16, QFont.Bold))
        layout.addWidget(self.lbl_title)

        # Input Field
        self.txt_input = QLineEdit()
        self.txt_input.setPlaceholderText("Click here and scan...")
        self.txt_input.setAlignment(Qt.AlignCenter)
        self.txt_input.returnPressed.connect(self.on_submit)
        layout.addWidget(self.txt_input)

        # Status Label
        self.lbl_status = QLabel("Ready")
        self.lbl_status.setAlignment(Qt.AlignCenter)
        self.lbl_status.setStyleSheet("color: #bdc3c7; font-size: 14px;")
        layout.addWidget(self.lbl_status)

        # Focus button
        self.btn_focus = QPushButton("Reset Focus")
        self.btn_focus.clicked.connect(self.txt_input.setFocus)
        layout.addWidget(self.btn_focus)
        
        # Settings Button
        self.btn_settings = QPushButton("⚙️ Cài đặt")
        self.btn_settings.setStyleSheet("background-color: #7f8c8d; color: white;")
        self.btn_settings.clicked.connect(self.show_settings)
        layout.addWidget(self.btn_settings)

    def show_settings(self):
        dialog = SettingsDialog(self)
        if dialog.exec():
            # If saved successfully, read new config and emit
            try:
                config_path = get_config_path()
                with open(config_path, 'r', encoding='utf-8') as f:
                    config = json.load(f)
                    self.settings_changed.emit(config)
            except Exception as e:
                print(f"Failed to emit new settings: {e}")

    def on_submit(self):
        code = self.txt_input.text()
        if code:
            self.txt_input.clear()
            self.lbl_status.setText("Processing...")
            self.lbl_status.setStyleSheet("color: #f1c40f; font-weight: bold; font-size: 14px;")
            self.code_scanned.emit(code)
    
    def show_success(self, attendee_name):
        self.lbl_status.setText(f"Success: {attendee_name}")
        self.lbl_status.setStyleSheet("color: #2ecc71; font-weight: bold; font-size: 14px;")

    def show_error(self, message):
        self.lbl_status.setText(f"Error: {message}")
        self.lbl_status.setStyleSheet("color: #e74c3c; font-weight: bold; font-size: 14px;")

    def keyPressEvent(self, event):
        if event.key() == Qt.Key_Escape:
            event.ignore() # Prevent Esc from closing the Scan Window
        else:
            super().keyPressEvent(event)
