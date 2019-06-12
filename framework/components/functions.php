<?php
namespace app\components;
use Yii;
class functions {
    public static function recurse_copy($src, $dst) {
        $dir = opendir($src);
        @mkdir($dst);
        while (false !== ($file = readdir($dir))) {
            if (($file != '.') && ($file != '..')) {
                if (is_dir($src . '/' . $file)) {
                    recurse_copy($src . '/' . $file, $dst . '/' . $file);
                }
                else {
                    copy($src . '/' . $file, $dst . '/' . $file);
                }
            }
        }
        closedir($dir);
    }
    public static function deleteDir($dirPath) {
        if (!is_dir($dirPath)) {
            return;
        }
        if (substr($dirPath, strlen($dirPath) - 1, 1) != '/') {
            $dirPath .= '/';
        }
        $files = glob($dirPath . '*', GLOB_MARK);
        foreach ($files as $file) {
            if (is_dir($file)) {
                self::deleteDir($file);
            }
            else {
                unlink($file);
            }
        }
        rmdir($dirPath);
    }
    public static function getdatetime($time = null) {
        if ($time != null) {
            return date('Y-m-d H:i:s', $time);
        }
        else {
            return date('Y-m-d H:i:s');
        }
    }
    public static function getdate($time = null) {
        if ($time == null) {
            $time = time();
        }
        return date('Y-m-d', $time);
    }
    public static function getjdate($time = '') {
        return jdf::jdate('Y/m/d', $time);
    }
    public static function gettime() {
        return date('H:i:s');
    }
    public static function tojdatetime($in_datetime) {
        if (is_string($in_datetime) && strlen($in_datetime) == 19 && $in_datetime != '0000-00-00 00:00:00') {
            return jdf::jdate('Y/m/d H:i:s', strtotime($in_datetime));
        }
        return null;
    }
    public static function tojdate($in_date) {
        if (is_string($in_date)) {
            if (strlen($in_date) > 10) {
                $in_date = substr($in_date, 0, 10);
            }
            if ($in_date != '0000-00-00') {
                return jdf::jdate('Y/m/d', strtotime($in_date));
            }
        }
        return null;
    }
    public static function togdate($in_date) {
        if (is_string($in_date)) {
            if (strlen($in_date) > 10) {
                $in_date = substr($in_date, 0, 10);
            }
            $jdate = explode('/', $in_date);
            if (count($jdate) == 3) {
                return implode('-', jdf::jalali_to_gregorian($jdate[0], $jdate[1], $jdate[2]));
            }
        }
        return null;
    }
    public static function datestring($in_date = null) {
        if (is_null($in_date)) {
            return jdf::jdate('l d F Y');
        }
        else if (is_string($in_date) && strlen($in_date) == 10 && $in_date != '0000-00-00') {
            return jdf::jdate('l d F Y', strtotime($in_date));
        }
        return null;
    }
    public static function httpNotFound($msg = null) {
        throw new \yii\web\NotFoundHttpException($msg ? $msg : Yii::t('app', 'The requested page does not exist.'));
    }
    public static function setSuccessFlash($message = null) {
        Yii::$app->session->setFlash('success', $message ? $message : Yii::t('app', 'Information saved.'));
    }
    public static function setFailFlash($message = null) {
        Yii::$app->session->setFlash('danger', $message ? $message : Yii::t('app', 'Information not saved.'));
    }
    public static function generateConfirmCode() {
        $numbers = range(0, 9);
        shuffle($numbers);
        $code = '';
        for ($index = 0; $index < 6; $index++) {
            $code .= $numbers[$index];
        }
        return $code;
    }
    public static function queryAll($sql) {
        $query = new \yii\db\Query();
        $command = $query->createCommand();
        $command->setSql($sql);
        return $command->queryAll();
    }
    public static function queryOne($sql) {
        $rows = self::queryAll($sql);
        return count($rows) > 0 ? $rows[0] : null;
    }
    public static function query($sql) {
        $query = new \yii\db\Query();
        $command = $query->createCommand();
        $command->setSql($sql);
        return $command->execute();
    }
    public static function number_format($number) {
        return number_format($number, 0, '.', '،');
    }
    public static function toman($number) {
        return self::number_format($number) . ' ' . Yii::t('app', 'Toman');
    }
    public static function getModule($id, $load = true) {
        return Yii::$app->getModule($id, $load);
    }
    public static function log() {
        if (Yii::$app->request->userIP != '::1' && Yii::$app->request->userIP != '2.180.1.3') {
            $action = Yii::$app->controller->action->getUniqueId();
            $ip     = Yii::$app->request->userIP;
            $ips    = explode('.', $ip);
            $space  = '';
            $space2 = '';
            foreach ($ips as &$ip1) {
                if ((int) $ip1 < 10) {
                    $space .= '  ';
                }
                else if ((int) $ip1 < 100) {
                    $space .= ' ';
                }
            }
            if (30 - strlen($action) > 0) {
                for ($i = 0, $j = 30 - strlen($action); $i < $j; $i++) {
                    $space2 .= ' ';
                }
            }
            
            $filename = 'log.txt';
            if ($ip == '212.16.91.10') {
                $filename = 'log_enamad.txt';
            }
            
            file_put_contents($filename, file_get_contents($filename) . "\n"
                    . functions::getdatetime()
                    . " | "
                    . $ip
                    . $space
                    . " | "
                    . $action
                    . $space2
                    . " | "
                    . Yii::$app->request->getUrl()
            );
        }
    }
}