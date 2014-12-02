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
	protected $filters = array('strip_tags', 'htmlentities', '\\cleaners\\soap::clean');
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
		$data['username'] = Security::clean(Input::post('username'),$this->filters);
		//email use to login the system
		$data['email'] = Input::post('email');
		//password use to login, will be encrypted
		$data['password'] = Security::clean(Input::post('password'),$this->filters);
		
		//information of user
		$data['lastname'] = Input::post('lastname');
		$data['firstname'] = Input::post('firstname');
		
		//validation data user		
		$result = User::validate_user($data);
		//var_dump($val);die;
		//have error message
		if ( $result !== true ) {
			
			//response the error code and message;
			//the code is 1001
			return $this->response( $result );
		
		} else {
			//return 1 for valid data
			
			//try catch to check and insert user into db
			try	{
				//check username exist or not return true if exist, else return false
				$status = User::check_user_exist( $data['username'] );	
				//return $this->response(array($status));
								
				if ( $status ) {
					
					//return error code and message
					$code = '2001';
					return $this->response( array(
					        'meta' => array(
							'code' => $code,
							'description' => 'Username exist in database',
							'message' => 'This username is already in used'),
							'data' => null)
					);
				} else {
					//start transaction
					//user create in db and create token ok
					DB::start_transaction() ;
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
					
					$user = User::create_token($data['username'], Security::clean(Input::post('password'),$this->filters)); 
					//commit transaction after insert and create token ok
					DB::commit_transaction();
					//add remember me cookie for check logged in
					Auth::remember_me();
					//response code 200 for success
					$code = '200';
					
					return $this->response( array(
					    'meta' => array(
						'code' => $code,
						 'message' => 'Account created success',
					),
					'data' => $user,
					));
					
					
					} else {
						//rollback if faild
						DB::rollback_transaction();
						
						$code = '9005';
						return $this->response( array(
						        'meta' => array(
								'code' => $code,
								'description' => 'can\'t insert into database',
								'message' => $status,
						        'data' => null)));
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
						'code' => '1204',
						'description' => 'You login more 1 times' ,
						'messages' => 'You had logged'		
					),
					'data' => null
				)		        	
			);
			
		} else {
			//set up data to login
			$username = Security::clean(Input::post('username'),$this->filters);
			$password = Security::clean(Input::post('password'),$this->filters);
			$rs =  User::login($username,$password) ;
			
			if ( false != $rs) {
				
				//return code 200 and message
				return $this->response( array(
						'meta' => array(
							'code' => '200',
							'messages' => 'Login success !'
						),
						'data' => $rs 
				)) ;
			} else {
				//set code for login failed
				$code = 1203 ;
					
				return $this->response(array (
						'meta' => array (
								'code' => $code,
								'description' => 'Username or password wrong . Or the user not exist in system',
								'message' => 'Username or password incorrect.'
						),
						'data' => null
				) );
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
		$token =Input::put('token') ;
		
		//check token is not empty
		if ( !empty($token) ) {
			//checktoken is correct
			$rs = User::check_token($token) ;

			if ( true === $rs ) {
				//called logout from model to update token = null
				$row = User::logout($token) ;
				//check rows affected is > 0 logout ok
				
				//reset session
				Auth::logout() ;
			
				return $this->response(
						array(
						'meta' => array(
								'code' => '200' ,
								'messages' => 'Logout success!'
						) ,
						'data' => null
				));
				 
				
			} else {
				//response error message return if check token wrong
				return $this->response($rs) ;
			}
		} else {
			//return error 1202 token invalid
			return $this->response(
					array(
				'meta' => array(
					'code' => '1202' ,
					'description' => 'Token is empty' ,
					'messages' => 'Token invalid'
				) ,
				'data' => null
			));
		}
	}
	
	
	 
}
