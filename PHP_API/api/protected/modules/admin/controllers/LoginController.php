<?php
class LoginController extends Controller
{
    // Do not use AdminController here to avoid access rules blocking the login page itself
    public $layout = '/layouts/column1';

    public function actionIndex()
    {
        // If user is already logged in, redirect to admin home
        if (!Yii::app()->user->isGuest) {
            $this->redirect(array('/admin/default/index'));
        }

        $model = new LoginForm;

        // if it is ajax validation request
        if (isset($_POST['ajax']) && $_POST['ajax'] === 'login-form') {
            echo CActiveForm::validate($model);
            Yii::app()->end();
        }

        // collect user input data
        if (isset($_POST['LoginForm'])) {
            $model->attributes = $_POST['LoginForm'];
            // validate user input and redirect to the previous page if valid
            if ($model->validate() && $model->login())
                $this->redirect(Yii::app()->user->returnUrl);
        }
        // display the login form
        $this->render('index', array('model' => $model));
    }

    public function actionLogout()
    {
        Yii::app()->user->logout();
        $this->redirect(array('/admin/login/index'));
    }
}
