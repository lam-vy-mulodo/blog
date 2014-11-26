<?php


/**
 * The Test for User Controller version 1.
 * @extends TestCase
 * 
 * @author Lam Vy
 * @group User1
 */
class Test_Controller_V1_User extends TestCase {
	
	/**
	 * called before each test method
	 * @before
	 */
	public function setUp() {
	
		echo 'Test for group user';
	}
	
	/**
	 * called after each test method
	 * @after
	 */
	public function tearDown() {
		//to do
	
	}
	
	/**
	 * funtion to test create user
	 * 
	 * @dataProvider additionProvider
	 */
	public function test_post_register() {
		
		$curl = Request::forge('http://localhost/test-blog/user_register', 'curl');
		$curl->set_method('post');
		$user_info = array(
				'email' => 'alex@mulodo.com',
				'password' => '123456',
				'username' => 'alex'
		);
		$curl->set_params($user_info);
		$curl->execute();
		// Get API Response
		$response = $curl->response();
		// Convert json response to array
		$arr_msg = json_decode($response, true);
		$this->set_user_id($arr_msg['data']['id']);
		// Get API Response code
		$status_actual = $arr_msg['message']['code'];
		$status_expected= 200;
		$this->assertEquals($status_expected, $status_actual);
	}
	
    public function additionProvider() {
       $test_data = array();
		// Null username or password , lastname, firstname
		$test_data[][] = array(
		'username' => '',
		'password' => '12345',
		'firstname' => 'vy',
		'lastname' => 'Lam',
		'email' => 'lam.vy@mulodo.com'
		);
		// username contains at least 5 character
		$test_data[][] = array(
				'username' => 'thuy',
				'password' => '12345',
				'firstname' => 'vy',
				'lastname' => 'Lam',
				'email' => 'lam.vy@mulodo.com'
		);
		
		//		
		
		return $test_data;
    }
}