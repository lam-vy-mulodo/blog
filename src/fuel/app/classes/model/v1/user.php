<?php

namespace Model\V1;

use Fuel\Core\Log;
use Fuel\Core\Validation;
use Model;
use Fuel\Core\DB;
use Auth\Auth;
use Fuel\Core\Fieldset;
use Fuel\Core\Database_Connection;

/*
 * extends from Model @var User contain method do some transaction with user table @validate_user to validate data to insert @check_user_exist to check username exist in db @create_user to insert new user
 * @extends Pakage orm use get user info
 */
class User extends \ORM\Model {
	
	//create table name for user
	protected static $_table_name = 'user';
	
	//create properties for user 
	protected  static  $_properties = array('id', 'username', 'lastname', 'firstname', 'email', 'created_at', 'modified_at');
	
	/*
	 * method validation for check information input to database
	 * @param input data  recieve from post method
	 * @return array error of input data
	 */
	public static function validate_user($data) {
		//create input field from data
		$input = array(
				'username' => $data['username'],
				'password' => $data['password'],
				'firstname' => $data['firstname'],
				'lastname' => $data['lastname'],
				'email' => $data['email'],
		);
		//check if Form validation created, use instance for retrieve it not use forge		
		$val = \Validation::active();
        if ($val) {
             $val = \Validation::forge();
        } else {
             $val = \Validation::instance();
        }
		//$val = Validation::forge();
		
		$val->add_field('username', 'Username', 'required|min_length[5]|max_length[50]|valid_string[alpha,numeric]');
		$val->add_field('password', 'Password', 'required|min_length[5]|max_length[50]|valid_string[alpha,numeric]');
		$val->add_field('email', 'Email address', 'required|valid_email');
		$val->add_field('lastname', 'Last name', 'required');
		$val->add_field('firstname', 'First name', 'required');
		
		// set custom message for rules
		$val->set_message('required', 'Username, password, email, lastname and firstname are required');
		$val->set_message('min_length', 'Username and password must be contain at least 5 characters');
		$val->set_message('max_length', 'Username may contain more than 50 characters');
		$val->set_message('valid_email', 'Email incorrect');
		$val->set_message('valid_string', 'Username or password may contain special characters');
		
		//print_r($data);
		// create message array
		$_error = array();
		if (! $val->run($input)) {
			
			foreach ($val->error() as $field => $error) {
				// add error message to array for return
				$_error[] = array(
						'message' => $error->get_message() 
				);
			}
			Validation::_empty($val);
			//var_dump($_error);die;
			// return error message
			
			return array(
					'meta' => array(
							'code' => USER_VALIDATE_ERROR,
							'description' => USER_VALIDATE_ERROR_MESS,
							'message' => $_error,
					),
					'data' => null,
			);
		} else {
			// return 1 for valid all data
			return true;
		}
	}
	/*
	 * function use to validate lastname, firstname,email update user info
	 * @param data user info
	 * @return if don't have error,return true, else return error
	 * 
	 */
	public static function validate_update($data) {
		
		$input = array(
				'firstname' => $data['firstname'],
				'lastname' => $data['lastname'],
				'email' => $data['email']
		);		
		
		//check if Form validation created, use instance for retrieve it not use forge
		
		$val = \Validation::active();
		if ($val) {
			$val = \Validation::forge();
		} else {
			$val = \Validation::instance();
		}
				
		
		$val->add_field('email', 'Email address', 'required|valid_email');
		$val->add_field('lastname', 'Last name', 'required');
		$val->add_field('firstname', 'First name', 'required');
		
		// set custom message for rules
		$val->set_message('required', 'Last name, first name and email must be required');
		$val->set_message('valid_email', 'Email incorrect');
				
		
		// create message array
		$_error = array();
		if (!$val->run($input)) {
			
			foreach($val->error() as $field => $error) {
				// add error message to array for return
				
				$_error[] = array(
						'message' => $error->get_message()
				);
			}
			Validation::_empty($val);
			//var_dump($_error);die;
			// return error message
			
			return array (
					'meta' => array(
							'code' => USER_VALIDATE_ERROR,
							'description' => USER_VALIDATE_ERROR_MESS,
							'message' => $_error,
					),
					'data' => null,
				);
		} else {
			// return 1 for valid all data
			return true;
		}
	}
	
	/*
	 * the method use to check username exist in database or not return true for exist else return false
	 * @return boolean true for exist, false for not exist
	 */
	public static function check_user_exist($username) {
		// try catch to execute query db
		try {
			$entry = DB::select('username')->from('user')->where('username', '=', $username)->execute();
			// exist
			
			if (count($entry) > 0) {
				//print_r($entry);
				return true;
			} else {
				
				return false; // not exist
			}
		} catch (Exception $ex) {
			
			Log::error( $ex->getMessage());
			return $ex->getMessage();
		}
	}
	
	/*
	 * the method use to insert new account into user table return true for success else return error
	 * @return new is of record inserted in db
	 * @param data is user info recieved from post_register in controller
	 */
	public static function create_user($data) {
		// try catch for insert
		try {
			// insert query
			$entry = DB::insert('user')->columns(array(
					'username',
					'password',
					'email',
					'lastname',
					'firstname',
					'created_at',
					'modified_at' 
			))->values(array(
					$data['username'],
					$data['password'],
					$data['email'],
					$data['lastname'],
					$data['firstname'],
					$data['created_at'],
					$data['modified_at'] 
			) );
			
			
			
			
			$result = $entry->execute();
			
			// Return id of username inserted
			return $result[0];
			
			
		} catch (\Exception $ex) {
			// write error to log
			Log::error($ex->getMessage());
			
			return $ex->getMessage();
		}
	}
	/*
	 * the method use to login
	 * @param username and password of user
	 * @return array data of user info and token to success
	 * return false for if not success
	*/
	public static function login($username, $password) {
		
		$rs = self::create_token($username, $password);
		//login success
		if( $rs != 0) {
			return $rs;
		} else {
			 return false;
		}
		
	}
	
	/*
	 * method use to create token for user @use Auth package for create token token have format sha1(\Config::get('simpleauth.login_hash_salt').$this->user['username'].$last_login)
	 * @return array data user info
	 * called after login or create user success.
	 */
	public static function create_token($username, $password) {
		// use auth login to creat token and insert db
		//throw new \Exception('Test transaction');
		$rs = 0;
		if (Auth::login($username, $password)) {
			
			$data ['id'] = Auth::get('id');
			$data ['username'] = Auth::get('username');
			$data ['lastname'] = Auth::get('lastname');
			$data ['firstname'] = Auth::get('firstname');
			$data ['created_at'] = date('Y-m-d', Auth::get('created_at'));
			$data ['modified_at'] = date('Y-m-d', Auth::get('modified_at'));
			$data ['email'] = Auth::get('email');
			$data ['token'] = Auth::get('login_hash');
			
			return $data;
		} else
			return $rs;
	}
	
	/*
	 * method use to check a token exist in db 
	 * @param token recieve method PUT
	 * @return if exist return true; else return false;
	 */
	public static function check_token($token) {
		
		try {
			$row = DB::select()->from('user')->where('login_hash', $token)->execute();
			
			//check if return the user id 
			if ($row[0]['id'] > 0) {
				
				
				//return id of user had this token
				return $row[0]['id'];
				
				
			} else {
				//return code is 1205 for token isn't exist in db 
				return array(
					'meta' => array(
						'code' => TOKEN_NOT_EXIST_ERROR ,
						'description' => TOKEN_NOT_EXIST_MESS ,
						'messages' => 'Access is denied '
				),
					'data' => null
				);
			}
		} catch (\Exception $ex) {
			Log::error($ex->getMessage());
			return $ex->getMessage();
		}
	}
	/*
	 * method to use is logout
	 * @param input data is token
	 * update login_hash =null by token input
	 * @return row affected
	 */
	public static function logout($token) {
		try {
			//update token
			$row = DB::update('user')->value('login_hash', '')->where('login_hash', '=', $token)->execute();
			//return number of row affected
			return $row;
		} catch (\Exception $ex) {
			Log::error($ex->getMessage());
			return $ex->getMessage();
		}
	}
	
	/*
	 * method to use get user info by token
	 * @param the token got after login
	 * @return data user info
	 */
	public static function get_user_by_id($id) {
		try {
			//select user info by id
			$rs = DB::select('id', 'username', 'lastname', 'firstname', 'email', 'created_at', 'modified_at')
			          ->from('user')
			          ->where('id', '=', $id)
			          ->execute();
			
			if ($rs[0]['id'] > 0) {
				
				return $rs[0];
			} else {
				
				//return error array
				return array(
						'meta' => array(
								'code' => USER_NOT_EXIST_ERROR ,
								'description' => USER_NOT_EXIST_MESS ,
								'messages' => 'Get information of user failed',
						),
						'data' => null,
				);
				
			}
			
		} catch(\Exception $ex) {
			Log::error($ex->getMessage());
			return $ex->getMessage();
		}
	}
	
	/*
	 * public function
	 * use to update user info
	 * @return user info after updated
	 */
	public static function update_user($data) {
		try {
			//call update
			$query = DB::update('user')->set(
					array(
						'lastname' => $data['lastname'],
						'firstname' => $data['firstname'],
						'email' => $data['email'],
						'modified_at' => time()
					))->where('id', $data['id'])->execute();
			
			//get data from user id
			//use compare before data
			$data = self::get_user_by_id($data['id']);
			
			return $data;
			
		} catch (\Exception $ex) {
			Log::error($ex->getMessage());
			return $ex->getMessage();
		}
		
		
	}
	
	/*
	* public function user ORM package
	* use to get user info by id
	* @param id input from get method
	* @return data user information
	*/
	public static function get_user_info($id) {
		try {
			
			$user = User::forge();
			//select user info from id input and check if user actived
			$entry = User::find(
					'all',
					 array('where' => array( array('id', $id), array('status', 1))
			));
			
			//count data return > 0 is user exist
			if (count($entry) > 0) {
				return array(
					'meta' => array(
						'code' => SUSSCESS_CODE,
						'messages' => 'Get user information success !'
					),
					'data' => $entry[$id]);
			} else {
				//return error array if user not exist
				return false;
			}
			
		} catch (\Exception $ex) {
			
			Log::error($ex->getMessage());
			return $ex->getMessage();
		}
	}
	
}