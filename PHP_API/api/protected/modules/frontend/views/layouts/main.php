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
    <div class="crystal-bg relative flex h-full min-h-screen w-full flex-col overflow-x-hidden">
        <header class="flex flex-col items-center p-6 pb-2 justify-center relative z-10 gap-1">
            <img src="<?php echo Yii::app()->request->baseUrl; ?>/assets/img/logo_header.png"
                alt="Mường Thanh Hospitality" class="h-16 object-contain mb-1" />
            <h1 class="text-3xl font-extrabold tracking-tight text-center uppercase text-gradient-gold drop-shadow-sm">
                GM Meeting 2026
            </h1>
        </header>

        <!-- Dynamic Content Section -->
        <div class="flex-1 w-full flex flex-col pb-32">
            <?php echo $content; ?>
        </div>

        <div class="fixed bottom-0 left-0 w-full z-50 pointer-events-none">
            <div class="w-full text-center px-6 py-2 pointer-events-auto">
                <p
                    class="text-xs md:text-sm font-light italic text-text-gold tracking-wide opacity-90 border-t border-primary/20 pt-3 w-[85%] mx-auto">
                    "Đổi mới tư duy - Chinh phục thử thách"
                </p>
            </div>

            <footer class="pb-4 pt-1 text-center pointer-events-auto">
                <p class="text-[10px] text-primary-light/40 font-medium">© 2026 Mường Thanh Hospitality. All rights
                    reserved.</p>
            </footer>
        </div>
    </div>
</body>

</html>