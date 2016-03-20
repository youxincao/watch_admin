<?php
namespace Home\Controller;
use Think\Controller;
class IndexController extends Controller {
    public function index(){
        $this->show();
    }

    public function device_record(){
        $devices = M('device')->select();
        $this->assign('devices', $devices)->display('device_record');
    }

    public function record_info(){
        $records = M('record')->select();
        $this->assign('records', $records)->display('record_info');
    }

    public function upload(){

        $upload = new \Think\Upload();// 实例化上传类j
        $upload->maxSize = 3145728;
        $upload->exts    = array("xls", "xlsx");
        $upload->rootPath = '/tmp/';

        $info = $upload->upload();

        if( !$info ){
            $this->error($upload->getError());
        }else{
            foreach ($info as $file) {
                $filepath = "/tmp/".$file["savepath"].$file["savename"];

                $this->import_device_from_excel($filepath);
            }
        }
    }

    private function import_device_from_excel($filePath){
        vendor("PHPExcel");

        $PHPExcel = \PHPExcel_IOFactory::load($filePath);

        // 读取第一个工作表
        $currentSheet = $PHPExcel->getSheet(0);

        $columns = 'D';
        $rows = $currentSheet->getHighestRow();

        $start_row = 1 ;
        for($row_index = 1; $row_index <= $rows; $row_index ++ ){
            $addr = 'A'.$row_index;
            $value = get_cell_value($addr);
            if( $value === '1'){
                $start_row = $row_index;
                break;
            }

        }

        echo $start_row;
        for($row_index = $start_row; $row_index <= $rows; $row_index ++ ){
            for($col_index = 'A'; $col_index <= $columns; $col_index ++ ){
                $addr = $col_index.$row_index;
                $cell = $currentSheet->getCell($addr)->getValue();
                if($cell instanceof PHPEXCEL_RichText)
                    $cell = $cell->_toString();

            }
        }

    }

    private function get_cell_value($addr){
        $cell = $currentSheet->getCell($addr)->getValue();
        if($cell instanceof PHPEXCEL_RichText)
            $cell = $cell->_toString();
        return $cell;
    }
}
