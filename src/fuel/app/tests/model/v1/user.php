<?php


use Model\V1\User;
use Fuel\Core\Validation;
use Fuel\Core\Date;
use Auth\Auth;
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
		
		unset($this->_user);
	}
	
	/**
	 * funtion to test validate data input to create new account
	 * @group validate
	 * @dataProvider validate_provider	
	 */
	public function test_validate_user($data) {
		//	
		$User = $this->_user;		
		$val = $User::validate_user($data);						
		print_r($val['meta']['message']);
		
		$this->assertEquals(1001,$val['meta']['code']);
		
	}
	
    
    /**
	 * funtion to test check username exist in database
	 * @group exist_user_ok
	 * 
	 */
    public function test_check_user_exist_ok($testdata = 'thuyvy') {
    	
    	$rs = $this->_user->check_user_exist( $testdata ) ;
    	//var_dump($rs) ; die;
    	$this->assertTrue( $rs ); 
    }
    
    /**
     * funtion to test check username not exist in database
     * @group exist_user_not_ok
     *
     */
    public function test_check_user_not_exist($testdata = 'thuyvy999') {
    	 
    	$rs = $this->_user->check_user_exist( $testdata ) ;
    	//var_dump($rs) ; die;
    	$this->assertEquals($rs,false);
    }
    
    /**
     * function to test insert new user into db
     * @group create_user
     * 
     */
    public function test_create_user() {
    	
    	$time = time();
    	// data to insert
    	$data = array(
    		'username' => 'thuyvy99',
    		'password' => Auth::hash_password('12345') ,
    		'lastname' => 'lam' ,
    		'firstname' => 'vy' ,
    		'email' => 'lam.vy@mulodo.com',
    		'created_at' => $time ,
    		'modified_at' => $time ,
    	);
    	
    	$user_id = $this->_user->create_user($data) ;    	
    	    	
    	$this->assertGreaterThan(0,$user_id);
    	
    }
    
    /**
     * function to test create a token 
     * @group create_token
     *    
     */
    public function test_create_token() {
    	
    	
    	//data to test 
    	$data = array(
    			'id' => 30 ,
    			'username' => 'thuyvy',
    			'password' => '12345' ,
    			'lastname' => 'lam' ,
    			'firstname' => 'vy' ,
    			'email' => 'lam.vy@mulodo.com',
    			'created_at' => '2014-11-26',
    			'modified_at' => '2014-11-26'
    			
    	);    	   	
    	
    	$result = $this->_user->create_token($data['username'], $data['password']) ;
    	
    	//compare expected data with result data 
    	$this->assertEquals($data['id'],$result['id']);
    	$this->assertEquals($data['username'],$result['username']);
    	$this->assertEquals($data['email'],$result['email']);
    	$this->assertEquals($data['lastname'],$result['lastname']);
    	$this->assertEquals($data['firstname'],$result['firstname']);
    	$this->assertEquals($data['created_at'],$result['created_at']);
    	$this->assertEquals($data['modified_at'],$result['modified_at']);
    	$this->assertEquals(40,strlen($result['token'])) ;
    	
    }
   

    /**
     * Define test data set
     *
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
    			'email' => 'lam.vy@mulodo.com'
    	);
    	//password contain special characters
    	$test_data[][] = array(
    			'username' => 'thuyvy',
    			'password' => '12345#$@%',
    			'firstname' => 'vy',
    			'lastname' => 'Lam',
    			'email' => 'lam.vy@mulodo.com'
    	);
    	//username contain more 50 characters
    	$test_data[][] = array(
    			'username' => 'aaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa',
    			'password' => '12345',
    			'firstname' => 'vy',
    			'lastname' => 'Lam',
    			'email' => 'lam.vy@mulodo.com'
    	);
    	//username contain more 50 characters
    	$test_data[][] = array(
    			'username' => 'thuyvy',
    			'password' => 'aaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa',
    			'firstname' => 'vy',
    			'lastname' => 'Lam',
    			'email' => 'lam.vy@mulodo.com'
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