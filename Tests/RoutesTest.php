<?php
require_once(__DIR__ .'/../Libraries/Slim/Slim.php');

/**
 * Test class holds tests concerning the routes of the main application and their
 * behavior. A mocke environment of Slim will be used. Be aware that real server connections
 * could be established!
 */
class RoutesTest extends PHPUnit_Framework_TestCase {

	/**
	 * Method mocks a request to the slim routes
	 * calling each of them in a testing environment
	 * @param  string $method  Holds the request method type (e.g. GET, POST, PUT, DELETE, etc.)
	 * @param  string $path    Route to call (e.g. /this/goes/somewhere/...)
	 * @param  array  $options Array which allows us to prefil the environment variables of slim (see http://www.slimframework.com/documentation/develop#environment)
	 * @return string          Actual output from stdout which is captured
	 */
	public function request($method, $path, $options = array()) {
		// Used to capture stdout
		//ob_start();

		// Prepare a mock environment
        Slim_Environment::mock(array_merge(array(
            'REQUEST_METHOD' => $method,
            'PATH_INFO'      => $path,
            'SERVER_NAME'    => 'testserver.foo',
        ), $options));

        // Require the actual application
        require __DIR__ .'/../public/index.php';

        $this->app      = $app;
        $this->request  = $app->request();
        $this->response = $app->response();

		//return ob_get_clean();
	}

	public function get($path, $options = array()) {
		$this->request('GET', $path, $options);
	}

	/**
	 * Test if a list of all stored maps can be returned
	 * @return [type] [description]
	 */
	public function testCanRetrieveMaps() {
		$this->get('/maps/');
		$this->assertEquals('200', $this->response->status());
	}
}