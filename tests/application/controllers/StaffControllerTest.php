<?php

/**
 * Unit Test for Programs
 * @package    Frp
 * @author     Ruixue Li 
 * @version    1.0
 */
require_once 'SetUpAbstract.php';

class StaffControllerTest extends Zend_Test_PHPUnit_ControllerTestCase {

    private $auth;

    /**
     * Bootstrap and login as a director
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
     * Logged in as a director
     */

    public function testGetFulltimeAccounts() {
        // Dispatch page request and ensure it's successful

        $this->dispatch('/staff/ajax');

        $this->assertController("ajax");
        $this->assertAction("index");
        $this->assertResponseCode(200);

        // Parse the response
        $response = Zend_Json::decode($this->getResponse()->getBody());
        $fullTimeAcc = $response['payload'];
//Two full time accounts: Director and Coordinator Test accounts
        $this->assertEquals(count($fullTimeAcc), 2);
    }

    /*
     * Test GetVolunteerStaff: 
     * Assertion: Asserts the database has no volunteer members.
     *
     */
    public function testGetVolunteerStaff() {

        $this->dispatch('/staff/ajax/volunteers');

        $this->assertController("ajax");
        $this->assertAction("volunteers");
        $this->assertResponseCode(200);

        // Parse the response
        $response = Zend_Json::decode($this->getResponse()->getBody());
        //Get only volunteer staff members
        $volunteers = $response['payload']['items'];

        //Assert that there are no volunteer staff members in the database as of now
        $this->assertEquals(0, count($volunteers));
    }

    /*
     * Test Contributions
      Assertion: Asserts the database currently has no contribution hours.
     */

    public function testContributions() {

        // Dispatch page request and ensure it's successful
        $this->dispatch('/staff/ajax/contributions');
        $this->assertController("ajax");
        $this->assertAction("contributions");
        $this->assertResponseCode(200);

        // Parse the response
        $response = Zend_Json::decode($this->getResponse()->getBody());
        $contributions = $response['payload']['items'];
        $programs = $response['payload']['programs'];
        //Assert that there are not contribution hours in the database as of now
        $this->assertEquals(0, count($contributions));
        //Assert that there are no programs in the database as of now
        $this->assertEquals(0, count($programs));
    }

    /*
     * Test creating a volunteer
     * Action:     Create a volunteer staff member
     * Assertion:    Asserts that this staff member is the only staff member returned and nothing else
     */

    public function testCreateVolunteer() {
        // create data array
        $data = array(
            'first_name' => 'VolunteerA',
            'last_name' => 'Volunteerlastname',
            'full_name'=>'VolunteerA Volunteerlastname',
            'email' => 'volunteeremail@email.com',
            'job_type' => 2
        );

        // Set post data
        $this->request->setMethod('POST')
                ->setPost($data);

        // Dispatch page request and ensure it's successful
        $this->dispatch('/staff/ajax/createvolunteer');
        $this->assertController("ajax");
        $this->assertAction("createvolunteer");
        $this->assertResponseCode(200);

        // Parse the response
        $response = Zend_Json::decode($this->getResponse()->getBody());


        $volunteerId = $response['payload']['id'];
        //Test getStaff, see if volunteer information matches
        $this->resetRequest()
                ->resetResponse();

        // Set get data
        $this->request->setMethod('GET')
                ->setPost(array(
                    'id' => $volunteerId
        ));

        // Dispatch page request to retrieve the newly created volunteer from the DB
        $this->dispatch('/staff/ajax/volunteers');
        $this->assertController("ajax");
        $this->assertAction("volunteers");
        $this->assertResponseCode(200);

        // Parse the response
        $response = Zend_Json::decode($this->getResponse()->getBody());

        $volunteerRetrieved = $response['payload'];
        $data['id'] = $volunteerId;
        $data['comments'] = NULL;
        
      
       
        $this->assertTrue($data == $volunteerRetrieved);
        $this->resetRequest()
                ->resetResponse();

        $this->request->setMethod('POST')
                ->setPost(array('id' => $volunteerId));


        $this->dispatch('/staff/ajax/volunteers');
        $this->assertController("ajax");
        $this->assertAction("volunteers");
        //check if created volunteer exists in the volunteer staff members database
        $this->assertResponseCode(200);

        // Parse the response
        $response = Zend_Json::decode($this->getResponse()->getBody());

        $volunteerFoundInDatabase = $response['payload'];

        //Esure that the created volunteer exists in the set of all volunteers

        $this->assertTrue($data == $volunteerFoundInDatabase);

//Delete the volunteer we just created
        // Reset the request and the response to do another dispatch
        $this->resetRequest()
                ->resetResponse();

        // Set post data
        $this->request->setMethod('POST')
                ->setPost(array(
                    'id' => $volunteerId
        ));

        // Dispatch page request to clean up and delete the new volunteer created
        $this->dispatch('/staff/ajax/deleteuser');
    }

    public function testDeleteUser() {
        //Test creating a user
        // create data array
        $data = array(
            'first_name' => 'Userfirstname',
            'last_name' => 'Userlastname',
             'full_name' => 'Userfirstname Userlastname',
            'email' => 'someEmail@email.com',
            'job_type' => 2
        );

        // Set post data
        $this->request->setMethod('POST')
                ->setPost($data);

        // Dispatch page request and ensure it's successful
        $this->dispatch('/staff/ajax/createvolunteer');
        $this->assertController("ajax");
        $this->assertAction("createvolunteer");
        $this->assertResponseCode(200);

        // Parse the response
        $response = Zend_Json::decode($this->getResponse()->getBody());


        $userId = $response['payload']['id'];
        $this->resetRequest()
                ->resetResponse();

        // Set get data
        $this->request->setMethod('GET')
                ->setPost(array(
                    'id' => $userId
        ));

        // Dispatch page request to retrieve the newly created user from the DB
        $this->dispatch('/staff/ajax/volunteers');
        $this->assertController("ajax");
        $this->assertAction("volunteers");
        $this->assertResponseCode(200);

        // Parse the response
        $response = Zend_Json::decode($this->getResponse()->getBody());
        $data['id'] = $userId;
        $data['comments'] = NULL;
        $userCreated = $response['payload'];
        
 
        
        $this->assertTrue($data == $userCreated);

        
        //Delete user
        // Reset the request and the response to do another dispatch
        $this->resetRequest()
                ->resetResponse();

        // Set post data
        $this->request->setMethod('POST')
                ->setPost(array(
                    'id' => $userId
        ));

        // Dispatch page request to clean up and delete the new volunteer created
        $this->dispatch('/staff/ajax/deleteuser');


        //Do another dispatch to check if this user is deleted
        $this->resetRequest()
                ->resetResponse();

        $this->request->setMethod('POST')
                ->setPost(array(
                    'id' => $userId
        ));

        // Dispatch page request to retrieve the newly created user from the DB
        $this->dispatch('/staff/ajax/volunteers');
        $this->assertController("ajax");
        $this->assertAction("volunteers");
        $this->assertResponseCode(200);

        // Parse the response
        $response = Zend_Json::decode($this->getResponse()->getBody());

        $this->assertEquals(count($response['payload']), 0);
    }

    /*
     * Test editing information of a volunteer
     */

    public function testEditStaffMemberInfo() {


        //Create a member
        // create data array
        $data = array(
            'first_name' => 'VolunteerA',
            'last_name' => 'Volunteerlastname',
             'full_name' => 'VolunteerA Volunteerlastname',
            'email' => 'volunteeremail@email.com',
            'job_type' => 2
        );

        // Set post data
        $this->request->setMethod('POST')
                ->setPost($data);

        // Dispatch page request and ensure it's successful
        $this->dispatch('/staff/ajax/createvolunteer');
        $this->assertController("ajax");
        $this->assertAction("createvolunteer");
        $this->assertResponseCode(200);

        // Parse the response
        $response = Zend_Json::decode($this->getResponse()->getBody());


        $volunteerId = $response['payload']['id'];
        //Test getStaff, see if volunteer information matches
        $this->resetRequest()
                ->resetResponse();

        // Set get data
        $this->request->setMethod('GET')
                ->setPost(array(
                    'id' => $volunteerId
        ));

        // Dispatch page request to retrieve the newly created volunteer from the DB
        $this->dispatch('/staff/ajax/volunteers');
        $this->assertController("ajax");
        $this->assertAction("volunteers");
        $this->assertResponseCode(200);

        // Parse the response
        $response = Zend_Json::decode($this->getResponse()->getBody());


        $volunteerRetrieved = $response['payload'];
        $data['id'] = $volunteerId;
        $data['comments'] = NULL;
        
             
        $this->assertTrue($data == $volunteerRetrieved);



//Edit the member we just created



        $editData = array(
            'id' => $volunteerId,
            'first_name' => 'Newname',
            'last_name' => 'Newlastname',
              'full_name' => 'Newname Newlastname',
            'email' => 'newEmail@email.com',
            'job_type' => 1
        );

        $this->resetRequest()
                ->resetResponse();

        // Set post data
        $this->request->setMethod('POST')
                ->setPost($editData);

        // Dispatch page request to clean up and delete the new volunteer created
        $this->dispatch('/staff/ajax/editvolunteer');
        $this->assertController("ajax");
        $this->assertAction("editvolunteer");
        $this->assertResponseCode(200);

        //Do another dispatch to see if the user with the id has been changed
        $this->resetRequest()
                ->resetResponse();

        // Set get data
        $this->request->setMethod('GET')
                ->setPost(array(
                    'id' => $volunteerId
        ));

        // Dispatch page request to retrieve the newly created volunteer from the DB
        $this->dispatch('/staff/ajax/volunteers');
        $this->assertController("ajax");
        $this->assertAction("volunteers");
        $this->assertResponseCode(200);

        // Parse the response
        $response = Zend_Json::decode($this->getResponse()->getBody());



        $newDataFound = $response['payload'];
        $editData['id'] = $volunteerId;
        $editData['comments'] = NULL;
      
        $this->assertTrue($editData == $newDataFound);



//delete the volunteer we just created
        // Reset the request and the response to do another dispatch
        $this->resetRequest()
                ->resetResponse();

        // Set post data
        $this->request->setMethod('POST')
                ->setPost(array(
                    'id' => $volunteerId
        ));

        // Dispatch page request to clean up and delete the new volunteer created
        $this->dispatch('/staff/ajax/deleteuser');
    }

    /*
     * Test creating hours for members
     * Action: Create Volunteer and program, and create hours for volunteer
     * Assertion: Verify that contributions exists under the volunteer
     */

    public function testCreateStaffHours() {
        //Create member
        // create data array
        $data = array(
            'first_name' => 'VolunteerB',
            'last_name' => 'Volunteerlastname1',
            'full_name' => 'VolunteerB Volunteerlastname1',
            'email' => 'volunteeremail1@email.com',
            'job_type' => 2
        );

        // Set post data
        $this->request->setMethod('POST')
                ->setPost($data);

        // Dispatch page request and ensure it's successful
        $this->dispatch('/staff/ajax/createvolunteer');
        $this->assertController("ajax");
        $this->assertAction("createvolunteer");
        $this->assertResponseCode(200);

        // Parse the response
        $response = Zend_Json::decode($this->getResponse()->getBody());


        $volunteerId = $response['payload']['id'];
        //Test getStaff, see if volunteer information matches
        $this->resetRequest()
                ->resetResponse();

        // Set get data
        $this->request->setMethod('GET')
                ->setPost(array(
                    'id' => $volunteerId
        ));

        // Dispatch page request to retrieve the newly created volunteer from the DB
        $this->dispatch('/staff/ajax/volunteers');
        $this->assertController("ajax");
        $this->assertAction("volunteers");
        $this->assertResponseCode(200);

        // Parse the response
        $response = Zend_Json::decode($this->getResponse()->getBody());


        $volunteerRetrieved = $response['payload'];
        $data['id'] = $volunteerId;
        $data['comments'] = Null;
       
        $this->assertTrue($data == $volunteerRetrieved);
        $this->resetRequest()
                ->resetResponse();


        //create contributions for user we just created
        //Create a program
        // create data array
        $programdata = array(
            'name' => 'Program Test A1_____',
            'coordinators' => array()
        );
        // Set post data
        $this->request->setMethod('POST')
                ->setPost($programdata);


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
        $programdata['id'] = $id;



        //check that the program name matches
        $this->assertEquals($programdata['name'], $programName);

        $contributionsData = array(
            'staff' => $volunteerId,
            'program' => $programdata['id'],
            'date' => '2012/3/01',
            'hours' => '10'
        );


        $this->resetRequest()
                ->resetResponse();

        // Set get data
        $this->request->setMethod('POST')
                ->setPost($contributionsData);

        // Dispatch page request to retrieve the newly created volunteer from the DB
        $this->dispatch('/staff/ajax/createcontribution');
        $this->assertController("ajax");
        $this->assertAction("createcontribution");
        $this->assertResponseCode(200);

        $response = Zend_Json::decode($this->getResponse()->getBody());
        $contributionsId = $response['payload']['id'];


//View contribution hours that we had created for the volunteer
        $this->resetRequest()
                ->resetResponse();

        $contributionsDataId = array(
            'id' => $contributionsId
        );

        // Set post data
        $this->request->setMethod('POST')
                ->setPost($contributionsDataId);

        // Dispatch page request and ensure it's successful
        $this->dispatch('/staff/ajax/contributions');
        $this->assertController("ajax");
        $this->assertAction("contributions");
        $this->assertResponseCode(200);

        $response = Zend_Json::decode($this->getResponse()->getBody());
        $hoursReceived = $response['payload']['hours'];
        $programsReceived = $response['payload']['programs'][0];



        $this->assertEquals($programsReceived['id'], $contributionsData['program']);
        //Ensure that the contribution hour details matches the $contributionsData and the volunteer information we created

        $this->assertEquals($hoursReceived['id'], $contributionsId);
        $this->assertEquals($hoursReceived['staff_id'], $contributionsData['staff']);
        $this->assertEquals($hoursReceived['program_id'], $contributionsData['program']);
        $this->assertEquals($hoursReceived['date'], '2012-03-01');
        $this->assertEquals($hoursReceived['hours'], $contributionsData['hours']);
        $this->assertEquals($hoursReceived['first_name'], $data['first_name']);
        $this->assertEquals($hoursReceived['last_name'], $data['last_name']);
        $this->assertEquals($hoursReceived['email'], $data['email']);
        $this->assertEquals($hoursReceived['comments'], $data['comments']);
        $this->assertEquals($hoursReceived['job_type'], $data['job_type']);


        //Delete the volunteer we just created
        // Reset the request and the response to do another dispatch
        $this->resetRequest()
                ->resetResponse();

        // Set post data
        $this->request->setMethod('POST')
                ->setPost(array(
                    'id' => $volunteerId
        ));

        // Dispatch page request to clean up and delete the new volunteer created
        $this->dispatch('/staff/ajax/deleteuser');

        $this->resetRequest()
                ->resetResponse();

        // Set post data
        $this->request->setMethod('POST')
                ->setPost(array(
                    'id' => $programdata['id']
        ));


        // Dispatch page request to clean up and delete the new program created
        $this->dispatch('/programs/ajax/delete');
    }

    /*
     * Test Edit Staff Hours
     */

    public function testEditStaffHours() {
        //Create Volunteer

        $data = array(
            'first_name' => 'VolunteerB',
            'last_name' => 'Volunteerlastname1',
            'full_name' => 'VolunteerB Volunteerlastname1',
            'email' => 'volunteeremail1@email.com',
            'job_type' => 2
        );

        // Set post data
        $this->request->setMethod('POST')
                ->setPost($data);

        // Dispatch page request and ensure it's successful
        $this->dispatch('/staff/ajax/createvolunteer');
        $this->assertController("ajax");
        $this->assertAction("createvolunteer");
        $this->assertResponseCode(200);

        // Parse the response
        $response = Zend_Json::decode($this->getResponse()->getBody());


        $volunteerId = $response['payload']['id'];
        //Test getStaff, see if volunteer information matches
        $this->resetRequest()
                ->resetResponse();

        // Set get data
        $this->request->setMethod('GET')
                ->setPost(array(
                    'id' => $volunteerId
        ));

        // Dispatch page request to retrieve the newly created volunteer from the DB
        $this->dispatch('/staff/ajax/volunteers');
        $this->assertController("ajax");
        $this->assertAction("volunteers");
        $this->assertResponseCode(200);

        // Parse the response
        $response = Zend_Json::decode($this->getResponse()->getBody());


        $volunteerRetrieved = $response['payload'];
        $data['id'] = $volunteerId;
        $data['comments'] = Null;
         
        $this->assertTrue($data == $volunteerRetrieved);
        $this->resetRequest()
                ->resetResponse();


        //Create contributions for user we just created
        //Create a program
        $programdata = array(
            'name' => 'Program Test A1_____',
            'coordinators' => array()
        );
        // Set post data
        $this->request->setMethod('POST')
                ->setPost($programdata);


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
        $programdata['id'] = $id;



        //check that the program name matches
        $this->assertEquals($programdata['name'], $programName);

        $contributionsData = array(
            'staff' => $volunteerId,
            'program' => $programdata['id'],
            'date' => '2012/3/01',
            'hours' => '100'
        );


        $this->resetRequest()
                ->resetResponse();

        // Set get data
        $this->request->setMethod('POST')
                ->setPost($contributionsData);

        // Dispatch page request to retrieve the newly created volunteer from the DB
        $this->dispatch('/staff/ajax/createcontribution');
        $this->assertController("ajax");
        $this->assertAction("createcontribution");
        $this->assertResponseCode(200);

        $response = Zend_Json::decode($this->getResponse()->getBody());
        $contributionsId = $response['payload']['id'];


//View contribution hours that we had created for the volunteer
        $this->resetRequest()
                ->resetResponse();

        $contributionsDataId = array(
            'id' => $contributionsId
        );

        // Set post data
        $this->request->setMethod('POST')
                ->setPost($contributionsDataId);

        // Dispatch page request and ensure it's successful
        $this->dispatch('/staff/ajax/contributions');
        $this->assertController("ajax");
        $this->assertAction("contributions");
        $this->assertResponseCode(200);

        $response = Zend_Json::decode($this->getResponse()->getBody());
        $hoursReceived = $response['payload']['hours'];
        $programsReceived = $response['payload']['programs'][0];


        //$this->assertEquals($hoursReceived, $data['email']);
        $this->assertEquals($programsReceived['id'], $contributionsData['program']);
        //Ensure that the contribution hour details matches the $contributionsData and the volunteer information we created

        $this->assertEquals($hoursReceived['id'], $contributionsId);
        $this->assertEquals($hoursReceived['staff_id'], $contributionsData['staff']);
        $this->assertEquals($hoursReceived['program_id'], $contributionsData['program']);
        $this->assertEquals($hoursReceived['date'], '2012-03-01');
        $this->assertEquals($hoursReceived['hours'], $contributionsData['hours']);
        $this->assertEquals($hoursReceived['first_name'], $data['first_name']);
        $this->assertEquals($hoursReceived['last_name'], $data['last_name']);
        $this->assertEquals($hoursReceived['email'], $data['email']);
        $this->assertEquals($hoursReceived['comments'], $data['comments']);
        $this->assertEquals($hoursReceived['job_type'], $data['job_type']);

        //Edit the contribution hours that we just created

        $editContributionHours = array(
            'id' => $contributionsId,
            'staff' => $volunteerId,
            'program' => $contributionsData['program'],
            'date' => '2012/5/22',
            'hours' => '200'
        );

        $this->resetRequest()
                ->resetResponse();
        $this->request->setMethod('POST')
                ->setPost($editContributionHours);

        // Dispatch page request and ensure it's successful
        $this->dispatch('/staff/ajax/editcontribution');
        $this->assertController("ajax");
        $this->assertAction("editcontribution");
        $this->assertResponseCode(200);

        //Do another dispatch to view contributions if it contains the new data changed
        $this->resetRequest()
                ->resetResponse();

        $this->request->setMethod('POST')
                ->setPost(array('id' => $contributionsId));

        // Dispatch page request and ensure it's successful
        $this->dispatch('/staff/ajax/contributions');
        $this->assertController("ajax");
        $this->assertAction("contributions");
        $this->assertResponseCode(200);

        $response = Zend_Json::decode($this->getResponse()->getBody());
        $hoursReceived = $response['payload']['hours'];

        $this->assertEquals($hoursReceived['id'], $contributionsId);
        $this->assertEquals($hoursReceived['staff_id'], $editContributionHours['staff']);
        $this->assertEquals($hoursReceived['program_id'], $editContributionHours['program']);
        $this->assertEquals($hoursReceived['date'], '2012-05-22');
        $this->assertEquals($hoursReceived['hours'], $editContributionHours['hours']);
        $this->assertEquals($hoursReceived['first_name'], $data['first_name']);
        $this->assertEquals($hoursReceived['last_name'], $data['last_name']);
        $this->assertEquals($hoursReceived['email'], $data['email']);
        $this->assertEquals($hoursReceived['comments'], $data['comments']);
        $this->assertEquals($hoursReceived['job_type'], $data['job_type']);



        //delete the volunteer we just created
        // Reset the request and the response to do another dispatch
        $this->resetRequest()
                ->resetResponse();

        // Set post data
        $this->request->setMethod('POST')
                ->setPost(array(
                    'id' => $volunteerId
        ));


        // Dispatch page request to clean up and delete the new volunteer created
        $this->dispatch('/staff/ajax/deleteuser');

        $this->resetRequest()
                ->resetResponse();

        // Set post data
        $this->request->setMethod('POST')
                ->setPost(array(
                    'id' => $programdata['id']
        ));


        // Dispatch page request to clean up and delete the new program created
        $this->dispatch('/programs/ajax/delete');
    }

    /*
      Test Delete Staff Hours
     */

    public function testDeleteStaffHours() {
        //Create staff and his/her hours
        $data = array(
            'first_name' => 'VolunteerB',
            'last_name' => 'Volunteerlastname1',
            'full_name' => 'VolunteerB Volunteerlastname1',
            'email' => 'volunteeremail1@email.com',
            'job_type' => 2
        );

        // Set post data
        $this->request->setMethod('POST')
                ->setPost($data);

        // Dispatch page request and ensure it's successful
        $this->dispatch('/staff/ajax/createvolunteer');
        $this->assertController("ajax");
        $this->assertAction("createvolunteer");
        $this->assertResponseCode(200);

        // Parse the response
        $response = Zend_Json::decode($this->getResponse()->getBody());


        $volunteerId = $response['payload']['id'];
        //Test getStaff, see if volunteer information matches
        $this->resetRequest()
                ->resetResponse();

        // Set get data
        $this->request->setMethod('GET')
                ->setPost(array(
                    'id' => $volunteerId
        ));

        // Dispatch page request to retrieve the newly created volunteer from the DB
        $this->dispatch('/staff/ajax/volunteers');
        $this->assertController("ajax");
        $this->assertAction("volunteers");
        $this->assertResponseCode(200);

        // Parse the response
        $response = Zend_Json::decode($this->getResponse()->getBody());


        $volunteerRetrieved = $response['payload'];
        $data['id'] = $volunteerId;
        $data['comments'] = Null;
         
        $this->assertTrue($data == $volunteerRetrieved);
        $this->resetRequest()
                ->resetResponse();


        //create contributions for user we just created
        //Create a program
        $programdata = array(
            'name' => 'Program Test A1_____',
            'coordinators' => array()
        );
        // Set post data
        $this->request->setMethod('POST')
                ->setPost($programdata);


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
        $programdata['id'] = $id;



        //check that the program name matches
        $this->assertEquals($programdata['name'], $programName);

        $contributionsData = array(
            'staff' => $volunteerId,
            'program' => $programdata['id'],
            'date' => '2012/3/01',
            'hours' => '10'
        );


        $this->resetRequest()
                ->resetResponse();

        // Set get data
        $this->request->setMethod('POST')
                ->setPost($contributionsData);

        // Dispatch page request to retrieve the newly created volunteer from the DB
        $this->dispatch('/staff/ajax/createcontribution');
        $this->assertController("ajax");
        $this->assertAction("createcontribution");
        $this->assertResponseCode(200);

        $response = Zend_Json::decode($this->getResponse()->getBody());
        $contributionsId = $response['payload']['id'];


//View contribution hours that we had created for the volunteer
        $this->resetRequest()
                ->resetResponse();

        $contributionsDataId = array(
            'id' => $contributionsId
        );

        // Set post data
        $this->request->setMethod('POST')
                ->setPost($contributionsDataId);

        // Dispatch page request and ensure it's successful
        $this->dispatch('/staff/ajax/contributions');
        $this->assertController("ajax");
        $this->assertAction("contributions");
        $this->assertResponseCode(200);

        $response = Zend_Json::decode($this->getResponse()->getBody());
        $hoursReceived = $response['payload']['hours'];
        $programsReceived = $response['payload']['programs'][0];


        //$this->assertEquals($hoursReceived, $data['email']);
        $this->assertEquals($programsReceived['id'], $contributionsData['program']);
        //Ensure that the contribution hour details matches the $contributionsData and the volunteer information we created

        $this->assertEquals($hoursReceived['id'], $contributionsId);
        $this->assertEquals($hoursReceived['staff_id'], $contributionsData['staff']);
        $this->assertEquals($hoursReceived['program_id'], $contributionsData['program']);
        $this->assertEquals($hoursReceived['date'], '2012-03-01');
        $this->assertEquals($hoursReceived['hours'], $contributionsData['hours']);
        $this->assertEquals($hoursReceived['first_name'], $data['first_name']);
        $this->assertEquals($hoursReceived['last_name'], $data['last_name']);
        $this->assertEquals($hoursReceived['email'], $data['email']);
        $this->assertEquals($hoursReceived['comments'], $data['comments']);
        $this->assertEquals($hoursReceived['job_type'], $data['job_type']);


        //Now, delete the hours of the volunteer
        $this->resetRequest()
                ->resetResponse();
        $this->request->setMethod('POST')
                ->setPost(array(
                    'id' => $contributionsId
        ));
        $this->dispatch('/staff/ajax/deletecontribution');
        $this->assertController("ajax");
        $this->assertAction("deletecontribution");
        $this->assertResponseCode(200);


        $response = Zend_Json::decode($this->getResponse()->getBody());

        $deleteSucess = $response['payload'];
        //Assert that the contribution hours was deleted successfully
        $this->assertEquals($deleteSucess, 1);
        //Delete the volunteer we just created
        // Reset the request and the response to do another dispatch
        $this->resetRequest()
                ->resetResponse();

        // Set post data
        $this->request->setMethod('POST')
                ->setPost(array(
                    'id' => $volunteerId
        ));

        // Dispatch page request to clean up and delete the new volunteer created
        $this->dispatch('/staff/ajax/deleteuser');

        $this->resetRequest()
                ->resetResponse();

        // Set post data
        $this->request->setMethod('POST')
                ->setPost(array(
                    'id' => $programdata['id']
        ));


        // Dispatch page request to clean up and delete the new program created
        $this->dispatch('/programs/ajax/delete');
    }

    /*
     * Test creating a user
     */

    public function testCreateUser() {

        $data = array(
            'first_name' => 'Staff',
            'last_name' => 'Lastname',
            'email' => 'StaffNameTest@gmail.com',
            'permission_level' => 0,
            'programs' => array()
        );

        $this->resetRequest()->resetResponse();
        // Set post data
        $this->request->setMethod('POST')->setPost($data);

        // Dispatch page request and ensure it's successful
        $this->dispatch('/staff/ajax/createuser');
        $this->assertController("ajax");
        $this->assertAction("createuser");
        $this->assertResponseCode(200);



        $response = Zend_Json::decode($this->getResponse()->getBody());



        $id = $response['payload']['staff_id'];


        $this->resetRequest()->resetResponse();

        // Set get data
        $this->request->setMethod('GET')->setPost(array('id' => $id));

// Dispatch page request to retrieve the newly created staff member from the DB
        $this->dispatch('/staff/ajax');
        $this->assertController("ajax");
        $this->assertAction("index");
        $this->assertResponseCode(200);
        //Ensure that the currently logged in Director could create a user
        $response = Zend_Json::decode($this->getResponse()->getBody());



        $foundUser = $response['payload']['user'];

        $data['id'] = $id;
        //Check that the user created by the director exists in the database
        $this->assertEquals($foundUser['programs'], NULL);

        $this->assertEquals($foundUser['email'], $data['email']);
        $this->assertEquals($foundUser['id'], $data['id']);
        $this->assertEquals($foundUser['first_name'], $data['first_name']);
        $this->assertEquals($foundUser['last_name'], $data['last_name']);
        $this->assertEquals($foundUser['permission_level'], $data['permission_level']);

        //Delete the user created
        $this->resetRequest()
                ->resetResponse();

        // Set post data
        $this->request->setMethod('POST')
                ->setPost(array(
                    'id' => $id
        ));


        $this->dispatch('/staff/ajax/deleteuser');
    }

    /**
     * Logout the user when done
     */
    public function tearDown() {
        $this->auth->logout();
    }

}
