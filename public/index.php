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
$app = new \Slim\Slim();
$app->config(array(
    'debug'          => true, //$config->app["debug"],
    'templates.path' => __DIR__ .'/../views/'
));

/**
 * Helper function to retrieve any type of entity in a json encoded version
 * @param  string        $entity            The entity type to retrieve
 * @param  int           $id                Id of that entity
 * @param  boolean       $fullySerialize    Flag indicating if the result shall be fully serialized (all data) or just partial (e.g. basic information)
 * @param  Slim          $app               Slim application handler
 * @param  EntityManager $em                Doctrine EntityManager
 */
function get($entity, $id, $fullySerialize, $app, $em) {
    // Define JSON as response type
    $response                 = $app->response();
    $response['Content-Type'] = 'application/json';

    $entity = $em->find($entity, (int) $id);

    if ($entity) {
        // Entity was found, create json encoded version of of it
        $entity->setFullySerialize($fullySerialize);
        $response->body(json_encode($entity));
    } else {
        // Map not found, return default 404 error (File not found)
        $response->status(404);
    }
}

/**
 * A helper function for entity creation and change which holds
 * all calls to the persists handler and transaction handling and
 * is just passed the action callback, application handler and entity manager
 * for further processing. That way all add methods can be reduced to single-line
 * calls
 * @param  function $call Function to call, retrieves EntityManager and post data array as parameters
 * @param  Slim $app  Slim application handler
 * @param  EnitityManager $em   Doctrine EntityManager
 */
function change($call, $app, $em) {
    // Define json response
    $response                 = $app->response();
    $response['Content-Type'] = 'application/json';

    if (is_callable($call)) {
        // Get request instance
        $req = $app->request();
        try {
            // Try to store the changes
            $entity = call_user_func($call, $em, $req->post());
            $entity->setFullySerialize(true);
            $response->body(json_encode(array('ok' => 'ok', 'entity' => $entity)));
        } catch (Entities\EntityException $ex) {
            // We encountered saving errors:
            $response->status($ex->getStatusCode());
            $response->body(json_encode(array("error" => array(
                'message'       => $ex->getMessage(),
                'invalidFields' => $ex->getInvalidFields()
            ))));
        }
    } else {
        // Callback is not a function
        $response->status(500);
    }
}

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



/**
 * GET - Retrieve map data by id
 * This will also include all regions, districts, locations, points of interests and buildings
 * in a json encoded format which will be used by the frontend to render the map using all information.
 */
$app->get('/map/:id', function($id) use ($app, $em) {
    get('Entities\Map', (int) $id, false, $app, $em);
});

$app->get('/district/:id', function($id) use ($app, $em) {
    get('Entities\District', (int) $id, true, $app, $em);
});

$app->post('/district/add/', function() use ($app, $em) {
    change('Entities\District::create', $app, $em);
});

$app->put('/district/edit/', function() use ($app, $em) {
    change('Entities\District::edit', $app, $em);
});

$app->post('/location/add/', function() use($app, $em) {
    change('Entities\Location::create', $app, $em);
});

$app->post('/pointofinterest/add/', function() use($app, $em) {
    change('Entities\PointOfInterest::create', $app, $em);
});

$app->post('/building/add/', function() use($app, $em) {
    change('Entities\Building::create', $app, $em);
});

// And finally run the application
$app->run();
