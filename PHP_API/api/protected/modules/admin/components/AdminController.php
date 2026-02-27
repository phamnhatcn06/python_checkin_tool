<?php
class AdminController extends Controller
{
    // Layout default for admin
    public $layout = '/layouts/column1'; // this will use the application's layout or module's layout depending on config

    public function filters()
    {
        return array(
            'accessControl', // perform access control for CRUD operations
        );
    }

    public function accessRules()
    {
        return array(
            array(
                'allow', // allow authenticated user to perform 'create' and 'update' actions
                'users' => array('@'),
                // Uncomment this to restrict to only admin role:
                // 'expression'=>'$user->getState("role") === "admin"',
            ),
            array(
                'deny',  // deny all users
                'users' => array('*'),
            ),
        );
    }
}
