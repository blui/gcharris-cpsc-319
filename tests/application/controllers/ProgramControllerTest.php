<?php

/**
 * Unit Test for Programs
 * @package    Frp
 * @author     Ruixue Li 
 * @version    1.0
 */
class ProgramControllerTest extends Zend_Test_PHPUnit_ControllerTestCase {

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
        $this->dispatch('/programs/ajax');
        $this->assertController("ajax");
        $this->assertAction("index");
        $this->assertResponseCode(200);

        // Parse the response
        $response = Zend_Json::decode($this->getResponse()->getBody());
        $programs = $response['payload'];

        // Ensure that there are no programs in the DB
        $this->assertEquals(count($programs['programs']), 0);
    }

//Test getting all Program data
    public function testGetProgramByID() {
        //Create a program
        $data = array(
            'name' => 'ProgramA1',
            'coordinators' => array()
        );
        // Set post data
        $this->request->setMethod('POST')
                ->setPost($data);


        // Dispatch page request and ensure it's successful
        $this->dispatch('/programs/ajax/create');
        $this->assertController("ajax");
        $this->assertAction("create");
        $this->assertResponseCode(200);

        // Parse the response
        $response = Zend_Json::decode($this->getResponse()->getBody());




        $id = $response['payload'];
        // Reset the request and the response to do another dispatch
        $this->resetRequest()->resetResponse();

        // Set get data
        $this->request->setMethod('GET')->setPost(array(
            'id' => $id
        ));


        // Dispatch page request to retrieve the newly created program from the DB
        $this->dispatch('/programs/ajax');
        $this->assertController("ajax");
        $this->assertAction("index");
        $this->assertResponseCode(200);



        // Parse the response
        $response = Zend_Json::decode($this->getResponse()->getBody());




        $programName = $response['payload']['programs'][0]['program_name'];




        //check that the program name matches
        $this->assertEquals($data['name'], $programName);
        //Check that the id of program created matches the one retrieved
        $this->assertEquals($id, $response['payload']['programs'][0]['program_id']);

        $this->resetRequest()
                ->resetResponse();

        // Set post data
        $this->request->setMethod('POST')
                ->setPost(array(
                    'id' => $id
        ));

// Dispatch page request to clean up and delete the new program created
        $this->dispatch('/programs/ajax/delete');
    }

//getAllProgramData

    /**
     * Creates a new program and test that the data in the DB is correct
     */
    public function testCreateProgram() {

        // create data array
        $data = array(
            'name' => 'Program Test A1_________',
            'coordinators' => array()
        );
        // Set post data
        $this->request->setMethod('POST')
                ->setPost($data);


        // Dispatch page request and ensure it's successful
        $this->dispatch('/programs/ajax/create');
        $this->assertController("ajax");
        $this->assertAction("create");
        $this->assertResponseCode(200);

        // Parse the response
        $response = Zend_Json::decode($this->getResponse()->getBody());


        $id = $response['payload']['id'];

        $id = $response['payload'];
        // Reset the request and the response to do another dispatch
        $this->resetRequest()->resetResponse();

        // Set get data
        $this->request->setMethod('GET')->setPost(array(
            'id' => $id
        ));


        // Dispatch page request to retrieve the newly created program from the DB
        $this->dispatch('/programs/ajax');
        // Parse the response
        $response = Zend_Json::decode($this->getResponse()->getBody());




        $programName = $response['payload']['programs'][0]['program_name'];
        $data['id'] = $id;



        //check that the program name matches
        $this->assertEquals($data['name'], $programName);

        $this->resetRequest()
                ->resetResponse();

        // Set post data
        $this->request->setMethod('POST')
                ->setPost(array(
                    'id' => $data['id']
        ));


        // Dispatch page request to clean up and delete the new program created
        $this->dispatch('/programs/ajax/delete');
    }

    public function testDeleteProgram() {

        // create data array
        $data = array(
            'name' => 'Program Test A1_________',
            'coordinators' => array()
        );
        // Set post data
        $this->request->setMethod('POST')
                ->setPost($data);


        // Dispatch page request and ensure it's successful
        $this->dispatch('/programs/ajax/create');
        $this->assertController("ajax");
        $this->assertAction("create");
        $this->assertResponseCode(200);

        // Parse the response
        $response = Zend_Json::decode($this->getResponse()->getBody());




        $id = $response['payload'];
        // Reset the request and the response to do another dispatch
        $this->resetRequest()->resetResponse();

        // Set get data
        $this->request->setMethod('GET')->setPost(array(
            'id' => $id
        ));


        // Dispatch page request to retrieve the newly created program from the DB
        $this->dispatch('/programs/ajax');
        // Parse the response
        $response = Zend_Json::decode($this->getResponse()->getBody());





        $programName = $response['payload']['programs'][0]['program_name'];
        //$data['id'] = $id;

        $id = $response['payload']['programs'][0]['program_id'];

        //check that the program name matches
        $this->assertEquals($data['name'], $programName);

        //Test deleting the program

        $data1 = array(
            'id' => $id
        );


        $this->resetRequest()
                ->resetResponse();



// Set post data
        $this->request->setMethod('POST')
                ->setPost($data1);


        // Dispatch page request and ensure it's successful
        $this->dispatch('/programs/ajax/delete');
        $this->assertController("ajax");
        $this->assertAction("delete");
        $this->assertResponseCode(200);

        // Parse the response
        $response = Zend_Json::decode($this->getResponse()->getBody());



        $id1 = $response['payload'];
        // Reset the request and the response to do another dispatch
        $this->resetRequest()->resetResponse();

        // Set get data
        $this->request->setMethod('GET')->setPost(array(
            'id' => $id1
        ));


        // Dispatch page request to retrieve the newly deleted program from the DB
        $this->dispatch('/programs/ajax');
        // Parse the response
        $response = Zend_Json::decode($this->getResponse()->getBody());



        //assert that the program created was deleted
        $this->assertEquals(0, count($response['payload']['programs']));
    }

    //Test editProgram
    public function testEditProgram() {

        //Create a program
        // create data array
        $data = array(
            'name' => 'ProgramTest',
            'coordinators' => array()
        );
        // Set post data
        $this->request->setMethod('POST')
                ->setPost($data);


        // Dispatch page request and ensure it's successful
        $this->dispatch('/programs/ajax/create');
        $this->assertController("ajax");
        $this->assertAction("create");
        $this->assertResponseCode(200);

        // Parse the response
        $response = Zend_Json::decode($this->getResponse()->getBody());




        $id = $response['payload'];
        // Reset the request and the response to do another dispatch
        $this->resetRequest()->resetResponse();

        // Set get data
        $this->request->setMethod('GET')->setPost(array(
            'id' => $id
        ));


        // Dispatch page request to retrieve the newly created program from the DB
        $this->dispatch('/programs/ajax');
        // Parse the response
        $response = Zend_Json::decode($this->getResponse()->getBody());




        $programName = $response['payload']['programs'][0]['program_name'];
        $data['id'] = $id;



        //check that the program name matches
        $this->assertEquals($data['name'], $programName);
        $this->assertEquals($id, $response['payload']['programs'][0]['program_id']);

        //Edit the program created
        // create data array
        $editData = array(
            'id' => $id,
            'name' => 'ProgramTestEdit',
            'coordinators' => array()
        );

        $this->resetRequest()
                ->resetResponse();

        // Set post data
        $this->request->setMethod('POST')
                ->setPost($editData);


        // Dispatch page request and ensure it's successful
        $this->dispatch('/programs/ajax/edit');
        $this->assertController("ajax");
        $this->assertAction("edit");
        $this->assertResponseCode(200);

        // Parse the response
        $this->resetRequest()->resetResponse();

        // Set get data
        $this->request->setMethod('GET')->setPost(array(
            'id' => $id
        ));


        // Dispatch page request to retrieve the newly created program from the DB
        $this->dispatch('/programs/ajax');
        // Parse the response
        $response = Zend_Json::decode($this->getResponse()->getBody());


        //assert that the program we Edited contains the updated information
        $this->assertEquals($editData['name'], $response['payload']['programs'][0]['program_name']);
        $this->assertEquals($id, $response['payload']['programs'][0]['program_id']);



        $this->resetRequest()
                ->resetResponse();

        // Set post data
        $this->request->setMethod('POST')
                ->setPost(array(
                    'id' => $id
        ));


        // Dispatch page request to clean up and delete the new program created
        $this->dispatch('/programs/ajax/delete');
    }

    public function testSession() {


        // create data array
        $data = array(
            'name' => 'Program Test A1',
            'coordinators' => array()
        );
        // Set post data
        $this->request->setMethod('POST')
                ->setPost($data);


        // Dispatch page request and ensure it's successful
        $this->dispatch('/programs/ajax/create');
        $this->assertController("ajax");
        $this->assertAction("create");
        $this->assertResponseCode(200);

        // Parse the response
        $response = Zend_Json::decode($this->getResponse()->getBody());


        $id = $response['payload']['id'];

        $id = $response['payload'];
        // Reset the request and the response to do another dispatch
        $this->resetRequest()->resetResponse();

        // Set get data
        $this->request->setMethod('GET')->setPost(array(
            'id' => $id
        ));


        // Dispatch page request to retrieve the newly created program from the DB
        $this->dispatch('/programs/ajax');
        // Parse the response
        $response = Zend_Json::decode($this->getResponse()->getBody());




        $programName = $response['payload']['programs'][0]['program_name'];
        $data['id'] = $id;



        //check that the program name matches
        $this->assertEquals($data['name'], $programName);

        // Reset the request and the response to do another dispatch
        $this->resetRequest()
                ->resetResponse();

        // create data array
        $data = array(
            'get' => 'referrals',
            'previous' => 1,
            'program_id' => $id
        );

        // Set post data
        $this->request->setMethod('POST')
                ->setPost($data);

        // Dispatch page request and ensure it's successful
        $this->dispatch('/programs/ajax/session');
        $this->assertController("ajax");
        $this->assertAction("session");
        $this->assertResponseCode(200);

        // Parse the response
        $response = Zend_Json::decode($this->getResponse()->getBody());
//Assert that nothing is returned

        $this->assertEquals(0, count($response['payload']));
        $this->resetRequest()
                ->resetResponse();

        // Set post data
        $this->request->setMethod('POST')
                ->setPost(array(
                    'id' => $id
        ));


        // Dispatch page request to clean up and delete the new program created
        $this->dispatch('/programs/ajax/delete');
    }

    /*
     * Test properties
     */

    public function testProperties() {
        //Create program
        // create data array
        $data = array(
            'name' => 'Program Test A1_________',
            'coordinators' => array()
        );
        // Set post data
        $this->request->setMethod('POST')
                ->setPost($data);


        // Dispatch page request and ensure it's successful
        $this->dispatch('/programs/ajax/create');
        $this->assertController("ajax");
        $this->assertAction("create");
        $this->assertResponseCode(200);

        // Parse the response
        $response = Zend_Json::decode($this->getResponse()->getBody());


        $id = $response['payload']['id'];

        $id = $response['payload'];
        // Reset the request and the response to do another dispatch
        $this->resetRequest()->resetResponse();

        // Set get data
        $this->request->setMethod('GET')->setPost(array(
            'id' => $id
        ));


        // Dispatch page request to retrieve the newly created program from the DB
        $this->dispatch('/programs/ajax');
        // Parse the response
        $response = Zend_Json::decode($this->getResponse()->getBody());




        $programName = $response['payload']['programs'][0]['program_name'];
        $data['id'] = $id;



        //check that the program name matches
        $this->assertEquals($data['name'], $programName);


        $newdata = array(
            'type' => 'referral',
            'act' => 'add',
            'name' => $data['name'],
            'program_id' => $id
        );
        $this->resetRequest()
                ->resetResponse();
        // Set post data
        $this->request->setMethod('POST')
                ->setPost($newdata);

        // Dispatch page request and ensure it's successful
        $this->dispatch('/programs/ajax/properties');
        $this->assertController("ajax");
        $this->assertAction("properties");
        $this->assertResponseCode(200);

        // Parse the response
        $response = Zend_Json::decode($this->getResponse()->getBody());
        $refId = $response['payload']['id'];

        $this->assertFalse(empty($refId));
        //Test edit
        $this->resetRequest()
                ->resetResponse();
        $newdata1 = array(
            'type' => 'referral',
            'act' => 'edit',
            'id' => $refId,
            'name' => 'New Referral Name',
            'program_id' => $id
        );

        // Set post data
        $this->request->setMethod('POST')
                ->setPost($newdata1);

        // Dispatch page request and ensure it's successful
        $this->dispatch('/programs/ajax/properties');
        $this->assertController("ajax");
        $this->assertAction("properties");
        $this->assertResponseCode(200);

        // Parse the response
        $response = Zend_Json::decode($this->getResponse()->getBody());
        //Ensure that referral was edited successfully
        //Ensure that the edit was successful
        $this->assertEquals($response['payload'], 1);

        //Try deleting referral
//Create session
        // create data array
        $data = array(
            'edit' => 'referrals',
            'id' => $refId,
            'previous' => 1,
            'field' => 'count',
            'value' => 0,
            'program_id' => $id
        );

        // Set post data
        $this->request->setMethod('POST')
                ->setPost($data);

        // Dispatch page request and ensure it's successful
        $this->dispatch('/programs/ajax/session');
        $this->assertController("ajax");
        $this->assertAction("session");
        $this->assertResponseCode(200);

        // Parse the response
        $response = Zend_Json::decode($this->getResponse()->getBody());


        //Ensure that referral was deleted successfully

        $this->assertTrue(empty($response['payload']['referral_id']));

        //Create resource

        $this->resetRequest()
                ->resetResponse();
        $newdata1 = array(
            'type' => 'resource',
            'act' => 'add',
            'name' => 'New Resource Name',
            'program_id' => $id
        );

        // Set post data
        $this->request->setMethod('POST')
                ->setPost($newdata1);

        // Dispatch page request and ensure it's successful
        $this->dispatch('/programs/ajax/properties');
        $this->assertController("ajax");
        $this->assertAction("properties");
        $this->assertResponseCode(200);
        // Parse the response
        $response = Zend_Json::decode($this->getResponse()->getBody());

        $resourceId = $response['payload']['id'];
        $this->assertFalse(empty($resourceId));


        $data = array(
            'edit' => 'resource',
            'id' => $resourceId,
            'previous' => 1,
            'field' => 'someField',
            'value' => 'value',
            'program_id' => $id
        );

        // Set post data
        $this->request->setMethod('POST')
                ->setPost($data);

        // Dispatch page request and ensure it's successful
        $this->dispatch('/programs/ajax/session');
        $this->assertController("ajax");
        $this->assertAction("session");
        $this->assertResponseCode(200);

        // Parse the response
        $response = Zend_Json::decode($this->getResponse()->getBody());


        //Ensure that the edit was successful
        $this->assertFalse(empty($response['payload']['program_session_id']));
        //Delete the resource

        $this->resetRequest()
                ->resetResponse();
        $newdata1 = array(
            'type' => 'resource',
            'act' => 'delete',
            'id' => $resourceId,
            'program_id' => $id
        );

        // Set post data
        $this->request->setMethod('POST')
                ->setPost($newdata1);

        // Dispatch page request and ensure it's successful
        $this->dispatch('/programs/ajax/properties');
        $this->assertController("ajax");
        $this->assertAction("properties");
        $this->assertResponseCode(200);
        // Parse the response, and ensure the resource was deleted successfully
        $response = Zend_Json::decode($this->getResponse()->getBody());

        //Ensure that the delete was successful
        $this->assertTrue($response['payload'] == 1);
        $this->resetRequest()
                ->resetResponse();

        // Set post data
        $this->request->setMethod('POST')
                ->setPost(array(
                    'id' => $id
        ));


        // Dispatch page request to clean up and delete the new program created
        $this->dispatch('/programs/ajax/delete');
    }

    /*
     * Test Sessions
     */

    public function testSessions() {


        $data = array(
            'programs' => array(),
            'families' => array(),
            'start_date' => '01/01/2013',
            'end_date' => '01/31/2013',
            'sort' => '',
            'dir' => 1,
            'p' => 1
        );

        // Set post data
        $this->request->setMethod('POST')
                ->setPost($data);

        // Dispatch page request and ensure it's successful
        $this->dispatch('/programs/ajax/sessions');
        $this->assertController("ajax");
        $this->assertAction("sessions");
        $this->assertResponseCode(200);

        // Parse the response
        $response = Zend_Json::decode($this->getResponse()->getBody());
        $this->assertFalse(empty($response['payload']['pagination']));

        $this->assertEquals($response['payload']['pagination']['pageCount'], 0);
        $this->assertEquals($response['payload']['pagination']['itemCountPerPage'], 10);
        $this->assertEquals($response['payload']['pagination']['first'], 1);
        $this->assertEquals($response['payload']['pagination']['last'], 0);
        //Assert that there are no items as of now
        $this->assertEquals(count($response['payload']['items']), 0);
        $this->assertEquals(count($response['payload']['programs']), 0);
    }

    /**
     * Logout the user when done
     */
    public function tearDown() {
        $this->auth->logout();
    }

}
