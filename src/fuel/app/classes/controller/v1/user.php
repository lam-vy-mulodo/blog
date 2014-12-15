<?php
use Model\V1\User;
use Fuel\Core\Controller_Rest;
use Auth\Auth;
use Fuel\Core\Validation;
use Fuel\Core\Security;
use Fuel\Core\Input;

/**
 * The User Controller.
 * @extends  Controller_Rest for API 
 * A user controller have function for user use the blog system
 * @package  app
 *
 */
class Controller_V1_User extends Controller_Rest {
	//return json format
	protected $format = 'json';
	//clean data
	protected $filters = array('strip_tags', 'htmlentities');
	/**
	 * The basic welcome message
	 * @link http://localhost/v1/users/
	 * @method : GET
	 * @access  public
	 * @return  Response
	 */
	public function action_index() {
		return $this->response(array(
				'meta' => array(
						'message' => 'Welcome to blog system'),
		));
	}
	
	
	/**
	 * A function post_index to create user
	 * @link http://localhost/v1/users/
	 * method POST 
	 * @access  public
	 * @return  Response
	 */
	public function post_register() {
		//create user data from input post
		
		
		//username use to login the system
		$data['username'] = Security::clean(Input::post('username'), $this->filters);
		//email use to login the system
		$data['email'] = Security::clean(Input::post('email'), $this->filters);
		//password use to login, will be encrypted
		$data['password'] = Security::clean(Input::post('password'), $this->filters);
		
		//information of user
		$data['lastname'] = Security::clean(Input::post('lastname'), $this->filters);
		$data['firstname'] = Security::clean(Input::post('firstname'), $this->filters);
		
		//validation data user		
		$result = User::validate_user($data);
		//var_dump($val);die;
		//have error message
		if ($result !== true) {
			
			//response the error code and message;
			//the code is 1001
			return $this->response($result);
		
		} else {
			//return 1 for valid data
			
			//try catch to check and insert user into db
			try	{
				//check username exist or not return true if exist, else return false
				$status = User::check_user_exist($data['username']);	
				//return $this->response(array($status));
								
				if ($status) {
					
					//return error code and message
					
					return $this->response(array(
					        'meta' => array(
								'code' => USER_EXIST_CODE,
								'description' => USER_EXIST_DESC,
								'message' => USER_EXIST_MSG,
					        ),
							'data' => null,
					        )
					);
				} else {
					//start transaction
					//user create in db and create token ok
					DB::start_transaction();
					//insert db
					//set date time for create account and modified by current date time
					$time = time();
					$data['created_at'] = $time;
					$data['modified_at'] = $time;	
								
					//hash password before insert into db
					//hash password by auth package, password become :12ac1f48d9649....**
					$data['password'] = Auth::hash_password($data['password']);
					$rs = User::create_user($data);
					
					if ($rs > 0) {
						
					/*
					 * login and create token for new user,use password before encrypted
					 * return data array contain information of user created 
					 */
					
					$user = User::create_token($data['username'], Security::clean(Input::post('password'), $this->filters)); 
					//commit transaction after insert and create token ok
					DB::commit_transaction();
					//add remember me cookie for check logged in
					Auth::remember_me();
					//response code SUSSCESS_CODE for success
										
					return $this->response(array(
					    'meta' => array(
								'code' => SUSSCESS_CODE,
						 		'message' => 'Account created success',
						),
						'data' => $user,
						));
					
					
					} else {
						//rollback if faild
						DB::rollback_transaction();
												
						return $this->response(array(
						        'meta' => array(
								    'code' => SYSTEM_ERROR,
								    'description' => 'can\'t insert into database',
								    'message' => $status,
						        'data' => null,
						        )));
					}
				}
				
				
			} catch (Exception $ex) {
				
				DB::rollback_transaction();
				return $ex->getMessage();
			}
			
		}

		
	}
	
	/**
	 * A function post_login to use login into blog
	 * @link http://localhost/v1/users/login
	 * method POST
	 * @access  public
	 * @return  Response
	 */
	public function post_login() {
		
		//check for logged
		if (Auth::check()) {
			
			return $this->response(
				array(
					'meta' => array(
						'code' => USER_LOGGED_CODE,
						'description' => USER_LOGGED_DESC,
						'messages' => USER_LOGGED_MSG,	
					),
					'data' => null,
				)		        	
			);
			
		} else {
			//set up data to login
			$username = Security::clean(Input::post('username'), $this->filters);
			$password = Security::clean(Input::post('password'), $this->filters);
			$rs = User::login($username, $password);
			
			if (false != $rs) {
				
				//return code SUSSCESS_CODE and message
				return $this->response(array(
						'meta' => array(
							'code' => SUSSCESS_CODE,
							'messages' => 'Login success !',
						),
						'data' => $rs ,
				));
			} else {
				//set code for login failed
									
				return $this->response(array(
						'meta' => array(
								'code' => USER_LOGIN_ERROR,
								'description' => USER_LOGIN_ERROR_DESC,
								'message' => USER_LOGIN_ERROR_MSG,
						),
						'data' => null,
				));
			}
			
		}
		
		
		
	}

	/**
	 * A function put_logout to use get out the blog
	 * @link http://localhost/v1/users/logout
	 * method PUT
	 * @access  public
	 * @return  Response
	 */
	public function put_logout() {
		//get token from method put
		$token =Input::put('token');
		
		//check token is not empty
		if (!empty($token)) {
			//checktoken is correct
			$rs = User::check_token($token);
 
			if (!is_array($rs) && $rs > 0) {
				//called logout from model to update token = null
				$row = User::logout($token);
				//check rows affected is > 0 logout ok
				
				//reset session
				Auth::logout();
			
				return $this->response(array(
						'meta' => array(
								'code' => SUSSCESS_CODE,
								'messages' => 'Logout success!',
						) ,
						'data' => null,
				));
				 
				
			} else {
				//response error message return if check token wrong
				return $this->response($rs);
			}
		} else {
			//return error 1202 token invalid
			return $this->response(array(
				'meta' => array(
					'code' => TOKEN_NULL_ERROR ,
					'description' => TOKEN_NULL_DESC ,
					'messages' => 'TOKEN_NULL_MSG',
				) ,
				'data' => null,
			));
		}
	}
	
	/**
	 * A function put_update to use update user info
	 * @link http://localhost/v1/users/{user_id}
	 * method PUT
	 * @access  public
	 * @return  Response
	 */
	public function put_update_user() {
		//check token valid to update
		$data['token'] = Input::put('token');
		
		if (!empty($data['token'])) {
			//check token exist
			$rs = User::check_token($data['token']);
			//check return data is user id ?
			if ( is_numeric($rs) && $rs > 0) {
				//get data used update and validate
				$data['id'] = $rs;
				$data['lastname'] = Security::clean(Input::put('lastname'), $this->filters);
				$data['firstname'] = Security::clean(Input::put('firstname'), $this->filters);
				$data['email'] = Security::clean(Input::put('email'), $this->filters);
								
				$rs = User::validate_update($data);
				//return error messgae if had error
				if (true !== $rs) {
					//error 1001 for data invalid
					return $this->response($rs);
				} else {
					//not have error, continue update
					$rs = User::update_user($data);
					return $this->response(
					array(
						'meta' => array(
							'code' => SUSSCESS_CODE,
							'messages' => 'Update success', 
						),
						'data' => $rs,
					));
				}
				
			} else {
				//response error message return if check token wrong
				return $this->response($rs);
			}
		} else {
			//return error empty token
			return $this->response(
					array(
							'meta' => array(
									'code' => TOKEN_NULL_ERROR ,
									'description' => TOKEN_NULL_DESC ,
									'messages' => 'TOKEN_NULL_MSG',
							) ,
							'data' => null,
					));
		}
	}
	/**
	 * A function get user info by id
	 * @link http://localhost/v1/users/{user_id}
	 * method GET
	 * @access  public
	 * @return  Response
	 */
	public function get_user_info($id) {
		//call function get user info use ORM package
		//input id get from url
		$result =  User::get_user_info($id);
		
		if (false == $result) {
			//response error message
			return array(
					'meta' => array(
							'code' => USER_NOT_EXIST_ERROR,
							'description' => USER_NOT_EXIST_MSG,
							'messages' => 'Get information of user failed',
					),
					'data' => null,
			);
		} else {
			//response the info of user
			return $this->response($result);
		}
		
	}
	/*
	 * function to test get user info use $this->param
	 * method GET
	 * config the routes for /:name_param - the name of param
	*/
	public function get_user_info2() {
		
		$id = $this->param('id');
		
		//call function get user info use ORM package
		//input id get from url
		$result =  User::get_user_info($id);
	
		if (false == $result) {
			//response error message
			return array(
					'meta' => array(
							'code' => USER_NOT_EXIST_ERROR,
							'description' => USER_NOT_EXIST_MSG,
							'messages' => 'Get information of user failed',
					),
					'data' => null,
			);
		} else {
			//response the info of user
			return $this->response($result);
		}
	
	}
	/**
	 * the function for user change current password
	 * @link http://localhost/v1/users/password
	 * method :PUT
	 * @access public
	 * @return Response
	 */
	public function put_change_password() {
		//get data
		$token = Security::clean(Input::put('token'), $this->filters);
		$password = Security::clean(Input::put('password'), $this->filters);
		$old_password = Security::clean(Input::put('old_password'), $this->filters);
		
		//check token is null
		if (!empty($token)) {
			
			$id = User::check_token($token); 
			//return the id of user when check the token correct
			//else return array error
			
			if(is_numeric($id) && $id > 0) {

				//token exist in db , continue change password				
				$result = User::change_password($password, $id, $old_password);
				
				//return the result
				return $this->response($result);
				
			} else {
				
				//token wrong, return error , access denied
				return $this->response($id);
			}
			
		} else {
			//response token null error
			return $this->response(
					array(
							'meta' => array(
									'code' => TOKEN_NULL_ERROR,
									'description' => TOKEN_NULL_DESC,
									'messages' => TOKEN_NULL_MSG,
							),
							'data' => null,
		 		)
			);
		}
	}
	
	/**
	 * the function for search user by name
	 * @link http://localhost/v1/users?name={name}
	 * method :GET
	 * @access public
	 * @return Response
	 */
	public function get_search_user() {
		//get the keyword for search
		$name = Security::clean(Input::get('name'), $this->filters);
		//check empty
		if (empty($name)) {
			return $this->response(
					array(
							'meta' => array(
									'code' => USER_SEARCH_ERROR,
									'description' => 'The keyword search is empty. Please input it.',
									'messages' => USER_SEARCH_MSG,
							),
							'data' => null,
					)
			);
		} else {
			//get the user for search
			$result = User::search_user($name);
			//count rs
			$num = count($result);
			//check have user
			if ($num > 0) {
				//have user for search
				return $this->response(
						array(
								'meta' => array(
										'code' => SUSSCESS_CODE,
										'messages' => 'Search complete!',
										'result' => $num . ' user matched .'
								),
								'data' => $result,
						)
				);
			} else {
				//return message not have any user
				return $this->response(
						array(
								'meta' => array(
										'code' => USER_SEARCH_ERROR,
										'description' => 'Not have any user for result.',
										'messages' => USER_SEARCH_MSG,
								),
								'data' => null,
						)
				);
			}
		}
		
		return $this->response($name);
	}
}
