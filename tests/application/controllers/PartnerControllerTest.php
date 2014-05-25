<?php

/**
 * Unit Test for Programs
 * @package    Frp
 * @author    Ali Alabbas and Ruixue Li 
 * @version    1.0
 */
class PartnerControllerTest extends Zend_Test_PHPUnit_ControllerTestCase {

    // private $auth;
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
     * Tests getting all partners
     */
    public function testGetAllPartners() {
        // Dispatch page request and ensure it's successful
        $this->dispatch('/partners/ajax');
        $this->assertController("ajax");
        $this->assertAction("index");
        $this->assertResponseCode(200);

        // Parse the response
        $response = Zend_Json::decode($this->getResponse()->getBody());
        $partners = $response['payload'];

        // Ensure that there are no partners in the DB as of yet
        $this->assertEquals(0, count($partners));
    }

    /**
     * Creates a new partner and test that the data in the DB is correct
     */
    public function testCreateNewPartner() {
        // create data array
        $data = array(
            'name' => 'Partner Contact A',
            'email' => 'partner@mailinator.com',
            'organization' => 'Organization A',
            'comments' => 'Testtttt'
        );

        // Set post data
        $this->request->setMethod('POST')
                ->setPost($data);

        // Dispatch page request and ensure it's successful
        $this->dispatch('/partners/ajax/create');
        $this->assertController("ajax");
        $this->assertAction("create");
        $this->assertResponseCode(200);

        // Parse the response
        $response = Zend_Json::decode($this->getResponse()->getBody());


        $id = $response['payload']['id'];

        // Reset the request and the response to do another dispatch
        $this->resetRequest()
                ->resetResponse();

        // Set get data
        $this->request->setMethod('GET')
                ->setPost(array(
                    'id' => $id
        ));

        // Dispatch page request to retrieve the newly created partner from the DB
        $this->dispatch('/partners/ajax');

        // Parse the response
        $response = Zend_Json::decode($this->getResponse()->getBody());



        $partner = $response['payload'][0];
        $data['id'] = $id;

        // Ensure that the partner data in the DB is equivalent to the data used to create it
        $this->assertTrue($data == $partner);

        // Reset the request and the response to do another dispatch
        $this->resetRequest()
                ->resetResponse();

        // Set post data
        $this->request->setMethod('POST')
                ->setPost(array(
                    'id' => $id
        ));

        // Dispatch page request to clean up and delete the new partner created
        $this->dispatch('/partners/ajax/delete');
    }

    public function testDeletePartner() {

        // create data array
        $data = array(
            'name' => 'Partner Contact B',
            'email' => 'partner1@mailinator.com',
            'organization' => 'Organization B',
            'comments' => 'Test2'
        );

        // Set post data
        $this->request->setMethod('POST')
                ->setPost($data);

        // Dispatch page request and ensure it's successful
        $this->dispatch('/partners/ajax/create');
        $this->assertController("ajax");
        $this->assertAction("create");
        $this->assertResponseCode(200);

        // Parse the response
        $response = Zend_Json::decode($this->getResponse()->getBody());


        $id = $response['payload']['id'];

        // Reset the request and the response to do another dispatch
        $this->resetRequest()
                ->resetResponse();

        // Set get data
        $this->request->setMethod('GET')
                ->setPost(array(
                    'id' => $id
        ));

        // Dispatch page request to retrieve the newly created partner from the DB
        $this->dispatch('/partners/ajax');

        // Parse the response
        $response = Zend_Json::decode($this->getResponse()->getBody());



        $partner = $response['payload'][0];
        $data['id'] = $id;


        // Ensure that the partner data in the DB is equivalent to the data used to create it
        $this->assertTrue($data == $partner);

        // Reset the request and the response to do another dispatch
        $this->resetRequest()
                ->resetResponse();

        // Set post data
        $this->request->setMethod('POST')
                ->setPost(array(
                    'id' => $id
        ));

        //Test deleting the new partner created
        $this->dispatch('/partners/ajax/delete');
        //Check to see if the deleted partner still exists
        $this->resetRequest()
                ->resetResponse();

        // Set get data
        $this->request->setMethod('GET')
                ->setPost(array(
                    'id' => $data['id']
        ));

        // Dispatch page request to retrieve the newly created partner from the DB
        $this->dispatch('/partners/ajax');
        $response = Zend_Json::decode($this->getResponse()->getBody());


        //Assert that the partners with the id we deleted does not exist now
        $this->assertEquals(0, count($response['payload']));
    }

    //Test editing a partner
    public function testEditPartnerInfo() {

        //Create Partner 
        // create data array
        $data = array(
            'name' => 'Partner Contact A Test',
            'email' => 'partner@mailinator.com',
            'organization' => 'Organization A Test',
            'comments' => 'Test'
        );

        // Set post data
        $this->request->setMethod('POST')
                ->setPost($data);

        // Dispatch page request and ensure it's successful
        $this->dispatch('/partners/ajax/create');
        $this->assertController("ajax");
        $this->assertAction("create");
        $this->assertResponseCode(200);

        // Parse the response
        $response = Zend_Json::decode($this->getResponse()->getBody());


        $id = $response['payload']['id'];

        // Reset the request and the response to do another dispatch
        $this->resetRequest()
                ->resetResponse();

        // Set get data
        $this->request->setMethod('GET')
                ->setPost(array(
                    'id' => $id
        ));

        // Dispatch page request to retrieve the newly created partner from the DB
        $this->dispatch('/partners/ajax');

        // Parse the response
        $response = Zend_Json::decode($this->getResponse()->getBody());



        $partner = $response['payload'][0];
        $data['id'] = $id;

        // Ensure that the partner data in the DB is equivalent to the data used to create it
        $this->assertTrue($data == $partner);




        //Edit the partner we just created
        // Set post data
        $editData = array(
            'id' => $data['id'],
            'name' => 'New Partner Name',
            'email' => 'newemail@mailinator.com',
            'organization' => 'New Organization',
            'comments' => 'New comment'
        );


        $this->resetRequest()
                ->resetResponse();
        $this->request->setMethod('POST')
                ->setPost($editData);

        // Dispatch page request and ensure it's successful
        $this->dispatch('/partners/ajax/edit');
        $this->assertController("ajax");
        $this->assertAction("edit");
        $this->assertResponseCode(200);

        //Dispatch and see if values changed for the partner with the given id
        $this->resetRequest()
                ->resetResponse();

        // Set post data
        $this->request->setMethod('GET')
                ->setPost(array(
                    'id' => $data['id']
        ));
        $this->dispatch('/partners/ajax');

        $response = Zend_Json::decode($this->getResponse()->getBody());



        //Ensure that the partner contains the updated information

        $this->assertTrue($editData == $response['payload'][0]);
        // $id = $response['payload']['id'];
        // Reset the request and the response to do another dispatch
        $this->resetRequest()
                ->resetResponse();

        // Set post data
        $this->request->setMethod('POST')
                ->setPost(array(
                    'id' => $id
        ));
        // Dispatch page request to clean up and delete the new partner created
        $this->dispatch('/partners/ajax/delete');
    }

    /**
     * Logout the user when done
     */
    public function tearDown() {
        $this->auth->logout();
    }

}
