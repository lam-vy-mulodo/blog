<?php
use Fuel\Core\Route;
return array(
	'_root_'  => 'welcome/index',  // The default route
	'_404_'   => 'welcome/404',    // The main 404 route
	
	'hello(/:name)?' => array( 'welcome/hello', 'name' => 'hello' ),
	//config routes for users controller
		
	'(:version)/users' => array( array( 'GET', new Route('$1/user/index') ), array( 'POST', new Route('$1/user/register') ), 
			array( 'PUT', new Route( '$1/user/update_user' )
)),
	'(:version)/users/login' => array( array( 'POST', new Route( '$1/user/login' ))) ,
	'(:version)/users/logout' => array( array( 'PUT', new Route( '$1/user/logout' ))) ,
	'(:version)/users/(:num)' => array( array( 'GET', new Route( '$1/user/user_info/$3' ))) ,
);