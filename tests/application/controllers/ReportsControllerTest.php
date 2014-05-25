<?php

/**
 * Unit Test for Programs
 * @package    Frp
 * @author     Ruixue Li 
 * @version    1.0
 */
class ReportsControllerTest extends Zend_Test_PHPUnit_ControllerTestCase {

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

    /**
     * Tests getting all programs
     */
    public function testGetAllPrograms() {

        // Dispatch page request and ensure it's successful
        $this->dispatch('/reports/ajax');
        $this->assertController("ajax");
        $this->assertAction("index");
        $this->assertResponseCode(200);

        // Parse the response
        $response = Zend_Json::decode($this->getResponse()->getBody());


        $programs = $response['payload'];

        // Ensure that there are no programs in the DB as of yet
        $this->assertEquals(0, count($programs));
    }

    //Test quarterly reports
    public function testQuarterlyReport() {

        $data = array(
            'programs' => array(),
            'start-date' => '01/01/2013',
            'end-date' => '01/31/2013',
            'compare-start-date' => '02/01/2013',
            'compare-end-date' => '02/28/2013',
            'first-day' => '12/12',
            'type' => 1
        );

        // Set post data
        $this->request->setMethod('POST')
                ->setPost($data);

        // Dispatch page request and ensure it's successful
        $this->dispatch('/reports/ajax/generate');
        $this->assertController("ajax");
        $this->assertAction("generate");
        $this->assertResponseCode(200);

        // Parse the response
        $response = Zend_Json::decode($this->getResponse()->getBody());


        $this->assertEquals($response['payload']['dates'][0], 'Jan 1, 2013');
        $this->assertEquals($response['payload']['dates'][1], 'Jan 31, 2013');
        $this->assertEquals($response['payload']['dates'][2], 'Feb 1, 2013');
        $this->assertEquals($response['payload']['dates'][3], 'Feb 28, 2013');
    }

    //Test annual reports
    public function testAnnualReport() {

        $data = array(
            'programs' => array(),
            'start-date' => '02/01/2013',
            'end-date' => '03/31/2013',
            'compare-start-date' => '02/01/2012',
            'compare-end-date' => '02/28/2012',
            'first-day' => '12/12',
            'type' => 0
        );

        // Set post data
        $this->request->setMethod('POST')
                ->setPost($data);

        // Dispatch page request and ensure it's successful
        $this->dispatch('/reports/ajax/generate');
        $this->assertController("ajax");
        $this->assertAction("generate");
        $this->assertResponseCode(200);

        // Parse the response
        $response = Zend_Json::decode($this->getResponse()->getBody());





        $this->assertEquals($response['payload']['dates'][0], 'Feb 1, 2013');
        $this->assertEquals($response['payload']['dates'][1], 'Mar 31, 2013');
        $this->assertEquals($response['payload']['dates'][2], 'Feb 1, 2012');
        $this->assertEquals($response['payload']['dates'][3], 'Feb 28, 2012');
    }

    public function testDownload() {
        $data = array(
            'filename' => '',
            'content' => ''
        );

        // Set post data
        $this->request->setMethod('POST')
                ->setPost($data);

        // Dispatch page request and ensure it's successful
        $this->dispatch('/reports/ajax/download');
        $this->assertController("ajax");
        $this->assertAction("download");
        $this->assertResponseCode(200);


        // Parse the response
        $response = $this->getResponse()->getBody();

        //Test that nothing is returned because filename and content is set to NULL


        $this->assertEquals($response, '');

        $data = array(
            'filename' => 'fileName.csv',
            'content' => 'somecontent'
        );

        // Set post data
        $this->request->setMethod('POST')
                ->setPost($data);

        // Dispatch page request and ensure it's successful
        $this->dispatch('/reports/ajax/download');
        $this->assertController("ajax");
        $this->assertAction("download");
        $this->assertResponseCode(200);

        // Parse the response

        $response = $this->getResponse()->getBody();

        $this->assertEquals($response, $data['content']);

        //Test Access control, log in in as a coordinator, assert that he/she is able to download reports

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

        $response = $this->getResponse()->getBody();

        $data = array(
            'filename' => 'SomeFile.csv',
            'content' => 'somecontent'
        );

        // Set post data
        $this->request->setMethod('POST')
                ->setPost($data);

        // Dispatch page request and ensure it's successful
        $this->dispatch('/reports/ajax/download');
        $this->assertController("ajax");
        $this->assertAction("download");
        $this->assertResponseCode(200);

        // Parse the response

        $response = $this->getResponse()->getBody();

        $this->assertFalse(empty($response));
        $this->assertEquals($response, $data['content']);
    }

    /**
     * Logout the user when done
     */
    public function tearDown() {
        $this->auth->logout();
    }

}
