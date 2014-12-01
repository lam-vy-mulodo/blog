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
 */
class User extends Model {
	
	/*
	 * method validation for check information input to database
	 */
	public static function validate_user($data) {
		//create input field from data
		$input = array (
				'username' => $data ['username'],
				'password' => $data ['password'],
				'firstname' => $data ['firstname'],
				'lastname' => $data ['lastname'],
				'email' => $data ['email'] 
		);
		//check if Form validation created, use instance for retrieve it not use forge		
		$val = \Validation::active();
        if ($val) {
             $val = \Validation::forge();
        } else {
             $val = \Validation::instance();
        }
		//$val = Validation::forge();
		
		$val->add_field ( 'username', 'Username', 'required|min_length[5]|max_length[50]|valid_string[alpha,numeric]' );
		$val->add_field ( 'password', 'Password', 'required|min_length[5]|max_length[50]|valid_string[alpha,numeric]' );
		$val->add_field ( 'email', 'Email address', 'required|valid_email' );
		$val->add_field ( 'lastname', 'Last name', 'required' );
		$val->add_field ( 'firstname', 'First name', 'required' );
		
		// set custom message for rules
		$val->set_message ( 'required', 'Username, password, email, lastname and firstname are required' );
		$val->set_message ( 'min_length', 'Username and password must be contain at least 5 characters' );
		$val->set_message ( 'max_length', 'Username may contain more than 50 characters' );
		$val->set_message ( 'valid_email', 'Email incorrect' );
		$val->set_message ( 'valid_string', 'Username or password may contain special characters' );
		
		//print_r($data) ;
		// create message array
		$_error = array ();
		if (! $val->run ($input)) {
			
			foreach ( $val->error () as $field => $error ) {
				// add error message to array for return
				$_error [] = array (
						'message' => $error->get_message () 
				);
			}
			//var_dump($_error);die;
			// return error message
			$code = '1001';
			return array (
					'meta' => array (
							'code' => $code,
							'description' => 'Input validation failed',
							'message' => $_error 
					),
					'data' => null 
			);
		} else {
			// return 1 for valid all data
			return true;
		}
	}
	
	/*
	 * the method use to check username exist in database or not return true for exist else return false
	 */
	public static function check_user_exist($username) {
		// try catch to execute query db
		try {
			$entry = DB::select ( 'username' )->from ( 'user' )->where ( 'username', '=', $username )->execute ();
			// exist
			
			if (count ( $entry ) > 0) {
				//print_r($entry) ;
				return true;
			} else {
				
				return false; // not exist
			}
		} catch ( Exception $ex ) {
			
			Log::error ( $ex->getMessage () );
			return $ex->getMessage ();
		}
	}
	
	/*
	 * the method use to insert new account into user table return true for success else return error
	 */
	public static function create_user($data) {
		// try catch for insert
		try {
			// insert query
			$entry = DB::insert ( 'user' )->columns ( array (
					'username',
					'password',
					'email',
					'lastname',
					'firstname',
					'created_at',
					'modified_at' 
			) )->values ( array (
					$data ['username'],
					$data ['password'],
					$data ['email'],
					$data ['lastname'],
					$data ['firstname'],
					$data ['created_at'],
					$data ['modified_at'] 
			) );
			
			
			
			
			$result = $entry->execute ();
			
			// Return id of username inserted
			return $result [0];
			
			
		} catch ( \Exception $ex ) {
			// write error to log
			Log::error ( $ex->getMessage () );
			
			return $ex->getMessage ();
		}
	}
	/*
	 * the method use to login
	*/
	public static function login($username, $password) {
		
		$rs = self::create_token($username, $password) ;
		//login success
		if( $rs != 0) {
			return $rs ;
		} else {
			 return false ;
		}
		
	}
	
	/*
	 * method use to create token for user @use Auth package for create token token have format sha1(\Config::get('simpleauth.login_hash_salt').$this->user['username'].$last_login)
	 */
	public static function create_token($username, $password) {
		// use auth login to creat token and insert db
		$rs = 0;
		if (Auth::login ( $username, $password )) {
			
			$data ['id'] = Auth::get ( 'id' );
			$data ['username'] = Auth::get ( 'username' );
			$data ['lastname'] = Auth::get ( 'lastname' );
			$data ['firstname'] = Auth::get ( 'firstname' );
			$data ['created_at'] = date ( 'Y-m-d', Auth::get ( 'created_at' ) );
			$data ['modified_at'] = date ( 'Y-m-d', Auth::get ( 'modified_at' ) );
			$data ['email'] = Auth::get ( 'email' );
			$data ['token'] = Auth::get ( 'login_hash' );
			
			return $data;
		} else
			return $rs;
	}
	
	
	
}