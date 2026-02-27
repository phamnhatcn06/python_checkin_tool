<?php

class DefaultController extends AdminController
{
    public function actionIndex()
    {
        if ($this->getViewFile('index')) {
            $this->render('index');
        } else {
            echo "Admin module is working.";
        }
    }
}
