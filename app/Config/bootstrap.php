<?php
/**
 * This file is loaded automatically by the app/webroot/index.php file after core.php
 *
 * This file should load/create any application wide configuration settings, such as
 * Caching, Logging, loading additional configuration files.
 *
 * You should also use this file to include any files that provide global functions/constants
 * that your application uses.
 *
 * PHP 5
 *
 * CakePHP(tm) : Rapid Development Framework (http://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link          http://cakephp.org CakePHP(tm) Project
 * @package       app.Config
 * @since         CakePHP(tm) v 0.10.8.2117
 * @license       http://www.opensource.org/licenses/mit-license.php MIT License
 */

// Setup a 'default' cache configuration for use in the application.
Cache::config('default', array('engine' => 'File'));

/**
 * The settings below can be used to set additional paths to models, views and controllers.
 *
 * App::build(array(
 *     'Model'                     => array('/path/to/models', '/next/path/to/models'),
 *     'Model/Behavior'            => array('/path/to/behaviors', '/next/path/to/behaviors'),
 *     'Model/Datasource'          => array('/path/to/datasources', '/next/path/to/datasources'),
 *     'Model/Datasource/Database' => array('/path/to/databases', '/next/path/to/database'),
 *     'Model/Datasource/Session'  => array('/path/to/sessions', '/next/path/to/sessions'),
 *     'Controller'                => array('/path/to/controllers', '/next/path/to/controllers'),
 *     'Controller/Component'      => array('/path/to/components', '/next/path/to/components'),
 *     'Controller/Component/Auth' => array('/path/to/auths', '/next/path/to/auths'),
 *     'Controller/Component/Acl'  => array('/path/to/acls', '/next/path/to/acls'),
 *     'View'                      => array('/path/to/views', '/next/path/to/views'),
 *     'View/Helper'               => array('/path/to/helpers', '/next/path/to/helpers'),
 *     'Console'                   => array('/path/to/consoles', '/next/path/to/consoles'),
 *     'Console/Command'           => array('/path/to/commands', '/next/path/to/commands'),
 *     'Console/Command/Task'      => array('/path/to/tasks', '/next/path/to/tasks'),
 *     'Lib'                       => array('/path/to/libs', '/next/path/to/libs'),
 *     'Locale'                    => array('/path/to/locales', '/next/path/to/locales'),
 *     'Vendor'                    => array('/path/to/vendors', '/next/path/to/vendors'),
 *     'Plugin'                    => array('/path/to/plugins', '/next/path/to/plugins'),
 * ));
 *
 */

/**
 * Custom Inflector rules, can be set to correctly pluralize or singularize table, model, controller names or whatever other
 * string is passed to the inflection functions
 *
 * Inflector::rules('singular', array('rules' => array(), 'irregular' => array(), 'uninflected' => array()));
 * Inflector::rules('plural', array('rules' => array(), 'irregular' => array(), 'uninflected' => array()));
 *
 */

/**
 * Plugins need to be loaded manually, you can either load them one by one or all of them in a single call
 * Uncomment one of the lines below, as you need. make sure you read the documentation on CakePlugin to use more
 * advanced ways of loading plugins
 *
 * CakePlugin::loadAll(); // Loads all plugins at once
 * CakePlugin::load('DebugKit'); //Loads a single plugin named DebugKit
 *
 */

/**
 * You can attach event listeners to the request lifecycle as Dispatcher Filter . By Default CakePHP bundles two filters:
 *
 * - AssetDispatcher filter will serve your asset files (css, images, js, etc) from your themes and plugins
 * - CacheDispatcher filter will read the Cache.check configure variable and try to serve cached content generated from controllers
 *
 * Feel free to remove or add filters as you see fit for your application. A few examples:
 *
 * Configure::write('Dispatcher.filters', array(
 *		'MyCacheFilter', //  will use MyCacheFilter class from the Routing/Filter package in your app.
 *		'MyPlugin.MyFilter', // will use MyFilter class from the Routing/Filter package in MyPlugin plugin.
 * 		array('callable' => $aFunction, 'on' => 'before', 'priority' => 9), // A valid PHP callback type to be called on beforeDispatch
 *		array('callable' => $anotherMethod, 'on' => 'after'), // A valid PHP callback type to be called on afterDispatch
 *
 * ));
 */
Configure::write('Dispatcher.filters', array(
	'AssetDispatcher',
	'CacheDispatcher'
));

/**
 * Configures default file logging options
 */
App::uses('CakeLog', 'Log');
CakeLog::config('debug', array(
	'engine' => 'FileLog',
	'types' => array('notice', 'info', 'debug'),
	'file' => 'debug',
));
CakeLog::config('error', array(
	'engine' => 'FileLog',
	'types' => array('warning', 'error', 'critical', 'alert', 'emergency'),
	'file' => 'error',
));

//Walk request default message
define("WALK_REQUEST_MSG", "This is an automatic walk request.");

//Default distance filter for nearby searches
define("NEARBY_DISTANCE", 10);

//User roles
define("ROLE_ADMIN", "1");
define("ROLE_USER", "2");

//Uploaded files path
$path = "/uploaded_files";
define("UPLOAD_PATH", $_SERVER["DOCUMENT_ROOT"].$path);
define("FILE_PATH", $path);
define("USER_PATH", "/users");
define("DOG_PATH", "/dogs");
define("PLACE_PATH", "/places");

//Activity types
define("ACTIVITY_WALK", 1);

//Notification types
define("NOTIFICATION_NEW_FOLLOWER", 1);
define("NOTIFICATION_WALK_REQUEST", 2);
define("NOTIFICATION_COMMENT_ACTIVITY", 3);
define("NOTIFICATION_LIKE_ACTIVITY", 4);
define("NOTIFICATION_AWARD_BADGE", 5);

//Feed types
define("FEED_NEW_WALK", 1);
define("FEED_NEW_DOG", 2);
define("FEED_FRIEND_NEW_FOLLOWER", 3);
define("FEED_FRIEND_LIKE_DOG", 4);
define("FEED_FRIEND_LIKE_ACTIVITY", 5);
define("FEED_FRIEND_COMMENT_ACTIVITY", 6);
define("FEED_CHECKIN", 7);

//Map filter types
define("MAP_FILTER_PARK", 1);
define("MAP_FILTER_HOMELESS", 2);
define("MAP_FILTER_CRUELTY", 3);
define("MAP_FILTER_PETSHOP", 4);
define("MAP_FILTER_VETERINARY", 5);
define("MAP_FILTER_DOG_HOSPITAL", 6);
define("MAP_FILTER_PUBLIC_PLACE", 7);
define("MAP_FILTER_BEACH", 8);
define("MAP_FILTER_WORKPLACE", 9);
define("MAP_FILTER_RECENTLY_OPEN", 100);
define("MAP_FILTER_MATING", 101);
define("MAP_FILTER_SAME_BREED", 102);
//Map filter additional properties
define("MAP_FILTER_RECENTLY_OPEN_DAYS", 100);

//Place categories
define("PLACE_CATEGORY_PARK", 1);
define("PLACE_CATEGORY_HOMELESS", 2);
define("PLACE_CATEGORY_CRUELTY", 3);
define("PLACE_CATEGORY_PETSHOP", 4);
define("PLACE_CATEGORY_VETERINARY", 5);
define("PLACE_CATEGORY_DOG_HOSPITAL", 6);
define("PLACE_CATEGORY_OTHER_PLACE", 7);
define("PLACE_CATEGORY_BEACH", 8);
define("PLACE_CATEGORY_WORKPLACE", 9);

//API general
define("REQUEST_OK", 1);
define("REQUEST_UNAUTHORISED", -100);
define("REQUEST_FAILED", -1);
define("REQUEST_INVALID", 100);

//API Error codes
define("ERROR_EMAIL_TAKEN", -2);
define("ERROR_USER_CREATION", -3);
define("ERROR_USER_PHOTO_UPLOAD", -4);
define("ERROR_DOG_CREATION", -5);
define("ERROR_DOG_PHOTO_UPLOAD", -6);
define("ERROR_USER_ALREADY_FOLLOWING", -7);
define("ERROR_USER_NOT_FOLLOWING", -8);
define("ERROR_PLACE_CREATION", -9);
define("ERROR_PLACE_PHOTO_UPLOAD", -10);
define("ERROR_COMMENT_CREATION", -11);
define("ERROR_ACTIVITY_CREATION", -12);
define("ERROR_ACTIVITY_COORDINATE_CREATION", -13);
define("ERROR_ACTIVITY_DOG_CREATION", -14);
define("ERROR_FEED_CREATION", -15);
define("ERROR_PHOTO_UPLOAD", -16);
define("ERROR_CHECKIN_CREATION", -17);
define("ERROR_NOTE_CREATION", -18);
define("ERROR_NOTE_DELETION", -19);
define("ERROR_DOG_DELETION", -20);

//PHOTO TYPES
define("USER_PHOTO_TYPE", 1);
define("DOG_PHOTO_TYPE", 2);
define("GALLERY_PHOTO_TYPE", 3);
define("PLACE_PHOTO_TYPE", 4);

//NOTES INTERACTION TYPE
define("ADD_NOTE", 2);
define("EDIT_NOTE", 3);

//Badges
define("BADGE_PUPPY", 1);
define("BADGE_LAZY", 2);
define("BADGE_OLYMPIAN", 3);
define("BADGE_SUPERFAMILY", 4);
define("BADGE_ATHLETIC", 5);
define("BADGE_CROSSFIT", 6);
define("BADGE_SAVIOR", 7);
define("BADGE_WORKIE", 8);
define("BADGE_SWIMMIE", 9);
define("BADGE_VIP", 10);
define("BADGE_CRUELTY", 11);
define("BADGE_GODFATHER", 12);
define("BADGE_ROOKIE", 13);
define("BADGE_SUPERSTAR", 14);
define("BADGE_HEALTHY", 15);
define("BADGE_101_DALMATIANS", 16);