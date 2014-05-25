<?php

/**
 * Unit Test for Programs
 * @package    Frp
 * @author     Ruixue Li 
 * @version    1.0
 */
class AuthControllerTest extends Zend_Test_PHPUnit_ControllerTestCase {

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
        $this->auth->authenticate($email, $password);
    }

    /*
     * Test authenticating a user
     * Assertion: Only staff members(directors and coordinators) can log in successfully
     */

    public function testAuthenticate() {
        $this->auth->logout();
        //Test logging in with an invalid email adress and password

        $data = array(
            'email' => 'invalidEmail@email.com',
            'password' => 'wrongPassword'
        );
        $this->resetRequest()->resetResponse();
        $this->request->setMethod('POST')
                ->setPost($data);

        $this->dispatch('/auth/login');

        $this->assertController("login");
        $this->assertAction("index");
        $this->assertResponseCode(200);

        // Parse the response
        $response = Zend_Json::decode($this->getResponse()->getBody());

        //Assert that the authentication was successful
        $this->assertEquals($response, 0);



        $this->auth->logout();

        //Test logging in with as a coordinator
        $data = array(
            'email' => 'coordinatorEmail@mailtest.com',
            'password' => 'frp!#@FRP'
        );
        $this->resetRequest()->resetResponse();
        $this->request->setMethod('POST')
                ->setPost($data);

        $this->dispatch('/auth/login');

        $this->assertController("login");
        $this->assertAction("index");
        $this->assertResponseCode(200);

        // Parse the response
        $response = Zend_Json::decode($this->getResponse()->getBody());

        //Assert that the authentication was successful
        $this->assertEquals($response, 1);
    }

    /*
     * Test the logout function
     * Assertion: The currently loggged in user is logged out
     */

    public function testLogout() {


        $this->dispatch('/auth/logout');

        $this->assertController("logout");
        $this->assertAction("index");
        $this->assertResponseCode(200);
        $response = Zend_Json::decode($this->getResponse()->getBody());

        $this->assertEquals($response, true);
    }

    /**
     * Logout the user when done
     */
    public function tearDown() {
        $this->auth->logout();
    }

}
