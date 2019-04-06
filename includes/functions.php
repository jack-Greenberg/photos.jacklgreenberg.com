<?php
//This file provides the set up for the functions used. It is where the Mustache
// and Medoo instances are set up, and the config is created.

/*
* Autoload composer files
*/
require_once(dirname(__FILE__).'/../vendor/autoload.php');

/*
* Set up template file engine for mustache
*/
$ms_template = new Mustache_Engine(array(
    'loader' => new Mustache_Loader_FilesystemLoader(dirname(__FILE__).'/../templates'),
));

/*
* Create general mustache engine
*/
$ms = new Mustache_Engine;

/*
* Set up config variables
*/
$cf = json_decode(file_get_contents(dirname(__FILE__).'/../config.json'));
$title = $cf->{'title'};
$author = $cf->{'author'};
$url = $cf->{'url'};

/*
* Set up Medoo database instance
*/
use Medoo\Medoo;
$db_config = json_decode(file_get_contents(dirname(__FILE__).'/../db_config.json'));
$db = new Medoo([
    'database_type' => $db_config->{'database_type'},
    'database_name' => $db_config->{'database_name'},
    'server' => $db_config->{'server'},
    'username' => $db_config->{'username'},
    'password' => $db_config->{'password'}
]);

?>
