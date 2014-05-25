<?php
/**
* Hashing and password functions for user creation and login
*
* @package    Frp
* @author     Grant Harris
* @version    1.0
*/
class Frp_Hashing {

    /**
     * Generate a hashing salt for storing user passwords
     * @return string
     */
    public static function generateSalt() {
        $salt = '$2a$13$';
        $salt = $salt . md5(mt_rand());
        return $salt;
    }

    /**
     * Given a salt and apssword, return a password hash
     * @param type $salt
     * @param type $password
     * @return string
     */
    public static function generateHash($salt, $password) {
        $hash = crypt($password, $salt);
        return substr($hash, 29);
    }

    /**
     * Generates a random password for use with new users
     * @return string
     */
    function generatePassword() {
        //Initialize the random password
        $password = '';

        //Initialize a random desired length
        $desired_length = rand(8, 12);
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        for ($length = 0; $length < $desired_length; $length++) {
            //Append a random ASCII character (including symbols)
            $password .= $characters[mt_rand(0, strlen($characters) - 1)];
        }

        return $password;
    }
    
    /**
     * Generates a random verificartion string, used for password reset
     * @return string
     */
    public static function generateResetString(){
        return base_convert(md5(mt_rand()), 16, 36);
    }


}
