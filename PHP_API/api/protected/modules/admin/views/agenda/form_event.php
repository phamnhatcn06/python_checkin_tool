<?php
$this->pageTitle = $isNewRecord ? 'Thêm Sự kiện Mới' : 'Cập nhật Sự kiện';
?>

<div class="mb-4">
    <a href="<?php echo Yii::app()->createUrl('/admin/agenda/viewDay', array('id' => $day['id'])); ?>"
        class="btn btn-secondary btn-sm">
        &larr; Quay lại danh sách Sự kiện Ngày
        <?php echo CHtml::encode($day['day_label']); ?>
    </a>
</div>

<h2>
    <?php echo $this->pageTitle; ?>
</h2>

<div class="card shadow-sm mt-4">
    <div class="card-body">
        <form method="POST" action="">
            <div class="mb-3">
                <label for="time_label" class="form-label">Thời gian (VD: 08:00 AM)</label>
                <input type="text" class="form-control" id="time_label" name="AgendaEvent[time_label]"
                    value="<?php echo CHtml::encode($model['time_label']); ?>" required>
            </div>

            <div class="mb-3">
                <label for="title" class="form-label">Tiêu đề Sự kiện (VD: Welcome Breakfast)</label>
                <input type="text" class="form-control" id="title" name="AgendaEvent[title]"
                    value="<?php echo CHtml::encode($model['title']); ?>" required>
            </div>

            <div class="mb-3">
                <label for="location" class="form-label">Trang phục (VD: Smart Casual, Formal)</label>
                <input type="text" class="form-control" id="location" name="AgendaEvent[location]"
                    value="<?php echo CHtml::encode($model['location']); ?>">
            </div>

            <div class="mb-3">
                <label for="description" class="form-label">Mô tả Chi tiết (Cho phép HTML, Hình ảnh URL)</label>
                <textarea class="form-control" id="description" name="AgendaEvent[description]"
                    rows="4"><?php echo CHtml::encode($model['description']); ?></textarea>
                <small class="text-muted">Ghi chú: Để chèn ảnh từ URL, dùng mã HTML
                    <code>&lt;img src="link_anh" /&gt;</code></small>
            </div>

            <div class="mb-3">
                <label for="event_type" class="form-label">Loại Thẻ (Màu CSS trên Frontend)</label>
                <select class="form-select" id="event_type" name="AgendaEvent[event_type]">
                    <option value="normal" <?php echo $model['event_type'] == 'normal' ? 'selected' : ''; ?>>Bình thường
                        (Chấm viền vàng, chữ trắng)</option>
                    <option value="highlight" <?php echo $model['event_type'] == 'highlight' ? 'selected' : ''; ?>>Nổi
                        bật / Keynote (Khối nền xám nổi, chấm lõi vàng)</option>
                    <option value="lunch" <?php echo $model['event_type'] == 'lunch' ? 'selected' : ''; ?>>Ăn uống (Như
                        bình thường, có hình dao nĩa)</option>
                </select>
            </div>

            <div class="mb-3">
                <label for="sort_order" class="form-label">Thứ tự hiển thị (Số nhỏ xếp trước, ví dụ 0900 cho 9h)</label>
                <input type="number" class="form-control" id="sort_order" name="AgendaEvent[sort_order]"
                    value="<?php echo CHtml::encode($model['sort_order']); ?>" required>
            </div>

            <button type="submit" class="btn btn-primary">
                <?php echo $isNewRecord ? 'Thêm Mới' : 'Lưu Thay Đổi'; ?>
            </button>
        </form>
    </div>
</div>