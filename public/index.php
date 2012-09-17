<?php
require __DIR__ .'/../vendor/autoload.php';

use Doctrine\ORM\Tools\Setup;
use Doctrine\ORM\EntityManager;

$dbParams = array(
    'driver'   => 'pdo_mysql',
    'user'     => 'mytho',
    'password' => 'wiki',
    'dbname'   => 'mythowiki',
);
$devMode = true;

$config = Setup::createAnnotationMetadataConfiguration(array(__DIR__ .'/../entities'), $devMode);
$em     = EntityManager::create($dbParams, $config);

$classLoader = new \Doctrine\Common\ClassLoader('Entities', __DIR__ . '/../', 'loadClass');
$classLoader->register();

// Instantiate application
$app = new Slim();
$app->config(array(
    'debug'          => true, //$config->app["debug"],
    'templates.path' => __DIR__ .'/../views/'
));

$app->get('/createSchema', function() use ($app, $em) {
    $tool = new \Doctrine\ORM\Tools\SchemaTool($em);
    $classes = array(
        $em->getClassMetadata('Entities\Map'),
        $em->getClassMetadata('Entities\Region'),
        $em->getClassMetadata('Entities\District'),
        $em->getClassMetadata('Entities\Location'),
        $em->getClassMetadata('Entities\PointOfInterest'),
        $em->getClassMetadata('Entities\Building')
    );

    $tool->dropSchema($classes);
    $tool->createSchema($classes);
});

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
$app->get('/map/:id', function($id) use ($app, $em) {
    // Define JSON as response type
    $response                 = $app->response();
    $response['Content-Type'] = 'application/json';

    $map = $em->find("Entities\Map", (int) $id);

    if ($map) {
        // A Map was found, create json encoded version of of it
        $response->body(json_encode($map));
    } else {
        // Map not found, return default 404 error (File not found)
        $response->status(404);
    }

});


// And finally run the application
$app->run();
