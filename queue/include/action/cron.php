<?php
class Action_Cron extends Action{

    public function init(){
        return $this->checkStart();
    }
    /**
     * 检查开始
     * @return bool
     */
    public function checkStart(){
        $ps_cmd = implode(' ', $this->getParam());
        $ps_wc = T_Cmd::getPsCnt($ps_cmd);

        if($ps_wc>1){
            T_logger::actionLog("CRON_DEBUG", 0, __CLASS__, 'script already run');
            return false;
        }
        return true ;
    }
}