<?php
use Fuel\Core\Route;
return array(
	'_root_'  => 'welcome/index',  // The default route
	'_404_'   => 'welcome/404',    // The main 404 route
	
	'hello(/:name)?' => array( 'welcome/hello', 'name' => 'hello' ),
	//config routes for users controller
	//register for post, home for get and update for put method	
	'(:version)/users' => array(array('POST', new Route('$1/user/register')), 
			                     array('PUT', new Route('$1/user/update_user')
)),
	//function login
	'(:version)/users/login' => array(array('POST', new Route('$1/user/login'))) ,
	//function logout	
	'(:version)/users/logout' => array(array('PUT', new Route('$1/user/logout'))) ,	
	//get user info by get id param
	'(:version)/users/:id' => array( array('GET', new Route('$1/user/user_info2/'))) ,
	//change the password
	'(:version)/users/password' => array(array('PUT', new Route('$1/user/change_password'))),	
	'(:version)/users(:name)?' =>  array(array('GET', new Route('$1/user/search_user'))), 
	//--------------------------POST--------------------------------
	'(:version)/posts' => array(array('POST', new Route('$1/post/create'))),
	//deactive a post
    '(:version)/posts/:post_id/inactive' => array(array('PUT', new Route('$1/post/inactive_post'))),
	//deactive a post
	'(:version)/posts/:post_id/active' => array(array('PUT', new Route('$1/post/active_post'))),
	//update a post, delete post
	'(:version)/posts/:post_id' => array(array('PUT', new Route('$1/post/update_post')), array('DELETE', new Route('$1/post/post'))),
	
	
);
//the routes for use function get_user_info($id)
//'(:version)/users/(:num)' => array( array( 'GET', new Route( '$1/user/user_info/$3' ))) ,