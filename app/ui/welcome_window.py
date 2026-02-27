from PySide6.QtWidgets import (QMainWindow, QWidget, QVBoxLayout, QHBoxLayout,
                               QLabel, QGraphicsDropShadowEffect, QFrame,
                               QSizePolicy, QSpacerItem, QStackedWidget)
from PySide6.QtCore import Qt, QSize, Property, QPropertyAnimation, QEasingCurve
from PySide6.QtGui import QFont, QPixmap, QImage, QPainter, QPainterPath, QColor, QLinearGradient, QPen
import requests
import os


import random
from PySide6.QtCore import QTimer, QPointF

class StarParticle:
    def __init__(self, center_x, center_y):
        self.x = center_x
        self.y = center_y
        angle = random.uniform(0, 2 * 3.14159)
        speed = random.uniform(2, 10)
        self.vx = speed * 1.5 * random.uniform(0.8, 1.2) *  (1 if random.random() < 0.5 else -1)
        import math
        self.vx = math.cos(angle) * speed
        self.vy = math.sin(angle) * speed
        self.life = 1.0  # 1.0 to 0.0
        self.decay = random.uniform(0.01, 0.03)
        self.size = random.randint(5, 15)
        self.color = random.choice(["#FFD700", "#FFFFFF", "#F0E68C", "#E6E6FA"])

class StarEffectOverlay(QWidget):
    def __init__(self, parent=None):
        super().__init__(parent)
        self.setAttribute(Qt.WA_TransparentForMouseEvents)
        self.setAttribute(Qt.WA_NoSystemBackground)
        self.particles = []
        self.timer = QTimer(self)
        self.timer.timeout.connect(self.update_particles)

    def explode(self, x=None, y=None):
        self.particles = []
        cx = x if x is not None else self.width() / 2
        cy = y if y is not None else self.height() / 2
        
        # Create 100 stars
        for _ in range(100):
            self.particles.append(StarParticle(cx, cy))
        
        self.timer.start(16) # ~60 FPS

    def update_particles(self):
        if not self.particles:
            self.timer.stop()
            self.update()
            return

        for p in self.particles:
            p.x += p.vx
            p.y += p.vy
            p.life -= p.decay
        
        # Remove dead particles
        self.particles = [p for p in self.particles if p.life > 0]
        self.update()

    def paintEvent(self, event):
        if not self.particles:
            return

        painter = QPainter(self)
        painter.setRenderHint(QPainter.Antialiasing)

        for p in self.particles:
            size = p.size * p.life
            painter.setBrush(QColor(p.color))
            painter.setPen(Qt.NoPen)
            painter.setOpacity(p.life)
            
            # Draw star graphics or just circles
            # Simple Circle for performance and aesthetic
            painter.drawEllipse(QPointF(p.x, p.y), size, size)

class OrnamentalSeparator(QWidget):
    def __init__(self, parent=None):
        super().__init__(parent)
        self.setFixedHeight(40)
        self.setAttribute(Qt.WA_TransparentForMouseEvents)

    def paintEvent(self, event):
        from PySide6.QtCore import QRectF, QPointF
        
        painter = QPainter(self)
        painter.setRenderHint(QPainter.Antialiasing)
        
        w = self.width()
        h = self.height()
        cy = h / 2.0
        cx = w / 2.0
        
        # Gold color: #d4af66
        base_color = QColor(212, 175, 102, 255)
        transparent = QColor(212, 175, 102, 0)
        
        # Draw Left Line (Fade In)
        left_grad = QLinearGradient(0, cy, cx - 25, cy)
        left_grad.setColorAt(0, transparent)
        left_grad.setColorAt(1, base_color)
        
        pen_left = QPen(left_grad, 1.5)
        painter.setPen(pen_left)
        painter.drawLine(QPointF(0, cy), QPointF(cx - 25, cy))
        
        # Draw Right Line (Fade Out)
        right_grad = QLinearGradient(cx + 25, cy, w, cy)
        right_grad.setColorAt(0, base_color)
        right_grad.setColorAt(1, transparent)
        
        pen_right = QPen(right_grad, 1.5)
        painter.setPen(pen_right)
        painter.drawLine(QPointF(cx + 25, cy), QPointF(w, cy))
        
        # Draw Diamond (Hollow)
        painter.translate(cx, cy)
        painter.rotate(45)
        
        diamond_size = 12.0
        rect = QRectF(-diamond_size/2, -diamond_size/2, diamond_size, diamond_size)
        
        pen_diamond = QPen(base_color, 2.0) # Thicker border for visibility
        painter.setPen(pen_diamond)
        painter.setBrush(Qt.NoBrush)
        painter.drawRect(rect)
        
        painter.resetTransform()

class FooterLine(QWidget):
    def __init__(self, parent=None):
        super().__init__(parent)
        self.setFixedHeight(10)
        self.setAttribute(Qt.WA_TransparentForMouseEvents)

    def paintEvent(self, event):
        from PySide6.QtCore import QPointF
        
        painter = QPainter(self)
        painter.setRenderHint(QPainter.Antialiasing)
        
        w = self.width()
        h = self.height()
        cy = h / 2.0
        
        # Gold/Brown color fading out
        base_color = QColor(180, 140, 80, 200) 
        transparent = QColor(180, 140, 80, 0)
        
        # Draw Line (Fade In -> Solid -> Fade Out)
        grad = QLinearGradient(0, cy, w, cy)
        grad.setColorAt(0, transparent)
        grad.setColorAt(0.1, base_color)
        grad.setColorAt(0.9, base_color)
        grad.setColorAt(1, transparent)
        
        pen = QPen(grad, 1.0)
        painter.setPen(pen)
        painter.drawLine(QPointF(0, cy), QPointF(w, cy))
        
        # Draw tiny dots near edges (e.g. at 5% and 95%)
        # Just drawing on right visually, but symmetry is safer
        painter.setPen(Qt.NoPen)
        painter.setBrush(base_color)
        painter.drawEllipse(QPointF(20, cy), 1.5, 1.5)
        painter.drawEllipse(QPointF(w - 20, cy), 1.5, 1.5)

class OrnamentalDividerSmall(QWidget):
    def __init__(self, parent=None):
        super().__init__(parent)
        self.setFixedSize(200, 10)
        self.setAttribute(Qt.WA_TransparentForMouseEvents)

    def paintEvent(self, event):
        from PySide6.QtCore import QPointF
        painter = QPainter(self)
        painter.setRenderHint(QPainter.Antialiasing)
        
        w = self.width()
        h = self.height()
        cy = h / 2.0
        cx = w / 2.0
        
        color = QColor("#f4c025")
        transparent = QColor(244, 192, 37, 0)
        
        # Left line
        grad_l = QLinearGradient(0, cy, cx - 10, cy)
        grad_l.setColorAt(0, transparent)
        grad_l.setColorAt(1, color)
        painter.setPen(QPen(grad_l, 1.5))
        painter.drawLine(QPointF(0, cy), QPointF(cx - 10, cy))
        
        # Right line
        grad_r = QLinearGradient(cx + 10, cy, w, cy)
        grad_r.setColorAt(0, color)
        grad_r.setColorAt(1, transparent)
        painter.setPen(QPen(grad_r, 1.5))
        painter.drawLine(QPointF(cx + 10, cy), QPointF(w, cy))
        
        # Center dot
        painter.setPen(Qt.NoPen)
        painter.setBrush(color)
        painter.drawEllipse(QPointF(cx, cy), 3, 3)

class GradientLabel(QLabel):
    def __init__(self, text, colors, parent=None):
        super().__init__(text, parent)
        self.colors = [QColor(c) for c in colors]
        self.setStyleSheet("background: transparent;")

    def sizeHint(self):
        fm = self.fontMetrics()
        width = fm.horizontalAdvance(self.text()) + 40
        height = int(fm.height() * 1.5)  # 50% extra padding to prevent accent clipping
        return QSize(width, height)

    def paintEvent(self, event):
        painter = QPainter(self)
        painter.setRenderHint(QPainter.Antialiasing)
        
        rect = self.rect()
        
        # Setup Font
        painter.setFont(self.font())
        
        # Create Gradient (Horizontal)
        gradient = QLinearGradient(rect.topLeft(), rect.topRight())
        
        # Distribute colors
        if len(self.colors) > 1:
            for i, color in enumerate(self.colors):
                pos = i / (len(self.colors) - 1)
                gradient.setColorAt(pos, color)
        else:
             gradient.setColorAt(0, self.colors[0])
        
        painter.setPen(Qt.NoPen)
        
        # Draw text to a path
        path = QPainterPath()
        fm = self.fontMetrics()
        text_width = fm.horizontalAdvance(self.text())
        
        # Ensure centered vertically without clipping accents
        x = (rect.width() - text_width) / 2.0
        y = (rect.height() + fm.ascent() - fm.descent()) / 2.0
        
        path.addText(x, y, self.font(), self.text())
        
        # Fill path with gradient
        painter.setBrush(gradient)
        painter.setPen(Qt.NoPen)
        painter.drawPath(path)

class SideLine(QWidget):
    def __init__(self, fade_direction=1, parent=None):
        # 1 = left to right fade-in, -1 = right to left fade-out
        super().__init__(parent)
        self.fade_direction = fade_direction
        self.setFixedHeight(10)
        self.setAttribute(Qt.WA_TransparentForMouseEvents)

    def paintEvent(self, event):
        from PySide6.QtCore import QPointF
        painter = QPainter(self)
        painter.setRenderHint(QPainter.Antialiasing)
        w = self.width()
        cy = self.height() / 2.0
        
        gold = QColor(244, 192, 37, 200)
        transparent = QColor(244, 192, 37, 0)
        grad = QLinearGradient(0, cy, w, cy)
        
        if self.fade_direction == 1: # Transparent to solid
            grad.setColorAt(0, transparent)
            grad.setColorAt(1, gold)
        else: # Solid to transparent
            grad.setColorAt(0, gold)
            grad.setColorAt(1, transparent)
            
        painter.setPen(QPen(grad, 2))
        painter.drawLine(0, cy, w, cy)

class CoverBackgroundWidget(QWidget):
    def __init__(self, image_path, parent=None):
        super().__init__(parent)
        self.image = QPixmap()
        if os.path.exists(image_path):
            self.image.load(image_path)
            
    def paintEvent(self, event):
        painter = QPainter(self)
        painter.setRenderHint(QPainter.SmoothPixmapTransform)
        if not self.image.isNull():
            rect = self.rect()
            scaled_img = self.image.scaled(rect.size(), Qt.KeepAspectRatioByExpanding, Qt.SmoothTransformation)
            x = (rect.width() - scaled_img.width()) // 2
            y = (rect.height() - scaled_img.height()) // 2
            painter.drawPixmap(x, y, scaled_img)
            
        # Draw child widgets correctly
        super().paintEvent(event)

class WelcomeWindow(QMainWindow):
    def __init__(self):
        super().__init__()
        self.setWindowTitle("Welcome - Guest Display")
        
        # Frameless, Fullscreen, and No Focus flags
        self.setWindowFlags(Qt.FramelessWindowHint | Qt.WindowDoesNotAcceptFocus)
        self.setAttribute(Qt.WA_ShowWithoutActivating)
        self.setFocusPolicy(Qt.NoFocus)
        
        # 1080x1920 Resolution (Vertical Full HD)
        self.resize(1080, 1920) 
        
        # Determine Asset Path
        base_dir = os.path.dirname(os.path.abspath(__file__))
        root_dir = os.path.dirname(os.path.dirname(base_dir)) 
        assets_dir = os.path.join(root_dir, 'assets')
        
        bg_path = os.path.join(assets_dir, 'background.png').replace('\\', '/')
        bg_jpg_path = os.path.join(assets_dir, 'background.jpg').replace('\\', '/')
        if os.path.exists(bg_jpg_path):
            bg_path = bg_jpg_path

        # --- Central Widget & Background ---
        self.central_widget = CoverBackgroundWidget(bg_path)
        self.setCentralWidget(self.central_widget)
        self.central_widget.setObjectName("CentralWidget")

        # Central Widget Styling (Background for Check-in)
        self.central_widget.setStyleSheet(f"""
            #CentralWidget {{
                background-color: #0b1021;
            }}
        """)

        # Main Layout (Stack)
        self.main_layout = QVBoxLayout(self.central_widget)
        self.main_layout.setContentsMargins(0, 0, 0, 0)
        
        self.stack = QStackedWidget()
        self.main_layout.addWidget(self.stack)

        # --- PAGE 1: STANDBY (HTML PORT) ---
        self.page_standby = QWidget()
        self.page_standby.setObjectName("PageStandby")
        self.page_standby.setStyleSheet(f"""
            #PageStandby {{
                background: transparent;
            }}
            QLabel {{
                background: transparent;
            }}
        """) 
        self.layout_standby = QVBoxLayout(self.page_standby)
        self.layout_standby.setContentsMargins(40, 80, 40, 80)
        self.layout_standby.setAlignment(Qt.AlignTop | Qt.AlignHCenter)
        
        # 1. TOP HEADER (Logo Image)
        container_top = QWidget()
        container_top.setStyleSheet("background: transparent;")
        layout_top = QVBoxLayout(container_top)
        layout_top.setSpacing(5)
        
        logo_header_path = os.path.join(assets_dir, 'logo_header.png').replace('\\', '/')
        lbl_logo_header = QLabel()
        lbl_logo_header.setAlignment(Qt.AlignCenter)
        if os.path.exists(logo_header_path):
            pix_header = QPixmap(logo_header_path)
            # Scale to reasonable height, e.g., 150px
            lbl_logo_header.setPixmap(pix_header.scaledToHeight(150, Qt.SmoothTransformation))
        else:
            lbl_logo_header.setText("LOGO HEADER MISSING")
            
        layout_top.addWidget(lbl_logo_header)
        
        self.layout_standby.addWidget(container_top)
        
        self.layout_standby.addStretch()

        # 2. CENTER HERO (Image instead of text - Latest Revision)
        container_hero = QWidget()
        container_hero.setStyleSheet("background: transparent;")
        layout_hero = QVBoxLayout(container_hero)
        layout_hero.setSpacing(0)
        
        hero_image_path = os.path.join(assets_dir, 'logo_GMM.png').replace('\\', '/')
        lbl_hero = QLabel()
        lbl_hero.setAlignment(Qt.AlignCenter)
        
        if os.path.exists(hero_image_path):
            pix_hero = QPixmap(hero_image_path)
            # Scale to appropriate width. Adjusted to 450px down from 700px
            lbl_hero.setPixmap(pix_hero.scaledToWidth(450, Qt.SmoothTransformation))
        else:
             lbl_hero.setText("HERO IMAGE MISSING")
             lbl_hero.setStyleSheet("color: white; font-size: 40px;")
             
        layout_hero.addWidget(lbl_hero)
        
        # Add explicit spacing between Logo and Separator
        layout_hero.addSpacing(60)
        
        # Ornamental Separator (Fading Line + Diamond)
        lbl_sep = OrnamentalSeparator()
        lbl_sep.setFixedSize(450, 40)
        lbl_sep.setStyleSheet("background: transparent;") # Reset style
        layout_hero.addWidget(lbl_sep, 0, Qt.AlignHCenter)

        self.layout_standby.addWidget(container_hero)
        
        # Add stretch above to push it down
        self.layout_standby.addStretch()
        
        # 3. FOOTER
        lbl_slogan1 = QLabel("ĐỔI MỚI TƯ DUY")
        lbl_slogan1.setAlignment(Qt.AlignCenter)
        lbl_slogan1.setFont(QFont("Manrope", 24, QFont.Normal))
        lbl_slogan1.setStyleSheet("color: #ffffff; letter-spacing: 3px; text-transform: uppercase; background-color: transparent; border: none; outline: none;")
        self.layout_standby.addWidget(lbl_slogan1)

        lbl_slogan2 = QLabel("CHINH PHỤC THỬ THÁCH")
        lbl_slogan2.setAlignment(Qt.AlignCenter)
        lbl_slogan2.setFont(QFont("Manrope", 24, QFont.Light))
        lbl_slogan2.setStyleSheet("color: #d4af66; letter-spacing: 3px; text-transform: uppercase; background-color: transparent; border: none; outline: none; margin-bottom: 20px;") 
        self.layout_standby.addWidget(lbl_slogan2)
        
        # Space between text and bottom line
        self.layout_standby.addSpacing(60)
        
        # Bottom Fading Line
        line_bottom = FooterLine()
        line_bottom.setFixedSize(450, 10)
        self.layout_standby.addWidget(line_bottom, 0, Qt.AlignHCenter)
        
        # Exact space from line to bottom edge (very close to footer)
        self.layout_standby.addSpacing(60)
        
        self.stack.addWidget(self.page_standby)

        # --- PAGE 2: USER INFO (WELCOME) ---
        self.page_info = QWidget()
        self.page_info.setObjectName("PageInfo")
        self.page_info.setStyleSheet(f"""
            #PageInfo {{
                background-color: transparent;
            }}
        """)
        
        self.page_info_layout = QVBoxLayout(self.page_info)
        self.page_info_layout.setContentsMargins(0, 0, 0, 0)
        
        self.info_overlay = QWidget()
        self.info_overlay.setObjectName("InfoOverlay")
        self.info_overlay.setStyleSheet("""
            #InfoOverlay {
                background: transparent;
            }
            QLabel {
                background: transparent;
            }
        """)
        self.page_info_layout.addWidget(self.info_overlay)
        
        self.layout_info = QVBoxLayout(self.info_overlay)
        self.layout_info.setContentsMargins(40, 60, 40, 60)
        self.layout_info.setAlignment(Qt.AlignTop | Qt.AlignHCenter)
        
        # Header Section
        container_welcome_header = QWidget()
        layout_welcome_header = QVBoxLayout(container_welcome_header)
        layout_welcome_header.setAlignment(Qt.AlignCenter)
        layout_welcome_header.setSpacing(0)
        
        self.lbl_logo = QLabel()
        self.lbl_logo.setAlignment(Qt.AlignCenter)
        logo_path = os.path.join(assets_dir, 'logo_header.png').replace('\\', '/')
        if os.path.exists(logo_path):
            pix_logo = QPixmap(logo_path)
            self.lbl_logo.setPixmap(pix_logo.scaledToHeight(150, Qt.SmoothTransformation))
        else:
            self.lbl_logo.setText("LOGO MISSING")
            self.lbl_logo.setStyleSheet("color: white; font-size: 30px;")
            
        layout_welcome_header.addWidget(self.lbl_logo)
        
        layout_welcome_header.addSpacing(30)
        
        # CHÀO MỪNG
        welcome_colors = ["#fceebb", "#f4c025", "#b8860b"]
        self.lbl_welcome = GradientLabel("CHÀO MỪNG", welcome_colors)
        self.lbl_welcome.setAlignment(Qt.AlignCenter)
        self.lbl_welcome.setFont(QFont("Noto Serif", 50, QFont.Bold))
        layout_welcome_header.addWidget(self.lbl_welcome)
        
        layout_welcome_header.addSpacing(30)
        
        divider_small = OrnamentalDividerSmall()
        layout_welcome_header.addWidget(divider_small, 0, Qt.AlignHCenter)
        
        self.layout_info.addWidget(container_welcome_header)

        self.layout_info.addSpacing(60)

        # Photo
        self.photo_container = QLabel()
        self.photo_container.setFixedSize(400, 400)
        self.photo_container.setAlignment(Qt.AlignCenter)
        
        glow = QGraphicsDropShadowEffect()
        glow.setBlurRadius(80)
        glow.setColor(QColor(244, 192, 37, 100))
        glow.setOffset(0, 0)
        self.photo_container.setGraphicsEffect(glow)
        self.layout_info.addWidget(self.photo_container, 0, Qt.AlignHCenter)
        
        self.layout_info.addSpacing(60)

        # Name
        name_colors = ["#ffeebb", "#f4c025", "#d4af37"]
        self.lbl_name = GradientLabel("", name_colors)
        self.lbl_name.setAlignment(Qt.AlignCenter)
        self.lbl_name.setFont(QFont("Noto Serif", 60, QFont.Black))
        self.layout_info.addWidget(self.lbl_name)

        # Space between Name and Position
        self.layout_info.addSpacing(30)

        # Position (Chức danh)
        self.lbl_position = QLabel("")
        self.lbl_position.setAlignment(Qt.AlignCenter)
        self.lbl_position.setWordWrap(False) # Force single line
        self.lbl_position.setFont(QFont("Noto Serif", 30, QFont.Bold))
        self.lbl_position.setStyleSheet("color: #fceebb; letter-spacing: 2px; text-transform: uppercase;") 
        self.layout_info.addWidget(self.lbl_position)
        
        # Space between Position and Company
        self.layout_info.addSpacing(15)
        
        # Company (Tên công ty)
        self.lbl_company = QLabel("")
        self.lbl_company.setAlignment(Qt.AlignCenter)
        self.lbl_company.setWordWrap(False) # Force single line
        self.lbl_company.setFont(QFont("Noto Serif", 30, QFont.Bold))
        self.lbl_company.setStyleSheet("color: #fceebb; letter-spacing: 2px; text-transform: uppercase;") 
        self.layout_info.addWidget(self.lbl_company)
        
        self.layout_info.addStretch()

        # Footer section mirroring Standby Screen
        container_welcome_footer = QWidget()
        layout_welcome_footer = QVBoxLayout(container_welcome_footer)
        layout_welcome_footer.setAlignment(Qt.AlignCenter)
        layout_welcome_footer.setSpacing(0)
        
        lbl_info_slogan1 = QLabel("ĐỔI MỚI TƯ DUY")
        lbl_info_slogan1.setAlignment(Qt.AlignCenter)
        lbl_info_slogan1.setFont(QFont("Manrope", 24, QFont.Normal))
        lbl_info_slogan1.setStyleSheet("color: #ffffff; letter-spacing: 3px; text-transform: uppercase; background-color: transparent; border: none; outline: none;")
        layout_welcome_footer.addWidget(lbl_info_slogan1)

        lbl_info_slogan2 = QLabel("CHINH PHỤC THỬ THÁCH")
        lbl_info_slogan2.setAlignment(Qt.AlignCenter)
        lbl_info_slogan2.setFont(QFont("Manrope", 24, QFont.Light))
        lbl_info_slogan2.setStyleSheet("color: #d4af66; letter-spacing: 3px; text-transform: uppercase; background-color: transparent; border: none; outline: none; margin-bottom: 20px;") 
        layout_welcome_footer.addWidget(lbl_info_slogan2)
        
        layout_welcome_footer.addSpacing(60)
        
        line_bottom_info = FooterLine()
        line_bottom_info.setFixedSize(450, 10)
        layout_welcome_footer.addWidget(line_bottom_info, 0, Qt.AlignHCenter)
        
        self.layout_info.addWidget(container_welcome_footer)
        
        # Exact space from line to bottom edge
        self.layout_info.addSpacing(60)
        
        self.stack.addWidget(self.page_info)

        # --- 4. Effects Overlay ---
        # Note: Overlay sets parent to `self` (MainWindow), so it stays on top of stack
        self.star_effect = StarEffectOverlay(self)
        self.star_effect.resize(self.size())
        self.star_effect.raise_() 

        # Auto-reset Timer
        self.reset_timer = QTimer(self)
        self.reset_timer.setInterval(10000) # 10 seconds
        self.reset_timer.setSingleShot(True)
        self.reset_timer.timeout.connect(self.reset_view)
        
        # Load initial settings from config
        self.load_initial_settings()

        # --- Initial State ---
        self.showFullScreen()
        self.reset_view()

    def load_initial_settings(self):
        try:
            base_dir = os.path.dirname(os.path.dirname(os.path.dirname(os.path.abspath(__file__))))
            config_path = os.path.join(base_dir, "config.json")
            if os.path.exists(config_path):
                import json
                with open(config_path, 'r', encoding='utf-8') as f:
                    config = json.load(f)
                    seconds = config.get("auto_reset_seconds", 10)
                    self.reset_timer.setInterval(int(seconds) * 1000)
                    print(f"Loaded timer setting: {self.reset_timer.interval()}ms")
        except Exception as e:
            print(f"Error loading initial settings in WelcomeWindow: {e}")

    def update_settings(self, config_data):
        print("update_settings triggered in WelcomeWindow:", config_data)
        seconds = config_data.get("auto_reset_seconds", 10)
        self.reset_timer.setInterval(int(seconds) * 1000)
        print(f"Timer updated to {self.reset_timer.interval()}ms")

    def resizeEvent(self, event):
        self.star_effect.resize(self.size())
        super().resizeEvent(event)

    def keyPressEvent(self, event):
        if event.key() == Qt.Key_Escape:
            event.ignore() # Prevent default close behavior
        else:
            super().keyPressEvent(event)
    
    def mouseDoubleClickEvent(self, event):
        if self.isFullScreen():
            self.showNormal()
        else:
            self.showFullScreen()
        super().mouseDoubleClickEvent(event)

    def update_attendee(self, attendee_data):
        try:
            print("Updating attendee view...")
            
            # Force window to show and come to front if hidden/minimized
            if self.isHidden() or self.isMinimized():
                self.showFullScreen()
            self.raise_()
            self.activateWindow()
            
            # Switch to Info Page
            self.stack.setCurrentWidget(self.page_info)

            # Update Data
            # Note: HTML design has "Ông Nguyễn Văn A" (not all caps)
            self.lbl_name.setText(attendee_data.get("name", ""))
            
            position = attendee_data.get('position', '').strip()
            company = attendee_data.get('company', '').strip()
            
            self.lbl_position.setText(position)
            self.lbl_company.setText(company)
            
            # Hide spacing if field is empty to keep layout crisp
            self.lbl_position.setVisible(bool(position))
            self.lbl_company.setVisible(bool(company))
            
            # Load Photo
            photo_url = attendee_data.get("photo_url")
            if photo_url:
                self.load_image_async(photo_url)
            else:
                self.set_circular_photo(None)

            # Effects
            from PySide6.QtCore import QPoint
            center_point = self.photo_container.mapTo(self, QPoint(self.photo_container.width() // 2, self.photo_container.height() // 2))
            self.star_effect.explode(center_point.x(), center_point.y())
            
        except Exception as e:
            print(f"Error in update_attendee: {e}")
            import traceback
            traceback.print_exc()
        finally:
            print(f"Starting Auto-reset Timer ({self.reset_timer.interval()}ms)...")
            self.reset_timer.start()

    def reset_view(self):
        print("Resetting view (Standby)...")
        self.reset_timer.stop()
        self.stack.setCurrentWidget(self.page_standby)

    def load_image_async(self, url):
        from PySide6.QtCore import QThread, Signal
        import requests
        
        if not hasattr(self, '_img_session'):
             self._img_session = requests.Session()
             self._img_session.proxies = {"http": None, "https": None}

        class ImageLoader(QThread):
            image_loaded = Signal(QPixmap)
            error = Signal()

            def __init__(self, url, session):
                super().__init__()
                self.url = url
                self.session = session

            def run(self):
                try:
                    import hashlib
                    import os
                    
                    # Create cache dir
                    try:
                        base_dir = os.path.dirname(os.path.dirname(os.path.dirname(os.path.abspath(__file__))))
                    except NameError:
                        base_dir = os.getcwd()
                        
                    cache_dir = os.path.join(base_dir, "assets", "avatars")
                    os.makedirs(cache_dir, exist_ok=True)
                    
                    # Hash URL for filename
                    url_hash = hashlib.md5(self.url.encode('utf-8')).hexdigest()
                    cached_path = os.path.join(cache_dir, f"{url_hash}.jpg")
                    
                    # Try to load from local cache first for instant rendering
                    if os.path.exists(cached_path):
                        image = QImage()
                        if image.load(cached_path):
                            self.image_loaded.emit(QPixmap.fromImage(image))
                            # Try to start a background thread to update the cache silently if needed? No, keep it simple.
                            return
                            
                    # If not in cache, fallback to live network request
                    response = self.session.get(self.url, timeout=5)
                    if response.status_code == 200:
                        image = QImage()
                        image.loadFromData(response.content)
                        # Save to cache on disk
                        image.save(cached_path)
                        self.image_loaded.emit(QPixmap.fromImage(image))
                    else:
                        self.error.emit()
                except Exception as e:
                    print(f"Image load error: {e}")
                    self.error.emit()

        if hasattr(self, 'loader') and self.loader is not None:
             try:
                 self.loader.image_loaded.disconnect()
                 self.loader.error.disconnect()
             except:
                 pass
                 
        self.loader = ImageLoader(url, self._img_session)
        self.loader.image_loaded.connect(self.set_circular_photo)
        self.loader.error.connect(lambda: self.set_circular_photo(None))
        self.loader.start()

    def set_circular_photo(self, pixmap):
        size = 400 
        out_pixmap = QPixmap(size, size)
        out_pixmap.fill(Qt.transparent)
        
        painter = QPainter(out_pixmap)
        painter.setRenderHint(QPainter.Antialiasing)
        
        from PySide6.QtCore import QRectF
        
        # 1. Draw Golden Frame (Outer)
        rect_outer = QRectF(5, 5, size-10, size-10)
        grad_frame = QLinearGradient(rect_outer.topLeft(), rect_outer.bottomRight())
        grad_frame.setColorAt(0, QColor("#8a6000"))
        grad_frame.setColorAt(0.5, QColor("#f4c025"))
        grad_frame.setColorAt(1, QColor("#8a6000"))
        
        pen_frame = QPen(grad_frame, 6)
        painter.setPen(pen_frame)
        painter.setBrush(Qt.NoBrush)
        painter.drawEllipse(rect_outer)
        
        # 2. Draw Inner Spacing (Burgundy color)
        rect_inner_space = QRectF(11, 11, size-22, size-22)
        pen_space = QPen(QColor("#2b0000"), 6)
        painter.setPen(pen_space)
        painter.drawEllipse(rect_inner_space)
        
        # 3. Draw Image Cropped
        img_size = size - 28
        rect_img = QRectF(14, 14, img_size, img_size)
        
        if not pixmap:
            painter.setPen(Qt.NoPen)
            painter.setBrush(QColor(255, 255, 255, 30))
            painter.drawEllipse(rect_img)
        else:
            scaled_pixmap = pixmap.scaled(img_size, img_size, Qt.KeepAspectRatioByExpanding, Qt.SmoothTransformation)
            x = (scaled_pixmap.width() - img_size) // 2
            y = (scaled_pixmap.height() - img_size) // 2
            cropped = scaled_pixmap.copy(x, y, img_size, img_size)
            
            path = QPainterPath()
            path.addEllipse(rect_img)
            painter.setClipPath(path)
            painter.translate(14, 14)
            painter.drawPixmap(0, 0, cropped)
            
        painter.end()
        self.photo_container.setPixmap(out_pixmap)
        
        # Restart the timer so the user gets the full duration AFTER the image is fully loaded
        if hasattr(self, 'reset_timer') and self.reset_timer.isActive():
            print("Restarting timer since image load finished.")
            self.reset_timer.start()
