<?php
defined('BASEPATH') OR exit('No direct script access allowed');
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/10/20
 * Time: 13:26
 */
//分页方法
function  separate_page($per_page,$url,$rows,$uri_seg)
{
    $this->load->library('pagination');

    $config['base_url'] = site_url($url);
    $config['total_rows'] =$rows;
    $config['per_page'] =  $per_page;
    $config['uri_segment'] = $uri_seg;

    $config['full_tag_open'] = '<div class="pagelist">';
    $config['full_tag_close'] = '</div>';
    $config['cur_tag_open'] = '<span class="current">';
    $config['cur_tag_close'] = '</span>';

    $config['first_link'] = '首页';
    $config['last_link'] = '尾页';
    $config['next_link'] = '下一页';
    $config['prev_link'] = '上一页';

    $this->pagination->initialize($config);
    $data= $this->pagination->create_links();
    return $data;
}
