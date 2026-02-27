<?php
$this->pageTitle = 'Sự kiện trong ngày: ' . CHtml::encode($day['day_label']);
?>

<div class="mb-4">
    <a href="<?php echo Yii::app()->createUrl('/admin/agenda/index'); ?>" class="btn btn-secondary btn-sm">
        &larr; Quay lại danh sách Ngày
    </a>
</div>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2>
        <?php echo $this->pageTitle; ?> <small class="text-muted">(
            <?php echo CHtml::encode($day['date_label']); ?>)
        </small>
    </h2>
    <a href="<?php echo Yii::app()->createUrl('/admin/agenda/createEvent', array('day_id' => $day['id'])); ?>"
        class="btn btn-primary btn-sm">
        + Thêm Sự kiện Mới
    </a>
</div>

<?php if (Yii::app()->user->hasFlash('success')): ?>
    <div class="alert alert-success">
        <?php echo Yii::app()->user->getFlash('success'); ?>
    </div>
<?php endif; ?>
<?php if (Yii::app()->user->hasFlash('error')): ?>
    <div class="alert alert-danger">
        <?php echo Yii::app()->user->getFlash('error'); ?>
    </div>
<?php endif; ?>

<div class="table-responsive">
    <table class="table table-bordered table-striped table-hover align-middle">
        <thead class="table-dark">
            <tr>
                <th width="60">ID</th>
                <th width="100">Thời gian</th>
                <th>Tiêu đề Sự kiện</th>
                <th>Trang phục</th>
                <th width="120">Loại thẻ (Style)</th>
                <th width="80">Sắp xếp</th>
                <th width="150" class="text-center">Hành động</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($events)): ?>
                <?php foreach ($events as $event): ?>
                    <tr>
                        <td>
                            <?php echo CHtml::encode($event['id']); ?>
                        </td>
                        <td><strong>
                                <?php echo CHtml::encode($event['time_label']); ?>
                            </strong></td>
                        <td>
                            <strong class="text-primary">
                                <?php echo CHtml::encode($event['title']); ?>
                            </strong>
                            <?php if (!empty($event['description'])): ?>
                                <br><small class="text-muted">
                                    <?php echo substr(CHtml::encode($event['description']), 0, 50) . '...'; ?>
                                </small>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php echo CHtml::encode($event['location']); ?>
                        </td>
                        <td>
                            <?php
                            $typeMap = [
                                'normal' => '<span class="badge bg-secondary">Bình thường</span>',
                                'highlight' => '<span class="badge bg-warning text-dark">Nổi bật (Gold)</span>',
                                'lunch' => '<span class="badge bg-success">An uống</span>',
                            ];
                            echo isset($typeMap[$event['event_type']]) ? $typeMap[$event['event_type']] : $event['event_type'];
                            ?>
                        </td>
                        <td>
                            <?php echo CHtml::encode($event['sort_order']); ?>
                        </td>
                        <td class="text-center">
                            <a href="<?php echo Yii::app()->createUrl('/admin/agenda/updateEvent', array('id' => $event['id'])); ?>"
                                class="btn btn-warning btn-sm">Sửa</a>
                            <a href="<?php echo Yii::app()->createUrl('/admin/agenda/deleteEvent', array('id' => $event['id'])); ?>"
                                class="btn btn-danger btn-sm"
                                onclick="return confirm('Bạn có chắc chắn muốn xoá sự kiện này?');">Xoá</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="7" class="text-center text-muted py-4">Chưa có Sự kiện nào trong ngày này. Hãy bấm Thêm Sự
                        kiện Mới.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>