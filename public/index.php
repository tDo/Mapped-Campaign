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

/**
 * A helper function for entity creation which holds
 * all calls to the persists handler and transaction handling and
 * is just passed the action callback, application handler and entity manager
 * for further processing. That way all add methods can be reduced to single-line
 * calls
 * @param  function $call Function to call, retrieves EntityManager and post data array as parameters
 * @param  Slim $app  Slim application handler
 * @param  EnitityManager $em   Doctrine EntityManager
 */
function add($call, $app, $em) {
    // Define json response
    $response                 = $app->response();
    $response['Content-Type'] = 'application/json';

    // Get request instance
    $req = $app->request();

    // And begin the transaction for instance creation
    $em->getConnection()->beginTransaction();
    try {
        // Create the instance by calling our callback
        $entity = call_user_func($call, $em, $req->post());
        // Seems to be createable, persists it
        $em->persist($entity);
        $em->flush();
        $em->getConnection()->commit();

        // Return OK response (for now)
        $response->body(json_encode(array("OK" => "OK")));

    } catch (Exception $ex) {
        // Something failed, rollback transaction and close the connection
        $em->getConnection()->rollback();
        $em->close();

        // TODO: Implement error handling
        $response->body(json_encode(array("Error" => $ex->getMessage())));
    }
}

/*
$app->post('/region/add/', function() use($app, $em) {
    add('Entities\Region::create', $app, $em);
});*/

$app->post('/district/add/', function() use ($app, $em) {
    add('Entities\District::create', $app, $em);
});

$app->post('/location/add/', function() use($app, $em) {
    add('Entities\Location::create', $app, $em);
});

$app->post('/pointofinterest/add/', function() use($app, $em) {
    add('Entities\PointOfInterest::create', $app, $em);
});

$app->post('/building/add/', function() use($app, $em) {
    add('Entities\Building::create', $app, $em);
});

// And finally run the application
$app->run();
