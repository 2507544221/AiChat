<?php
namespace app\index\validate;

use think\validate;

class UserValidate extends validate{
    protected $rule =   [
        'username'  => 'require|min:6|max:25',
        'password'   => 'require|min:6|max:25',
        'repassword'  => 'confirm:password',
        'email' => 'require|email',
    ];

    protected $message  =   [
        'username.min' => '账户名必须在6-25位',
        'username.max' => '账户名必须在6-25位',
        'username.require' => '账户名不能为空',
        'password.min' => '密码必须在6-25位',
        'password.max' => '密码必须在6-25位',
        'password.require'   => '密码不能为空',
        'repassword.confirm'  => '两次密码必须一致',
        'email.email'   => '邮箱格式错误',
        'email.require'   => '邮箱格式错误',
    ];
}