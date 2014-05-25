#!/usr/bin/php
<?php
$lockPath = '/var/vhosts/grant.cpsc319.tk/cron/lock';
$logPath = '/var/vhosts/grant.cpsc319.tk/logs/mail.log';

require_once 'init.php';
//Start calculating time
$startTime = microtime(true);


//Open up lock file
$lockFile = fopen($lockPath, "r+");
if (flock($lockFile, LOCK_EX)) {
    //Send mail
    $mail = new Application_Model_MailQueue();
    $number = $mail->sendMailInQueue();

    if ($number > 0) {
        //Open up file for logging
        $logFile = fopen($logPath, 'a');
        
        //Calculate ending time
        $endTime = microtime(true);
        fwrite($logFile, @date('[d/M/Y:H:i:s]') . ' - ' . $number . ' sent in ' . round(($endTime - $startTime), 3) . 's' . PHP_EOL);
        fclose($logFile);
    }
    fclose($lockFile);
}

