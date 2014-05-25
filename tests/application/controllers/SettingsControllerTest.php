<?php

/**
 * Unit Test for Programs
 * @package    Frp
 * @author     Ruixue Li 
 * @version    1.0
 */
class SettingsControllerTest extends Zend_Test_PHPUnit_ControllerTestCase {

    private $auth;

    /**
     * Bootstrap and login a user
     */
    public function setUp() {
        // Bootstrap
        $this->bootstrap = new Zend_Application(APPLICATION_ENV, APPLICATION_PATH . '/configs/application.ini');
        parent::setUp();

        // Login a user
        $this->auth = new Application_Model_Auth();
  $email = 'directorEmail@mailtest.com';
        $password = 'frp!#@FRP';
        $s = $this->auth->authenticate($email, $password);
    }

    /*
     * Test No Parameters Action
     */

    public function testIndexNoParametersAction() {

        // Dispatch page request and ensure it's successful
        //test given no parameters     

        $this->dispatch('/settings/ajax');
        $this->assertController("ajax");
        $this->assertAction("index");
        $this->assertResponseCode(200);

        // Parse the response
        $response = Zend_Json::decode($this->getResponse()->getBody());

        //Ensure that the authenticated identity information matches the logged in user with the email and password in setUp()
       
        $this->assertTrue($response['payload']['first_name'] == 'admin');

        $this->assertEquals($response['payload']['email'], 'directorEmail@mailtest.com');
        
        $this->assertTrue($response['payload']['job_type'] == 1);
    }

    /*
     * Test Manage account
     */

    public function testManage() {
        $this->dispatch('/settings/ajax');
        $this->assertController("ajax");
        $this->assertAction("index");
        $this->assertResponseCode(200);

        // Parse the response
        $response = Zend_Json::decode($this->getResponse()->getBody());

    $this->assertTrue($response['payload']['first_name'] == 'admin');
        $this->assertEquals($response['payload']['email'], 'directorEmail@mailtest.com');      
        $this->assertTrue($response['payload']['job_type'] == 1);
    }

    /**
     * Logout the user when done
     */
    public function tearDown() {
        $this->auth->logout();
    }

}