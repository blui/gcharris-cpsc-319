<?php

/**
 * Unit Test for Programs
 * @package    Frp
 * @author     Ruixue Li 
 * @version    1.0
 */
class ParticipantsControllerTest extends Zend_Test_PHPUnit_ControllerTestCase {

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
     * Tests getting all families
     */
    public function testGetAllFamilies() {

        $this->request->setMethod('POST')
                ->setPost(array());


        // Dispatch page request and ensure it's successful
        $this->dispatch('/participants/ajax');
        $this->assertController("ajax");
        $this->assertAction("index");
        $this->assertResponseCode(200);

        $response = Zend_Json::decode($this->getResponse()->getBody());


        // Ensure that there are no families in the DB as of yet
        $families = $response['payload'];

        $this->assertEquals(0, count($families['items']));
        $this->assertEquals(0, $families['pagination']['pageCount']);
        $this->assertEquals(1, $families['pagination']['first']);
        $this->assertEquals(1, $families['pagination']['current']);
        //Reset response and do another dispatch setting csv to 1
        $this->resetRequest()->resetResponse();
        $data = array(
            'q' => '',
            'programs' => array(),
            'languages' => array(),
            'countries' => array(),
            'start_date' => '01/01/2013',
            'end_date' => '01/31/2013',
            'sort' => 'id',
            'dir' => 1,
            'csv' => 1,
            'p' => 1,
            'count' => 1
        );
        // Dispatch page request and ensure it's successful
        $this->dispatch('/participants/ajax');
        $this->assertController("ajax");
        $this->assertAction("index");
        $this->assertResponseCode(200);
        // Parse the response
        $response = Zend_Json::decode($this->getResponse()->getBody());

        $families = $response['payload'];

        $this->assertEquals(0, count($families['items']));
        $this->assertEquals(0, $families['pagination']['pageCount']);
        $this->assertEquals(1, $families['pagination']['first']);
        $this->assertEquals($data['p'], $families['pagination']['current']);
    }

    //Test creating a family
    public function testCreateFamily() {

        $data = array(
            'allergies' => 'None',
            'emerg_contact_first_name' => 'Jennifer',
            'emerg_contact_last_name' => 'Lastname',
            'emerg_contact_phone' => '0123456789',
            'emerg_contact_relation' => 'sister',
            'guardian_email' => 'guardianemail@email.com',
            'guardian_first_lang' => 'gle',
            'guardian_first_name' => 'Guardianname',
            'guardian_last_name' => 'Guardianlastname',
            'guardian_origin_country' => 'BE',
            'guardian_partner_first_name' => 'Partner',
            'guardian_partner_last_name' => 'Lastname',
            'guardian_role' => 'grandma',
            'hear_about_us' => array(),
            'phone_number' => '1234567890',
            'postal_3dig' => 'h9n',
            'first_attendance_date' => '01/01/2013',
            'hear_about_us' => array(),
        );

        // Set post data
        $this->request->setMethod('POST')
                ->setPost($data);

        // Dispatch page request and ensure it's successful
        $this->dispatch('/participants/ajax/create');
        $this->assertController("ajax");
        $this->assertAction("create");
        $this->assertResponseCode(200);

        // Parse the response
        $response = Zend_Json::decode($this->getResponse()->getBody());


        $id = $response['payload']['family']['id'];

        //Do another dispatch to see if the new created family exists in database

        $this->resetRequest()->resetResponse();
        // Set post data
        $this->request->setMethod('POST')
                ->setPost(array('id' => $id));

        // Dispatch page request and ensure it's successful
        $this->dispatch('/participants/ajax');
        $this->assertController("ajax");
        $this->assertAction("index");
        $this->assertResponseCode(200);

        $response = Zend_Json::decode($this->getResponse()->getBody());

        $familyCreated = $response['payload']['family'];

        $data['id'] = $id;
        $data['registration_date'] = $familyCreated['registration_date'];
        $data['hear_about_us'] = $familyCreated['hear_about_us'];
        $data['guardian_first_lang_name'] = $familyCreated['guardian_first_lang_name'];
        $data['guardian_full_name'] = $familyCreated['guardian_full_name'];
        $data['guardian_origin_country_name'] = $familyCreated['guardian_origin_country_name'];



//Assert that the created family exists in the database, and matches the family data created 
        $this->assertEquals($data, $familyCreated);

        //Delete the family we just created in the database
        $this->resetRequest()
                ->resetResponse();

        // Set post data
        $this->request->setMethod('POST')
                ->setPost(array(
                    'id' => $id
        ));

        // Dispatch page request to clean up and delete the new volunteer created
        $this->dispatch('/participants/ajax/delete');
    }

    /*
     * Test delete a family from database
     */

    public function testDeleteFamily() {
//Create a family to delete 

        $data = array(
            'allergies' => 'None',
            'emerg_contact_first_name' => 'Name',
            'emerg_contact_last_name' => 'Lastname',
            'emerg_contact_phone' => '0123456789',
            'emerg_contact_relation' => 'sister',
            'guardian_email' => 'guardianemail@email.com',
            'guardian_first_lang' => 'gle',
            'guardian_first_name' => 'Guardianname',
            'guardian_last_name' => 'Guardianlastname',
            'guardian_origin_country' => 'BE',
            'guardian_partner_first_name' => 'Partner',
            'guardian_partner_last_name' => 'Lastname',
            'guardian_role' => 'grandma',
            'hear_about_us' => array(),
            'phone_number' => '1234567890',
            'postal_3dig' => 'h9n',
            'first_attendance_date' => '01/01/2013',
            'hear_about_us' => array(),
        );






        // Set post data
        $this->request->setMethod('POST')
                ->setPost($data);

        // Dispatch page request and ensure it's successful
        $this->dispatch('/participants/ajax/create');
        $this->assertController("ajax");
        $this->assertAction("create");
        $this->assertResponseCode(200);

        // Parse the response
        $response = Zend_Json::decode($this->getResponse()->getBody());


        $id = $response['payload']['family']['id'];

        //Do another dispatch to see if the new created family exists in database

        $this->resetRequest()->resetResponse();
        // Set post data
        $this->request->setMethod('POST')
                ->setPost(array('id' => $id));

        // Dispatch page request and ensure it's successful
        $this->dispatch('/participants/ajax');
        $this->assertController("ajax");
        $this->assertAction("index");
        $this->assertResponseCode(200);


        $response = Zend_Json::decode($this->getResponse()->getBody());


        $familyCreated = $response['payload']['family'];

        $data['id'] = $id;
        $data['registration_date'] = $familyCreated['registration_date'];
        $data['hear_about_us'] = $familyCreated['hear_about_us'];
        $data['guardian_first_lang_name'] = $familyCreated['guardian_first_lang_name'];
        $data['guardian_full_name'] = $familyCreated['guardian_full_name'];
        $data['guardian_origin_country_name'] = $familyCreated['guardian_origin_country_name'];
//Assert that the created family exists in the database, and matches the family data created 
        $this->assertEquals($data, $familyCreated);


        //Delete the family we just created in the database
        $this->resetRequest()
                ->resetResponse();

        // Set post data
        $this->request->setMethod('POST')
                ->setPost(array(
                    'id' => $id
        ));

        //Test deleting the family we created
        $this->dispatch('/participants/ajax/delete');
        $this->assertController("ajax");
        $this->assertAction("delete");
        $this->assertResponseCode(200);

        //Do another dispatch to see if family data is gone 

        $this->resetRequest()->resetResponse();
        // Set post data
        $this->request->setMethod('POST')
                ->setPost(array('id' => $id));

        // Dispatch page request and ensure it's successful
        $this->dispatch('/participants/ajax');
        $this->assertController("ajax");
        $this->assertAction("index");
        $this->assertResponseCode(200);


        $response = Zend_Json::decode($this->getResponse()->getBody());

        $family = $response['payload']['family'];
        //Assert that the family deleted does not exist
        $this->assertTrue(empty($family));
    }

    public function testUpdateFamily() {

        //Create a family to edit



        $data = array(
            'allergies' => 'None',
            'emerg_contact_first_name' => 'Jennifer',
            'emerg_contact_last_name' => 'Lastname',
            'emerg_contact_phone' => '0123456789',
            'emerg_contact_relation' => 'sister',
            'guardian_email' => 'guardianemail@email.com',
            'guardian_first_lang' => 'gle',
            'guardian_first_name' => 'Guardianname',
            'guardian_last_name' => 'Guardianlastname',
            'guardian_origin_country' => 'BE',
            'guardian_partner_first_name' => 'Partner',
            'guardian_partner_last_name' => 'Lastname',
            'guardian_role' => 'grandma',
            'hear_about_us' => array(),
            'phone_number' => '1234567890',
            'postal_3dig' => 'h9n',
            'first_attendance_date' => '01/01/2013',
            'hear_about_us' => array(),
        );


        // Set post data
        $this->request->setMethod('POST')
                ->setPost($data);

        // Dispatch page request and ensure it's successful
        $this->dispatch('/participants/ajax/create');
        $this->assertController("ajax");
        $this->assertAction("create");
        $this->assertResponseCode(200);

        // Parse the response
        $response = Zend_Json::decode($this->getResponse()->getBody());


        $id = $response['payload']['family']['id'];

        //Do another dispatch to see if the new created family exists in database

        $this->resetRequest()->resetResponse();
        // Set post data
        $this->request->setMethod('POST')
                ->setPost(array('id' => $id));

        // Dispatch page request and ensure it's successful
        $this->dispatch('/participants/ajax');
        $this->assertController("ajax");
        $this->assertAction("index");
        $this->assertResponseCode(200);


        $response = Zend_Json::decode($this->getResponse()->getBody());


        $familyCreated = $response['payload']['family'];

        $data['id'] = $id;
        $data['registration_date'] = $familyCreated['registration_date'];
        $data['hear_about_us'] = $familyCreated['hear_about_us'];

        $data['guardian_first_lang_name'] = $familyCreated['guardian_first_lang_name'];
        $data['guardian_full_name'] = $familyCreated['guardian_full_name'];
        $data['guardian_origin_country_name'] = $familyCreated['guardian_origin_country_name'];

//Assert that the created family exists in the database, and matches the family data created 
        $this->assertEquals($data, $familyCreated);

//test editing the participant

        $editData = array(
            'id' => $id,
            'allergies' => 'Dairy',
            'emerg_contact_first_name' => 'Editfirstname',
            'emerg_contact_last_name' => 'Editlastname',
            'emerg_contact_phone' => '2134567890',
            'emerg_contact_relation' => 'Niece',
            // 'children' => array(),
            'guardian_email' => 'guardianemail@someaddress.com',
            'guardian_first_lang' => 'aar',
            'guardian_first_name' => 'Editguardianfirstname',
            'guardian_last_name' => 'Editguardianlastname',
            'guardian_origin_country' => 'US',
            'guardian_partner_first_name' => 'Editpartnername',
            'guardian_partner_last_name' => 'Editpartnerlastname',
            'guardian_role' => 'Parent',
            'hear_about_us' => 3,
            'phone_number' => '7134567890',
            'postal_3dig' => 'c3d'
        );

        $this->resetRequest()->resetResponse();
        $this->request->setMethod('POST')->setPost($editData);

        $this->dispatch('/participants/ajax/edit');

        $this->assertController("ajax");
        $this->assertAction("edit");
        $this->assertResponseCode(200);


        //Do another dispatch to see if family with $id has been changed in database, matching the $editaData fields


        $this->resetRequest()->resetResponse();
        // Set post data
        $this->request->setMethod('POST')
                ->setPost(array('id' => $id));

        // Dispatch page request and ensure it's successful
        $this->dispatch('/participants/ajax');
        $this->assertController("ajax");
        $this->assertAction("index");
        $this->assertResponseCode(200);


        $response = Zend_Json::decode($this->getResponse()->getBody());


        $familyEdited = $response['payload']['family'];

        $response = Zend_Json::decode($this->getResponse()->getBody());
        //Let the edit data contain the original data that was not changed
        $editData['registration_date'] = $data['registration_date'];
        $editData['first_attendance_date'] = $data['first_attendance_date'];

        $editData['guardian_first_lang_name'] = $familyEdited['guardian_first_lang_name'];
        $editData['guardian_full_name'] = $familyEdited['guardian_full_name'];
        $editData['guardian_origin_country_name'] = $familyEdited['guardian_origin_country_name'];

        $this->assertEquals($editData, $familyEdited);


        //Delete the family we created in the database
        $this->resetRequest()
                ->resetResponse();

        // Set post data
        $this->request->setMethod('POST')
                ->setPost(array(
                    'id' => $id
        ));

        // Dispatch page request to clean up and delete the new volunteer created
        $this->dispatch('/participants/ajax/delete');
    }

    /*
     * Test the search function
     */

    public function testSearch() {


//Create a family and then use search

        $data = array(
            'allergies' => 'None',
            'emerg_contact_first_name' => 'Jennifer',
            'emerg_contact_last_name' => 'Lastname',
            'emerg_contact_phone' => '0123456789',
            'emerg_contact_relation' => 'sister',
            'guardian_email' => 'guardianemail@email.com',
            'guardian_first_lang' => 'gle',
            'guardian_first_name' => 'Guardianname',
            'guardian_last_name' => 'Guardianlastname',
            'guardian_origin_country' => 'BE',
            'guardian_partner_first_name' => 'Partner',
            'guardian_partner_last_name' => 'Lastname',
            'guardian_role' => 'grandma',
            'hear_about_us' => array(),
            'phone_number' => '1234567890',
            'postal_3dig' => 'h9n',
            'first_attendance_date' => '01/01/2013',
            'hear_about_us' => array(),
        );

        // Set post data
        $this->request->setMethod('POST')
                ->setPost($data);

        // Dispatch page request and ensure it's successful
        $this->dispatch('/participants/ajax/create');
        $this->assertController("ajax");
        $this->assertAction("create");
        $this->assertResponseCode(200);

        // Parse the response
        $response = Zend_Json::decode($this->getResponse()->getBody());


        $id = $response['payload']['family']['id'];

        //Do another dispatch to see if the new created family exists in database

        $this->resetRequest()->resetResponse();
        // Set post data
        $this->request->setMethod('POST')
                ->setPost(array('id' => $id));

        // Dispatch page request and ensure it's successful
        $this->dispatch('/participants/ajax');
        $this->assertController("ajax");
        $this->assertAction("index");
        $this->assertResponseCode(200);


        $response = Zend_Json::decode($this->getResponse()->getBody());


        $familyCreated = $response['payload']['family'];

        $data['id'] = $id;
        $data['registration_date'] = $familyCreated['registration_date'];
        $data['hear_about_us'] = $familyCreated['hear_about_us'];


        $data['guardian_first_lang_name'] = $familyCreated['guardian_first_lang_name'];
        $data['guardian_full_name'] = $familyCreated['guardian_full_name'];
        $data['guardian_origin_country_name'] = $familyCreated['guardian_origin_country_name'];

//Assert that the created family exists in the database, and matches the family data created 
        $this->assertEquals($data, $familyCreated);

        //Perform search on family created
        //Try setting $q
//              
        $this->resetRequest()
                ->resetResponse();
        $data = array(
            'q' => $data['phone_number'],
            'p' => 1,
        );
        $this->request->setMethod('POST')
                ->setPost($data);

        // Dispatch page request and ensure it's successful
        $this->dispatch('/participants/ajax/search');
        $this->assertController("ajax");
        $this->assertAction("search");
        $this->assertResponseCode(200);
        $response = Zend_Json::decode($this->getResponse()->getBody());


        //Assert the pagination exists
        $this->assertFalse(empty($response['payload']['pagination']));

        //Ensure that the search returns correct results about the family searched
        $guardianName = 'Guardianname Guardianlastname';
        $familyFound = $response['payload']['items'][0];
        $this->assertEquals($familyFound['guardian_name'], $guardianName);
        $this->assertEquals($familyFound['family_id'], $id);
        $this->assertEquals($familyFound['phone_number'], '1234567890');
        $this->assertEquals($familyFound['children_name'], NULL);

        //Delete the family we just created in the database
        $this->resetRequest()
                ->resetResponse();

        // Set post data
        $this->request->setMethod('POST')
                ->setPost(array(
                    'id' => $id
        ));

        // Dispatch page request to clean up and delete the new volunteer created
        $this->dispatch('/participants/ajax/delete');
    }

    /**
     * Logout the user when done
     */
    public function tearDown() {
        $this->auth->logout();
    }

}
