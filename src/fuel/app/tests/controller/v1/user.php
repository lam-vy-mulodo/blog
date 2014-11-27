<?php


use Fuel\Core\Input;
use Fuel\Core\Request;
use Fuel\Core\DB;
/**
 * The Test for User Controller version 1.
 * @extends TestCase
 * 
 * @author Lam Vy
 * @group User
 */
class Test_Controller_V1_User extends TestCase {
	
	/**
	 * called before each test method
	 * @before
	 */
	public function setUp() {
			
	}
	
	/**
	 * called after each test method
	 * @after
	 */
	public function tearDown() {
		//to do
		
	}
	
	/**
	 * funtion to test create user , method POST
	 * link is http://localhost/_blog/blog/src/v1/users/
	 * @group validate
	 * @dataProvider validate_provider
	 */
	public function test_validate_user($test_data) {
		
		$method = 'POST' ;
		$link = 'http://localhost/_blog/blog/src/v1/users/' ;
		// called function init_curl to execute request
		$res = $this->init_curl($test_data, $method, $link) ;
		// Compare result
		$this->assertEquals(1001, $res['meta']['code']);
		
	}
	
	/**
	 * function to test check user not exist in controller
	 * method POST
	 * link http://localhost/_blog/blog/src/v1/users/
	 * compare with code error 2001 for exist
	 * @group exist_user_ok
	 *
	 */
	public function test_check_user_exist() {
	
		//create data to test, username is existed in db
		$test_data = array();
		$test_data = array(
				'username' => 'thuyvy',
				'password' => '12345',
				'firstname' => 'vy',
				'lastname' => 'Lam',
				'email' => 'lam.vy@mulodo.com'
		);
		$method = 'POST' ;
		$link = 'http://localhost/_blog/blog/src/v1/users/' ;
		$res =  $this->init_curl($test_data, $method, $link) ;
		
		$this->assertEquals(2001, $res['meta']['code']);
	}
	
	/**
	 * function to test after check username not exist in db
	 * insert and created new account
	 * return code 200 , user info and token
	 * @group create_user_ok
	 *
	 */
	public function test_create_user_ok() {
	
		//create data to test, username is not existed in db, valid data to validation
		$test_data = array();
		$test_data = array(
				'username' => 'thuyvy9999',
				'password' => '12345',
				'firstname' => 'vy',
				'lastname' => 'Lam',
				'email' => 'lam.vy@mulodo.com'
		);
		$method = 'POST' ;
		$link = 'http://localhost/_blog/blog/src/v1/users/' ;
		
		$res =  $this->init_curl($test_data, $method, $link) ;
	    
		$this->assertEquals(200, $res['meta']['code']);
		//compare expected data with result data
		$this->assertGreaterThan(0, $res['data']['id']);
		$this->assertEquals($test_data['username'], $res['data']['username']);
		$this->assertEquals($test_data['email'], $res['data']['email']);
		$this->assertEquals($test_data['lastname'], $res['data']['lastname']);
		$this->assertEquals($test_data['firstname'], $res['data']['firstname']);		
		$this->assertEquals(40, strlen($res['data']['token'])) ;
				
		//delete user have been created to test username is not exist in db
		//user data can insert when username not exist
		self::remove_user($res['data']['id']) ;
	}
	
	/**
	 * function to test create user not ok
	 * 
	 * before run this test, stop execute query insert in model user
	 * return 0 if not record created
	 * error code 9005
	 * @group create_user_not_ok
	 *
	 */
	public function test_create_user_not_ok() {
	
		//create data to test, username is existed in db
		$test_data = array();
		$test_data = array(
				'username' => 'thuyvy9999',
				'password' => '12345',
				'firstname' => 'vy',
				'lastname' => 'Lam',
				'email' => 'lam.vy@mulodo.com'
		);
		$method = 'POST' ;
		$link = 'http://localhost/_blog/blog/src/v1/users/' ;
		$res =  $this->init_curl($test_data, $method, $link) ;
		print_r($res) ;
		$this->assertEquals(9005, $res['meta']['code']);
	}
        
    /**
     * function to init curl used to api
     * set method, link for request
     * get result from response
     * 
     */
    public function init_curl($test_data,$method,$link) {
    	
    	// create a Request_Curl object
    	$curl = Request::forge($link, 'curl');
    	// this is going to be an HTTP POST
    	$curl->set_method($method);
    	// set some parameters    	
    	$curl->set_params($test_data);
    	// execute the request
    	$curl->execute();
    	// Get response object
    	$result = $curl->response();
    	
    	// Get response body
    	$res = json_decode($result->body(), true);
    	// return response
    	//print_r($test_data) ; die ;
    	return $res ;
    }
    
    /**
     * reset username - it will not exist to test create
     * remove user account have been created when call test create user
     */

	public static function remove_user($user_id) {
		
		// try catch to execute query db
		try {
			$query = DB::delete('user')->where('id' ,' = ' ,$user_id)->execute();
			
			return ( $query == 1) ? true : false ;
		} catch ( Exception $ex ) {
				
			Log::error ( $ex->getMessage () );
			return $ex->getMessage ();
		}
	}
	/**
	 * provider data for function test validate_user
	 * @return array Test data
	 */
	public function validate_provider() {
		$test_data = array();
		// Null username
		$test_data[][] = array(
				'username' => '',
				'password' => '12345',
				'firstname' => 'vy',
				'lastname' => 'Lam',
				'email' => 'lam.vy@mulodo.com'
		);
		//Null password
		$test_data[][] = array(
				'username' => 'thuyvy',
				'password' => '',
				'firstname' => 'vy',
				'lastname' => 'Lam',
				'email' => 'lam.vy@mulodo.com'
		);
		// Null email
		$test_data[][] = array(
				'username' => 'thuyvy',
				'password' => '12345',
				'firstname' => 'vy',
				'lastname' => 'Lam',
				'email' => ''
		);
		// Null  lastname
		$test_data[][] = array(
				'username' => 'thuyvy',
				'password' => '12345',
				'firstname' => 'vy',
				'lastname' => '',
				'email' => 'lam.vy@mulodo.com'
		);
		// Null firstname
		$test_data[][] = array(
				'username' => 'thuyvy',
				'password' => '12345',
				'firstname' => '',
				'lastname' => 'Lam',
				'email' => 'lam.vy@mulodo.com'
		);
		//username contain at least 5 characters
		$test_data[][] = array(
				'username' => 'th',
				'password' => '12345',
				'firstname' => 'vy',
				'lastname' => 'Lam',
				'email' => 'lam.vy@mulodo.com'
		);
		//password contain at least 5 characters
		$test_data[][] = array(
				'username' => 'thuyvy',
				'password' => '123',
				'firstname' => 'vy',
				'lastname' => 'Lam',
				'email' => 'lam.vy@mulodo.com'
		);
		//username contain special characters
		$test_data[][] = array(
				'username' => 'vyE^%^',
				'password' => '12345',
				'firstname' => 'vy',
				'lastname' => 'Lam',
				'email' => 'lam.vy.mulodo.com'
		);
		//password contain special characters
		$test_data[][] = array(
				'username' => 'thuyvy',
				'password' => '12345#$@%',
				'firstname' => 'vy',
				'lastname' => 'Lam',
				'email' => 'lam.vy.mulodo.com'
		);
		//username contain more 50 characters
		$test_data[][] = array(
				'username' => 'aaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa',
				'password' => '12345',
				'firstname' => 'vy',
				'lastname' => 'Lam',
				'email' => 'lam.vy.mulodo.com'
		);
		//username contain more 50 characters
		$test_data[][] = array(
				'username' => 'thuyvy',
				'password' => '12345hggsirge345iywyiuhqihejqwb2556621jdhusfhiuiyuqwueg742394676769y98i',
				'firstname' => 'vy',
				'lastname' => 'Lam',
				'email' => 'lam.vy.mulodo.com'
		);
		//email not correct format
		$test_data[][] = array(
				'username' => 'thuyvy',
				'password' => '12345',
				'firstname' => 'vy',
				'lastname' => 'Lam',
				'email' => 'lam.vy.mulodo.com'
		);
		return $test_data;
	}
	
}