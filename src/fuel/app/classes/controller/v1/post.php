<?php

use Model\V1\Post;
use Model\V1\User;
use Fuel\Core\Controller_Rest;
use Auth\Auth;
use Fuel\Core\Validation;
use Fuel\Core\Security;
use Fuel\Core\Input;

/**
 * The Post Controller.
 * @extends  Controller_Rest for API
 * A post controller have function for post use the blog system
 * @package  app
 *
 */
class Controller_V1_Post extends Controller_Rest {
	//return json format
	protected $format = 'json';
	//clean data
	protected $filters = array('strip_tags', 'htmlentities');
	
	/**
	 * The method add new post
	 * @link http://localhost/v1/posts/
	 * @method : POST
	 * @access  public
	 * @return  Response
	 */
	public function post_create() {
		
		$token = Security::clean(Input::post('token'), $this->filters);
		//check the token null
		if (!empty($token)) {
			//check the token is exist
			
			$rs = User::check_token($token);
			if (is_numeric($rs) && $rs > 0) {
				//set data to create post
				$data['author_id'] = $rs;
				$data['title'] = Security::clean(Input::post('title'), $this->filters);
				$data['content'] = Security::clean(Input::post('content'), $this->filters);
				//call create from model
				$result = Post::create_post($data);
				//return result
				return $this->response($result);	
			} else {
				return $this->response($rs);	
			}
		} else {
			//return token null error
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
	 * The method update status deactive for the post
	 * @link http://localhost/v1/posts/inactive
	 * @method : PUT
	 * @access  public
	 * @return  Response
	 */
	public function put_inactive_post() {
		//check token
		$token = Security::clean(Input::put('token'), $this->filters);
		//check token is emtpy
		if (empty($token)) {
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
			
		} else {
			//check token valid
			$rs = User::check_token($token);
			if (is_numeric($rs) && $rs > 0) {
				//set status for inactive is 0
				$status = POST_INACTIVE_STATUS ;
				$post_id = Security::clean($this->param('post_id'), $this->filters);
				$author_id = $rs;
				//call update status
				$data = Post::update_status($post_id, $status, $author_id);
				
				//check update success
				if (false !== $data) {
					return $this->response(
						   array(
						       'meta' => array(
					                     'code' => SUSSCESS_CODE,
						       		     'message' => 'Deactive post success'    	
						        ),
						   		'data' => $data
						       ));
				} else {
					return $this->response(array(
							'meta' => array(
									'code' => POST_STATUS_ERROR,
									'desc' => POST_STATUS_DESC,
									'messages' => POST_STATUS_MSG,
							),
							'data' => null
					));
				}
				return $this->response($data);
			} else {
				return $this->response($rs);
			}
		}
	}
	
	/**
	 * The method update status active for the post
	 * @link http://localhost/v1/posts/active
	 * @method : PUT
	 * @access  public
	 * @return  Response
	 */
	public function put_active_post() {
		//check token
		$token = Security::clean(Input::put('token'), $this->filters);
		//check token is emtpy
		if (empty($token)) {
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
				
		} else {
			//check token valid
			$rs = User::check_token($token);
			if (is_numeric($rs) && $rs > 0) {
				//set status for deactive is 0
				$status = POST_ACTIVE_STATUS ;
				$post_id = Security::clean($this->param('post_id'), $this->filters);
				$author_id = $rs;
				//call update status
				$data = Post::update_status($post_id, $status, $author_id);
			
				//check update success
				if (false !== $data) {
					return $this->response(
						   array(
						       'meta' => array(
					                     'code' => SUSSCESS_CODE,
						       		     'message' => 'Active post success'    	
						        ),
						   		'data' => $data
						       ));
				} else {
					return $this->response(array(
							'meta' => array(
									'code' => POST_STATUS_ERROR,
									'desc' => POST_STATUS_DESC,
									'messages' => POST_STATUS_MSG,
							),
							'data' => null
					));
				}
				return $this->response($data);
			} else {
				return $this->response($rs);
			}
		}
	}
	
	public function put_update_post() {
		//token
		$token = Security::clean(Input::put('token'), $this->filters);
		if (empty($token)) {
			//error null token
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
		} else {
			
			//check token valid
			$rs = User::check_token($token);
			
			if (is_numeric($rs) && $rs > 0) {
				//set data to update
				$data = array ();
				$data['title'] = Security::clean(Input::put('title'), $this->filters);
				$data['content'] = Security::clean(Input::put('content'), $this->filters);
				$data['post_id'] = $this->param('post_id');
				$data['author_id'] = $rs;
				//call update
				$result = Post::update_post($data);
				//check success update
				if(false !== $result) {
					//return data
					return $this->response($result);
					
				} else {
			    	
					//error about validate update
					return $this->response(
							array(
							'meta' => array(
									'code' => POST_CREATE_ERROR,
									'desc' => POST_CREATE_DESC,
									'messages' => array(
											array('message' => 'The title and content the post are required'),
											array('message' => 'The title not contain more than 255 characters')
									)),
							'data' => null
					));
				}
				
				
			} else {
				//return error token invalid
				return $this->response($rs);
			}
		}
		
	}
	
	/**
	 * The method delete a post
	 * @link http://localhost/v1/posts/{post_id}
	 * @method : DELETE
	 * @access  public
	 * @return  Response
	 */
	public function delete_post() {
		//get the post id
		$post_id = $this->param('post_id');
		$token = Security::clean(Input::delete('token'), $this->filters);
		//check token empty
		if (empty($token)) {
			//error code
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
		} else {
			$rs = User::check_token($token);
			//check user id return 
			if (is_numeric($rs) && $rs > 0) {
				$author_id = $rs;
				$result = Post::delete_post($post_id, $author_id);
				//response result
				return $this->response($result);
			} else {
				//error token is not exist db
				return $this->response($rs) ;
			}
		}
		
		
	}
}