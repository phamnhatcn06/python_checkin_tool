<?php

class AdminModule extends CWebModule
{
    public function init()
    {
        // this method is called when the module is being created
        // you may place code here to customize the module or the application

        // import the module-level models and components
        $this->setImport(array(
            'admin.models.*',
            'admin.components.*',
        ));

        // configure admin specific user component 
        Yii::app()->setComponents(array(
            'user' => array(
                'class' => 'CWebUser',
                'allowAutoLogin' => true,
                'stateKeyPrefix' => '_admin',
                'loginUrl' => array('/admin/login/index'),
            ),
        ), false); // false means merge, but don't overwrite if not necessary, actually we want to overwrite so maybe true? Let's use false but provide array directly. But wait, `Yii::app()->setComponents` takes merge boolean. Or we can just use Yii::app()->user->loginUrl = ...
    }

    public function beforeControllerAction($controller, $action)
    {
        if (parent::beforeControllerAction($controller, $action)) {
            // this method is called before any module controller action is performed
            // you may place customized code here
            return true;
        } else
            return false;
    }
}
