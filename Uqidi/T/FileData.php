<?php
/**
 * @fileoverview:   FileData
 * @author:         Uqidi
 * @date:           2015-11-15
 * @copyright:      uqidi.com
 */
class T_FileData {
	private static $_DATA = array();
    /**
     * 获取数据
     * @param $file
     * @param bool $reload
     * @return mixed
     */
	public static function get($file, $reload=false) {
        if(!$reload && isset(self::$_DATA[$file])){
            return self::$_DATA[$file];
        }

        $explode = explode('_', $file);
        $file_name = array_pop($explode);
        $dir = '';
        if(!empty($explode)){
            $dir = implode('/', $explode).'/';
        }
        $file_name = $file_name.'.php';

        $file_path = SYS_DATA_PATH.$dir.$file_name;

        if(!is_file($file_path))
            return false;

         self::$_DATA[$file] = include($file_path);

        return self::$_DATA[$file];
	}

    /**
     * 设置数据
     * @param $file
     * @param array $data
     * @return bool|int
     */
    public static function set($file, $data=array()) {
        if(empty($data))
            return false;

        $explode = explode('_', $file);
        $file_name = array_pop($explode);
        $dir = '';
        if(!empty($explode)){
            $dir = implode('/', $explode).'/';
        }
        $file_name = $file_name.'.php';

        $file_path = SYS_DATA_PATH.$dir.$file_name;

		$content = self::data_to_php($data);

		return T_File::save($file_path, $content);
	}

    /**
     * 删除数据文件
     * @param $file
     * @return bool|int
     */
    public static function delete($file) {
        if(empty($data))
            return false;

        $explode = explode('_', $file);
        $file_name = array_pop($explode);
        $dir = '';
        if(!empty($explode)){
            $dir = implode('/', $explode).'/';
        }
        $file_name = $file_name.'.php';

        $file_path = SYS_DATA_PATH.$dir.$file_name;

        return unlink($file_path);
    }


    public static function data_to_php($data){
		return "<?php\r\nreturn " . var_export($data, true) . ";\r\n?>";
	}
}
