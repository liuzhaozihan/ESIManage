<?php
defined('BASEPATH') OR exit('No direct script access allowed');
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/10/13
 * Time: 13:57
 */
class Format_model extends CI_Model{

    public function search_all()
    {
        $data=$this->db->from('thesis')
                      ->get()
                      ->result_array();
        return $data;
    }

    public function add($key,$data)
    {
        $this->db->update('thesis',$data,array('accession_number'=>$key));
    }

    /**
     * @param array $title_arr
     * @param array $data_arr
     * @param string $filename
     * @param string $savepath
     * @param bool $isdowm
     */
    //ESI论文管理 导出表格
    public function export($title=array(), $data=array(), $fileName='', $savePath='./', $isDown=false){
        $this->load->library('PHPExcel');
        $obj=new PHPExcel();

        //横向单元格标识
        $cellName = array('A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O','P','Q','R');

        $obj->getActiveSheet(0)->setTitle('sheet');   //设置sheet名称
        $_row = 1;   //设置纵向单元格标识

        if($title){
            $i = 0;
            foreach($title AS $v){   //设置列标题
                $obj->setActiveSheetIndex(0)->setCellValue($cellName[$i].$_row, $v);
                $i++;
            }
            $_row++;
        }
        //设置表格宽度
        $obj->getActiveSheet()->getColumnDimension('A')->setWidth(21);
        $obj->getActiveSheet()->getColumnDimension('B')->setWidth(70);
        $obj->getActiveSheet()->getColumnDimension('C')->setWidth(35);
        $obj->getActiveSheet()->getColumnDimension('D')->setWidth(35);
        $obj->getActiveSheet()->getColumnDimension('E')->setWidth(20);
        $obj->getActiveSheet()->getColumnDimension('F')->setWidth(36);
        $obj->getActiveSheet()->getColumnDimension('G')->setWidth(21);
        $obj->getActiveSheet()->getColumnDimension('H')->setWidth(9);
        $obj->getActiveSheet()->getColumnDimension('I')->setWidth(9);
        $obj->getActiveSheet()->getColumnDimension('J')->setWidth(9);
        $obj->getActiveSheet()->getColumnDimension('K')->setWidth(9);
        $obj->getActiveSheet()->getColumnDimension('L')->setWidth(9);
        $obj->getActiveSheet()->getColumnDimension('M')->setWidth(13);
        $obj->getActiveSheet()->getColumnDimension('N')->setWidth(15);
        $obj->getActiveSheet()->getColumnDimension('O')->setWidth(15);
        $obj->getActiveSheet()->getColumnDimension('P')->setWidth(15);
        $obj->getActiveSheet()->getColumnDimension('Q')->setWidth(15);
        $obj->getActiveSheet()->getColumnDimension('R')->setWidth(15);

        //填写数据

        if($data){
            $i = 0;
            foreach($data AS $v){

                $obj->getActiveSheet(0)->setCellValue($cellName[0].($i+$_row),$v['accession_number']);
                $obj->getActiveSheet(0)->setCellValue($cellName[1].($i+$_row),$v['address']);
                $obj->getActiveSheet(0)->setCellValue($cellName[2].($i+$_row),$v['doi']);
                $obj->getActiveSheet(0)->setCellValue($cellName[3].($i+$_row),$v['title']);
                $obj->getActiveSheet(0)->setCellValue($cellName[4].($i+$_row),$v['author']);
                $obj->getActiveSheet(0)->setCellValue($cellName[5].($i+$_row),$v['source']);
                $obj->getActiveSheet(0)->setCellValue($cellName[6].($i+$_row),$v['subject']);
                $obj->getActiveSheet(0)->setCellValue($cellName[7].($i+$_row),$v['roll']);
                $obj->getActiveSheet(0)->setCellValue($cellName[8].($i+$_row),$v['period']);
                $obj->getActiveSheet(0)->setCellValue($cellName[9].($i+$_row),$v['page']);
                $obj->getActiveSheet(0)->setCellValue($cellName[10].($i+$_row),$v['year']);
                $obj->getActiveSheet(0)->setCellValue($cellName[11].($i+$_row),$v['times_cited']);
                $obj->getActiveSheet(0)->setCellValue($cellName[12].($i+$_row),$v['impact_factor']);
                $obj->getActiveSheet(0)->setCellValue($cellName[13].($i+$_row),$v['update_time']);
                $obj->getActiveSheet(0)->setCellValue($cellName[14].($i+$_row),$v['increase_claim']);
                if(isset($v['name']))//有名字user表与thesis表联立查询 单位也存在
                {
                    $obj->getActiveSheet(0)->setCellValue($cellName[15].($i+$_row),$v['name']);//设置姓名字段
                    $obj->getActiveSheet(0)->setCellValue($cellName[17].($i+$_row),$v['academy']);//设置学院字段

                }else if($v['owner']!=null) //owner 不为空 只是thesis 表查询
                {
                    $author_info= $this->db->from('user')->where('job_number',$v['owner'])->get()->result_array();
                    $obj->getActiveSheet(0)->setCellValue($cellName[15].($i+$_row),$author_info[0]['name']);
                    $obj->getActiveSheet(0)->setCellValue($cellName[17].($i+$_row),$author_info[0]['academy']);
                }else{
                    $obj->getActiveSheet(0)->setCellValue($cellName[15].($i+$_row),"");
                    $obj->getActiveSheet(0)->setCellValue($cellName[17].($i+$_row),"");
                }

                if($v['claim_time']!=null)
                {
                    $date_time=date("Y-m-d",$v['claim_time']);//将时间戳转化为日期
                    $obj->getActiveSheet(0)->setCellValue($cellName[16].($i+$_row), $date_time);

                }else{
                    $obj->getActiveSheet(0)->setCellValue($cellName[16].($i+$_row),"");
                }



                $i++;

            }

        }


        //文件名处理

        if(!$fileName){

            $fileName = time();

        }

        $objWrite = PHPExcel_IOFactory::createWriter($obj, 'Excel5');

        if($isDown){   //网页下载

            header('pragma:public');

            header('Content-type:application/vnd.ms-excel;charset=utf-8;name="'.$fileName.'.xls"');
            header("Content-Disposition:attachment;filename=$fileName.xls");

            $objWrite->save('php://output');exit;

        }

    }
    //ESI论文引用次数查询 导出表格
    public function export_claim($title=array(), $data=array(), $fileName='', $savePath='./', $isDown=false){
        $this->load->library('PHPExcel');
        $obj=new PHPExcel();
       // '入藏号','地址','DOI','论文标题','作者全称','来源','学科领域','卷','期','页','出版年','被引频次',$begin_time.'新增引用频次','期刊影响因子','认领人','所在单位','认领时间'
        //横向单元格标识
        $cellName = array('A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O','P','Q');

        $obj->getActiveSheet(0)->setTitle('sheet');   //设置sheet名称
        $_row = 1;   //设置纵向单元格标识

        if($title){
            $i = 0;
            foreach($title AS $v){   //设置列标题
                $obj->setActiveSheetIndex(0)->setCellValue($cellName[$i].$_row, $v);
                $i++;
            }
            $_row++;
        }
        //设置表格宽度
        $obj->getActiveSheet()->getColumnDimension('A')->setWidth(21);
        $obj->getActiveSheet()->getColumnDimension('B')->setWidth(70);
        $obj->getActiveSheet()->getColumnDimension('C')->setWidth(35);
        $obj->getActiveSheet()->getColumnDimension('D')->setWidth(35);
        $obj->getActiveSheet()->getColumnDimension('E')->setWidth(20);
        $obj->getActiveSheet()->getColumnDimension('F')->setWidth(36);
        $obj->getActiveSheet()->getColumnDimension('G')->setWidth(21);
        $obj->getActiveSheet()->getColumnDimension('H')->setWidth(9);
        $obj->getActiveSheet()->getColumnDimension('I')->setWidth(9);
        $obj->getActiveSheet()->getColumnDimension('J')->setWidth(9);
        $obj->getActiveSheet()->getColumnDimension('K')->setWidth(9);
        $obj->getActiveSheet()->getColumnDimension('L')->setWidth(20);
        $obj->getActiveSheet()->getColumnDimension('M')->setWidth(20);
        $obj->getActiveSheet()->getColumnDimension('N')->setWidth(20);
        $obj->getActiveSheet()->getColumnDimension('O')->setWidth(15);
        $obj->getActiveSheet()->getColumnDimension('P')->setWidth(15);
        $obj->getActiveSheet()->getColumnDimension('Q')->setWidth(15);

        //填写数据
        if($data){
            $i = 0;
            foreach($data AS $v){

                $obj->getActiveSheet(0)->setCellValue($cellName[0].($i+$_row),$v['accession_number']);
                $obj->getActiveSheet(0)->setCellValue($cellName[1].($i+$_row),$v['address']);
                $obj->getActiveSheet(0)->setCellValue($cellName[2].($i+$_row),$v['doi']);
                $obj->getActiveSheet(0)->setCellValue($cellName[3].($i+$_row),$v['title']);
                $obj->getActiveSheet(0)->setCellValue($cellName[4].($i+$_row),$v['author']);
                $obj->getActiveSheet(0)->setCellValue($cellName[5].($i+$_row),$v['source']);
                $obj->getActiveSheet(0)->setCellValue($cellName[6].($i+$_row),$v['subject']);
                $obj->getActiveSheet(0)->setCellValue($cellName[7].($i+$_row),$v['roll']);
                $obj->getActiveSheet(0)->setCellValue($cellName[8].($i+$_row),$v['period']);
                $obj->getActiveSheet(0)->setCellValue($cellName[9].($i+$_row),$v['page']);
                $obj->getActiveSheet(0)->setCellValue($cellName[10].($i+$_row),$v['year']);
                $obj->getActiveSheet(0)->setCellValue($cellName[11].($i+$_row),$v['times_cited']);
                $obj->getActiveSheet(0)->setCellValue($cellName[12].($i+$_row),$v['increase_claim']);

                $obj->getActiveSheet(0)->setCellValue($cellName[13].($i+$_row),$v['impact_factor']);
                if(isset($v['name']))
                {
                    $obj->getActiveSheet(0)->setCellValue($cellName[14].($i+$_row),$v['name']);

                }else if($v['owner']!=null)
                {
                    $author_name= $this->db->select('name')->from('user')->where('job_number',$v['owner'])->get()->result_array();
                    $obj->getActiveSheet(0)->setCellValue($cellName[14].($i+$_row),$author_name[0]['name']);
                }else{
                    $obj->getActiveSheet(0)->setCellValue($cellName[14].($i+$_row),"");
                }
                $obj->getActiveSheet(0)->setCellValue($cellName[15].($i+$_row),$v['academy']);
                if($v['claim_time']!=null)
                {
                    $date_time=date("Y-m-d",$v['claim_time']);//将时间戳转化为日期
                    $obj->getActiveSheet(0)->setCellValue($cellName[16].($i+$_row), $date_time);

                }else{
                    $obj->getActiveSheet(0)->setCellValue($cellName[16].($i+$_row),"");
                }


                $i++;

            }

        }


        //文件名处理

        if(!$fileName){

            $fileName = time();

        }

        $objWrite = PHPExcel_IOFactory::createWriter($obj, 'Excel5');

        if($isDown){   //网页下载

            header('pragma:public');

            header('Content-type:application/vnd.ms-excel;charset=utf-8;name="'.$fileName.'.xls"');
            header("Content-Disposition:attachment;filename=$fileName.xls");

            $objWrite->save('php://output');exit;

        }

    }
    //nature index 论文管理导出表格
    public function  export_nature($title=array(), $data=array(), $fileName='', $savePath='./', $isDown=false)
    {

        $this->load->library('PHPExcel');
        $obj=new PHPExcel();

        //横向单元格标识
        $cellName = array('A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O','P');

        $obj->getActiveSheet(0)->setTitle('sheet');   //设置sheet名称
        $_row = 1;   //设置纵向单元格标识

        if($title){
            $i = 0;
            foreach($title AS $v){   //设置列标题
                $obj->setActiveSheetIndex(0)->setCellValue($cellName[$i].$_row, $v);
                $i++;
            }
            $_row++;
        }
        //设置表格宽度
        $obj->getActiveSheet()->getColumnDimension('A')->setWidth(70);
        $obj->getActiveSheet()->getColumnDimension('B')->setWidth(15);
        $obj->getActiveSheet()->getColumnDimension('C')->setWidth(40);
        $obj->getActiveSheet()->getColumnDimension('D')->setWidth(40);
        $obj->getActiveSheet()->getColumnDimension('E')->setWidth(15);
        $obj->getActiveSheet()->getColumnDimension('F')->setWidth(15);
        $obj->getActiveSheet()->getColumnDimension('G')->setWidth(10);
        $obj->getActiveSheet()->getColumnDimension('H')->setWidth(5);
        $obj->getActiveSheet()->getColumnDimension('I')->setWidth(5);
        $obj->getActiveSheet()->getColumnDimension('J')->setWidth(5);
        $obj->getActiveSheet()->getColumnDimension('K')->setWidth(15);
        $obj->getActiveSheet()->getColumnDimension('L')->setWidth(10);
        $obj->getActiveSheet()->getColumnDimension('M')->setWidth(25);
        $obj->getActiveSheet()->getColumnDimension('N')->setWidth(10);
        $obj->getActiveSheet()->getColumnDimension('O')->setWidth(12);
        $obj->getActiveSheet()->getColumnDimension('P')->setWidth(70);

        //填写数据
        if($data){
            $i = 0;
            foreach($data AS $v){
                //  $title=array('论文标题','第一作者','通讯作者','全部作者','第一单位','发表期刊','发表时间','卷','期','页','作者工号','作者姓名','作者学院/单位','添加时间','审核状态','审核意见');

                $obj->getActiveSheet(0)->setCellValue($cellName[0].($i+$_row),$v['title']);
                $obj->getActiveSheet(0)->setCellValue($cellName[1].($i+$_row),$v['first_author']);
                $obj->getActiveSheet(0)->setCellValue($cellName[2].($i+$_row),$v['corresponding_author']);
                $obj->getActiveSheet(0)->setCellValue($cellName[3].($i+$_row),$v['whole_author']);
                $obj->getActiveSheet(0)->setCellValue($cellName[4].($i+$_row),$v['first_unit']);
                $obj->getActiveSheet(0)->setCellValue($cellName[5].($i+$_row),$v['periodical']);
                $obj->getActiveSheet(0)->setCellValue($cellName[6].($i+$_row),date('Y-m-d',$v['publish_time']));//处理
                $obj->getActiveSheet(0)->setCellValue($cellName[7].($i+$_row),$v['roll']);
                $obj->getActiveSheet(0)->setCellValue($cellName[8].($i+$_row),$v['period']);
                $obj->getActiveSheet(0)->setCellValue($cellName[9].($i+$_row),$v['page']);
                $obj->getActiveSheet(0)->setCellValue($cellName[10].($i+$_row),$v['job_number']);
                $obj->getActiveSheet(0)->setCellValue($cellName[11].($i+$_row),$v['name']);
                $obj->getActiveSheet(0)->setCellValue($cellName[12].($i+$_row),$v['academy']);
                $obj->getActiveSheet(0)->setCellValue($cellName[13].($i+$_row),date('Y-m-d',$v['add_time']));//处理
                if($v['status']=='1')
                {
                    $status="通过审核";
                }elseif($v['status']=='0')
                {
                    $status="正在审核";
                }else{
                    $status="未通过审核";
                }
                $obj->getActiveSheet(0)->setCellValue($cellName[14].($i+$_row),  $status);//处理
                $obj->getActiveSheet(0)->setCellValue($cellName[15].($i+$_row),$v['comment']);

                $i++;

            }

        }

       // p($data);die;
        //文件名处理

        if(!$fileName){

            $fileName = time();

        }

        $objWrite = PHPExcel_IOFactory::createWriter($obj, 'Excel5');

        if($isDown){   //网页下载

            header('pragma:public');

            header('Content-type:application/vnd.ms-excel;charset=utf-8;name="'.$fileName.'.xls"');
            header("Content-Disposition:attachment;filename=$fileName.xls");

            $objWrite->save('php://output');exit;

        }
    }
}