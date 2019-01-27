<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/10/27
 * Time: 21:14
 */
class Nature_model extends CI_Model{

    //获取数据条数
    public function all_counts($where_arr = array()){
        return $this->db->where($where_arr)->count_all_results('nature');
    }

    //插入数据
    public function insert($table, $data){
       return $this->db->insert($table, $data);
    }

    //修改数据
    public function update($table , $data, $where_arr=array()){
        return $this->db->update($table, $data, $where_arr);
    }

    //获取json数据
    public function get_json($code, $message, $data = array()){
        $array = array(
            'code' => $code,
            'message' => $message,
            'data' => $data,
        );
        return json_encode($array, JSON_UNESCAPED_UNICODE);
    }

    /* 获取nature列表 */
    public function get_nature_list($where_arr=array(), $offset, $per_page){
        return $this->db->join('user','user.job_number=nature.owner')->order_by('add_time DESC')->get_where('nature', $where_arr, $per_page, $offset)->result_array();
    }

    /* 获取nature信息 */
    public function get_nature_info($where_arr=array()){
        return $this->db->get_where('nature', $where_arr)->result_array();
    }

    /* 搜索nature */
    public function nature_search($type, $keywords, $where_arr=array()){
        $keywords = addslashes($keywords);
        $this->db->like($type, $keywords);
        return $this->db->join('user','user.job_number=nature.owner')->get_where('nature', $where_arr)->result_array();
    }
}