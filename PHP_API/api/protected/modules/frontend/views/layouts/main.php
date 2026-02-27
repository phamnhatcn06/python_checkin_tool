<!DOCTYPE html>
<html class="dark" lang="en">

<head>
    <meta charset="utf-8" />
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />
    <title>Guest Home Gate - GM Meeting 2026</title>
    <link href="https://fonts.googleapis.com" rel="preconnect" />
    <link crossorigin="" href="https://fonts.gstatic.com" rel="preconnect" />
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@300;400;500;600;700;800&display=swap"
        rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap"
        rel="stylesheet" />
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <script id="tailwind-config">
        tailwind.config = {
            darkMode: "class",
            theme: {
                extend: {
                    colors: {
                        "primary": "#d4af37", // Rich metallic gold
                        "primary-light": "#f3e5ab", // Lighter gold/champagne
                        "burgundy-dark": "#2a0a0e", // Deep background base
                        "burgundy-light": "#4a1219", // Lighter burgundy for gradients
                        "text-gold": "#e6c975",
                    },
                    fontFamily: {
                        "display": ["Manrope", "sans-serif"]
                    },
                    backgroundImage: {
                        'gold-gradient': 'linear-gradient(135deg, #bf953f 0%, #fcf6ba 40%, #b38728 60%, #fbf5b7 100%)',
                        'crystalline-pattern': 'linear-gradient(135deg, rgba(255,255,255,0.03) 0%, rgba(255,255,255,0) 100%)',
                    },
                    borderRadius: { "DEFAULT": "0.25rem", "lg": "0.5rem", "xl": "0.75rem", "full": "9999px" },
                    boxShadow: {
                        'soft': '0 4px 20px -2px rgba(212, 175, 55, 0.1)',
                        'glow': '0 0 25px -5px rgba(212, 175, 55, 0.4)',
                    }
                },
            },
        }
    </script>
    <style>
        .crystal-bg {
            background-color: #2a0a0e;
            background-image:
                radial-gradient(circle at 80% 20%, rgba(74, 18, 25, 0.6) 0%, transparent 40%),
                radial-gradient(circle at 10% 80%, rgba(74, 18, 25, 0.6) 0%, transparent 40%),
                linear-gradient(30deg, rgba(255, 255, 255, 0.02) 0%, transparent 1px, transparent 100%),
                linear-gradient(150deg, rgba(255, 255, 255, 0.02) 0%, transparent 1px, transparent 100%),
                linear-gradient(270deg, rgba(255, 255, 255, 0.01) 0%, transparent 1px, transparent 100%);
            background-size: 100% 100%, 100% 100%, 80px 80px, 80px 80px, 80px 80px;
        }

        .text-gradient-gold {
            background: linear-gradient(to right, #bf953f, #fcf6ba, #b38728, #fbf5b7);
            -webkit-background-clip: text;
            background-clip: text;
            color: transparent;
            background-size: 200% auto;
            animation: shine 5s linear infinite;
        }

        @keyframes shine {
            to {
                background-position: 200% center;
            }
        }

        @keyframes float {
            0% {
                transform: translateY(0px) scale(1);
                box-shadow: 0 0 25px -5px rgba(212, 175, 55, 0.4);
            }

            50% {
                transform: translateY(-10px) scale(1.03);
                box-shadow: 0 0 45px -5px rgba(212, 175, 55, 0.8);
            }

            100% {
                transform: translateY(0px) scale(1);
                box-shadow: 0 0 25px -5px rgba(212, 175, 55, 0.4);
            }
        }

        body {
            min-height: max(884px, 100dvh);
        }

        .no-scrollbar::-webkit-scrollbar {
            display: none;
        }

        .no-scrollbar {
            -ms-overflow-style: none;
            scrollbar-width: none;
        }

        .details-expand {
            transition: max-height 0.3s ease-in-out, opacity 0.3s ease-in-out;
            max-height: 0;
            opacity: 0;
            overflow: hidden;
        }

        .group:focus-within .details-expand,
        .group:hover .details-expand {
            max-height: 200px;
            opacity: 1;
        }

        .font-serif-display {
            font-family: 'Playfair Display', serif;
        }
    </style>
</head>

<body class="bg-burgundy-dark min-h-screen font-display text-primary-light selection:bg-primary/30">
    <div class="crystal-bg relative flex h-full min-h-screen w-full flex-col">
        <header id="main-header"
            class="fixed top-0 left-0 w-full flex flex-col items-center p-6 pb-2 justify-center z-40 gap-1 transition-all duration-300">
            <?php
            $code = null;
            if (isset($_GET['code'])) {
                $code = $_GET['code'];
            } elseif (isset(Yii::app()->request->cookies['checkin_code'])) {
                $code = Yii::app()->request->cookies['checkin_code']->value;
            }
            $homeUrl = Yii::app()->createUrl('/frontend/default/index');
            if ($code) {
                $homeUrl .= '?code=' . urlencode($code);
            }
            ?>
            <a href="<?php echo $homeUrl; ?>" class="block transition-transform hover:scale-[1.02]">
                <img id="header-logo" src="<?php echo Yii::app()->request->baseUrl; ?>/assets/img/logo_header_new.png"
                    alt="Mường Thanh Hospitality"
                    class="h-24 max-[480px]:h-20 object-contain mb-1 transition-all duration-300" />
            </a>
        </header>

        <!-- Dynamic Content Section -->
        <div class="flex-1 w-full flex flex-col pt-32 pb-32">
            <?php echo $content; ?>
        </div>

        <div class="fixed bottom-0 left-0 w-full z-50">
            <?php
            // Determine if we are on the homepage or an inner page
            $controllerId = Yii::app()->controller->id;
            $actionId = Yii::app()->controller->action->id;
            $isHomePage = ($controllerId === 'default' && $actionId === 'index') || ($controllerId === 'site' && $actionId === 'index');

            if ($isHomePage):
                ?>
                <!-- Home Page Footer -->
                <div class="pointer-events-none">
                    <div
                        class="w-full text-center px-6 py-2 pointer-events-auto bg-gradient-to-t from-burgundy-dark via-burgundy-dark/90 to-transparent">
                        <p
                            class="text-xs md:text-sm font-light italic text-text-gold tracking-wide opacity-90 border-t border-primary/20 pt-3 w-[85%] mx-auto">
                            "Đổi mới tư duy - Chinh phục thử thách"
                        </p>
                    </div>

                    <footer class="pb-4 pt-1 text-center pointer-events-auto bg-burgundy-dark">
                        <p class="text-[10px] text-primary-light/40 font-medium">© 2026 Mường Thanh Hospitality. All rights
                            reserved.</p>
                    </footer>
                </div>
            <?php else: ?>
                <!-- Inner Pages Navigation Footer -->
                <div
                    class="bg-burgundy-dark/95 backdrop-blur-md border-t border-primary/20 pb-safe pt-2 px-1 rounded-t-2xl shadow-[0_-4px_25px_-5px_rgba(212,175,55,0.15)] flex justify-between items-end relative pb-2 md:pb-4">

                    <!-- Home -->
                    <a href="<?php echo $homeUrl; ?>"
                        class="flex flex-col items-center justify-center flex-1 py-1 <?php echo ($actionId === 'index') ? 'text-primary' : 'text-primary-light/60 hover:text-primary transition-colors'; ?>">
                        <span class="material-symbols-outlined text-[24px] max-[480px]:text-[20px] mb-1">home</span>
                        <span class="text-[10px] max-[480px]:text-[9px] font-medium tracking-wide">Trang chủ</span>
                    </a>

                    <!-- Agenda -->
                    <a href="<?php echo Yii::app()->createUrl('/frontend/default/agenda') . ($code ? '?code=' . urlencode($code) : ''); ?>"
                        class="flex flex-col items-center justify-center flex-1 py-1 <?php echo ($actionId === 'agenda') ? 'text-primary' : 'text-primary-light/60 hover:text-primary transition-colors'; ?>">
                        <span
                            class="material-symbols-outlined text-[24px] max-[480px]:text-[20px] mb-1">calendar_month</span>
                        <span class="text-[10px] max-[480px]:text-[9px] font-medium tracking-wide">Lịch trình</span>
                    </a>

                    <!-- Documents -->
                    <a href="javascript:void(0)"
                        class="flex flex-col items-center justify-center flex-1 py-1 <?php echo ($actionId === 'documents') ? 'text-primary' : 'text-primary-light/60 hover:text-primary transition-colors'; ?>">
                        <span class="material-symbols-outlined text-[24px] max-[480px]:text-[20px] mb-1">folder_open</span>
                        <span class="text-[10px] max-[480px]:text-[9px] font-medium tracking-wide">Tài liệu</span>
                    </a>

                    <!-- Profile -->
                    <a href="javascript:void(0)"
                        class="flex flex-col items-center justify-center flex-1 py-1 <?php echo ($actionId === 'profile') ? 'text-primary' : 'text-primary-light/60 hover:text-primary transition-colors'; ?>">
                        <span class="material-symbols-outlined text-[24px] max-[480px]:text-[20px] mb-1">person</span>
                        <span class="text-[10px] max-[480px]:text-[9px] font-medium tracking-wide">Cá nhân</span>
                    </a>

                </div>
            <?php endif; ?>
        </div>
    </div>
</body>
<script>
    document.addEventListener('scroll', function () {
        const header = document.getElementById('main-header');
        const logo = document.getElementById('header-logo');
        const agendaTabs = document.getElementById('agenda-tabs-header');

        if (window.scrollY > 30) {
            header.classList.add('bg-burgundy-dark/95', 'backdrop-blur-md', 'border-b', 'border-primary/20', 'shadow-sm', 'py-2');
            header.classList.remove('p-6');
            logo.classList.replace('h-24', 'h-14');
            logo.classList.replace('max-[480px]:h-20', 'max-[480px]:h-12');

            if (agendaTabs) {
                agendaTabs.style.top = (header.offsetHeight - 1) + 'px';
            }
        } else {
            header.classList.remove('bg-burgundy-dark/95', 'backdrop-blur-md', 'border-b', 'border-primary/20', 'shadow-sm', 'py-2');
            header.classList.add('p-6');
            logo.classList.replace('h-14', 'h-24');
            logo.classList.replace('max-[480px]:h-12', 'max-[480px]:h-20');

            if (agendaTabs) {
                agendaTabs.style.top = (header.offsetHeight - 1) + 'px'; // Keep it correctly positioned relative to big header too
            }
        }
    });

    // Initial call to set proper offset on load if needed
    window.dispatchEvent(new Event('scroll'));

</script>

</html>