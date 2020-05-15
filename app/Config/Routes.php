<?php namespace Config;

// Create a new instance of our RouteCollection class.
$routes = Services::routes();

// Load the system's routing file first, so that the app and ENVIRONMENT
// can override as needed.
if (file_exists(SYSTEMPATH . 'Config/Routes.php'))
{
	require SYSTEMPATH . 'Config/Routes.php';
}

/**
 * --------------------------------------------------------------------
 * Router Setup
 * --------------------------------------------------------------------
 */
$routes->setDefaultNamespace('App\Controllers');
$routes->setDefaultController('Page');
$routes->setDefaultMethod('index');
$routes->setTranslateURIDashes(false);
$routes->set404Override();
$routes->setAutoRoute(false);

/**
 * --------------------------------------------------------------------
 * Route Definitions
 * --------------------------------------------------------------------
 */

// We get a performance increase by specifying the default
// route since we don't have to scan directories.

$routes->group('api', function($routes){
	$routes->get('/', 'Page::index');

	$routes->group('auth', function($routes){
		$routes->post('login', 'Auth::login', ['filter' => 'guest-user']);
		$routes->get('logout', 'Auth::logout', ['filter' => 'auth-user']);
		$routes->post('register', 'Auth::register', ['filter' => 'guest-user']);
	});

	$routes->group('user', ['filter' => 'auth-user'], function($routes){
		$routes->get('profile', 'User::profile');
		$routes->delete('disconnect', 'User::disconnect');
		$routes->put('update', 'User::update');
		$routes->put('password', 'User::password');
	});

	$routes->group('blog', function($routes){
		$routes->get('/', 'Blog::index');
		$routes->get('view/(:any)', 'Blog::view/$1');
		$routes->get('search/(:any)', 'Blog::search/$1');
		$routes->post('create', 'Blog::create');
		$routes->put('update', 'Blog::update');
		$routes->delete('delete/(:any)', 'Blog::delete/$1');
	});
});

/**
 * --------------------------------------------------------------------
 * Additional Routing
 * --------------------------------------------------------------------
 *
 * There will often be times that you need additional routing and you
 * need to it be able to override any defaults in this file. Environment
 * based routes is one such time. require() additional route files here
 * to make that happen.
 *
 * You will have access to the $routes object within that file without
 * needing to reload it.
 */
if (file_exists(APPPATH . 'Config/' . ENVIRONMENT . '/Routes.php'))
{
	require APPPATH . 'Config/' . ENVIRONMENT . '/Routes.php';
}
