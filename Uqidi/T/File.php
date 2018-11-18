<?php
/**
 * @fileoverview:   File
 * @author:         Uqidi
 * @date:           2015-11-15
 * @copyright:      uqidi.com
 */
class T_File {
    /**
     * 覆盖方式保存字符串到文件
     * @author Uqidi
     * @param string $file
     * @param string $string
     * @return bool|int
     */
	static public function save($file, $string) {
		self::check_path ( $file );
		$fp = fopen ( $file, 'w' );
		if(!$fp)
			return false;
		$r = fwrite ( $fp, $string );
		fclose ( $fp );
		return $r;
	}

    /**
     * 追加方式保存字符串到文件
     * @param string $file
     * @param string $string
     * @return bool|int
     */
	static public function save_a($file, $string) {
		self::check_path ( $file );
		$fp = fopen ( $file, 'a');
		if(!$fp)
			return false;
		$r = fwrite ( $fp, $string );
		fclose ( $fp );
		return $r;
	}

    /**
     * 检查路径
     * $author Uqidi
     * @param string $filepath
     * @param string $user
     * @param string $group
     * @return bool
     */
	static  public function check_path($filepath, $user='www', $group='www') {
		$filedir = dirname ($filepath);
		if (!is_dir($filedir)) {
			$rs = mkdir($filedir, 0755, true);
            if($rs){
                @chown($filedir, "www");
                @chgrp($filedir, "www");
                return true;
            }
            return false;
		}
        return true;
	}

    /**
     * 下载文件
     * @author Uqidi
     * @param $path
     */
    static public function down($path){
		if(!file_exists($path))   {
			exit($path.' file not exists!');
		}else{
			$file = fopen($path,"r");
			$file_name = substr($path, strrpos($path, '/')+1);
			$filesize = filesize($path);
			Header("Content-type: application/octet-stream");
			Header("Accept-Ranges: bytes");
            Header("Content-Length: ".$filesize);
			Header("Content-Disposition: attachment; filename=" . $file_name);
			echo fread($file, $filesize);
			fclose($file);
			exit();
		}
	}
}
