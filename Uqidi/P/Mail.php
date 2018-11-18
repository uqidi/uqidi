<?php
/**
 * @fileoverview:   MAIL插件
 * @author:         Uqidi
 * @date:           2015-11-15
 * @copyright:      Uqidi.com
 */
require_once(dirname(__FILE__) . '/PhpMailer/class.phpmailer.php');
class P_Mail{
    private static $_Mail = null;
    private static $_Inst = null;
    private $error = array();
    static public function instance($config=array()){
        if(!self::$_Inst)
            self::$_Inst = new self($config);
        return self::$_Inst;
    }

    private function __construct($config=array()){
        self::$_Mail = new PHPMailer(true);
        $sconfig = T_FileData::get('Setup_mail');
        if(is_array($sconfig)){
            $config = array_merge($sconfig, $config);
        }
        $this->setConfig($config);
    }

    /**
     * 设置配置
     * @param array $config
     * @return $this
     */
    public function setConfig($config=array()){
        $param = array(
            'mail_lang'     => 'zh_cn',         /* 语言 */
            'mail_auth'     => true,            /* 开启邮件服务器验证 */
            'mail_port'     => '25',            /* 端口 */
            'charset'       => 'utf-8',         /* 字符编码 */
            'encoding'      => 'base64',        /* 加密编码 */
            'is_html'       => true,            /* 是否是html*/
            'alt_body'      => '如果要查看此邮件，请使用HTML兼容的电子邮件阅读器',
        );

        if(empty($config['mail_server'])){
            $this->setError('服务器 必须');
            return false;
        }

        if(empty($config['mail_user'])){
            $this->setError('邮件服务器的用户名 必须');
            return false;
        }

        if(empty($config['mail_password'])){
            $this->setError('邮件服务器的密码 必须');
            return false;
        }

        if(empty($config['mail_from'])){
            $this->setError('服务器上发送此邮件的邮箱 必须');
            return false;
        }


        $config = array_merge($param, $config);
        self::$_Mail->IsSMTP();
        self::$_Mail->SetLanguage($config['mail_lang']);
        self::$_Mail->SMTPAuth  = $config['mail_auth'];
        self::$_Mail->Port      = $config['mail_port'];
        self::$_Mail->CharSet   = $config['charset'];
        self::$_Mail->Encoding  = $config['encoding'];
        self::$_Mail->IsHTML($config['is_html']);
        self::$_Mail->AltBody = $config['alt_body'];

        self::$_Mail->Host      = $config['mail_server'];       /* 服务器 */
        self::$_Mail->Username  = $config['mail_user'];			/* 邮件服务器的用户名 */
        self::$_Mail->Password  = $config['mail_password'];		/* 邮件服务器的密码 */
        self::$_Mail->From      = $config['mail_from'];         /* 服务器上发送此邮件的邮箱，与self::$_Mail->Username 的值是对应的 */


        self::$_Mail->FromName  = isset($config['mail_from_name']) ? $config['mail_from_name'] : Config_mail::MAIL_FROM_USER;		 /* 真实发件人的姓名等信息 */

        if(empty($config['mail_reply']) && isset($config['mail_reply_name'])){
            self::$_Mail->AddReplyTo($config['mail_reply'], $config['mail_reply_name']);
        }

        return $this;
    }

    /**
     * 单条发送
     * @param $email
     * @param string $subject
     * @param string $body
     * @param string $name
     * @param bool $is_html
     * @return bool
     */
    public function send($email, $subject='', $body='', $name='', $is_html=true){
        if(!empty($this->error))
            return false;
        self::$_Mail->Subject = $subject;
        self::$_Mail->Body    = $body;
        try{
            self::$_Mail->IsHTML($is_html);
            self::$_Mail->AddAddress($email, $name);
            self::$_Mail->Send();
            return true;
        }catch (phpmailerException $e){
            $this->setError('邮件发送失败，错误信息：'.self::$_Mail->ErrorInfo);
            T_Logger::monitorLog(__CLASS__, __FUNCTION__.':'.$this->getError(true));
            return false;
        }
        return false;
    }

    /**
     * 批量发送
     * @param $emails
     * @param string $subject
     * @param string $body
     * @param bool $is_html
     * @return bool
     */
    public function sendBatch($emails, $subject='', $body='', $is_html=true){
        if(!empty($this->error))
            return false;
        self::$_Mail->Subject = $subject;
        self::$_Mail->Body    = $body;

        try{
            self::$_Mail->IsHTML($is_html);
            foreach($emails as $email){
                if(is_array($email))
                    self::$_Mail->AddAddress($email[0], $email[1]);
                else
                    self::$_Mail->AddAddress($email);
            }
            self::$_Mail->Send();
            return true;
        }catch (phpmailerException $e){
            $this->setError('邮件发送失败，错误信息：'.self::$_Mail->ErrorInfo);
            T_Logger::monitorLog(__CLASS__, __FUNCTION__.':'.$this->getError(true));
            return false;
        }
    }

    public function getError($toString=false){
        return $toString ? implode(',', $this->error) : $this->error;
    }

    public function setError($error){
        $this->error[] = $error;
    }

}

