<?php

// This is the configuration for yiic console application.
// Any writable CConsoleApplication properties can be configured here.
return array(
	'basePath'=>dirname(__FILE__).DIRECTORY_SEPARATOR.'..',
	'name'=>'My Console Application',
	// application components
    	// autoloading model and component classes
	'import'=>array(
		'application.models.*',
	),

	'components'=>array(
        /*
		'db'=>array(
			'connectionString' => 'sqlite:'.dirname(__FILE__).'/../data/testdrive.db',
		),*/
		// uncomment the following to use a MySQL database
		'db'=>array(
			'connectionString' => 'mysql:host=localhost;dbname=drm',
			'emulatePrepare' => true,
			'username' => 'drm',
			'password' => 's7zGldD1rSyXri',
			'charset' => 'utf8',
		),
	),
   	'params'=>array(
		// this is used in contact page
		'adminEmail'=>'webmaster@example.com',
		'logsEmail'=>'ferllings@gmail.com',
	),
);