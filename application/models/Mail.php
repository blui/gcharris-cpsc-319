<?php

/**
 * Mail system sotrage object. Contains information for a partially built email.
 * The system then saves this object in the database and a cron is used to
 * constract Frp_Mail objects for sending
 *
 * @package    Frp
 * @author     Grant Harris
 * @version    1.0
 */
class Application_Model_Mail {

    var $attachments;
    var $recipients;
    var $message;
    var $mail_merge;
    var $subject;
    var $from_name;
    var $from_email;
    var $uploads;

    const NAME_REPLACE_STRING = '{name}';
    const NULL_NAME_REPLACEMENT = 'Sir/Madam';

    function __construct($subject, $message, $from_name, $from_email, $mail_merge = false, $uploads = array()) {
        $this->attachments = array();
        $this->recipients = array();
        $this->subject = $subject;
        $this->message = $message;
        $this->mail_merge = $mail_merge;
        $this->from_name = $from_name;
        $this->from_email = $from_email;
        $this->uploads = $uploads;
    }

    /**
     * Add a recipient to the mail object
     * @param string $name
     * @param string $email
     */
    public function addRecipient($name, $email) {
        $this->recipients[] = array('name' => $name, 'email' => $email);
    }

    private function attachments() {
        $mail_attachments = new Application_Model_DbTable_MailAttachment();

        foreach ($this->uploads as $upload) {
            $path = APPLICATION_PATH . "/../tmp/" . $upload;
            $attachment_info = $mail_attachments->getAttachmentInfo($upload);
            $at = new Zend_Mime_Part(file_get_contents($path));
            $at->type = $attachment_info['type'];
            $at->disposition = Zend_Mime::DISPOSITION_INLINE;
            $at->encoding = Zend_Mime::ENCODING_BASE64;
            $at->filename = $attachment_info['name'];

            $this->attachments[] = $at;
           
            //We've added the files, delete now
            $mail_attachments->deleteSelected();
            unlink($path);
        }
    }

    /**
     * Run by the cron job. Builds Frp_Mail objects, mail merges, and sends to
     * listed recipients
     * @return int total number of mails sent
     */
    public function buildAndSendMail() {

        $sent = 0;

        $this->attachments();

        foreach ($this->recipients as $recipient) {
            $mail = new Frp_Mail('UTF-8');

            $mail->setSubject($this->subject);
            $mail->setFrom($this->from_email, $this->from_name);
            $mail->addTo($recipient['email']);

            //Do name replacemnts if the flag is set
            $message = ($this->mail_merge == true) ? $this->mailMerge($recipient['name']) : $this->message;

            $mail->setBodyHtml($message);

            //Add any attachments
            foreach ($this->attachments as $attachment) {
                $mail->addAttachment($attachment);
            }

            $mail->send();
            $sent++;
        }
        return $sent;
    }

    /**
     * Replace any occurances of self::NAME_REPLACE_STRING with $recipient_name
     * @param string $recipient_name
     * @return string mail merged message
     */
    private function mailMerge($recipient_name) {
        if ($recipient_name == null) {
            $recipient_name = self::NULL_NAME_REPLACEMENT;
        }
        return str_replace(self::NAME_REPLACE_STRING, $recipient_name, $this->message);
    }

}

