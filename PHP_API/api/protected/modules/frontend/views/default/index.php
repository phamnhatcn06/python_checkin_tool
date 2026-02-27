<section class="flex flex-col items-center justify-center pt-6 pb-4 px-6 z-10">
    <?php if ($attendee): ?>
        <!-- Thêm class animate-[float_4s_ease-in-out_infinite] cho hiệu ứng nổi nhẹ nhàng -->
        <div class="relative group cursor-pointer mb-5 w-[50vw] max-w-xs rounded-full"
            style="animation: float 4s ease-in-out infinite;">
            <div class="absolute -inset-2 bg-primary/20 rounded-full blur-xl animate-pulse">
            </div>
            <div
                class="relative w-full aspect-square rounded-full border-[3px] border-transparent bg-gold-gradient p-[2px]">
                <div class="h-full w-full rounded-full bg-burgundy-dark p-[2px]">
                    <?php
                    $photoUrl = !empty($attendee['photo_url']) ? $attendee['photo_url'] : Yii::app()->request->baseUrl . '/assets/img/default-avatar.png'; // Fallback to a default if null
                    ?>
                    <div class="h-full w-full rounded-full bg-cover bg-center bg-no-repeat overflow-hidden border border-primary/30"
                        data-alt="Portrait of attendee <?php echo CHtml::encode($attendee['full_name']); ?>"
                        style='background-image: url("<?php echo CHtml::encode($photoUrl); ?>");'>
                    </div>
                </div>
            </div>
        </div>
        <div class="text-center mb-4 space-y-1">
            <p class="text-xs max-[480px]:text-[10px] font-medium text-primary-light/70 tracking-[0.2em] uppercase">Xin chào
            </p>
            <h2 class="text-2xl max-[480px]:text-xl font-bold leading-tight tracking-tight px-4 text-white">
                <?php echo CHtml::encode($attendee['full_name']); ?>
            </h2>
            <?php if (!empty($attendee['position'])): ?>
                <p class="text-sm max-[480px]:text-[12px] font-medium text-primary-light/90 mt-1">
                    <?php echo CHtml::encode($attendee['position']); ?>
                </p>
            <?php endif; ?>
            <?php if (!empty($attendee['company'])): ?>
                <p class="text-sm max-[480px]:text-[12px] text-primary-light/70 mt-0.5">
                    <?php echo CHtml::encode($attendee['company']); ?>
                </p>
            <?php endif; ?>
        </div>

    <?php else: ?>
        <div class="text-center mb-8 mt-4 space-y-2">
            <p class="text-xs max-[480px]:text-[10px] font-medium text-primary-light/70 tracking-[0.2em] uppercase">Xin chào
            </p>
            <h2 class="text-2xl max-[480px]:text-xl font-bold leading-tight tracking-tight px-4 text-white">
                Thành viên tham dự
            </h2>
            <p class="text-sm max-[480px]:text-[12px] font-medium text-primary-light/70 tracking-[0.1em] uppercase mt-2">
                Vui lòng quét mã QR để định danh
            </p>
        </div>
    <?php endif; ?>
</section>

<main class="flex-1 w-full max-w-md mx-auto p-6 z-10 flex flex-col justify-center">
    <div class="grid grid-cols-2 gap-4">
        <a href="<?php echo Yii::app()->createUrl('frontend/default/agenda'); ?>"
            class="block group relative flex flex-col gap-3 rounded-xl border border-primary/20 bg-burgundy-light/30 backdrop-blur-sm p-5 items-center justify-center text-center shadow-lg transition-all duration-300 hover:bg-burgundy-light/60 hover:border-primary/50 hover:-translate-y-1 hover:shadow-glow active:scale-95 overflow-hidden">
            <div
                class="absolute inset-0 bg-gold-gradient opacity-0 group-hover:opacity-5 transition-opacity duration-300">
            </div>
            <span
                class="material-symbols-outlined text-4xl max-[480px]:text-3xl text-gradient-gold group-hover:scale-110 transition-transform duration-300">calendar_month</span>
            <span
                class="text-sm max-[480px]:text-[12px] font-bold leading-tight text-primary-light uppercase tracking-wide">Xem
                Agenda</span>
        </a>
        <button
            class="group relative flex flex-col gap-3 rounded-xl border border-primary/20 bg-burgundy-light/30 backdrop-blur-sm p-5 items-center justify-center text-center shadow-lg transition-all duration-300 hover:bg-burgundy-light/60 hover:border-primary/50 hover:-translate-y-1 hover:shadow-glow active:scale-95 overflow-hidden">
            <div
                class="absolute inset-0 bg-gold-gradient opacity-0 group-hover:opacity-5 transition-opacity duration-300">
            </div>
            <span
                class="material-symbols-outlined text-4xl max-[480px]:text-3xl text-gradient-gold group-hover:scale-110 transition-transform duration-300">folder_open</span>
            <span
                class="text-sm max-[480px]:text-[12px] font-bold leading-tight text-primary-light uppercase tracking-wide">Tài
                liệu</span>
        </button>
        <button
            class="group relative flex flex-col gap-3 rounded-xl border border-primary/20 bg-burgundy-light/30 backdrop-blur-sm p-5 items-center justify-center text-center shadow-lg transition-all duration-300 hover:bg-burgundy-light/60 hover:border-primary/50 hover:-translate-y-1 hover:shadow-glow active:scale-95 overflow-hidden">
            <div
                class="absolute inset-0 bg-gold-gradient opacity-0 group-hover:opacity-5 transition-opacity duration-300">
            </div>
            <span
                class="material-symbols-outlined text-4xl max-[480px]:text-3xl text-gradient-gold group-hover:scale-110 transition-transform duration-300">location_on</span>
            <span
                class="text-sm max-[480px]:text-[12px] font-bold leading-tight text-primary-light uppercase tracking-wide">Địa
                điểm</span>
        </button>
        <button
            class="group relative flex flex-col gap-3 rounded-xl border border-primary/20 bg-burgundy-light/30 backdrop-blur-sm p-5 items-center justify-center text-center shadow-lg transition-all duration-300 hover:bg-burgundy-light/60 hover:border-primary/50 hover:-translate-y-1 hover:shadow-glow active:scale-95 overflow-hidden">
            <div
                class="absolute inset-0 bg-gold-gradient opacity-0 group-hover:opacity-5 transition-opacity duration-300">
            </div>
            <span
                class="material-symbols-outlined text-4xl max-[480px]:text-3xl text-gradient-gold group-hover:scale-110 transition-transform duration-300">chat_bubble_outline</span>
            <span
                class="text-sm max-[480px]:text-[12px] font-bold leading-tight text-primary-light uppercase tracking-wide">Gửi
                phản hồi</span>
        </button>
    </div>
</main>