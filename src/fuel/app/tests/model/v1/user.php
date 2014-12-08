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
		//print_r($val['meta']['message']);
		if( $val!=1 ) {
		$this->assertEquals(1001,$val['meta']['code']);
		} else {
			$this->assertEquals(true,$val);
		}
		
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
    public function test_check_user_not_exist($testdata = 'thuyvy1010') {
    	 
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
    	//delete user have been created to test username is not exist in db
    	//user data can insert when username not exist
    	self::remove_user($user_id) ;
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
    	//print_r($result) ; die;
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
     * function to test login ok
     * @group login_ok
     *
     */
    public function test_login_ok() {
    	//create data to test ok
    	$username = 'thuyvy' ;
    	$password = '12345' ;
    	$rs = $this->_user->login($username, $password) ;
    	//compare id return is greater than 0 is login ok
    	
    	$this->assertGreaterThan(0 , $rs['id']) ;
    	 
    }
    
    
    /**
     * function to test login not ok
     * @group login_not_ok
     * @dataProvider login_not_provider
     */
    public function test_login_not_ok($test_data) {
    	//create data to test ok
    	 
    	$username = $test_data['username'] ;
    	$password = $test_data['password'] ;
    	$rs = $this->_user->login($username, $password) ;
    	//compare boolean false  to check login not ok
    	$this->assertEquals(false , $rs) ;
    }
    
    /**
     * Define test data for check login not ok
     *
     * @return array Test data
     */
    
    public function login_not_provider() {
    	$test_data = array() ;
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
    	 
    	return $test_data ;
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
    	//data valid
    	$test_data[][] = array(
    			'username' => 'thuyvy',
    			'password' => '12345',
    			'firstname' => 'vy',
    			'lastname' => 'Lam',
    			'email' => 'lam.vy@mulodo.com'
    	);
    
    	return $test_data;
    }
    
    /**
     * function to test check token exist ok in db
     * compare with boolean true
     * @group check_token
     */
    public function test_token_exist_ok() {
    	//first is login to create token in db 
    	//get token return to test check    	
    	$username = 'thuyvy' ;
    	$password = '12345' ;
    	$data = $this->_user->login($username, $password) ;
    	$token = $data['token'] ;
    	
    	$result = User::check_token($token ) ;
    	//compare result return with id of username vy (30)- token exist
    	
    	$this->assertEquals(30, $result) ;
    	
    	
    }
    
    /**
     * function to test check token is not exist in db
     * compare with return code is 1205
     * @group check_token_notok
     * @dataProvider token_provider
     */
    public function test_token_not_exist($test_data) {
    	$rs = User::check_token($test_data) ;
    	//compare with code return is 1205 for not exist token
    	$this->assertEquals('1205', $rs['meta']['code']) ;
    	
    }
    /**
     * Define test data set
     * create token by use sha1
     * @return array Test data
     */
    public function token_provider() {
    	$test_data = array();
    	//loop for auto create
    	for ($i = 0 ; $i < 10 ; $i++) {
    		$test_data[][] = array('token' => sha1(time()) ) ;
    	}
    	
    	return $test_data ;
    }
    /**
     * function use to test logout ok
     * @param input is token was create from login function
     * compare number rows affected greate than 0
     * @group logout
     */
    public function test_logout() {
    	//first is login to create token in db 
    	//get token return to test check    	
    	$username = 'thuyvy' ;
    	$password = '12345' ;
    	$data = $this->_user->login($username, $password) ;
    	$token = $data['token'] ;
    	
    	//call logout and compare with row affected
    	$row = User::logout($token) ;
    	$this->assertGreaterThan(0,$row) ;
    	
    	//call test token not exist in db is logout ok
    	$result = User::check_token($token) ;
    	//compare status with error code 1205
    	$this->assertEquals('1205', $result['meta']['code']) ;
    }

    
    
    /**
     * function use test validate update user
     * compare error code 1001
     * @group validate_update_notok
     * @dataProvider update_provider
     */
    public function test_validate_update_notok($test_data) {
    	//
    	//$User = $this->_user;
    	$val = $this->_user->validate_update($test_data);
    	//compare with error code 1001 when have data invalid    	
    	//print_r($val) ;
    	$this->assertEquals(1001,$val['meta']['code']);
    	
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
    			'firstname' => '',
    			'lastname' => 'Lam',
    			'email' => 'lam.vy@mulodo.com'
    	);
    	//null lastname
    	$test_data[][] = array(
    			'firstname' => 'thuyvy',
    			'lastname' => '',
    			'email' => 'lam.vy@mulodo.com'
    	);
    	//email incorrect
    	$test_data[][] = array(
    			'firstname' => 'Thuy vy',
    			'lastname' => 'Lam',
    			'email' => 'lam.vy.mulodo.com'
    	);
    	return $test_data ;
    }
    /**
     * function use test validate update user
     * compare with true
     * @group validate_update_ok
     */
    public function test_validate_update_ok() {
    	//create data valid to test
    	$test_data = array();
    	$test_data = array(
    			'lastname' => 'Lam',
    			'firstname' => 'thuy vy',
    			'email' => 'lam.vy@mulodo.com',
    			
    	) ;
    
    	//$User = $this->_user;
    	$val =User::validate_update($test_data);    
    	//compare with true when have data valid    	
    	$this->assertTrue($val);
    
    }
    
    /**
     * function use test get user info by id 
     * @group get_user_ok
     */
    public function test_get_userinfo_ok() {
    	//expected data use to compare
    	$user = array (
    		'id' => '88',
    		'username' => 'kenny3',
    		'firstname' => 'vy',
    		'lastname' => 'lam' ,
    		'email' => 'lam.vy@gmail.com' ,
    		'created_at' => '1417675134',
    		'modified_at' => '1417675134'
    	);
    	//call get user info by id =30
    	$data = $this->_user->get_user_by_id(88) ;
    	$this->assertEquals($user['id'] , $data['id']) ;
    	$this->assertEquals($user['username'] , $data['username']) ;
    	$this->assertEquals($user['lastname'] , $data['lastname']) ;
    	$this->assertEquals($user['firstname'] , $data['firstname']) ;
    	$this->assertEquals($user['email'] , $data['email']) ;
    	$this->assertEquals($user['created_at'] , $data['created_at']) ;
    	$this->assertEquals($user['modified_at'] , $data['modified_at']) ;
    	
    }
    
    /**
     * function use test get user info by id but not ok
     * compare with error 2004
     * @group get_user_notok
     */
    public function test_get_userinfo_notok() { 
    	//id  = 1 not exist in db
    	$data = $this->_user->get_user_by_id(1) ;
    	//compare with error code 2004
    	//print_r($data) ;
    	$this->assertEquals(2004 , $data['meta']['code']) ;
    }
    /**
     * function use test update user info by id
     * compare with expected after update info
     * @group update_user
     */
    public function test_put_update() {
    	//expected data use to compare
    	//lastname, firstname, email use to update when function update_user called
    	$user = array (
    			'id' => '88',
    			'username' => 'kenny3',
    			'firstname' => 'vy2',
    			'lastname' => 'lam2' ,
    			'email' => 'lam.vy2@mulodo.com'
    			
    	);
    	//call update user info by id = 88
    	$data = $this->_user->update_user($user) ;
    	$this->assertEquals($user['id'] , $data['id']) ;
    	$this->assertEquals($user['username'] , $data['username']) ;
    	$this->assertEquals($user['lastname'] , $data['lastname']) ;
    	$this->assertEquals($user['firstname'] , $data['firstname']) ;
    	$this->assertEquals($user['email'] , $data['email']) ;
    	//call update user info return old data
    	//use run test get user info
    	$this->reset_data_update() ;
    }
    
    /**
     * function used reset data after call update
     */
    public function reset_data_update() {
    	//data reset
    	$user = array (
    			'id' => '88',
    			'firstname' => 'vy',
    			'lastname' => 'lam' ,
    			'email' => 'lam.vy@gmail.com',
    			'modified_at' => '1417675134'
    
    	);
    	//update reset user info id 88
    	$query = DB::update('user')->set(
    		array(
    			'firstname' => $user['firstname'] ,
    			'lastname' => $user['lastname'] ,
    			'email' => $user['email'] ,
    			'modified_at' => $user['modified_at']
    		)
    	)->where('id',$user['id'])->execute();
    	
    	
    }
    

















/*
* This comment is used for making conflict situation. This should be removed when you merged and resolved completely. 
*
*/


}