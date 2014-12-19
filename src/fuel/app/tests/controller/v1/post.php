<?php

use Model\V1\Post;

use Model\V1\User;
/**
 * The Test for Post Controller version 1.
* @extends TestCase
*
* @author Lam Vy
* @group Post
*/
class Test_Controller_V1_Post extends TestCase {

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
		$username = 'kenny4';
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
	 * use test create the post is ok
	 * method POST
	 * link http://localhost/_blog/blog/src/v1/posts/
	 * compare with code is 200
	 * @group create_ok
	 *
	 */
	public function test_create_post_ok() {
		//create test data
		$test_data = array(
				'token' => self::$user['token'],
				'title' => 'title for unit testing in controller',
				'content' => 'Lorem Ipsum is simply dummy text of the printing and typesetting industry.
				              Lorem Ipsum has been the industry...'
		);
		//set method and link
		$method = 'POST';
		$link = 'http://localhost/_blog/blog/src/v1/posts';
		$rs = $this->init_curl($test_data, $method, $link);
		//compare with the result and expected data
		$this->assertEquals(200, $rs['meta']['code']);
		$this->assertGreaterThan(0, $rs['data']['id']);		
		$this->assertEquals($test_data['title'], $rs['data']['title']);
	}
	/**
	 * use test create the post is not ok
	 * method POST
	 * link http://localhost/_blog/blog/src/v1/posts/
	 * compare with code is 1002 return
	 * @group create_not_ok
	 * @dataProvider create_provider
	 */
	public function test_create_post_not_ok($test_data) {
		//create test data
		$test_data['token'] = self::$user['token'];
		//set method and link
		$method = 'POST';
		$link = 'http://localhost/_blog/blog/src/v1/posts';
		$rs = $this->init_curl($test_data, $method, $link);
		//compare with the result and expected data
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
	 * use test inactive the post is  ok
	 * method PUT
	 * link http://localhost/_blog/blog/src/v1/posts/{post_id}/inactive
	 * compare with code is 200, the data expected
	 * @group update_inactive
	 * 
	 */
	public function test_inactive_post_ok() {
		//set expected data
		$test_data = array(
				'post_id' => '17',
				'author_id' => '89',
				'status' => POST_INACTIVE_STATUS,
				'title' => 'title for unit testing in controller',
				'created_at' => '1418798556',
				'modified_at' => '1418798556'
		);
		//set the token param
		$params['token'] = self::$user['token'];
		//set method and link
		$method = 'PUT';
		$link = 'http://localhost/_blog/blog/src/v1/posts/'.$test_data['post_id'].'/inactive';
		$data = $this->init_curl($params, $method, $link);
		//compare with the result and expected data
		$this->assertEquals('200', $data['meta']['code']);
		//compare data return
		$this->assertEquals($test_data['post_id'], $data['data']['id']);
		$this->assertEquals($test_data['title'], $data['data']['title']);
		$this->assertEquals($test_data['status'], $data['data']['status']);
		$this->assertEquals($test_data['created_at'], $data['data']['created_at']);
		$this->assertEquals($test_data['modified_at'], $data['data']['modified_at']);
		return $test_data['post_id'];
	}
	/**
	 * use test inactive the post is  notok
	 * method PUT
	 * link http://localhost/_blog/blog/src/v1/posts/{post_id}/inactive
	 * compare with code is 2504 
	 * @group update_inactive
	 * @depends test_inactive_post_ok
	 */
	public function test_inactive_post_notok($id) {
		//set the token param
		$params['token'] = self::$user['token'];
		//set method and link
		$method = 'PUT';
		$link = 'http://localhost/_blog/blog/src/v1/posts/'.$id.'/inactive';
		$data = $this->init_curl($params, $method, $link);
		//compare with the result and expected data
		$this->assertEquals('2504', $data['meta']['code']);
		
		
	}
	/**
	 * use test active the post is  ok
	 * method PUT
	 * link http://localhost/_blog/blog/src/v1/posts/{post_id}/active
	 * compare with code is 200, the data expected
	 * @group update_active
	 *
	 */
	public function test_active_post_ok() {
		//set expected data
		$test_data = array(
				'post_id' => '17',
				'author_id' => '89',
				'status' => POST_ACTIVE_STATUS,
				'title' => 'title for unit testing in controller',
				'created_at' => '1418798556',
				'modified_at' => '1418798556'
		);
		//set the token param
		$params['token'] = self::$user['token'];
		//set method and link
		$method = 'PUT';
		$link = 'http://localhost/_blog/blog/src/v1/posts/'.$test_data['post_id'].'/active';
		$data = $this->init_curl($params, $method, $link);
		//compare with the result and expected data
		$this->assertEquals('200', $data['meta']['code']);
		//compare data return
		$this->assertEquals($test_data['post_id'], $data['data']['id']);
		$this->assertEquals($test_data['title'], $data['data']['title']);
		$this->assertEquals($test_data['status'], $data['data']['status']);
		$this->assertEquals($test_data['created_at'], $data['data']['created_at']);
		$this->assertEquals($test_data['modified_at'], $data['data']['modified_at']);
		//return post id for test active is not ok
		//bc the post want to active was actived
		return $test_data['post_id'];
	}
	
	/**
	 * use test inactive the post is  notok
	 * method PUT
	 * link http://localhost/_blog/blog/src/v1/posts/{post_id}/active
	 * compare with code is 2504
	 * @group update_active
	 * @depends test_active_post_ok
	 */
	public function test_active_post_notok($id) {
		//set the token param
		$params['token'] = self::$user['token'];
		//set method and link
		$method = 'PUT';
		$link = 'http://localhost/_blog/blog/src/v1/posts/'.$id.'/active';
		$data = $this->init_curl($params, $method, $link);
		//compare with the result and expected data
		$this->assertEquals('2504', $data['meta']['code']);
	
	
	}
}