<?php
/**
 * Abstract class to be extended by all Ajax controllers
 * @package    Frp
 * @author     Grant Harris
 * @version    1.0
 * @abstract
 */
abstract class Frp_Controller_AjaxAction extends Frp_Controller_Action {

    const OPERATION_FAILURE = 0;
    const OPERATION_SUCCESS = 1;

    /**
     * We have the pre-dispatch in here, because we want to always run the 
     * authentication page before we do anything else in the controller
     */
    public function preDispatch() {
        $this->setAuthIdentity();
        //Not logged in so we want to abort
        
        if (!$this->authenticatedIdentity) {
            $this->returnJSON(array("type" => "login" , "code" => 1, "message"=> "You are no longer logged in"), 0);
        }
    }

    /**
     * Returns the JSON sent to the server
     * @return type
     */
    public function getJSONRequest() {
        $body = $this->getRequest()->getRawBody();
        return Zend_Json::decode($body);
    }

    /**
     * Formats and returns JSON data to the view
     * @param type $payload
     * @param type $ok
     */
    public function returnJSON($payload = null, $ok = 0) {
        $data_package = array();
        $data_package['payload'] = $payload;
        $data_package['ok'] = $ok;

        $this->_helper->json($data_package);
    }
    
    public function uploadFiles(){
        $info = array();
        if(isset($_FILES['user_file'])){
            $info['name'] = $_FILES['user_file']['name'][0];
            $info['size'] = $this->sizeReadable($_FILES['user_file']['size'][0]);
             
            $mail_queue = new Application_Model_MailQueue();
            $response = $mail_queue->addAttachmentToMailQueue($info['name'], $_FILES['user_file']["type"][0]);
            $info['id'] = $response['id'];
            
            $path = Frp_Config::getFrpConfig();
            $tmp_name = $_FILES['user_file']['tmp_name'][0];  
            move_uploaded_file($tmp_name, $path['tmp'] . "/" . $info['id']);
 
        }
        
        return $info;
    }
    
    private function sizeReadable($size) {
        $mod = 1024;
 
        $units = explode(' ','B KB MB GB TB PB');
        for ($i = 0; $size > $mod; $i++) {
            $size /= $mod;
        }
        return round($size, 2) . ' ' . $units[$i];

    }

}

