<?php
class LoginForm extends CFormModel
{
    public $username;
    public $password;
    public $rememberMe;

    private $_identity;

    public function rules()
    {
        return array(
            array('username, password', 'required', 'message' => '{attribute} không được để trống.'),
            array('rememberMe', 'boolean'),
            array('password', 'authenticate'),
        );
    }

    public function attributeLabels()
    {
        return array(
            'username' => 'Tên đăng nhập',
            'password' => 'Mật khẩu',
            'rememberMe' => 'Ghi nhớ đăng nhập',
        );
    }

    public function authenticate($attribute, $params)
    {
        if (!$this->hasErrors()) {
            $this->_identity = new UserIdentity($this->username, $this->password);
            if (!$this->_identity->authenticate())
                $this->addError('password', 'Tên đăng nhập hoặc mật khẩu không chính xác.');
        }
    }

    public function login()
    {
        if ($this->_identity === null) {
            $this->_identity = new UserIdentity($this->username, $this->password);
            $this->_identity->authenticate();
        }
        if ($this->_identity->errorCode === UserIdentity::ERROR_NONE) {
            $duration = $this->rememberMe ? 3600 * 24 * 30 : 0; // 30 days
            Yii::app()->user->login($this->_identity, $duration);
            return true;
        }
        return false;
    }
}
