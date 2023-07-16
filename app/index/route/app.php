<?php
use think\facade\Route;
Route::get('show',function(){
    return "show it!";
});

Route::rule('config','index/config','GET')->allowCrossDomain();
Route::rule('login','user/login','POST')->allowCrossDomain();
Route::rule('token','user/token','GET')->allowCrossDomain();
Route::rule('reg','user/reg','POST')->allowCrossDomain();
Route::rule('pay','user/pay','POST')->allowCrossDomain();
Route::rule('chatgpt','index/chatgpt','POST')->allowCrossDomain();