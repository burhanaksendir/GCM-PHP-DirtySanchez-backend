<?php
require_once 'config.class.php';

try {
 
	config::set('host', 'localhost');
	config::set('user', 'argenteam');
	config::set('pass', 'argenteam');
	config::set('db', 'argenteam');
 
	config::set('db_driver', 'mysql');
 
	config::set('pdo_database_settings', array(
 
		'dsn' => config::get('db_driver').':host='.config::get('host').';dbname='.config::get('db'),
		'user' => config::get('user'),
		'pass' => config::get('pass'),
		'db'   => config::get('db')
 
	));
 
}catch(Exception $e){
 
	echo $e->getMessage();
 
}