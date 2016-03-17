<?php
namespace Home\Controller;
use Think\Controller;
class LoginController extends Controller {
    public function login(){
        $this->success('登录成功', 'index.php?m=home&c=index&a=index');
    }

    public function logout(){
        $this->display("login");
    }
}
