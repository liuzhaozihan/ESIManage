<?php
defined('BASEPATH') OR exit('No direct script access allowed');
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/10/18
 * Time: 11:10
 */
class Login extends  CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->library('pwdhash'); //载入phpass加密类
        $this->load->model('user_model', 'user'); //载入model
    }

    //登录界面
    public function index(){
        $this->load->view('admin/login.html');
    }

    //登录验证
    public function check(){
        $code = $this->input->post('code');
        if($code != $this->session->userdata('code')){
            error_alert('验证码错误');
        }
        $job_number = $this->input->post('name');
        $password = $this->input->post('password');
        $login_status = false;
        $user = $this->user->get_user_info(array('job_number' => $job_number));
        if(!empty($user)){ //工号存在，继续检查密码是否正确
            $login_status = $this->pwdhash->CheckPassword($password, $user[0]['password']);
        }


        if($login_status){ //登录成功
            $token = time().mt_rand(0, 9999);
            $mem = new Memcached();
            $mem->addServer('127.0.0.1', '11211');
            $mem->set('token'.$user[0]['job_number'], $token);
            $this->session->set_userdata(array('name'=>$user[0]['name'], 'job_number'=>$user[0]['job_number'], 'identity'=>$user[0]['identity'],'academy'=>$user[0]['academy'], 'full_spell'=>$user[0]['full_spell'], 'token'.$user[0]['job_number']=>$token));
            $data['msg'] = '登陆成功';
            $data['jumpUrl']= site_url();
        }else{
            $data['msg'] = '登陆失败，工号或密码错误！';
            $data['failure'] = TRUE;
            $data['jumpUrl'] = site_url('login');
        }

        $data['waitSecond']=3;
        $this->load->view('admin/tips.html',$data);
    }

    //退出登录
    public function logout(){
        $this->session->sess_destroy();
        $data['jumpUrl'] = site_url('login');
        $data['waitSecond']=3;
        $this->load->view('admin/tips.html',$data);
    }

    //验证码
    public function auth_code(){
        $config=array(
            'width'=>100,
            'height'=>30,
            'fontSize'=>18,
            'codeLen'=>4
        );
        $this->load->library('code',$config);
        $this->code->show();
    }

    public function other_login(){
        $data['jumpUrl'] = site_url('login');
        $data['failure'] = TRUE;
        $data['msg'] = '您的账号在别处登录!<br>如非本人操作，请立即修改密码！';
        $data['waitSecond']=5;
        $this->session->sess_destroy();
        $this->load->view('admin/tips.html',$data);
    }

    public function tips(){
        $data['jumpUrl'] = site_url('login');
        $data['failure'] = TRUE;
        $data['msg'] = '您的账号在别处登录!<br>如非本人操作，请立即修改密码！';
        $data['waitSecond']=999999;
        $this->load->view('admin/tips.html',$data);
    }

    public function ended(){
        $data['jumpUrl'] = 'http://xkc.henu.edu.cn';
        $data['failure'] = TRUE;
        $data['msg'] = '对不起，认领工作已结束<br>系统处于关闭状态暂不能登录。';
        $data['waitSecond']=666;
        $this->load->view('admin/tips.html',$data);
    }
}