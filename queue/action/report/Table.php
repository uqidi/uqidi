<?php

/**
 * /usr/local/Cellar/php56/5.6.16/bin/php  index.php  report Table game@Game
 * Class TableAction
 */
class TableAction extends Action_Cron{
    public function run(){
        $model_name = $this->getParam(3);
        if(empty($model_name)){
            echo "model_name is must\n";
            return false;
        }

        if($model_name){
            $rs = $this->_createTableByDay($model_name);
            if($rs){
                echo "create table '$model_name' succ\n";
                return true;
            }

            echo "create table '$model_name' fail\n";
            return true;
        }else{
            $this->_createTableByHash($model_name);
        }
    }

    private function _createTableByHash($model_name){
        $config = Loader::modelConfig($model_name);
        $cnt = $config['tb_n'];

        for($i=0;$i<$cnt;$i++){
            $p = sprintf("%02s", dechex($i));
            $table_name = $config['tb_prefix'].'_'.$p;
            $sql[] = $this->_makeSql($table_name, $config);
            if(false === $sql)
                return false;
        }

        T_File::save(SYS_DATA_PATH.'game.txt', implode(";\n", $sql).";\n");

    }

    /**
     * 创建表 按照天建表
     * @param $model_name
     * @return bool
     */
    private function _createTableByDay($model_name) {
        $off = (int)$this->getParam(4);

        $off = intval($off);
        $today = date('Ymd', NOW_TIME+$off*86400);

        $config = Loader::modelConfig($model_name);
        $table_name = $config['tb_prefix'].'_'.$today;

        $sql = $this->_makeSql($table_name, $config);
        if(false === $sql)
            return false;

        C('checkField', false);
        $Model = Loader::model($model_name);

        $result = array();
        $rs = $Model->query("SHOW TABLES LIKE '$table_name'", $result);
        if(false === $rs)
            return false;

        if(count($result)>0)
            return true;

        $rs = $Model->execute($sql);
        C('checkField', true);
        return $rs;
    }

    private function  _makeSql($table_name, $config){
        $table_config = Loader::loadConfig('table', $config['model']['name']);
        if(empty($table_config)){
            return false;
        }

        $sql = "CREATE TABLE IF NOT EXISTS `" . $table_name . "` (";
        $fields  = $table_config['fields'];

        if(isset($table_config['keys']) && is_array($table_config['keys'])) {
            $fields = array_merge($fields, $table_config['keys']);
        }

        $sql .= implode(',', $fields).")".$table_config['options'];
        return $sql;
    }

}