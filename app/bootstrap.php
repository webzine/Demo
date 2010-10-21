<?php
/**
 * Demo - knihovní systém
 *
 * @author  Tomáš Penc
 * @package Demo
 */

use Nette\Debug;
use Nette\Environment;
use Nette\Application\Route;
use Nette\Application\CliRouter;

require LIBS_DIR . '/Nette/Nette/loader.php';

Debug::$strictMode = TRUE;
Debug::enable();

Environment::loadConfig();

$application = Environment::getApplication();
$application->errorPresenter = 'Error';
//$application->catchExceptions = TRUE;

if (Environment::isConsole()) {
	$application->allowedMethods = FALSE;
}

$router = $application->getRouter();

$router[] = new CliRouter(array(
	'presenter' => 'Cli',
	'action' => 'createSchema'
));

$router[] = new Route('index.php', array(
	'presenter' => 'Book',
	'action' => 'default',
), Route::ONE_WAY);

$router[] = new Route('<presenter>/<action>/<id>', array(
	'presenter' => 'Book',
	'action' => 'default',
	'id' => NULL,
));

$application->run();
