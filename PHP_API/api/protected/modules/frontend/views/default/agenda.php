<?php
// Extracted specific content for the Agenda view
?>
<div class="px-6 pt-2 pb-6 relative z-10 mt-4">
    <h2 class="text-3xl font-serif-display font-medium text-text-main leading-tight text-center">
        <span class="block text-primary-light">General Manager</span>
        <span class="text-primary italic text-2xl opacity-90">Meeting 2026</span>
    </h2>
</div>

<div class="sticky top-0 z-30 bg-background-light/95 backdrop-blur-sm border-b border-primary/20 -mx-6 px-6">
    <div class="flex justify-between">
        <?php if (!empty($days)): ?>
            <?php
            $dayCount = count($days);
            foreach ($days as $index => $day):
                $isActive = ($index === 0); // Default to first day active for now
                ?>
                <button
                    class="flex flex-col items-center justify-center pb-3 pt-3 flex-1 relative group <?php echo $isActive ? '' : 'opacity-60 hover:opacity-100 transition-opacity'; ?>">
                    <span
                        class="<?php echo $isActive ? 'text-primary' : 'text-text-main'; ?> text-sm font-bold tracking-wide uppercase"><?php echo CHtml::encode($day['date_label']); ?></span>
                    <span
                        class="absolute bottom-0 <?php echo $isActive ? 'w-full' : 'w-0 group-hover:w-1/2'; ?> h-[2px] bg-gradient-to-r from-transparent via-primary<?php echo $isActive ? '' : '/50'; ?> to-transparent <?php echo $isActive ? '' : 'transition-all duration-300'; ?>"></span>
                </button>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="py-3 text-center w-full text-text-muted text-sm">Chưa có lịch trình</div>
        <?php endif; ?>
    </div>
</div>

<main class="flex-1 overflow-y-auto pt-4 pb-24 relative z-10 w-full max-w-md mx-auto">
    <?php
    if (!empty($days) && isset($eventsGroupedByDay[$days[0]['id']])):
        ?>
        <div class="mb-5 pl-2">
            <h3 class="text-sm font-bold text-primary-light uppercase tracking-wide">
                <?php echo CHtml::encode($days[0]['day_label']); ?></h3>
        </div>

        <div
            class="absolute left-[15px] top-[52px] bottom-0 w-[1px] bg-gradient-to-b from-primary/10 via-primary/30 to-primary/10 z-0">
        </div>

        <?php foreach ($eventsGroupedByDay[$days[0]['id']] as $event):
            $type = $event['event_type'];
            // Determine styles based on event type
            $outerDivClass = "relative z-10 group mb-3 pl-8";
            $dotClass = "absolute left-[8px] top-1.5 size-[8px] rounded-full z-10 ";
            $cardClass = "flex flex-col transition-all duration-300 ";
            $timeClass = "font-serif-display text-base font-medium italic ";
            $titleClass = "font-bold leading-tight mb-1 transition-colors ";

            if ($type === 'highlight') {
                $dotClass .= "bg-primary border border-primary shadow-[0_0_0_3px_#381216] shadow-primary/20";
                $cardClass .= "bg-card-bg/40 backdrop-blur-sm p-3 -ml-3 rounded-xl shadow-md border border-primary/30 hover:scale-[1.01]";
                $timeClass .= "text-primary-light text-lg";
                $titleClass .= "text-lg text-white";
            } else {
                $dotClass .= "bg-background-light border border-primary group-hover:bg-primary transition-colors shadow-[0_0_0_3px_#381216]";
                $cardClass .= "border border-transparent hover:border-primary/20 p-2 -ml-2 rounded-xl";
                $timeClass .= "text-primary";
                $titleClass .= "text-base text-text-main group-hover:text-primary-light";
            }
            ?>
            <div class="<?php echo $outerDivClass; ?>">
                <div class="<?php echo $dotClass; ?>"></div>
                <div class="<?php echo $cardClass; ?>">
                    <div class="mb-1">
                        <span class="<?php echo $timeClass; ?>"><?php echo CHtml::encode($event['time_label']); ?></span>
                    </div>

                    <h3 class="<?php echo $titleClass; ?>">
                        <?php echo CHtml::encode($event['title']); ?>
                    </h3>

                    <div
                        class="flex items-center gap-1.5 text-text-muted text-[11px] uppercase tracking-wide <?php echo ($type === 'highlight') ? 'mb-2' : 'mb-1'; ?>">
                        <span
                            class="material-symbols-outlined text-[14px]"><?php echo ($type === 'lunch') ? 'restaurant' : (($type === 'highlight') ? 'podium' : 'checkroom'); ?></span>
                        <span><?php echo CHtml::encode($event['location'] ? $event['location'] : 'Tự do'); ?></span>
                    </div>

                    <?php if (!empty($event['description'])): ?>
                        <div
                            class="pt-1 border-t <?php echo ($type === 'highlight') ? 'border-dashed border-primary/20 mt-1' : 'border-primary/10 mt-1'; ?>">
                            <div class="text-[13px] text-text-main/80 leading-relaxed font-light html-content">
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
        <div class="text-center text-text-muted mt-10">Chưa có sự kiện nào trong ngày này.</div>
    <?php endif; ?>
</main>