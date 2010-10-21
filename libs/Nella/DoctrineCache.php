<?php
/**
 * This file is part of the Nella.
 *
 * Copyright (c) 2006, 2010 Patrik Votoček (http://patrik.votocek.cz)
 *
 * This source file is subject to the "New BSD license", and/or GPL license.
 */
 
namespace Nella;

/**
 * Doctrine cache use Nette\Cache
 *
 * @author     Patrik Votoček
 */
class DoctrineCache extends \Doctrine\Common\Cache\AbstractCache
{
	/** @var Nette\Caching\Cache */
    private $storage = array();

    /**
     * @param Nette\Caching\Cache $storage
     */
	public function  __construct($storage)
	{
		$this->storage = $storage;
	}

    /**
     * {@inheritdoc}
     */
    public function getIds()
    {
        return array_keys($this->storage);
    }

    /**
     * {@inheritdoc}
     */
    protected function _doFetch($id)
    {
        if (isset($this->storage[$id])) {
            return $this->storage[$id];
        }
        return FALSE;
    }

    /**
     * {@inheritdoc}
     */
    protected function _doContains($id)
    {
        return isset($this->storage[$id]);
    }

    /**
     * {@inheritdoc}
     */
    protected function _doSave($id, $data, $lifeTime = 0)
    {
		if ($lifeTime != 0)
			$this->storage->save($id, $data, array('expire' => time() + $lifeTime, 'tags' => array("doctrine")));
		else
			$this->storage->save($id, $data, array('tags' => array("doctrine")));
        return TRUE;
    }

    /**
     * {@inheritdoc}
     */
    protected function _doDelete($id)
    {
        unset($this->storage[$id]);
        return TRUE;
    }
}