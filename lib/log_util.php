<?php

class log_util {
    
    private static $log_file='';

    public static function set_log_file($log_file) {
        date_default_timezone_set("Asia/Jakarta");
        log_util::$log_file = $log_file;
        //file_put_contents(log_util::$log_file, '');
    }   


    public static function write($msg) {
        $msg = strtoupper(date('Y-m-d h:i:sa')).'>'.$msg."\n";              
        //file_put_contents(log_util::$log_file, $msg, FILE_APPEND);
        echo $msg;
    }
    


}


?>
