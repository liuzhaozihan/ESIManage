<?php
defined('BASEPATH') OR exit('No direct script access allowed');
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/10/21
 * Time: 16:26
 */
class Upload_file extends MY_Controller{
    public function __construct()
    {
        parent::__construct();
        if($this->session->userdata('identity') < 1){
            msg_alert('该操作不被允许');
        }
    }

    public function index()
    {
        $this->load->view('admin/upload_file.html');
    }
    public function upload_excle()
    {
        set_time_limit(0);
        $config['upload_path'] = './uploads/';
        $config['allowed_types'] = 'xlsx|xls';
        $config['max_size'] = '10000';
        $config['file_name']=time().mt_rand(1000,9999);


        $this->load->library('upload',$config);

        $this->upload->do_upload('excle');
        $wrong=$this->upload->display_errors();
        if($wrong)
        {
          $arr = array('code'=>400, 'msg'=>$wrong);
        }else{
            $info=$this->upload->data();
            //存session 文件名，和后缀名
            $info_arr=array(
                'path'=>$config['upload_path'].$info['file_name'],
                'ext'=>$info['file_ext']
            );
            $this->session->set_userdata( $info_arr);
            $this->info_import($this->session->userdata('path'),ltrim($this->session->userdata('ext'),'.'));
            $arr = array('code'=>200, 'msg'=>'导入成功');
        }
        echo json_encode($arr, JSON_UNESCAPED_UNICODE);
    }
    public function info_import($filenmae,$ext='xls')
    {
        $this->load->model('article_model','am');
        $this->load->library('PHPExcel');  //载入PHPExcel类库
        $PHPExcel=new PHPExcel();
        $PHPReader = new PHPExcel_Reader_Excel2007();   //建立reader对象
        if(!$PHPReader->canRead($filenmae)){
            $PHPReader = new PHPExcel_Reader_Excel5();
            if(!$PHPReader->canRead($filenmae)){
                die('No Excel!');
            }
        }

        $PHPExcel=$PHPReader->load($filenmae);      //装载工作表

        $currentSheet=$PHPExcel->getSheet(0);
        $allColumn=$currentSheet->getHighestColumn();            //获取总列数
        $allRow=$currentSheet->getHighestRow();                  //获取总行数

        for($currentRow=2;$currentRow<=$allRow;$currentRow++)
        {
            for($currentColumn='A';$currentColumn<=$allColumn;$currentColumn++)
            {
                $address=$currentColumn.$currentRow; //数据坐标

                $data[$currentColumn]=$currentSheet->getCell($address)->getValue();
            }
            $this->save_data($data,$currentRow);
        }

        $this->session->set_userdata('import', 'success');
    }

    public function save_data($data,$currentRow)
    {

        $accession_number=$data['A'];
        $address=$data['B'];
        $doi=$data['C'];
        $title=$data['D'];
        $author=$data['E'];
        $source=$data['F'];
        $subject=$data['G'];
        $roll=$data['H'];
        $period=$data['I'];
        $page=$data['J'];
        $year=$data['K'];
        $times_cited=$data['L'];
        $impact_factor=$data['M'];
        $increase_cited=$data['N'];

        $author_del= str_replace(', ',' ', preg_replace('/([a-z|A-Z]{2,})(, | )([a-z|A-Z]{2,})( |-)([a-z|A-Z]{2,})(;|])/', '${1} ${3}${5}${6}', $address));//格式化作者字段

        $history=s_year() .":" .$increase_cited ."-";  //设置每年新增的引用次数


        $where_arr=array(
            'accession_number'=> $accession_number
        );

        $article=$this->am->get_article_info($where_arr);//查找是否有这条记录
        if($article)//存在
        {
            if(strpos($article[0]['history'],s_year() .":")===false)// 该记录存在且history未被更新
            {
                $info_arr2=array(             //更新信息
                    'times_cited'=>$times_cited,
                    'history'=>$article[0]['history'].$history  //history字段追加每年的引用次数
                );
            }else  //该记录存在history已被更新，（上传失败重新上传）
            {
                $info_arr2=array(             //更新信息
                    'times_cited'=>$times_cited,
                );
            }

            $status=$this->db->update("thesis",$info_arr2,$where_arr);//对引用次数进行更新
        }else{ //不存在
            $info_arr1=array(                //插入信息
                'accession_number'=> $accession_number,
                'address'=>$address,
                'doi'=>$doi,
                'title'=>$title,
                'author'=>$author,
                'source'=>$source,
                'subject'=>$subject,
                'roll'=>$roll,
                'period'=>$period,
                'page'=>$page,
                'year'=>$year,
                'times_cited'=>$times_cited,
                'impact_factor'=>$impact_factor,
                'author_del'=> $author_del,
                'history'=>$history              //history字段追 每年的引用次数
            );

            $status=$this->db->insert('thesis',$info_arr1);           //对新数据进行插入
        }
        //p($info_arr);

        if(!$status)
        {
            $data['failure']=true;
            $data['msg']="第".$currentRow."行操作失败,请更改数据后重新上传";
            $data['jumpUrl']= site_url().'upload_file/index';
            $data['waitSecond']=3;
            $this->load->view('admin/tips.html',$data);
        }
    }

    //判断是否完全导入成功
    public function import_status(){
        if($this->session->userdata('import') != 'success'){
            $data = array(
                'code'=>400,
                'msg'=>'未执行完成'
            );
        }else{
            $this->session->unset_userdata('import');
            $data = array(
                'code'=>200,
                'msg'=>'执行成功',
            );
        }

        echo json_encode($data, JSON_UNESCAPED_UNICODE);
    }

}