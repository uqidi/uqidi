<?php
/********************************************************************
 *
 * @fileoverview:   验证码
 * @author:         Uqidi
 * @date:           2016-03-29
 * @copyright:      Uqidi.com
 *
 ********************************************************************
 * 图片验证码
 *  验证 T_Captcha::check_image_code('ff95',  key);
 *  销毁 T_Captcha::destroy_image_code(key);
 *  获取 T_Captcha::get_image_code(key);
 *******************************************************************/

class T_Captcha{

    public static function send_mail_code($email, $type, $data=array(), $tpl=array()){
        if(empty($tpl)){
            $tpl = T_FileData::get('MailTpl_'.$type);
            if(!$tpl)
                return false;
        }
        $code = self::get_mail_code($email, $type, $tpl['expires'], array('uid'=>$data['uid']));
        if(false === $code)
            return false;
        $data['code']       = $code;
        $data['expires']    = ceil($tpl['expires']/3600);
        $Mail = P_Mail::instance();

        $content = self::make_mail_content($tpl['content'], $data);
        return $Mail->send($email, $tpl['subject'], $content);
    }

    public static function get_mail_code($email, $type, $expires=0, $data=array()){
        $code = T_String::random(6, '0123456789');
        $Mcd = K_Cache::getInstance(C_Mcd::SERVER_DEFAULT, K_Cache::CLASS_MCD);
        $data['code'] = $code;
        $rs = $Mcd->set(C_Mcd::PRE_CAPTCHA_CODE.$type.'_'.$email, serialize($data), $expires);
        if(!$rs)
            return false;
        return $code;
    }

    public static function check_mail_code($email, $type, $code, &$data=array()){
        $Mcd = K_Cache::getInstance(C_Mcd::SERVER_DEFAULT, K_Cache::CLASS_MCD);
        $rs = $Mcd->get(C_Mcd::PRE_CAPTCHA_CODE.$type.'_'.$email);
        if(!$rs)
            return false;
        $data = unserialize($rs);
        if(!isset($data['code']) || $code !== $data['code']){
            return false;
        }
        return true;
    }

    public static function destroy_mail_code($email, $type){
        $Mcd = K_Cache::getInstance(C_Mcd::SERVER_DEFAULT, K_Cache::CLASS_MCD);
        return $Mcd->delete(C_Mcd::PRE_CAPTCHA_CODE.$type.'_'.$email);
    }

    public static function make_mail_content($content, $data=array()){
        extract($data);
        $body = '';
        eval("\$body = \"$content\";");
        return $body;
    }

    /**
     * 检查图片验证码
     * @param $code
     * @param $type
     * @param bool $is_clean
     * @return bool
     */
    public static function check_image_code($code, $type, $is_clean=true){
        if(empty($code))
            return false;
        $vcode = P_Session::getInstance()->get('verify_' . $type);
        if(strtolower($vcode) != strtolower($code))
            return false;
        if($is_clean)
            self::destroy_image_code($type);
        return true;
    }

    /**
     * 销毁图片验证码
     * @param $type
     * @return mixed
     */
    public static function destroy_image_code($type){
        return P_Session::getInstance()->unregister('verify_'.$type)->save();
    }

    /**
     * 获取图片验证码
     * @param string $type
     * @param array $config
     * @return bool
     */
    public static function get_image_code($type='login',  $config=array()){
        $length     = isset($config['length']) && $config['length']>4           ? $config['length'] : 4;
        $width      = isset($config['width'])  && $config['width']>0            ? $config['width']  : 105;
        $height     = isset($config['height']) && $config['height']>0           ? $config['height'] : 40;
        $chars      = isset($config['chars'])  && isset($config['chars'][0])    ? $config['chars']  : '0123456789';

        $code = T_String::random($length, $chars);
        $Session = P_Session::getInstance();
        $Session->set('verify_'.$type, $code);
        $Session->save();
        return self::_output_image_code($code, $width, $height);
    }

    /**
     * 输出验证码图片
     * @param $code
     * @param $width
     * @param $height
     * @return bool
     */
    private static function _output_image_code($code, $width, $height){
        header('Content-type: image/jpeg');
        $ffiles[0] = UQIDI_PATH . 'Font/bookos.ttf';
        $ffiles[1] = UQIDI_PATH . 'Font/cour.ttf';
        $ffiles[2] = UQIDI_PATH . 'Font/gothic.ttf';
        $ffiles[3] = UQIDI_PATH . 'Font/georgia.ttf';

        //create image
        $img = imagecreatetruecolor($width, $height);
        imagefill($img, 0, 0, imagecolorallocate($img, 255, 255, 255));
        //get the index or the closest value of assigned color
        $colors[0] = imagecolorresolve($img, 255, 255, 255); //white
        $colors[1] = imagecolorresolve($img, 0, 0, 0); //black
        $colors[2] = imagecolorresolve($img, 9, 9, 53);
        $colors[3] = imagecolorresolve($img, 53, 9, 9);
        $colors[4] = imagecolorresolve($img, 10, 53, 10);
        $colors[5] = imagecolorresolve($img, 53, 52, 58);
        $colors[6] = imagecolorresolve($img, 41, 39, 29);
        $colors[7] = imagecolorresolve($img, 41, 44, 14);
        $colors[8] = imagecolorresolve($img, 16, 51, 54);
        $colors[9] = imagecolorresolve($img, 34, 54, 27);
        $colors[10] = imagecolorresolve($img, 71, 33, 16);
        $x = 8;
        $y = 30;
        $fakeimg = imagecreate($width, $height);
        //draw some confusion lines
        for ($i = 0; $i < strlen($code); $i++){
            imagesetthickness($img, 3 * $i);
            $line_color = imagecolorallocate($img, rand(150, 255), rand(150, 255), rand(150, 255));
            //draw a line
            imageline($img, rand()%$width, rand()%$height, rand()%$width, rand()%$height, $line_color);
        }
        $angle_rand = mt_rand(1,9)*100;
        for ($i = 0; $i < strlen($code); $i++){
            $size = self::_getrand(18, 25);
            $angle = self::_getrand(-1000+$angle_rand, 1000+$angle_rand) * M_PI / 180;
            $nFont = self::_getrand(0, count($ffiles) - 1);
            //can not user colors[0](white), number won't be shown well in white
            $nColor = self::_getrand(1, count($colors) - 1);
            $lastpos = array_fill(0, 7, 0);
            //draw virtually
            $lastpos = imagettftext($fakeimg, $size, $angle, 0, 0, 0, $ffiles[$nFont], $code[$i]);
            if ($lastpos[0] > $lastpos[6]){
                $leftlean = TRUE;
            }else{
                $leftlean = FALSE;
            }
            $drift_x = $leftlean ? $lastpos[0] - $lastpos[6] : 0;
            //draw a real number of pincode
            $lastpos = imagettftext($img, $size, $angle, $x, $y, $colors[$nColor], $ffiles[$nFont], $code[$i]);
            //$x += $leftlean ? $lastpos[2] - $lastpos[6] : $lastpos[4] - $lastpos[0] + 1;
            //$x -= mt_rand(2,5);

            $x_plus = 0;
            if(in_array(strtolower($code[$i]),array('m','w')))
                $x_plus = mt_rand(2,5);
            else if((in_array(strtolower($code[$i]),array('i','j','t'))))
                $x_plus = mt_rand(-3,-1);


            $x += 10+ceil($size/5)+$x_plus;

        }
        for ($i = 0; $i < intval($width * $height / 70); $i++){
            //draw a pixel
            imagesetpixel($img, rand() % $width, rand() % $height, $colors[1]);
        }
        imagejpeg($img);
        imagedestroy($img);
        return true;
    }

    private static  function _getrand($min, $max){
        $n = (double)rand();
        $n = $min + ((double)($max - $min + 1.0) * ($n / (getrandmax() + 1.0)));
        return $n;
    }

}