<?php
/**
 * Demo - knihovní systém
 *
 * @package    Presenters
 */

use Nette\Environment;
use Doctrine\ORM\EntityManager;
use Nette\Application\Presenter;

/**
 * Base presenter.
 *
 * @author     Tomáš Penc
 * @package    Presenters
 * 
 * @property-read EntityManager $em
 */
abstract class BasePresenter extends Presenter
{
    /** @return EntityManager */
    public function getEm()
    {
        return Environment::getService('Doctrine\ORM\EntityManager');
    }
}
