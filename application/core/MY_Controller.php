<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/10/18
 * Time: 15:16
 */
class MY_Controller extends CI_Controller{
    public function __construct()
    {
        parent::__construct();
        $job_number = $this->session->userdata('job_number');
        $name = $this->session->userdata('name');
        $identity = $this->session->userdata('identity');
        $mem = new Memcached();
        $mem->addServer('127.0.0.1', '11211');
        $token = $mem->get('token'.$job_number);

        if(empty($job_number) || empty($name) || !is_numeric($identity) || !$token){
            header('location:'.site_url('login'));exit;
        }

        //认领结束关闭普通用户登录
        if($identity != 1){
            header('location:'.site_url('login/ended'));exit;
        }
        if($token != $this->session->userdata('token'.$job_number)){
            header('location:'.site_url('login/other_login'));
        }
    }
}