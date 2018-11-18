<?php
/**
 * @fileoverview:   EXCEL插件
 * @author:         Uqidi
 * @date:           2015-11-15
 * @copyright:      Uqidi.com
 */

require_once(dirname(__FILE__) . '/Excel/PHPExcel.php');
class P_Excel{

    private static $_instances;

    public static function getInstance(){

        if(!is_resource(self::$_instances)){
            self::$_instances = new PHPExcel();
        }
        return self::$_instances;
    }

    /**
     *
     * @param $path
     * @param array $field
     * @return array|bool
     */
    public static function getDataByPath($path, $field=array()){
        if(!is_file($path)){
            return false;
        }
        try{
            $objPHPExcel = PHPExcel_IOFactory::load($path);
            $sheetData = $objPHPExcel->getActiveSheet()->toArray(null,true,true,true);
            if(empty($field))
                $is_field = false;
            else
                $is_field = true;
            foreach($sheetData as $v){
                if($is_field)
                    $data[] = array_combine($field, $v);
                else
                    $data[] = $v;
            }
            return $data;
        }catch (PHPExcel_Reader_Exception $e){
            return false;
        }

    }


    /**
     * 下载
     * @param array $data
     * @param array $header
     * @param array $fields
     * @param string $title
     * @param array $widths
     * @return bool
     */

    public static function down($data=array(),  $header=array(), $fields=array(), $title='Simple', $widths=array()){

        if(!$data || !$header || !$fields){
            return false;
        }

        $len = count($header);
        for($i=0;$i<$len;$i++){
            $cols[] = chr(65+$i);
            $cols_title[] = chr(65+$i).'1';
        }

        $cols_name = array_combine($cols_title, $header);
        $rows_name = array_combine($cols, $fields);
        if($widths){
            $style_width = array_combine($cols, $widths);
        }else{
            foreach($cols as $k=>$v){
                $style_width[$v] = 20;
            }
        }

        $objPHPExcel =  self::getInstance();


        /* Set document properties */
        $objPHPExcel->getProperties()->setCreator("Maarten Balliauw")
            ->setLastModifiedBy("Maarten Balliauw")
            ->setTitle("Office 2007 XLSX Test Document")
            ->setSubject("Office 2007 XLSX Test Document")
            ->setDescription("Test document for Office 2007 XLSX, generated using PHP classes.")
            ->setKeywords("office 2007 openxml php")
            ->setCategory("Test result file");


        $styleArray1 = array(
            'font' => array(
                'bold' => true,
                'size' => 12,
                'color' => array(
                    'argb' => '00000000',
                ),
            ),
            'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
            ),
        );


        foreach($cols_title as $k=>$v){
            $objPHPExcel->getActiveSheet()->getStyle($v)->applyFromArray($styleArray1);
        }

        /* 设置宽度 */
        foreach($style_width as $k=>$v){
            $objPHPExcel->getActiveSheet()->getColumnDimension($k)->setWidth($v);
        }


        /* Add some data 添加一些数据 */
        foreach($cols_name as $k=>$v){
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue($k, $v);
        }


        /* Miscellaneous glyphs, UTF-8 各种各样的符号,utf - 8 */
        $i = 2;
        foreach ($data as $key => $value){
            $n = $key+$i;
            foreach($rows_name as $k=>$v){
                $val = isset($value[$v]) ? $value[$v] : null;
                $objPHPExcel->setActiveSheetIndex(0)->setCellValue($k.$n, ' '.$val.' ');
            }
        }


        /* Redirect output to a client’s web browser (Excel5) */
        $fileName = iconv("utf-8", "gb2312", $title);
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="'.$fileName.'.xls"');
        header('Cache-Control: max-age=0');

        /* If you're serving to IE 9, then the following may be needed */
        header('Cache-Control: max-age=1');

        /* If you're serving to IE over SSL, then the following may be needed */
        header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');               /* Date in the past */
        header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT');  /* always modified */
        header('Cache-Control: cache, must-revalidate');                /* HTTP/1.1 */
        header('Pragma: public');                                       /* HTTP/1.0 */

        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');

        $objWriter->save('php://output');
        exit;
    }

}