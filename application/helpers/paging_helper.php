<?php
defined('BASEPATH') OR exit('No direct script access allowed');
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/10/20
 * Time: 13:59
 */
function  separate_page($per_page,$url,$rows,$uri_seg)
{

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
    return $config;
}