<?php


use Model\V1\User;
use Fuel\Core\Validation;
/**
 * The Test for User Model version 1.
 * @extends TestCase
 * 
 * @author Lam Vy
 * @group User
 */
class Test_Model_V1_User extends TestCase {
	
	private $_user;
	
	/**
	 * called before each test method
	 * @before
	 */
	public function setUp() {
	
		//instance user
		$this->_user = new User();
		
	}
	
	/**
	 * called after each test method
	 * @after
	 */
	public function tearDown() {
		//to do
		unset($this->_user);
		
	}
	
	/**
	 * funtion to test create user
	 * @group validateUser
	 * 
	 */
	public function test_validate_user() {
		//@dataProvider validateProvider
		$expected = array();
		$this->vali =  Validation::forge('user');
		
		$test_data = array();
		$test_data[][] = array(
				'username' => '',
				'password' => '12345',
				'firstname' => 'vy',
				'lastname' => 'Lam',
				'email' => 'lam.vy@mulodo.com'
		);
		
		$test_data[][] = array(
				'username' => 'thuy',
				'password' => '12345',
				'firstname' => 'vy',
				'lastname' => 'Lam',
				'email' => 'lam.vy@mulodo.com'
		);
		//print_r($test_data[1][0]); die ;
		for ($i = 0; $i < count($test_data); $i++) {
			$val = User::validate_user('user');
			
			$expected[] = $val;
			print_r($expected);
			
		}
		die ;
		//print_r($expected); die ;
		$this->assertEquals($expected,false);
		
	}
	/**
	 * Define test data set
	 *
	 * @return array Test data
	 */
    public function validateProvider() {
    	$test_data = array();
		// Null username or password , lastname, firstname
		$test_data[][] = array(
		'username' => '',
		'password' => '12345',
		'firstname' => 'vy',
		'lastname' => 'Lam',
		'email' => 'lam.vy@mulodo.com'
		);
		
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