<?php
class User extends CActiveRecord
{
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    public function tableName()
    {
        return 'users';
    }

    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('username, password', 'required'),
            array('username, password', 'length', 'max' => 255),
            array('role', 'length', 'max' => 50),
        );
    }

    public function validatePassword($password)
    {
        // If your users table has clear text, you can change this back to $this->password === $password
        return CPasswordHelper::verifyPassword($password, $this->password);
    }

    public function hashPassword($password)
    {
        return CPasswordHelper::hashPassword($password);
    }
}
