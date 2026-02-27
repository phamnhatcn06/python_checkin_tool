<?php
$this->pageTitle = $isNewRecord ? 'Thêm Ngày Mới' : 'Cập nhật Ngày';
?>

<div class="mb-4">
    <a href="<?php echo Yii::app()->createUrl('/admin/agenda/index'); ?>" class="btn btn-secondary btn-sm">
        &larr; Quay lại danh sách
    </a>
</div>

<h2>
    <?php echo $this->pageTitle; ?>
</h2>

<div class="card shadow-sm mt-4">
    <div class="card-body">
        <form method="POST" action="">
            <div class="mb-3">
                <label for="day_label" class="form-label">Tên Ngày (VD: Day 1)</label>
                <input type="text" class="form-control" id="day_label" name="AgendaDay[day_label]"
                    value="<?php echo CHtml::encode($model['day_label']); ?>" required>
            </div>

            <div class="mb-3">
                <label for="date_label" class="form-label">Ngày Tháng (VD: Nov 12)</label>
                <input type="text" class="form-control" id="date_label" name="AgendaDay[date_label]"
                    value="<?php echo CHtml::encode($model['date_label']); ?>" required>
            </div>

            <div class="mb-3">
                <label for="sort_order" class="form-label">Thứ tự hiển thị (Số nhỏ xếp trước)</label>
                <input type="number" class="form-control" id="sort_order" name="AgendaDay[sort_order]"
                    value="<?php echo CHtml::encode($model['sort_order']); ?>" required>
            </div>

            <button type="submit" class="btn btn-primary">
                <?php echo $isNewRecord ? 'Thêm Mới' : 'Lưu Thay Đổi'; ?>
            </button>
        </form>
    </div>
</div>