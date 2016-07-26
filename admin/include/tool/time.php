<?php
class Tool_Time{
	public static function time_ago($cur_time, $time=0){
		!$time ? $time = time(): '';
		$agoTime = $time - $cur_time;
	    if($agoTime <= 60){
	        return $agoTime.'秒前';
	    }elseif($agoTime <= 3600 && $agoTime > 60){
	        return intval($agoTime/60) .'分钟前';
	    }elseif (date('d', $cur_time) == date('d', $time) && $agoTime > 3600){
			return '今天 '.date('H:i',$cur_time);
	    }elseif(date('d',$cur_time+86400) == date('d',$time) && $agoTime < 172800){
			return '昨天 '.date('H:i',$cur_time);
	    }else{
	        return date('Y年m月d日 H:i',$cur_time);
	    }
	}
}
?>