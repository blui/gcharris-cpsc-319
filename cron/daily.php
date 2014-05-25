#!/usr/bin/php
<?php

require_once 'init.php';

$session = new Application_Model_ProgramSession();
$session->closeOpenSessions();

$attachments = new Application_Model_MailQueue();
print_r($attachments->deleteDaysOld(1));

$auth = new Application_Model_Auth();
$auth->deleteResetEmailDaysOld(1);


?>
