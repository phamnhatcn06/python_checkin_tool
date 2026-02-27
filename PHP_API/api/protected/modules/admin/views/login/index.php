<?php
/* @var $this LoginController */
/* @var $model LoginForm */
/* @var $form CActiveForm */

$this->pageTitle = Yii::app()->name . ' - Đăng nhập Quản trị';
?>

<div class="row justify-content-center mt-5">
    <div class="col-md-6 col-lg-4">
        <div class="card shadow-sm">
            <div class="card-header bg-primary text-white text-center">
                <h4 class="mb-0">Đăng nhập Quản trị</h4>
            </div>
            <div class="card-body">
                <p class="text-muted text-center">Vui lòng điền thông tin đăng nhập:</p>

                <div class="form">
                    <?php $form = $this->beginWidget('CActiveForm', array(
                        'id' => 'login-form',
                        'enableClientValidation' => true,
                        'clientOptions' => array(
                            'validateOnSubmit' => true,
                        ),
                        'htmlOptions' => array('class' => 'needs-validation')
                    )); ?>

                    <p class="small text-danger">Các trường có <span class="required">*</span> là bắt buộc.</p>

                    <div class="mb-3">
                        <?php echo $form->labelEx($model, 'username', array('class' => 'form-label')); ?>
                        <?php echo $form->textField($model, 'username', array('class' => 'form-control')); ?>
                        <?php echo $form->error($model, 'username', array('class' => 'text-danger small')); ?>
                    </div>

                    <div class="mb-3">
                        <?php echo $form->labelEx($model, 'password', array('class' => 'form-label')); ?>
                        <?php echo $form->passwordField($model, 'password', array('class' => 'form-control')); ?>
                        <?php echo $form->error($model, 'password', array('class' => 'text-danger small')); ?>
                    </div>

                    <div class="mb-3 form-check">
                        <?php echo $form->checkBox($model, 'rememberMe', array('class' => 'form-check-input')); ?>
                        <?php echo $form->label($model, 'rememberMe', array('class' => 'form-check-label')); ?>
                        <?php echo $form->error($model, 'rememberMe', array('class' => 'text-danger small')); ?>
                    </div>

                    <div class="d-grid mt-4">
                        <?php echo CHtml::submitButton('Đăng nhập', array('class' => 'btn btn-primary btn-block')); ?>
                    </div>

                    <?php $this->endWidget(); ?>
                </div><!-- form -->
            </div>
        </div>
    </div>
</div>