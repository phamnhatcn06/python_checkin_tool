<?php
// Extracted specific content for the Agenda view
?>
<div id="agenda-tabs-header"
    class="sticky top-0 z-30 bg-background-light/95 backdrop-blur-sm border-b border-white/20 -mx-6 px-6 transition-all duration-300">
    <div class="flex justify-between">
        <?php if (!empty($days)): ?>
            <?php
            $dayCount = count($days);
            foreach ($days as $index => $day):
                $isActive = ($index === 0); // Default to first day active for now
                ?>
                <button onclick="switchDay(<?php echo $day['id']; ?>, this)"
                    class="day-tab flex flex-col items-center justify-center pb-3 pt-3 flex-1 relative group <?php echo $isActive ? '' : 'opacity-60 hover:opacity-100 transition-opacity'; ?>">
                    <span
                        class="day-tab-text text-primary text-sm max-[480px]:text-[11px] font-bold tracking-wide uppercase"><?php echo CHtml::encode($day['date_label']); ?></span>
                    <span
                        class="day-tab-indicator absolute bottom-0 <?php echo $isActive ? 'w-full' : 'w-0 group-hover:w-1/2'; ?> h-[2px] bg-gradient-to-r from-transparent via-primary<?php echo $isActive ? '' : '/50'; ?> to-transparent <?php echo $isActive ? '' : 'transition-all duration-300'; ?>"></span>
                </button>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="py-3 text-center w-full text-white/70 text-sm">Chưa có lịch trình</div>
        <?php endif; ?>
    </div>
</div>

<main class="flex-1 overflow-y-auto pb-24 relative z-10 w-full max-w-md mx-auto">
    <?php if (!empty($days)): ?>
        <?php foreach ($days as $index => $day):
            $isActive = ($index === 0);
            $dayId = $day['id'];
            ?>
            <div id="day-content-<?php echo $dayId; ?>" class="day-content relative h-full w-full"
                style="<?php echo $isActive ? '' : 'display: none;'; ?>">
                <?php if (isset($eventsGroupedByDay[$dayId]) && !empty($eventsGroupedByDay[$dayId])): ?>
                    <div class="px-6 pt-2 pb-6 relative z-10 flex justify-center">
                        <img src="<?php echo Yii::app()->request->baseUrl; ?>/assets/img/tab_agenda-top.png"
                            alt="GM Meeting 2026 Logo" class="h-24 object-contain drop-shadow-lg">
                    </div>
                    <div class="mb-5 pl-2 text-center">
                        <h3 class="text-base max-[480px]:text-sm font-bold text-white uppercase tracking-wide">
                            <?php echo CHtml::encode($day['day_label']); ?>
                        </h3>
                    </div>

                    <div
                        class="absolute left-[11.5px] top-[52px] bottom-0 w-[1px] bg-gradient-to-b from-white/10 via-white/30 to-white/10 z-0">
                    </div>

                    <?php foreach ($eventsGroupedByDay[$dayId] as $event):
                        $type = $event['event_type'];
                        
                        // Check if the event is currently active
                        $isActiveEvent = false;
                        $currentDate = date('d/m/Y');
                        $currentDateShort = date('d/m');
                        $currentTime = date('H:i');
                        
                        $dayLabel = trim($day['date_label']);
                        if ($dayLabel === $currentDate || $dayLabel === $currentDateShort || strpos($currentDate, $dayLabel) === 0) {
                            $timeParts = explode('-', $event['time_label']);
                            if (count($timeParts) >= 2) {
                                $startTime = trim($timeParts[0]);
                                $endTime = trim($timeParts[1]);
                                if ($currentTime >= $startTime && $currentTime < $endTime) {
                                    $isActiveEvent = true;
                                }
                            }
                        }
                        
                        // Determine styles based on event type
                        $outerDivClass = "relative z-10 group mb-2 pl-8"; // Reduced margin between blocks
                        $dotClass = "absolute left-[7.5px] top-1.5 size-[8px] rounded-full z-10 ";
                        $cardClass = "flex flex-col transition-all duration-300 ";
                        $timeClass = "font-serif-display text-base max-[480px]:text-[13px] font-medium italic text-white ";
                        $titleClass = "font-bold leading-tight mb-1 transition-colors text-white ";

                        if ($type === 'highlight') {
                            $dotClass .= "bg-primary border border-primary ring-[3px] ring-primary/20 shadow-md shadow-primary/20";
                            $cardClass .= "bg-card-bg/40 backdrop-blur-sm p-3 max-[480px]:p-2 -ml-3 max-[480px]:-ml-2 rounded-xl shadow-md border border-white/30 hover:scale-[1.01]";
                            $timeClass .= "text-lg max-[480px]:text-[15px]";
                            $titleClass .= "text-lg max-[480px]:text-[15px]";
                        } else {
                            $dotClass .= "bg-transparent border border-primary group-hover:bg-primary transition-colors ring-[3px] ring-primary/10";
                            $cardClass .= "border border-transparent hover:border-white/20 p-2 max-[480px]:p-1 -ml-2 max-[480px]:-ml-1 rounded-xl";
                            $timeClass .= "";
                            $titleClass .= "text-base max-[480px]:text-[13px] group-hover:text-white/90";
                        }
                        
                        // Apply active blinking effect
                        if ($isActiveEvent) {
                            $dotClass = str_replace("bg-transparent", "bg-primary", $dotClass);
                            $dotClass .= " animate-pulse ring-primary/60 shadow-[0_0_10px_rgba(255,215,0,0.5)]";
                            $cardClass .= " bg-primary/10 border border-primary/30";
                            $titleClass .= " text-primary-light";
                        }
                        ?>
                        <div class="<?php echo $outerDivClass; ?>">
                            <div class="<?php echo $dotClass; ?>"></div>
                            <div class="<?php echo $cardClass; ?>">
                                <div class="mb-1 flex items-center gap-2">
                                    <span class="<?php echo $timeClass; ?>"><?php echo CHtml::encode($event['time_label']); ?></span>
                                    <?php if ($isActiveEvent): ?>
                                        <div class="flex items-center gap-1.5 ml-2">
                                            <span class="relative flex h-2 w-2">
                                              <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-red-400 opacity-75"></span>
                                              <span class="relative inline-flex rounded-full h-2 w-2 bg-red-500"></span>
                                            </span>
                                            <span class="bg-primary text-burgundy-dark text-[9px] max-[480px]:text-[8px] font-bold px-1.5 py-0.5 rounded-sm uppercase tracking-wider">Đang diễn ra</span>
                                        </div>
                                    <?php endif; ?>
                                </div>

                                <h3 class="<?php echo $titleClass; ?>">
                                    <?php echo CHtml::encode($event['title']); ?>
                                </h3>
                                <?php if ($event['location'] != ''): ?>
                                    <div
                                        class="flex items-center gap-1.5 text-white/70 text-[11px] max-[480px]:text-[10px] tracking-wide <?php echo ($type === 'highlight') ? 'mb-2 max-[480px]:mb-1' : 'mb-1 max-[480px]:mb-0.5'; ?>">
                                        <span
                                            class="material-symbols-outlined text-[14px] max-[480px]:text-[12px]"><?php echo ($type === 'lunch') ? 'restaurant' : (($type === 'highlight') ? 'podium' : 'checkroom'); ?></span>
                                        <span>Trang phục: <?php echo CHtml::encode($event['location'] ? $event['location'] : ''); ?></span>
                                    </div>
                                <?php endif; ?>
                                <?php if (!empty($event['description'])): ?>
                                    <div
                                        class="pt-1 border-t <?php echo ($type === 'highlight') ? 'border-dashed border-white/20 mt-1 max-[480px]:mt-0.5' : 'border-white/10 mt-1 max-[480px]:mt-0.5'; ?>">
                                        <div
                                            class="text-[13px] max-[480px]:text-[11px] text-white/80 leading-relaxed font-light html-content">
                                            <?php
                                            // allow simple HTML like img 
                                            echo $event['description'];
                                            ?>
                                        </div>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                        <?php
                    endforeach;
                else:
                    ?>
                    <div class="text-center text-white/70 mt-10">Chưa có sự kiện nào trong ngày này.</div>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <div class="text-center text-white/70 mt-10">Chưa có lịch trình</div>
    <?php endif; ?>
</main>

<script>
    function switchDay(dayId, btn) {
        // Hide all day contents
        document.querySelectorAll('.day-content').forEach(function (el) {
            el.style.display = 'none';
        });
        // Show selected day
        var selected = document.getElementById('day-content-' + dayId);
        if (selected) {
            selected.style.display = 'block';
        }

        // Reset all tabs
        document.querySelectorAll('.day-tab').forEach(function (tab) {
            tab.className = 'day-tab flex flex-col items-center justify-center pb-3 pt-3 flex-1 relative group opacity-60 hover:opacity-100 transition-opacity';

            var text = tab.querySelector('.day-tab-text');
            if (text) text.className = 'day-tab-text text-primary text-sm max-[480px]:text-[11px] font-bold tracking-wide uppercase';

            var indicator = tab.querySelector('.day-tab-indicator');
            if (indicator) indicator.className = 'day-tab-indicator absolute bottom-0 w-0 group-hover:w-1/2 h-[2px] bg-gradient-to-r from-transparent via-primary/50 to-transparent transition-all duration-300';
        });

        // Set active tab
        btn.className = 'day-tab flex flex-col items-center justify-center pb-3 pt-3 flex-1 relative group';

        var btnText = btn.querySelector('.day-tab-text');
        if (btnText) btnText.className = 'day-tab-text text-primary text-sm max-[480px]:text-[11px] font-bold tracking-wide uppercase';

        var btnIndicator = btn.querySelector('.day-tab-indicator');
        if (btnIndicator) btnIndicator.className = 'day-tab-indicator absolute bottom-0 w-full h-[2px] bg-gradient-to-r from-transparent via-primary to-transparent';
    }
</script>