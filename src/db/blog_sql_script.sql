/**************CREATE DATABASE BLOG *********************/
CREATE DATABASE blog CHARACTER SET utf8 COLLATE utf8_general_ci;
use blog;


/*************CREATE TABLE user**********************/
CREATE TABLE IF NOT EXISTS user(
id INT(11) not null auto_increment,
username varchar(50) not null,
password varchar(255) not null,
email varchar(100) not null,
lastname varchar(50) not null,
firstname varchar(50) not null,
created_at int(11) not null,
modified_at int(11) not null,
last_login int(11) not null,
login_hash varchar(255) not null,
status int(1) not null default 1,
primary key(id)
)engine=InnoDB charset='utf8' auto_increment=1;



/*************CREATE TABLE post**********************/

CREATE TABLE IF NOT EXISTS post(
id int(11) not null auto_increment primary key,
title varchar(255) character set utf8 not null,
content text character set utf8 not null,
created_at int(11) not null,
modified_at int(11) not null,
author_id int(11) not null,
status int(1) not null default'1'
)engine=InnoDB charset='utf8' auto_increment=1;


/*************CREATE TABLE comment*******************/

CREATE TABLE IF NOT EXISTS comment(
id int(11) not null auto_increment primary key,
content text character set utf8 not null,
created_at int(11) not null,
modified_at int(11) not null,
author_id int(11) not null,
post_id int(11) not null
)engine=InnoDB charset='utf8' auto_increment=1;

/***********************FK key************************/
alter table post add foreign key(author_id) references user(id);
alter table comment add foreign key(author_id) references user(id);
alter table comment add foreign key(post_id) references post(id);