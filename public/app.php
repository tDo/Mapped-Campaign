<?php

// Check if a config instance has been defined
if (!$config) die('No configuration provided, terminating...');

// Extend include paths to point to libraries as well as models subfolders
set_include_path(get_include_path() . PATH_SEPARATOR . __DIR__ .'/../Libraries/');
set_include_path(get_include_path() . PATH_SEPARATOR . __DIR__ .'/../Models/');

// Require libraries
require_once 'Slim/Slim.php';
require_once 'idiorm.php';
require_once 'paris.php';

// Configure ORM
ORM::configure('mysql:host='. $config->database['host'] .';dbname='. $config->database['name']);
ORM::configure('username', $config->database['username']);
ORM::configure('password', $config->database['password']);
ORM::configure('driver_options', array(PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8'));

// Require models
require_once 'Base.php';
require_once 'Map.php';
require_once 'Region.php';
require_once 'District.php';
require_once 'PointOfInterest.php';
require_once 'Location.php';
require_once 'Building.php';

// Instantiate application
$app = new Slim();
$app->config(array(
    'debug'          => $config->app["debug"],
    'templates.path' => __DIR__ .'/../Views/'
));

// Define routes
$app->get('/', function() use ($app) {
    $app->render('index.php');
});

$app->get('/maps/', function() use($app) {
    
});

/**
 * GET - Retrieve map data by id
 * This will also include all regions, districts, locations, points of interests and buildings
 * in a json encoded format which will be used by the frontend to render the map using all information.
 */
$app->get('/map/:id', function($id) use ($app) {
    // Define response instance and type (Which is Json)
    $response                 = $app->response();
    $response['Content-Type'] = 'application/json';

    $map = Model::factory('Models\Map')->find_one($id);
    if ($map) {
        // A map was found, encode it
        // TODO: Implement a caching procedure for faster access...
        $response->body(json_encode($map));
    } else {
        // Map was not found, return default 404 error (File not found)
        $response->status(404);
    }
});


// And finally run the application
$app->run();
