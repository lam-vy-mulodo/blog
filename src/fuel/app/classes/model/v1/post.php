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
 * extends from Model 
 * @var Post contain method do some transaction with post table 
 * ex : create, update, delete, active/deactive the post
 * 
 */

class Post extends \Orm\Model {
	//create table name for user
	protected static $_table_name = 'post';
	
	//create properties for user
	protected  static  $_properties = array('id', 'title', 'content', 'author_id', 'created_at', 'modified_at');
	
   /*
	* method use to create a new post
	* @param input data  include title, content, user_id
	* @return array data of the post created
	*/
	public static function create_post($data) {
		
		//first, validate title and content data
		if (!empty($data['title']) && !empty($data['content'])
	    	&& strlen($data['title'] <= 255)) {
	    		//set the time created and first modified
	    		$time = time();
	    		$data['created_at'] = $time;
	    		$data['modified_at'] = $time;
	    		//use orm to insert new
	    		$post = Post::forge($data);
	    		$post->save();
	    		
	    			//return the data of the post
	    			return array(
	    					'meta' => array(
	    							'code' => SUSSCESS_CODE,
	    							'messages' => 'Create post success!'
	    					),
	    					'data' => array(
	    							'id' => $post->id,
	    							'title' => $data['title'],
	    							'created_at' => $time,
	    							'modified_at' => $time,
	    							'author_id' => $data['author_id'],
	    					)
	    			);
	    		
	    		
	    		
		} else {
			//create the post failed
			return array(
					'meta' => array(
							'code' => POST_CREATE_ERROR,
							'desc' => POST_CREATE_DESC,
							'messages' => array(
									array('message' => 'The title and content the post are required'),
							        array('message' => 'The title not contain more than 255 characters')
					)),
					'data' => null
			);
		
		}
		
		
	}
	
   /*
	* method use to update the status of post
	* status 1 is active, 0 is de-active
	* @param input data  include post_id,status, author_id
	* @return array data of the post updated
	*/
	public static function update_status($post_id, $status, $author_id) {
		try {
			//update
			$row = DB::update('post')->value('status', $status)->where('id', $post_id)->where('author_id', $author_id)->execute();
			//check row affected
			// > 0 is ok
			
			if ($row > 0) {
				//get info of post
				$data = DB::select('id', 'title', 'status', 'created_at', 'modified_at')
				          ->from('post')
				          ->where('id', '=', $post_id)
				          ->execute();
				return $data[0];
			} else {
				return false;
			}
			
	    } catch(\Exception $ex) {
	    	Log::error($ex->getMessage());
	    	return $ex->getMessage();
	    }
	}
	
   /*
	* method use to update the title ,content of post
	* @param input data  include title, content and post_id
	* @return array data of the post updated
	*/
	
	public static function update_post($data) {
		//first, validate title and content data
		if (!empty($data['title']) && !empty($data['content'])
				&& strlen($data['title'] <= 255)) {
					//set the time created and first modified
					$time = time();
					$data['modified_at'] = $time;
					//update
					//call update
					$row = DB::update('post')->set(
							array(
									'title' => $data['title'],
									'content' => $data['content'],
									'modified_at' => $time
							))->where('id', $data['post_id'])->where('author_id', $data['author_id'])->execute();
					if ($row > 0) {	
						//get data from user id
						$rs = DB::select('id', 'title', 'content', 'created_at', 'modified_at')
						->from('post')
						->where('id', '=', $data['post_id'])
						->execute();
						
						//return data of the post
						return array(
								'meta' => array(
										'code' => SUSSCESS_CODE,
										'messages' => 'Update post success!'
								),
								'data' =>$rs[0]
								);
					} else {
						//not row affected
						//update failed, not any row change
					return array(
							'meta' => array(
									'code' => POST_UPDATE_ERROR,
									'desc' => POST_UPDATE_DESC,
									'messages' => POST_UPDATE_MSG
							),
							'data' => null
					        );
						
					}
			   
			   
				} else {
					//return false for validate error
					return false;
		
				}
	}
	
   /*
	* method use to delete the post by id
	* @param input post_id
	* @return the code 200 when success, 2503 when error
	*/
	public static function delete_post($post_id, $author_id) {
		//query delete
		$entry = DB::delete('post')->where('id', '=', $post_id)->where('author_id', '=', $author_id)->execute();
		//check entry > 0 = delete success
		//entry is empty  = delete unsuccess
		if (is_numeric($entry) && $entry > 0) {
			$rs = array(
							'meta' => array(
									'code' => SUSSCESS_CODE,
									'messages' => 'Delete post success!'
							),
							'data' => null
					        );
		} else {
			$rs = array(
					'meta' => array(
							'code' => POST_DELETE_ERROR,
							'description' => POST_DELETE_DESC,
							'messages' => POST_DELETE_MSG
					),
					'data' => null
			);
		}
		
		return $rs;
	} 
}