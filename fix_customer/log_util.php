<?php

class log_util {
    
    private static function _log($file, $msg) {

        date_default_timezone_set("Asia/Jakarta");
        $msg = strtoupper(date('Y-m-d h:i:sa')).'>'.$msg."\n";    	    	
        file_put_contents($file, $msg, FILE_APPEND);
        //log_util::_log_app($msg);        

    }

    
    public static function error($msg) {
    
        $file = 'error.log';
        log_util::_log($file, $msg);
               
    }
    
    public static function info($msg) {        

        $file = 'info.log';    
        log_util::_log($file, $msg);        

    }
    
    
    private static function _log_app($msg) {
        
        $file = 'app.log';
        file_put_contents($file, $msg, FILE_APPEND);  
    }


    public static function get_params($class, $method) {
        $reflector = new ReflectionClass($class);
        $params = $reflector->getMethod($method)->getParameters();
        return $params;
    }

    public static function log_in($class, $method, $args) {

        $str = "[INPUT][CLASS]=[$class]:[METHOD]=[$method]\n";      
        $str.= "[PARAMS]=\n";        
        foreach($args as $key=>$value) {
            if(is_object($value)) {
                $str.="[$key]=".print_r($value, true);
            }
            else {
                $str.="[$key]=[$value]\n";    
            }
        }  

        log_util::info($str);

    }


    public static function log_out($value) {

        $str = "[RETURN]=";
        if(is_object($value)) {
            $str.=print_r($value,true);    
        }
        else if(is_array($value)) {
            $str.=print_r($value,true);       
        }
        else {
            $str.=$value;
        }
        log_util::info($str);
    }


    public static function log_method($method) {
        log_util::info("[METHOD]=[$method]");
    }


}


?>
