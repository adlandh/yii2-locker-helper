# yii2-locker-helper
Simple file locker for yii2

Usage:

use yii\helpers\Locker;

if (!Locker::lock("some_process")) {
        echo "Process already running";
}