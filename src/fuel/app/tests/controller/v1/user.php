<?php


use Fuel\Core\Input;
use Fuel\Core\Request;
use Fuel\Core\DB;
use Auth\Auth;
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
		
		$method = 'POST';
		$link = 'http://localhost/_blog/blog/src/v1/users/';
		// called function init_curl to execute request
		$res = $this->init_curl($test_data, $method, $link);
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
				'email' => 'lam.vy@mulodo.com',
		);
		$method = 'POST';
		$link = 'http://localhost/_blog/blog/src/v1/users/';
		$res =  $this->init_curl($test_data, $method, $link);
		
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
				'username' => 'thuyvy1010',
				'password' => '12345',
				'firstname' => 'vy',
				'lastname' => 'Lam',
				'email' => 'lam.vy@mulodo.com',
		);
		$method = 'POST';
		$link = 'http://localhost/_blog/blog/src/v1/users/';
		
		$res =  $this->init_curl($test_data, $method, $link);
	    
		$this->assertEquals(200, $res['meta']['code']);
		//compare expected data with result data
		$this->assertGreaterThan(0, $res['data']['id']);
		$this->assertEquals($test_data['username'], $res['data']['username']);
		$this->assertEquals($test_data['email'], $res['data']['email']);
		$this->assertEquals($test_data['lastname'], $res['data']['lastname']);
		$this->assertEquals($test_data['firstname'], $res['data']['firstname']);		
		$this->assertEquals(40, strlen($res['data']['token']));
				
		//delete user have been created to test username is not exist in db
		//user data can insert when username not exist
		self::remove_user($res['data']['id']);
	}
	
	/**
	 * function to test check login ok
	 * return code 200
	 * @link http://localhost/_blog/blog/src/v1/users/login
	 * @group login
	 *
	 */
	public function test_login_ok() {
		//login first for test
		$data['username'] = 'thuyvy';
		$data['password'] = '12345';
	
		//call curl for login
		$method = 'POST';
		$link = 'http://localhost/_blog/blog/src/v1/users/login';
		$rs = $this->init_curl($data, $method, $link);
		//compare rs code for 1204
		//print_r($rs);
		$this->assertEquals($rs['meta']['code'], '200');
		//return token to use update user info function
		return $rs['data'];
	}
	/**
	 * used check login not ok, compare code 1203
	 * @link http://localhost/_blog/blog/src/v1/users/login
	 * @group login
	 * @dataProvider login_not_provider
	 */
	public function test_login_not_ok($test_data) {
			
		
		//call curl for login
		$method = 'POST';
		$link = 'http://localhost/_blog/blog/src/v1/users/login';
		$rs = $this->init_curl($test_data, $method, $link);
	
		//compare rs code for 1204
		//print_r($rs); die;
		$this->assertEquals($rs['meta']['code'], '1203');
		
	}
	
	
	/**
	 * Define test data for check login not ok
	 *
	 * @return array Test data
	 */
	
	public function login_not_provider() {
		$test_data = array();
		//username not exist
		$test_data[][] = array(
				'username' => 'thuyvy88',
				'password' => '1234'
		);
		$test_data[][] = array(
				'username' => 'lam.vy',
				'password' => '1234'
		);
		//test for sql injection
		$test_data[][] = array(
				'username' => 'username` or 1 = 1 -- ',
				'password' => '1234'
		);
	
		return $test_data;
	}
	
    /**
     * function to init curl used to api
     * set method, link for request
     * get result from response
     * 
     */
    public function init_curl($test_data, $method, $link) {
    	
    	
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
    	//print_r($test_data); die;
    	return $res;
    }
    
    /**
     * reset username - it will not exist to test create
     * remove user account have been created when call test create user
     */

	public static function remove_user($user_id) {
		
		// try catch to execute query db
		try {
			$query = DB::delete('user')->where('id', ' = ', $user_id)->execute();
			
			return ($query == 1) ? true : false;
		} catch (Exception $ex) {
				
			Log::error($ex->getMessage());
			return $ex->getMessage();
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
				'email' => 'lam.vy@mulodo.com',
		);
		//Null password
		$test_data[][] = array(
				'username' => 'thuyvy',
				'password' => '',
				'firstname' => 'vy',
				'lastname' => 'Lam',
				'email' => 'lam.vy@mulodo.com',
		);
		// Null email
		$test_data[][] = array(
				'username' => 'thuyvy',
				'password' => '12345',
				'firstname' => 'vy',
				'lastname' => 'Lam',
				'email' => '',
		);
		// Null  lastname
		$test_data[][] = array(
				'username' => 'thuyvy',
				'password' => '12345',
				'firstname' => 'vy',
				'lastname' => '',
				'email' => 'lam.vy@mulodo.com',
		);
		// Null firstname
		$test_data[][] = array(
				'username' => 'thuyvy',
				'password' => '12345',
				'firstname' => '',
				'lastname' => 'Lam',
				'email' => 'lam.vy@mulodo.com',
		);
		//username contain at least 5 characters
		$test_data[][] = array(
				'username' => 'th',
				'password' => '12345',
				'firstname' => 'vy',
				'lastname' => 'Lam',
				'email' => 'lam.vy@mulodo.com',
		);
		//password contain at least 5 characters
		$test_data[][] = array(
				'username' => 'thuyvy',
				'password' => '123',
				'firstname' => 'vy',
				'lastname' => 'Lam',
				'email' => 'lam.vy@mulodo.com',
		);
		//username contain special characters
		$test_data[][] = array(
				'username' => 'vyE^%^',
				'password' => '12345',
				'firstname' => 'vy',
				'lastname' => 'Lam',
				'email' => 'lam.vy.mulodo.com',
		);
		//password contain special characters
		$test_data[][] = array(
				'username' => 'thuyvy',
				'password' => '12345#$@%',
				'firstname' => 'vy',
				'lastname' => 'Lam',
				'email' => 'lam.vy.mulodo.com',
		);
		//username contain more 50 characters
		$test_data[][] = array(
				'username' => 'aaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa',
				'password' => '12345',
				'firstname' => 'vy',
				'lastname' => 'Lam',
				'email' => 'lam.vy.mulodo.com',
		);
		//username contain more 50 characters
		$test_data[][] = array(
				'username' => 'thuyvy',
				'password' => '12345hggsirge345iywyiuhqihejqwb2556621jdhusfhiuiyuqwueg742394676769y98i',
				'firstname' => 'vy',
				'lastname' => 'Lam',
				'email' => 'lam.vy.mulodo.com',
		);
		//email not correct format
		$test_data[][] = array(
				'username' => 'thuyvy',
				'password' => '12345',
				'firstname' => 'vy',
				'lastname' => 'Lam',
				'email' => 'lam.vy.mulodo.com',
		);
		return $test_data;
	}
	
	/**
	 * function to test check token is empty when logout
	 * method PUT
	 * link http://localhost/_blog/blog/src/v1/users/logout
	 * compare with code error 1202
	 * @group token_empty
	 */
	public function test_token_empty() {
		//data
		$test_data['token'] = '';
		$link = 'http://localhost/_blog/blog/src/v1/users/logout';
		$method = 'PUT';
		$rs = $this->init_curl($test_data, $method, $link);
		//compare with error code 1202
		
		$this->assertEquals('1202' ,$rs['meta']['code']);
		
	}
	/**
	 * use test logout function
	 * function to test check token is not exist in db
	 * method PUT
	 * link http://localhost/_blog/blog/src/v1/users/logout
	 * compare with code error 1205
	 * @group token_invalid
	 * @dataProvider token_provider
	 */
	public function test_token_invalid($test_data) {
		
		$link = 'http://localhost/_blog/blog/src/v1/users/logout';		
		$method = 'PUT';
		//call curl request
		$rs = $this->init_curl($test_data, $method, $link);
		//compare with error code
		
		$this->assertEquals('1205', $rs['meta']['code']);
	}
	/**
	 * Define test data set
	 * create token by use sha1
	 * @return array Test data
	 */
	public function token_provider() {
		$test_data = array();
		//loop for auto create
		for ($i = 0; $i < 10; $i++) {
			$test_data[][] = array('token' => sha1(time()));
		}
		 
		return $test_data;
	}
	
	/**
	 * use test validate change password not ok
	 * method PUT
	 * link http://localhost/_blog/blog/src/v1/users/password
	 * compare with code 1004 for not valid
	 * @group validate_pass_notok2
	 * @dataProvider password_provider
	 * @depends test_login_ok
	 */
	public function test_validate_pass_notok($test_data,$data) {
	
		$test_data['token'] = $data['token'];
        //print_r($test_data); die;
		$method = 'PUT';
		//add the user id into the link
		$link = 'http://localhost/_blog/blog/src/v1/users/password';
		$result = $this->init_curl($test_data, $method, $link);
		print_r($result);
		//compare with error code 1004 for invalid data
		$this->assertEquals('1004', $result['meta']['code']);
	}
	
	/**
	 * use test change password ok
	 * method PUT
	 * link http://localhost/_blog/blog/src/v1/users/password
	 * compare with code 200 when success
	 * @group update_pass_ok2
	 * @depends test_login_ok
	 */
	public function test_change_pass_ok($user) {
	
		$data['token'] = $user['token'];
		$data['old_password'] = '12345';
		$data['password'] = 'aaa456';
		//set method and link
		$method = 'PUT';
		//add the user id into the link
		$link = 'http://localhost/_blog/blog/src/v1/users/password';
		$result = $this->init_curl($data, $method, $link);
	
		//compare with 200 code ok
		$this->assertEquals('200', $result['meta']['code']);
		//reset the old password for user id 30
		print_r($result) ;
		$this->reset_password();
		
	}
	
	/**
	 * use test change password not ok
	 * the old password is not match with db
	 * method PUT
	 * link http://localhost/_blog/blog/src/v1/users/password
	 * compare with code 2004 error code
	 * @group update_pass_notok2
	 * @depends test_login_ok
	 */
	public function test_change_pass_notok($user) {
	
		$data['token'] = $user['token'];
		$data['old_password'] = '123456';
		$data['password'] = 'aaa456';
		//set method and link
		$method = 'PUT';
		//add the user id into the link
		$link = 'http://localhost/_blog/blog/src/v1/users/password';
		$result = $this->init_curl($data, $method, $link);
	
		//compare with 200 code ok
		$this->assertEquals('2004', $result['meta']['code']);
		//print the response message to check
		print_r($result) ;
		
		
	}
	
	/**
	 * the function use to reset the new password have updated
	 * id user is 30
	 * old pass is 12345
	 */
	public function reset_password() {
		$password = Auth::hash_password('12345');
		//call get user info by id =30
		$query = DB::update('user')->set(
				array(
						'password' => $password,
				))->where('id', 30)->execute();
	}
	
	/**
	 * use test logout function ok
	 * get token from login, test with this token
	 * method PUT
	 * link http://localhost/_blog/blog/src/v1/users/logout
	 * compare with code error 200
	 * @group logout
	 */
	public function test_logout() {
		//first is login
		//login first for test
		$data['username'] = 'kenny4';
		$data['password'] = '12345';
	
		//call curl for login
		$method = 'POST';
		$link = 'http://localhost/_blog/blog/src/v1/users/login';
		$rs = $this->init_curl($data, $method, $link);
		//get token return from login
		$test_data['token'] = $rs['data']['token'];
		$method = 'PUT';
		$link = 'http://localhost/_blog/blog/src/v1/users/logout';
		$rs = $this->init_curl($test_data, $method, $link);
		//compare with code 200
		$this->assertEquals('200', $rs['meta']['code']);
		
	}
	/**
	 * use test update user info function ok 
	 * method PUT
	 * link http://localhost/_blog/blog/src/v1/users/{user_id}
	 * compare with code error 200
	 * @group update_user2
	 * @depends test_login_ok
	 */
	public function test_put_update($data) {
		/*
		//first is login
		//login first for test
		$data['username'] = 'kenny3';
		$data['password'] = '12345';
		
		//call curl for login
		$method = 'POST';
		$link = 'http://localhost/_blog/blog/src/v1/users/login';
		$rs = $this->init_curl($data, $method, $link);
		*/
		//get token return from login
		$token = $data['token'];//$rs['data']['token'];
		
		$user = array (
				'token' => $token ,
				'firstname' => 'vy2',
				'lastname' => 'lam2' ,
				'email' => 'lam.vy2@mulodo.com',
    
		);
		$method = 'PUT';
		$link = 'http://localhost/_blog/blog/src/v1/users/';
		$result = $this->init_curl($user, $method, $link);
		
		//compare
		$this->assertEquals('200', $result['meta']['code']);
		//reset dataupdate
		$this->reset_data_update();
	}
	/**
	 * use test update user info function not ok
	 * method PUT
	 * link http://localhost/_blog/blog/src/v1/users/{user_id}
	 * compare with code error 1001 for invalid data input
	 * @group update_user3
	 * @dataProvider update_provider
	 */
	public function test_validate_update($test_data) {
		
		$method = 'PUT';
		$link = 'http://localhost/_blog/blog/src/v1/users/';
		$result = $this->init_curl($test_data, $method, $link);
		print_r($result);
		//compare
		$this->assertEquals('1001', $result['meta']['code']);
	}
	/**
	 * Define test data set
	 *
	 * @return array Test data
	 */
	public function update_provider() {
		
		$test_data = array();
		// Null firstname
		$test_data[][] = array(
				'token' => '3d447f7b28cebe841c3f837213e1d7db5a10de8f' ,
				'firstname' => '',
				'lastname' => 'Lam',
				'email' => 'lam.vy@mulodo.com',
		);
		
		//null lastname
		$test_data[][] = array(
				'token' => '3d447f7b28cebe841c3f837213e1d7db5a10de8f' ,
				'firstname' => 'thuyvy',
				'lastname' => '',
				'email' => 'lam.vy@mulodo.com',
		);
		//email incorrect
		$test_data[][] = array(
				'token' => '3d447f7b28cebe841c3f837213e1d7db5a10de8f' ,
				'firstname' => 'Thuy vy',
				'lastname' => 'Lam',
				'email' => 'lam.vy.mulodo.com',
		);
		
		return $test_data;
	}
	/**
	 * function used reset data after call update
	 */
	public function reset_data_update() {
		//data reset
		$user = array(
				'token' => '',
				'id' => '30',
				'firstname' => 'vy',
				'lastname' => 'lam' ,
				'email' => 'lam.vy@mulodo.com',
				'modified_at' => '1416983742',
	
		);
		//call get user info by id =30
		$query = DB::update('user')->set(
				    array(
						'firstname' => $user['firstname'] ,
						'lastname' => $user['lastname'] ,
						'email' => $user['email'] ,
						'modified_at' => $user['modified_at'],
						'login_hash' => $user['token'],
				    ))->where('id', $user['id'])->execute();
		 
		 
	}
	/**
	 * use test get user info function ok
	 * method GET
	 * link http://localhost/_blog/blog/src/v1/users/{user_id}
	 * compare with code 200 and data user for success
	 * @group get_info_ok
	 */
	public function test_get_info_ok() {
		//expected data use to compare
		$user = array (
				'id' => '88',
				'username' => 'kenny3',
				'firstname' => 'vy',
				'lastname' => 'lam' ,
				'email' => 'lam.vy@gmail.com',
				'created_at' => '1417675134',
				'modified_at' => '1417675134',
		);
		//call get user info by id =88
		$test_data['id'] = 88;
		//set method and link
		$method = 'GET';		
		$link = 'http://localhost/_blog/blog/src/v1/users/88';
		//call curl
		$data = $this->init_curl($test_data, $method, $link);
		//compare
		$this->assertEquals('200', $data['meta']['code']);
		$this->assertEquals($user['id'], $data['data']['id']);
		$this->assertEquals($user['username'], $data['data']['username']);
		$this->assertEquals($user['lastname'], $data['data']['lastname']);
		$this->assertEquals($user['firstname'], $data['data']['firstname']);
		$this->assertEquals($user['email'], $data['data']['email']);
		$this->assertEquals($user['created_at'], $data['data']['created_at']);
		$this->assertEquals($user['modified_at'], $data['data']['modified_at']);
	}
	
	/**
	 * use test get user info function not ok
	 * method GET
	 * link http://localhost/_blog/blog/src/v1/users/{user_id}
	 * compare with code 2004 for user is not exist
	 * @group get_info_notok2
	 * @dataProvider id_provider
	 */
	public function test_get_info_notok($test_data) {
		//set method and link
		$method = 'GET';
		//add the user id into the link
		$link = 'http://localhost/_blog/blog/src/v1/users/'.$test_data['id'];
		$result = $this->init_curl(null, $method, $link);
		//compare with error code 2004 , failure 1 for code 200 b/c id 30 exist in db
		$this->assertEquals('2004', $result['meta']['code']);
	}
	/**
	 * Define test data set
	 *
	 * @return array Test data
	 */
	public function id_provider() {
		$test_data = array();
		//create data from 0-10 - id is not exist in db except id 30
		for ($i = 0; $i < 40; $i++) {
			$test_data[][] = array('id' => $i);
		}
		//return test data provider
		return $test_data;
		 
	}
	
	/**
	 * Define test data set
	 * 
	 * @return array password
	 * 
	 */
	public function password_provider() {
		$test_data = array();
	
		//null password
		$test_data[][] = array(
				'password' => '',
				'token' => '',
				'old_password' => '12345'
		);
		//password at least 5 characters
		$test_data[][] = array(
				'password' => '123',
				'token' => '',
				'old_password' => '12345'
		);
		//password contain greater than 50 char
		$test_data[][] = array(
				'password' => 'weudhweuiryuiw4y5377846127361825376154yrhesjbfseufhiosuou897284647',
				'token' => '',
				'old_password' => '12345'
		);
		//password contain special char
		$test_data[][] = array(
				'password' => ' jhbfsh@#$@#%',
				'token' => '',
				'old_password' => '12345'
		);
		//return array test data provider
		return $test_data;
			
	}
	
	/**
	 * use test search user by name ok, have user account return
	 * method GET
	 * link http://localhost/_blog/blog/src/v1/users/{name}
	 * compare with code is 200, the number return greater than 0
	 * @group search_user2
	 * @dataProvider search_user_provider
	 */

	public function test_search_user_ok($test_data) {
		//set method and link
		$method = 'GET';
		//add the user id into the link
		$link = 'http://localhost/_blog/blog/src/v1/users/';
		$result = $this->init_curl($test_data, $method, $link);		
		//compare with error code 200
		$this->assertEquals('200', $result['meta']['code']);
		$this->assertGreaterThan(0, $result['meta']['result']); 
	}
	
	/**
	 * use test search user by name not ok,don't have any user return
	 * method GET
	 * link http://localhost/_blog/blog/src/v1/users/{name}
	 * compare with code is 2005
	 * @group search_user_notok2
	 * @dataProvider search_notok_provider
	 */
	public function test_search_user_notok($test_data) {
		$expected_desc = 'Not have any user for result.';
		//set method and link
		$method = 'GET';
		//add the user id into the link
		$link = 'http://localhost/_blog/blog/src/v1/users/';
		$result = $this->init_curl($test_data, $method, $link);
		//compare with error code 2004 , failure 1 for code 200 b/c id 30 exist in db
		$this->assertEquals('2005', $result['meta']['code']);
		//compare number of result > 0
		$this->assertEquals($expected_desc, $result['meta']['description']);
	}
	/**
	 * use test search user by name not ok, the keyqord use search is empty
	 * method GET
	 * link http://localhost/_blog/blog/src/v1/users/{name}
	 * compare with code is 2005
	 * compare msg return : The keyword search is empty. Please input it.
	 * @group search_user_empty
	 * 
	 */
	public function test_search_empty() {
		//the code when search error is 2005.
		//compare more the description return
		$expected_desc = 'The keyword search is empty. Please input it.';
		$test_data['name'] = '';
		//set method and link
		$method = 'GET';
		//add the user id into the link
		$link = 'http://localhost/_blog/blog/src/v1/users/';
		$result = $this->init_curl($test_data, $method, $link);
		//compare with error code 2004 , failure 1 for code 200 b/c id 30 exist in db
		$this->assertEquals('2005', $result['meta']['code']);
		//compare number of result > 0
		$this->assertEquals($expected_desc, $result['meta']['description']);
	}
	/**
	 * Define test data set
	 * use for search user
	 * @return array keyword
	 */
	public function search_user_provider() {
		$test_data = array();
	
		//the keyword contain 4 chars
		$test_data[][] = array(
				'name' => 'thuy',
		);
		//keyword at least 4 characters
		$test_data[][] = array(
				'name' => 'vy',
		);
		//keyword at least 4 characters
		$test_data[][] = array(
				'name' => 'lam',
		);
		//the keyword = username
		$test_data[][] = array(
				'name' => 'nga77',
		);
		//return array test data provider
		return $test_data;
			
	}
	/**
	 * Define test data set
	 * use for search user not ok
	 * @return array keyword
	 */
	public function search_notok_provider() {
		$test_data = array();
	
		//the keyword contain 4 chars
		$test_data[][] = array(
				'name' => 'hanh',
		);
		//keyword at least 4 characters
		$test_data[][] = array(
				'name' => 'shu',
		);
		//the keyword = username
		$test_data[][] = array(
				'name' => 'shuri',
		);
		//return array test data provider
		return $test_data;
			
	}
	
}