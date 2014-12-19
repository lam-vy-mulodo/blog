<?php

use Model\V1\Post;

use Model\V1\User;
/**
 * The Test for Post Model version 1.
 * @extends TestCase
 *
 * @author Lam Vy
 * @group Post
 */
class Test_Model_V1_Post extends TestCase {

	protected static $user = array();
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
	public static function setUpBeforeClass() {
		self::$user = self::do_login();
	}
	/**
	* Cleanup test resource (CLASS LEVEL).
	*/
	public static function tearDownAfterClass() {
		
		self::do_logout(self::$user['token']);
		self::$user = null;	
	}
	/**
	 * funtion to test login ok
	 *
	 * @return the data of user logged
	 */
	public static function do_login() {
		//create data to test ok
		$username = 'thuyvy';
		$password = '12345';
		$rs = User::login($username, $password);
		//compare id return is greater than 0 is login ok
		 
		return $rs;
		
	
	}
	
	/**
	 * funtion to test logout after login ok
	 *
	 * @return reset token in db
	 */
	public static function do_logout($token) {
		
		$rs = User::logout($token);
	
	}
	
	/**
	 * funtion to test create post is ok
	 * @group create_ok
	 * 
	 */
	public function test_create_post_ok() {
		
		//create data for test
		$test_data = array(
				'author_id' => self::$user['id'],
				'title' => 'title for unit testing',
				'content' => 'Lorem Ipsum is simply dummy text of the printing and typesetting industry.
				              Lorem Ipsum has been the industry...'
		);
		$rs = Post::create_post($test_data);
		$this->assertEquals(200, $rs['meta']['code']);
		$this->assertGreaterThan(0, $rs['data']['id']);
		$this->assertEquals($test_data['author_id'], $rs['data']['author_id']);
		$this->assertEquals($test_data['title'], $rs['data']['title']);
		
	}
	
	/**
	 * funtion to test create post is not ok
	 * b/c the title or content is empty
	 * or the title's length contain more 255 char
	 * compare with error code is 1002
	 * @group create_not_ok
	 * @dataProvider create_provider
	 */
	public function test_create_post_notok($test_data) {
		//set the author_id
		$test_data['author_id'] = self::$user['id'];
		$rs = Post::create_post($test_data);
		//compare with the code 1002 is have not post created
		$this->assertEquals(1002, $rs['meta']['code']);
		
	}
	
	/**
	 * Define test data for check login not ok
	 *
	 * @return array Test data
	 */
	
	public function create_provider() {
		$test_data = array();
		//the title is empty
		
		$test_data[][] = array(
				'title' => '',
				'content' => 'Lorem Ipsum has been the industry...'
		);
		//content is empty
		$test_data[][] = array(
				'title' => 'Let\'s it go ',
				'content' => ''
		);
		//the title is more 255 char
		$characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
		$title = '';
		for ($i=0; $i < 5; $i++) {
			$title .= $characters;
		}
		
		$test_data[][] = array(
				'title' => $title,
				'content' => 'Lorem Ipsum has been the industry...'
		);
	
		return $test_data;
	}
	
	/**
	 * funtion to test update status for post is ok
	 * @param : author_id, post_id and status
	 * compare with expected data
	 * @group update_status_ok
	 * @dataProvider status_change_ok_provider
	 */
	public function test_change_status_post($test_data) {
		//call update status in the model
		$data = Post::update_status($test_data['post_id'], $test_data['status'], $test_data['author_id']);
		
		//compare with the data expected
		$this->assertEquals($test_data['post_id'], $data['id']);
		$this->assertEquals($test_data['title'], $data['title']);		
		$this->assertEquals($test_data['created_at'], $data['created_at']);
		$this->assertEquals($test_data['modified_at'], $data['modified_at']);
	}
	
	/**
	 * Define test data for check login not ok
	 *
	 * @return array Test data
	 */
	public function status_change_ok_provider() {
		$test_data = array();
		//deactive post\
		$test_data[][] = array(
			'post_id' => '17',
			'author_id' => '89',
			'status'	=>	'0',
			'title' => 'title for unit testing in controller',
			'created_at' => '1418798556',
			'modified_at' => '1418798556'
		);
		
		//active post
		$test_data[][] = array(
				'post_id' => '17',
				'author_id' => '89',
				'status'	=>	'1',
				'title' => 'title for unit testing in controller',
				'created_at' => '1418798556',
				'modified_at' => '1418798556'
		);
		return $test_data;
	}
	
	/**
	 * funtion to test update status for post is not ok
	 * @param : author_id, post_id and status
	 * compare with false
	 * @group update_status_notok
	 * @dataProvider status_change_notok_provider
	 */
	public function test_change_status_post_notok($test_data) {
		//call update status in the model
		$rs = Post::update_status($test_data['post_id'], $test_data['status'], $test_data['author_id']);
	
		//compare with the data expected
		$this->assertEquals(false, $rs);
		
	}
	/**
	 * Define test data for check login not ok
	 *
	 * @return array Test data
	 */
	public function status_change_notok_provider() {
		$test_data = array();
		//active the post was actived
		$test_data[][] = array(
				'post_id' => '6',
				'author_id' => '30',
				'status'	=>	'1'
		);
	
		//inactive post was inactived
		$test_data[][] = array(
				'post_id' => '1',
				'author_id' => '30',
				'status'	=>	'0',
				
		);
		//active the post when author id wrong
		$test_data[][] = array(
				'post_id' => '1',
				'author_id' => '31',//true is 30
				'status'	=>	'1',
		
		);
		return $test_data;
	}
}