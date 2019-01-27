<?php
defined('BASEPATH') OR exit('No direct script access allowed');
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/10/18
 * Time: 11:21
 */
class User_model extends CI_Model{
    public function get_user_info($where_arr){
        return $this->db->get_where('user', $where_arr)
                         ->result_array();
    }
    //查找所有教师
    public function get_user_list($where_arr, $offset, $per_page, $order_str){
        return $this->db->order_by($order_str)
                         ->get_where('user', $where_arr, $per_page, $offset)
                         ->result_array();
    }
    //添加教师
    public function add_teacher($data_arr)
    {
         $this->db->insert('user',$data_arr);
         $aff_rows=$this->db->affected_rows();
         return $aff_rows;

    }
    //按工号,姓名查找教师
    public function search_teacher($col,$key)
    {
        $data['user']=$this->db->get_where('user',array($col=>$key))->result_array();
        $data['count']=$this->db->from('user')->where($col,$key)->count_all_results();
        return $data;
    }
    //删除教师
    public function del_teacher($job_num)
    {
        $this->db->delete('user',array('job_number'=>$job_num));
        $aff_rows=$this->db->affected_rows();
        return $aff_rows;

    }
    //管理员重置密码
    public function  reset_password($data_arr,$where_arr)
    {
         $this->db->update('user',$data_arr,$where_arr);
         $aff_rows=$this->db->affected_rows();
         return $aff_rows;
    }
   //完善个人信息，修改密码
    public function change_user_info($data_arr,$where_arr)
    {
       $this->db->update('user',$data_arr,$where_arr);
       $aff_rows=$this->db->affected_rows();
       return $aff_rows;
    }
    //删除文章
    public function del_article($id)
    {
       $this->db->delete('thesis',array('accession_number'=>$id));
       $aff_rows=$this->db->affected_rows();
       return $aff_rows;
    }
    //重置文章认领者
    public function reset_claim($data_arr,$where_arr)
    {
        $this->db->update('thesis',$data_arr,$where_arr);
        $aff_rows=$this->db->affected_rows();
        return $aff_rows;
    }

    public function set_full_spell($full_spell, $where_arr){
        return $this->db->update('user', array('full_spell'=>$full_spell), $where_arr);
    }
}