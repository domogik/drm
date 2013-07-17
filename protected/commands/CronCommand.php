<?php

/**
 * Send mail method
 */
function sendMail($email,$subject,$message) {
	$adminEmail = Yii::app()->params['adminEmail'];
	$headers = "MIME-Version: 1.0\r\nFrom: $adminEmail\r\nReply-To: $adminEmail\r\nContent-Type: text/html; charset=utf-8";
	$message = wordwrap($message, 70);
	$message = str_replace("\n.", "\n..", $message);
	return mail($email,'=?UTF-8?B?'.base64_encode($subject).'?=',$message,$headers);
}

class CronCommand extends CConsoleCommand
{
    public function actionEmailLogs()
    {
        $logs = Logs::model()->today()->findAll();
        if (count($logs) > 0) {
            $message = CController::renderInternal(Yii::app()->baseUrl . '/views/site/_emailLogs.php',array('logs'=>$logs), true);
            sendMail(Yii::app()->params['logsEmail'], "DRM actions for " . date("d/m/Y"), $message);
        }
        Yii::app()->end();
    }
 }

?>