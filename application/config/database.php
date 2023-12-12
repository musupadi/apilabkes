<?php
defined('BASEPATH') or exit('No direct script access allowed');


$active_group = 'default';
$query_builder = TRUE;
$db['default'] = array(
	'dsn'	=> '',
	'hostname' => 'localhost',
	// DB
	'username' => 'u1575019_musupadi',
	'password' => '99162262s',
	'database' => 'u1575019_labkes',
	// 'username' => 'root',
	// 'password' => '',
	// 'database' => 'labkes',
	// DB
	'dbdriver' => 'mysqli',
	'dbprefix' => '',
	'pconnect' => FALSE,
	'db_debug' => (ENVIRONMENT !== 'production'),
	'cache_on' => FALSE,
	'cachedir' => '',
	'char_set' => 'utf8',
	'dbcollat' => 'utf8_general_ci',
	'swap_pre' => '',
	'encrypt' => FALSE,
	'compress' => FALSE,
	'stricton' => FALSE,
	'failover' => array(),
	'save_queries' => TRUE
);


// $ServerName = $_SERVER['SERVER_NAME'];
// switch ($ServerName) {

// 	case 'api.podomorouniversity.ac.id' : 
// 		$db['default'] = array(
// 			'dsn'	=> '',
// 			'hostname' => (ENVIRONMENT == 'development') ? 'localhost' : 'pu-conn0123.mariadb.ap-southeast-5.rds.aliyuncs.com',
// 			'username' => (ENVIRONMENT == 'development') ? 'root' : 'db_itpu',
// 			'password' => (ENVIRONMENT == 'development') ? '' : '4dm1n5!S',
// 			'database' => 'db_it',
// 			'dbdriver' => 'mysqli',
// 			'dbprefix' => '',
// 			'pconnect' => FALSE,
// 			'db_debug' => (ENVIRONMENT !== 'production'),
// 			'cache_on' => FALSE,
// 			'cachedir' => '',
// 			'char_set' => 'utf8',
// 			'dbcollat' => 'utf8_general_ci',
// 			'swap_pre' => '',
// 			'encrypt' => FALSE,
// 			'compress' => FALSE,
// 			'stricton' => FALSE,
// 			'failover' => array(),
// 			'save_queries' => TRUE
// 		);

// 		$db['cloud'] = array(
// 			'dsn'	=> '',
// 			'hostname' => (ENVIRONMENT == 'development') ? 'localhost' : 'pu-conn0123.mariadb.ap-southeast-5.rds.aliyuncs.com',
// 			'username' => (ENVIRONMENT == 'development') ? 'root' : 'db_itpu',
// 			'password' => (ENVIRONMENT == 'development') ? '' : '4dm1n5!S',
// 			'database' => 'db_it',
// 			'dbdriver' => 'mysqli',
// 			'dbprefix' => '',
// 			'pconnect' => FALSE,
// 			'db_debug' => (ENVIRONMENT !== 'production'),
// 			'cache_on' => FALSE,
// 			'cachedir' => '',
// 			'char_set' => 'utf8',
// 			'dbcollat' => 'utf8_general_ci',
// 			'swap_pre' => '',
// 			'encrypt' => FALSE,
// 			'compress' => FALSE,
// 			'stricton' => FALSE,
// 			'failover' => array(),
// 			'save_queries' => TRUE
// 		);



// 		$db['server59'] = array(
// 			'dsn'	=> '',
// 			'hostname' => (ENVIRONMENT == 'development') ? 'localhost' : '10.1.30.59', // '10.1.30.18',
// 			'username' => (ENVIRONMENT == 'development') ? 'root' : 'db_itpu',
// 			'password' => (ENVIRONMENT == 'development') ? '' : 'Uap)(*&^%',
// 			'database' => 'db_it',
// 			'dbdriver' => 'mysqli',
// 			'dbprefix' => '',
// 			'pconnect' => FALSE,
// 			'db_debug' => (ENVIRONMENT !== 'production'),
// 			'cache_on' => FALSE,
// 			'cachedir' => '',
// 			'char_set' => 'utf8',
// 			'dbcollat' => 'utf8_general_ci',
// 			'swap_pre' => '',
// 			'encrypt' => FALSE,
// 			'compress' => FALSE,
// 			'stricton' => FALSE,
// 			'failover' => array(),
// 			'save_queries' => TRUE
// 		);

// 		// $db['server22'] = array(
// 		// 	'dsn'   => '',
// 		// 	'hostname' => (ENVIRONMENT == 'development') ? 'localhost' : '10.1.30.63',
// 		// 	'username' => (ENVIRONMENT == 'development') ? 'root' : 'root',
// 		// 	'password' => (ENVIRONMENT == 'development') ? '' : '4dm1n5!S',
// 		// 	'database' => 'library',
// 		// 	'dbdriver' => 'mysqli',
// 		// 	'dbprefix' => '',
// 		// 	'pconnect' => FALSE,
// 		// 	'db_debug' => (ENVIRONMENT !== 'production'),
// 		// 	'cache_on' => FALSE,
// 		// 	'cachedir' => '',
// 		// 	'char_set' => 'utf8',
// 		// 	'dbcollat' => 'utf8_general_ci',
// 		// 	'swap_pre' => '',
// 		// 	'encrypt' => FALSE,
// 		// 	'compress' => FALSE,
// 		// 	'stricton' => FALSE,
// 		// 	'failover' => array(),
// 		// 	'save_queries' => TRUE
// 		// );
// 		break;
// 	case 'apitraining.podomorouniversity.ac.id' : 
// 		$db['default'] = array(
// 			'dsn'	=> '',
// 			'hostname' => (ENVIRONMENT == 'development') ? 'localhost' : '147.139.208.171', // '10.1.30.18',
// 			'username' => (ENVIRONMENT == 'development') ? 'root' : 'db_itpu',
// 			'password' => (ENVIRONMENT == 'development') ? '' : '4dm1n5!S',
// 			'database' => 'db_it',
// 			'dbdriver' => 'mysqli',
// 			'dbprefix' => '',
// 			'pconnect' => FALSE,
// 			'db_debug' => (ENVIRONMENT !== 'production'),
// 			'cache_on' => FALSE,
// 			'cachedir' => '',
// 			'char_set' => 'utf8',
// 			'dbcollat' => 'utf8_general_ci',
// 			'swap_pre' => '',
// 			'encrypt' => FALSE,
// 			'compress' => FALSE,
// 			'stricton' => FALSE,
// 			'failover' => array(),
// 			'save_queries' => TRUE
// 		);

// 		$db['cloud'] = array(
// 			'dsn'	=> '',
// 			'hostname' => (ENVIRONMENT == 'development') ? 'localhost' : '147.139.208.171',
// 			'username' => (ENVIRONMENT == 'development') ? 'root' : 'db_itpu',
// 			'password' => (ENVIRONMENT == 'development') ? '' : '4dm1n5!S',
// 			'database' => 'db_it',
// 			'dbdriver' => 'mysqli',
// 			'dbprefix' => '',
// 			'pconnect' => FALSE,
// 			'db_debug' => (ENVIRONMENT !== 'production'),
// 			'cache_on' => FALSE,
// 			'cachedir' => '',
// 			'char_set' => 'utf8',
// 			'dbcollat' => 'utf8_general_ci',
// 			'swap_pre' => '',
// 			'encrypt' => FALSE,
// 			'compress' => FALSE,
// 			'stricton' => FALSE,
// 			'failover' => array(),
// 			'save_queries' => TRUE
// 		);



// 		$db['server59'] = array(
// 			'dsn'	=> '',
// 			'hostname' => (ENVIRONMENT == 'development') ? 'localhost' : '10.1.30.59', // '10.1.30.18',
// 			'username' => (ENVIRONMENT == 'development') ? 'root' : 'db_itpu',
// 			'password' => (ENVIRONMENT == 'development') ? '' : 'Uap)(*&^%',
// 			'database' => 'db_it',
// 			'dbdriver' => 'mysqli',
// 			'dbprefix' => '',
// 			'pconnect' => FALSE,
// 			'db_debug' => (ENVIRONMENT !== 'production'),
// 			'cache_on' => FALSE,
// 			'cachedir' => '',
// 			'char_set' => 'utf8',
// 			'dbcollat' => 'utf8_general_ci',
// 			'swap_pre' => '',
// 			'encrypt' => FALSE,
// 			'compress' => FALSE,
// 			'stricton' => FALSE,
// 			'failover' => array(),
// 			'save_queries' => TRUE
// 		);

// 		// $db['server22'] = array(
// 		// 	'dsn'   => '',
// 		// 	'hostname' => (ENVIRONMENT == 'development') ? 'localhost' : '10.1.30.63',
// 		// 	'username' => (ENVIRONMENT == 'development') ? 'root' : 'root',
// 		// 	'password' => (ENVIRONMENT == 'development') ? '' : '4dm1n5!S',
// 		// 	'database' => 'library',
// 		// 	'dbdriver' => 'mysqli',
// 		// 	'dbprefix' => '',
// 		// 	'pconnect' => FALSE,
// 		// 	'db_debug' => (ENVIRONMENT !== 'production'),
// 		// 	'cache_on' => FALSE,
// 		// 	'cachedir' => '',
// 		// 	'char_set' => 'utf8',
// 		// 	'dbcollat' => 'utf8_general_ci',
// 		// 	'swap_pre' => '',
// 		// 	'encrypt' => FALSE,
// 		// 	'compress' => FALSE,
// 		// 	'stricton' => FALSE,
// 		// 	'failover' => array(),
// 		// 	'save_queries' => TRUE
// 		// );
// 		break;

// 	default : 
// 		$db['default'] = array(
// 			'dsn'	=> '',
// 			'hostname' => (ENVIRONMENT == 'development') ? 'localhost' : 'pu-conn0123.mariadb.ap-southeast-5.rds.aliyuncs.com',
// 			'username' => (ENVIRONMENT == 'development') ? 'root' : 'db_itpu',
// 			'password' => (ENVIRONMENT == 'development') ? '' : '4dm1n5!S',
// 			'database' => 'db_it',
// 			'dbdriver' => 'mysqli',
// 			'dbprefix' => '',
// 			'pconnect' => FALSE,
// 			'db_debug' => (ENVIRONMENT !== 'production'),
// 			'cache_on' => FALSE,
// 			'cachedir' => '',
// 			'char_set' => 'utf8',
// 			'dbcollat' => 'utf8_general_ci',
// 			'swap_pre' => '',
// 			'encrypt' => FALSE,
// 			'compress' => FALSE,
// 			'stricton' => FALSE,
// 			'failover' => array(),
// 			'save_queries' => TRUE
// 		);

// 		$db['cloud'] = array(
// 			'dsn'	=> '',
// 			'hostname' => (ENVIRONMENT == 'development') ? 'localhost' : 'pu-conn0123.mariadb.ap-southeast-5.rds.aliyuncs.com',
// 			'username' => (ENVIRONMENT == 'development') ? 'root' : 'db_itpu',
// 			'password' => (ENVIRONMENT == 'development') ? '' : '4dm1n5!S',
// 			'database' => 'db_it',
// 			'dbdriver' => 'mysqli',
// 			'dbprefix' => '',
// 			'pconnect' => FALSE,
// 			'db_debug' => (ENVIRONMENT !== 'production'),
// 			'cache_on' => FALSE,
// 			'cachedir' => '',
// 			'char_set' => 'utf8',
// 			'dbcollat' => 'utf8_general_ci',
// 			'swap_pre' => '',
// 			'encrypt' => FALSE,
// 			'compress' => FALSE,
// 			'stricton' => FALSE,
// 			'failover' => array(),
// 			'save_queries' => TRUE
// 		);



// 		$db['server59'] = array(
// 			'dsn'	=> '',
// 			'hostname' => (ENVIRONMENT == 'development') ? 'localhost' : '10.1.30.59', // '10.1.30.18',
// 			'username' => (ENVIRONMENT == 'development') ? 'root' : 'db_itpu',
// 			'password' => (ENVIRONMENT == 'development') ? '' : 'Uap)(*&^%',
// 			'database' => 'db_it',
// 			'dbdriver' => 'mysqli',
// 			'dbprefix' => '',
// 			'pconnect' => FALSE,
// 			'db_debug' => (ENVIRONMENT !== 'production'),
// 			'cache_on' => FALSE,
// 			'cachedir' => '',
// 			'char_set' => 'utf8',
// 			'dbcollat' => 'utf8_general_ci',
// 			'swap_pre' => '',
// 			'encrypt' => FALSE,
// 			'compress' => FALSE,
// 			'stricton' => FALSE,
// 			'failover' => array(),
// 			'save_queries' => TRUE
// 		);

// 		// $db['server22'] = array(
// 		// 	'dsn'   => '',
// 		// 	'hostname' => (ENVIRONMENT == 'development') ? 'localhost' : '10.1.30.63',
// 		// 	'username' => (ENVIRONMENT == 'development') ? 'root' : 'root',
// 		// 	'password' => (ENVIRONMENT == 'development') ? '' : '4dm1n5!S',
// 		// 	'database' => 'library',
// 		// 	'dbdriver' => 'mysqli',
// 		// 	'dbprefix' => '',
// 		// 	'pconnect' => FALSE,
// 		// 	'db_debug' => (ENVIRONMENT !== 'production'),
// 		// 	'cache_on' => FALSE,
// 		// 	'cachedir' => '',
// 		// 	'char_set' => 'utf8',
// 		// 	'dbcollat' => 'utf8_general_ci',
// 		// 	'swap_pre' => '',
// 		// 	'encrypt' => FALSE,
// 		// 	'compress' => FALSE,
// 		// 	'stricton' => FALSE,
// 		// 	'failover' => array(),
// 		// 	'save_queries' => TRUE
// 		// );
// 	break;

// }
