<?php
/**
 * This file is part of the Nella.
 *
 * Copyright (c) 2006, 2010 Patrik Votoček (http://patrik.votocek.cz)
 *
 * This source file is subject to the "New BSD license", and/or GPL license.
 */
 
namespace Nella;

use Nette\Environment;

/**
 * Doctrine cache use Nette\Cache
 *
 * @author     Patrik Votoček
 */
class Doctrine
{
	/**
	 * @return Doctrine\ORM\EntityManager
	 */
	public static function createEntityFactory()
	{
		$config = new \Doctrine\ORM\Configuration;
		$cache = new DoctrineCache(Environment::getCache('Doctrine'));
		
		$config->setMetadataCacheImpl($cache);
		$config->setMetadataDriverImpl($config->newDefaultAnnotationDriver(array(
			Environment::getVariable('modelDir', Environment::getVariable("modelDir"))
		)));
		$config->setQueryCacheImpl($cache);
		$config->setProxyDir(Environment::getVariable('proxyDir', Environment::getVariable("proxyDir")));
		$config->setProxyNamespace('Models\Proxies');
		$config->setAutoGenerateProxyClasses(TRUE);
		
		$database = (array) Environment::getConfig('database');
		
		if (isset($database['profiler']) && $database['profiler']) {
			$config->setSQLLogger(Panels\Doctrine2Panel::getAndRegister());
			unset($database['profiler']);
		}

		$eventManager = new \Doctrine\Common\EventManager;
		if ($database['driver'] == "pdo_mysql" && isset($database['charset'])) {
			$eventManager->addEventSubscriber(new \Doctrine\DBAL\Event\Listeners\MysqlSessionInit(
					$database['charset'],
					isset($database['collation']) ? $database['collation'] : FALSE
			));
		}
		
		return \Doctrine\ORM\EntityManager::create($database, $config, $eventManager);
	}
}