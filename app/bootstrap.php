<?php

/**
 * My Application bootstrap file.
 *
 * @copyright  Copyright (c) 2010 John Doe
 * @package    MyApplication
 */


use Nette\Debug,
	Nette\Environment,
	Nette\Application\Route,
	Nette\Application\SimpleRouter,
        Nette\Loaders\RobotLoader;



// Step 1: Load Nette Framework
// this allows load Nette Framework classes automatically so that
// you don't have to litter your code with 'require' statements
require LIBS_DIR . '/Nette/loader.php';
//require LIBS_DIR . '/Ext/simple_html_dom.php';



// Step 2: Configure environment
// 2a) enable Nette\Debug for better exception and error visualisation
Debug::enable();

// 2b) load configuration from config.ini file
Environment::loadConfig();

$loader = new RobotLoader();
//$loader->addDirectory(MIXED_DIR);


// Step 3: Configure application
// 3a) get and setup a front controller
$application = Environment::getApplication();
$application->errorPresenter = 'Error';
//$application->catchExceptions = TRUE;



// Step 4: Setup application router
$router = $application->getRouter();

$router[] = new Route('administrace/<presenter>/<action>/<id>', array(
                'module' => 'Admin',
		'presenter' => 'Homepage',
		'action' => 'default',
		'id' => NULL,
	));

$router[] = new Route('index.php', array(
        'module' => 'Front',
	'presenter' => 'Homepage',
	'action' => 'default',
), Route::ONE_WAY);

$router[] = new Route('<presenter>/<action>/<id>', array(
        'module' => 'Front',
	'presenter' => 'Homepage',
	'action' => 'default',
	'id' => NULL,
));

$application->onStartup[] = 'BaseModel::connect';
$application->onShutdown[] = 'BaseModel::disconnect';

$date = new \DateTime();
$date = $date->format('Y-m-d H:i:s');
define('DATE_NOW', $date);

// Step 5: Run the application!
$application->run();
