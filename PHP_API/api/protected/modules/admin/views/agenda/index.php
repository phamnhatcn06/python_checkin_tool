<?php
$this->pageTitle = 'Quản lý Agenda';
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2>Quản lý các Ngày Sự kiện (Agenda Days)</h2>
    <div>
        <a href="<?php echo Yii::app()->createUrl('/admin/agenda/initDb'); ?>" class="btn btn-warning btn-sm me-2"
            onclick="return confirm('Khởi tạo Database (Không xoá dữ liệu cũ)?');">
            Khởi tạo Bảng DB
        </a>
        <a href="<?php echo Yii::app()->createUrl('/admin/agenda/createDay'); ?>" class="btn btn-primary btn-sm">
            + Thêm Ngày Mới
        </a>
    </div>
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
<?php if (Yii::app()->user->hasFlash('warning')): ?>
    <div class="alert alert-warning">
        <?php echo Yii::app()->user->getFlash('warning'); ?>
    </div>
<?php endif; ?>

<div class="table-responsive">
    <table class="table table-bordered table-striped table-hover align-middle">
        <thead class="table-dark">
            <tr>
                <th width="60">ID</th>
                <th>Tên Ngày (Day 1)</th>
                <th>Ngày Tháng (Nov 12)</th>
                <th width="100">Sắp xếp</th>
                <th width="250" class="text-center">Hành động</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($days)): ?>
                <?php foreach ($days as $day): ?>
                    <tr>
                        <td>
                            <?php echo CHtml::encode($day['id']); ?>
                        </td>
                        <td><strong>
                                <?php echo CHtml::encode($day['day_label']); ?>
                            </strong></td>
                        <td>
                            <?php echo CHtml::encode($day['date_label']); ?>
                        </td>
                        <td>
                            <?php echo CHtml::encode($day['sort_order']); ?>
                        </td>
                        <td class="text-center">
                            <a href="<?php echo Yii::app()->createUrl('/admin/agenda/viewDay', array('id' => $day['id'])); ?>"
                                class="btn btn-info btn-sm text-white">Quản lý Events</a>
                            <a href="<?php echo Yii::app()->createUrl('/admin/agenda/updateDay', array('id' => $day['id'])); ?>"
                                class="btn btn-warning btn-sm">Sửa</a>
                            <a href="<?php echo Yii::app()->createUrl('/admin/agenda/deleteDay', array('id' => $day['id'])); ?>"
                                class="btn btn-danger btn-sm"
                                onclick="return confirm('Xoá ngày này sẽ XOÁ TOÀN BỘ SỰ KIỆN bên trong nó. Bạn có chắc chắn?');">Xoá</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="5" class="text-center text-muted py-4">Chưa có Ngày Sự kiện nào. Hãy thêm mới hoặc click
                        Khởi tạo Bảng DB nếu là lần đầu.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>