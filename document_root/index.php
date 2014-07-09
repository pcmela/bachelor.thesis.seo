<?php

// absolute filesystem path to the web root
define('WWW_DIR', __DIR__);

// absolute filesystem path to the application root
define('APP_DIR', WWW_DIR . '/../app');

define('MIXED_DIR', APP_DIR . '/mixed');

// absolute filesystem path to the libraries
define('LIBS_DIR', WWW_DIR . '/../libs');

define('COMPONENTS_DIR', APP_DIR . '/components');


// load bootstrap file
require APP_DIR . '/bootstrap.php';
