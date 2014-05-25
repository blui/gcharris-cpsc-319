#!/usr/bin/php
<?php

require_once 'init.php';

$family = new Application_Model_Family();

$first = csv_to_array('first.csv');
$last = csv_to_array('last.csv');

$first_count = count($first); 
$last_count = count($last); 


$countries = new Application_Model_DbTable_Country();
$languages = new Application_Model_DbTable_Language();


$country_list = $countries->fetchAll()->toArray();
$language_list = $languages->fetchAll()->toArray();


for($i = 0; $i < 1000000; $i++){
	
	$data = array();
	$firstname = $first[rand(0, $first_count - 1)]['firstname'];
	$lastname = $last[rand(0, $last_count - 1)]['lastname'];
	
	$data['guardian_first_name'] = $firstname;
	$data['guardian_last_name'] =  $lastname;
	$data['guardian_role'] = randomRelation();
	$data['guardian_origin_country'] =  $country_list[rand(0, count($country_list) - 1)]['code'];
	$data['guardian_first_lang'] = $language_list[rand(0, count($language_list) - 1)]['lang_code'];
	$data['guardian_partner_first_name'] = $first[rand(0, $first_count - 1)]['firstname'];
	$data['guardian_partner_last_name'] = $lastname;
	$data['guardian_email'] = randomEmail($firstname, $lastname);
	$data['phone_number'] = substr(number_format(time() * rand(),0,'',''),0,10);
	$data['children'] = createSomeChildren($lastname, $first, $first_count);
	$data['registration_date'] = date('Y-m-d');
	$data['postal_3dig'] = rand3dig();
	$data['allergies'] = randomAllergy();
	$data['hear_about_us'] = rand(1,6);
	$data['emerg_contact_first_name'] = $first[rand(0, $first_count - 1)]['firstname']; 
	$data['emerg_contact_last_name'] = $last[rand(0, $last_count - 1)]['lastname']; 
	$data['emerg_contact_relation'] = randomRelationEmerg();
	$data['emerg_contact_phone'] = substr(number_format(time() * rand(),0,'',''),0,10);
	print_r($data);

	$family->createFamily($data);
	
	

}

function weightedrand($min, $max, $gamma) {
    $offset= $max-$min+1;
    return floor($min+pow(lcg_value(), $gamma)*$offset);
}

function createSomeChildren($lastname, $first_list, $first_count){
$children = array();
$number =  weightedrand(1, 6, 7);
  for($i = 0; $i < $number; $i++){
  	$child = array();         
                    $child['first_name'] = $first_list[rand(0, $first_count - 1)]['firstname'];
                    $child['last_name'] = $lastname;
                 //   $child['child_full_name'] =  $child['first_name'] . " " . $child['last_name'];
                    $child['birthday'] = rand(0,12) . "/" . rand(0,28) . "/" . rand(1998,2012);
    $children[] = $child;
  }              
           return $children;
                    	
	
}

function randomRelation(){
 
 $relation = array('Parent/Step-parent', 'Grandparent', 'Siblng');
 return $relation[weightedrand(0, 2, 6)];
}

function randomRelationEmerg(){
 $relation = array('Friend', 'Grandparent', 'Uncle', 'Aunt', 'Teacher');
 return $relation[rand(0, 4)];
}

function randomEmail($firstname, $lastname){
	

	
	$email = "";
	
	$domains = array('google.ca', 'google.com', 'example.com', 'mailtest.com', 'hotmail.com', 'live.ca','test.com','email.com');

	
	switch(rand(0,5)){
		
		case 0:
			$email .= $firstname . "." . $lastname;
		break;
		
		case 1:
		 	$email .= $lastname . "." . $firstname;
		break;

		case 2:
			$email .= substr($firstname, 0, 1) . $lastname;

		break;		
		
		case 3:
			$email .= $firstname . substr($lastname, 0, 1);

		break;
				
		case 4:
			$email .= $firstname . "_" . $lastname;

		break;
		
		case 5:
			$email .= $firstname . "-" . $lastname;
		break;
		
	}
	
	$email .= rand(0,10000);	
	
	if(rand(0,2)==1){
		$email = strtolower($email);
	}
	$email .= "@" . $domains[rand(0, 7)];
	
	
	return $email;

}

function randomAllergy(){
if(rand(0,12) > 8){
	$allergy = array("Peanuts", "Bees", "Nuts");
	return $allergy[rand(0,2)];
}else{
	return "";
}
}

function rand3dig(){
if(rand(0,10)>2){
	$letters = array('A', 'M', 'B', 'N', 'C','P','E','R','G','S','H','T','J','V','K','X','L');
	$first = $letters[rand(0,16)];
	$second = rand(1,9);
	$third = $letters[rand(0,16)];
	return $first . $second . $third;
}else{
	
	return "";
}
	
}

function csv_to_array($filename='')
{
ini_set('auto_detect_line_endings', true);
    if(!file_exists($filename) || !is_readable($filename))
        return FALSE;

    $header = NULL;
    $data = array();
    if (($handle = fopen($filename, 'r')) !== FALSE)
    {
        while (($row = fgetcsv($handle, 1000)) !== FALSE)
        {
            if(!$header)
                $header = $row;
            else
                $data[] = array_combine($header, $row);
        }
        fclose($handle);
    }
    return $data;
}
