/**************CREATE DATABASE BLOG *********************/
CREATE DATABASE blog CHARACTER SET utf8 COLLATE utf8_general_ci;
use blog;


/*************CREATE TABLE user**********************/
CREATE TABLE IF NOT EXISTS user(
id INT(11) NOT NULL AUTO_INCREMENT primary key,
username VARCHAR(50) NOT NULL,
password VARCHAR(255) NOT NULL,
email VARCHAR(100) NOT NULL,
lastname VARCHAR(50) NOT NULL,
firstname VARCHAR(50) NOT NULL,
created_at INT(11) NOT NULL,
modified_at INT(11) NOT NULL,
last_login INT(11) NOT NULL,
login_hash VARCHAR(255) NOT NULL,
status INT(1) NOT NULL DEFAULT 1,
FULLTEXT INDEX(username,lastname,firstname)
)ENGINE=InnoDB CHARSET='utf8' AUTO_INCREMENT=1;



/*************CREATE TABLE post**********************/

CREATE TABLE IF NOT EXISTS post(
id INT(11) NOT NULL AUTO_INCREMENT primary key,
title VARCHAR(255) character set utf8 NOT NULL,
content text character set utf8 NOT NULL,
created_at INT(11) NOT NULL,
modified_at INT(11) NOT NULL,
author_id INT(11) NOT NULL,
status INT(1) NOT NULL DEFAULT'1'
)ENGINE=InnoDB CHARSET='utf8' AUTO_INCREMENT=1;


/*************CREATE TABLE comment*******************/

CREATE TABLE IF NOT EXISTS comment(
id INT(11) NOT NULL AUTO_INCREMENT primary key,
content text character set utf8 NOT NULL,
created_at INT(11) NOT NULL,
modified_at INT(11) NOT NULL,
author_id INT(11) NOT NULL,
post_id INT(11) NOT NULL
)ENGINE=InnoDB CHARSET='utf8' AUTO_INCREMENT=1;

/***********************FK key************************/
ALTER TABLE post ADD FOREIGN KEY(author_id) REFERENCES user(id);
ALTER TABLE comment ADD FOREIGN KEY(author_id) REFERENCES user(id);
ALTER TABLE comment ADD FOREIGN KEY(post_id) REFERENCES post(id) ON DELETE CASCADE;