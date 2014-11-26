<?php
namespace Model\V1;
use Fuel\Core\Log;
use Fuel\Core\Validation;
use Model;
use Fuel\Core\DB;
use Auth\Auth;
use Fuel\Core\Fieldset;

/*
 * extends from Model 
 * @var User contain method do some transaction with user table
 * 
 * @validate_user to validate data to insert
 * @check_user_exist to check username exist in db
 * @create_user to insert new user 
 * 
 */

class User extends Model {
		
	/*
	 * method validation for check information input to database
	 */
	
	
	public static function validate_user($factory) {
		
		$val = Validation::forge($factory);		
		
		$val->add_field('username', 'Username', 'required|min_length[5]|max_length[50]');
		$val->add_field('password', 'Password', 'required|min_length[5]');
		$val->add_field('email','Email address','required|valid_email');
		$val->add_field('lastname', 'Last name', 'required');
		$val->add_field('firstname', 'First name', 'required');
		
		//set custom message for rules
		$val->set_message('required', 'Username, password, email, lastname and firstname are required');
		$val->set_message('min_length', 'Username and password must be contain at least 5 characters');
		$val->set_message('max_length', 'Username may contain more than 50 characters');
		$val->set_message('valid_email', 'Email incorrect');
		
		//create message array
		$_error = array();
		if (!$val->run(array())) {
			
			foreach ($val->error() as $field =>$error)
			{
				//add error message to array for return
				$_error[] = array('message' => $error->get_message());
			}
			//return error message
			$code = '1001';
			return array(
			    'meta' => array(
				'code' => $code,
				'description' => 'Input validation failed',
				'message' => $_error
			),'data' => null);
			
		} else {
			//return 1 for valid all data
			return true;
		}
		
	}
	
	/*
	 * the method use to check username exist in database or not
	 * return true for exist
	 * else return false
	 */
	public static function check_user_exist($username) {
		//try catch to execute query db
	try {
			$entry = DB::select('username')->from('user')->where('username','=',$username)->execute();
			//exist
			if (count($entry) > 0) {
				return true;
				
			} else {
				
				return false;//not exist
			}
		} catch (Exception $ex) {
			
			Log::error($ex->getMessage());
			return $ex->getMessage();
		}
	}
	
	/*
	 * the method use to insert new account into user table
	 * return true for success
	 * else return error
	 * 
	 */
	public static function create_user($data) {
		//try catch for insert
		try {
			//insert query
			$entry = DB::insert('user')->columns(
					array(
							'username',
							'password',
							'email','lastname',
							'firstname',
							'created_at',
							'modified_at'))
			->values( 
				array(
					$data['username'],
					$data['password'],
					$data['email'],
					$data['lastname'],
					$data['firstname'],
					$data['created_at'],
					$data['modified_at']));
			$entry->execute();
			
			return true;	
			
		} catch (\Exception $ex) {
			//write error to log
			Log::error($ex->getMessage());
			
			return $ex->getMessage();
		}
	}
	
	/*
	 * method use to create token for user
	 * @use Auth package for create token
	 * token have format 
	 * sha1(\Config::get('simpleauth.login_hash_salt').$this->user['username'].$last_login)
	 */
	public static function create_token($username,$password) {
		//use auth login to creat token and insert db
		
		$rs = 0;
	    if (Auth::login($username,$password)) {
			
			$data['id'] = Auth::get('id');
			$data['username'] =	Auth::get('username');
			$data['lastname'] = Auth::get('lastname');
			$data['firstname'] = Auth::get('firstname');
			$data['created_at'] = date('Y-m-d',Auth::get('created_at'));
			$data['modified_at'] = date('Y-m-d',Auth::get('modified_at'));
			$data['email'] = Auth::get('email');
			$data['token'] = Auth::get('login_hash');
			
			return $data;
		} 	else 
			return $rs;
		
		
	}
}