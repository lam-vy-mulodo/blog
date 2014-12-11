<?php
/**
 * define the constant in system
 * must be required in bootstrap.php file
 */
//return success code 
define('SUSSCESS_CODE', '200');
//cant insert new user in db
define('SYSTEM_ERROR', '9005');
//set error constant code

/*
 * THE USER ERROR CONSTANT
 */

//user exist in db
define('USER_EXIST_CODE', '2001');
define('USER_EXIST_DESC','Username exist in database');
define('USER_EXIST_MSG','This username is already in used');
//user id not exist- when get user info from id
define('USER_NOT_EXIST_ERROR', '2004');
define('USER_NOT_EXIST_MSG', 'User id not exist.');
//validation input data error code OF USER
define('USER_VALIDATE_ERROR', '1001');
define('USER_VALIDATE_ERROR_MSG', 'Input validation failed');
//user logged
define('USER_LOGGED_CODE', '1204');
define('USER_LOGGED_DESC', 'You login more 1 times.');
define('USER_LOGGED_MSG', 'You had logged'	);
//user or pass wrong for login failed
define('USER_LOGIN_ERROR', '1203');
define('USER_LOGIN_ERROR_DESC', 'Username or password wrong . Or the user not exist in system');
define('USER_LOGIN_ERROR_MSG','Username or password incorrect.');

//chnage password error
define('USER_VALIDATE_CHANGE_PASS_ERROR', '1004');
define('USER_CHANGE_PASS_ERROR', '2004');
define('USER_CHANGE_PASS_DESC', 'The old password incorrect or new password same as the old_password!');
define('USER_CHANGE_PASS_MSG', 'Can\' change password');
//token null error code

/*
 *  THE TOKEN CONSTANT
 */

define('TOKEN_NULL_ERROR', '1202');
define('TOKEN_NULL_DESC', 'Token is null');
define('TOKEN_NULL_MSG', 'Token empty');
//token not exist in db error code
define('TOKEN_NOT_EXIST_ERROR', '1205');
define('TOKEN_NOT_EXIST_MSG', 'Token is not exist in db.');
