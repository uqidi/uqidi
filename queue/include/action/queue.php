<?php
class Action_Queue extends Action{
    const MAX_ERROR_NUM = 5;    /* 最大失败次数 */
    const MAX_EMPTY_NUM = 30;   /* 最大取空次数 */
    const TIMEOUT_TIME  = 600;  /* 超时时间 */
    public static $signal_killed = false;
    protected $_queue;
    protected $_data;
    protected $_done_num = 0;
    protected $_start_time = 0;
    protected $_end_time = 0;

    public function init(){
        $rs = $this->checkStart();
        if(false == $rs)
            return false;
        $this->_queue = new P_Queue_Rds();
        if(method_exists($this, '_init')){
            $rs = $this->_init();
            if(false === $rs)
                return false;
        }
        return true;
    }
    /**
     * 心跳注册
     * @param $bool
     */
    public static function setTermHandler($bool){
        self::$signal_killed = $bool;
    }

    /**
     * 心跳检查
     */
    public function heartBeat(){
        if(self::$signal_killed){
            T_logger::actionLog("QUEUE_DEBUG", 0, __CLASS__, 'someone killed me'. getmypid());
            exit('signal_killed');
        }
    }

    /**
     * 格式化消息,用于判断取到的小时是否合法
     * @parm bool log_self_class 记录日志到父类日志文件或子类日志文件
     */
    public function formatMessage(){
        if(isset($this->_data['error_num'])){
            (int)$this->_data['error_num']++;
        }else{
            $this->_data['error_num'] = 0;
        }


        if(isset($this->_data['sys_time'])){
            if(time() - $this->_data['sys_time'] > self::TIMEOUT_TIME){
                /* 此处去统一监控队列拥堵情况 */
                T_logger::actionLog("QUEUE_DEBUG", 0, __CLASS__, $this->_queue->getKey().'||TIMEOUT');
            }
        }else{
            $this->_data['sys_time'] = time();
        }

        /* 超过最大失败次数，放弃处理 */
        if($this->_data['error_num'] > self::MAX_ERROR_NUM){
            T_logger::actionLog("QUEUE_DEBUG", 0, __CLASS__, $this->_queue->getKey().'||DEALERR||'.serialize($this->_data));
            return false;
        }
        return true;
    }

    /**
     * 获取mq消息
     * @return bool
     */
    public function getMessage(){
        $value = $this->_queue->get();
        if(isset($value[0])){
            T_logger::actionLog("QUEUE_SOURCE", 0, __CLASS__, $this->_queue->getKey().'||'. $value);
            return $value;
        }
        return false;
    }

    public function setMessage($value){
        if(is_array($value)){
            $value = serialize($value);
        }
        return $this->_queue->set($value);
    }

    public function setServer($server){
        return $this->_queue->setServer($server);
    }

    public function getServer(){
        return $this->_queue->getServer();
    }

    public function setKey($key){
        return $this->_queue->setKey($key);
    }

    public function getKey(){
        return $this->_queue->getKey();
    }

    public function setRunNum(){
        $this->_done_num++;
        return $this->_done_num;
    }

    public function resetRunNum(){
        $this->_done_num = 0;
    }
    /**
     * 检查开始
     * @return bool
     */
    public function checkStart(){
        $ps_cmd = implode(' ', $this->getParam());
        $ps_wc = T_Cmd::getPsCnt($ps_cmd);
        $_SERVER['REQUEST_URI'] = implode('/', $this->getParam());
        if($ps_wc>1){
            T_logger::actionLog("QUEUE_DEBUG", 0, __CLASS__, 'script already run');
            return false;
        }
        return true ;
    }

    public function run(){
        while ($this->_queue->connect()){
            $this->resetRunNum();
            while (true){
                $this->heartBeat();
                $message_data = $this->getMessage();
                $message_data = unserialize($message_data);

                /* 取不到数据超过次数时退出 */
                if(!$message_data || !is_array($message_data)){
                    /* 取空n次推出 */
                    if($this->setRunNum() > self::MAX_EMPTY_NUM){
                        T_logger::actionLog("QUEUE_DEBUG", 0, __CLASS__, 'empty to exit');
                        break;
                    }
                    sleep(1);
                    echo $this->_done_num."\n";
                    continue;
                }else{
                    $this->_data = $message_data;
                    //数据校验通过后，执行应用逻辑
                    if(true == $this->formatMessage()){
                        $this->run_job($this->_data);
                    }
                }
            }
        }
    }
}