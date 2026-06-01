<?php

declare(strict_types=1);

define('ROOT_DIR', dirname(__DIR__));
define('APP_DIR', ROOT_DIR . '/app');
define('CONTENT_DIR', ROOT_DIR . '/content');
define('TEMPLATES_DIR', APP_DIR . '/templates');

require APP_DIR . '/helpers.php';
require APP_DIR . '/content.php';
require APP_DIR . '/router.php';
