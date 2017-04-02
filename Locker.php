<?php
/**
 * Created by PhpStorm.
 * User: dh
 * Date: 02.04.17
 * Time: 15:09
 */

namespace yii\helpers;

use Yii;

class Locker
{
    public static function lock($name="process",$debug=false) {
        $lock = sys_get_temp_dir()."/$name.lock";
        $aborted = file_exists($lock) ? filemtime($lock) : null;
        $fp = fopen($lock, 'w');

        if (!flock($fp, LOCK_EX|LOCK_NB)) {
            // Unable to lock file, because there's one more copy of the process
            if(YII_DEBUG or $debug)
            {
                YII::warning(sprintf("Already running %s", date('c', $aborted)),'locker-lock');
            }
            return false;
        }
        // File blocked

        // If file exists than someone killed the process
        if ($aborted && (YII_DEBUG or $debug)) {
            YII::error(sprintf("The previous process was killed %s", date('c', $aborted)),'lock');
        }

        // Removing blocking on the end of the process
        register_shutdown_function(function() use ($fp, $lock) {
            flock($fp, LOCK_UN);
            fclose($fp);
            unlink($lock);
        });

        return true;
    }
}