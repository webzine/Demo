<?php
/**
 * Demo - knihovní systém
 *
 * @package    Presenters
 */

use Nette\Environment,
    Nette\Debug;
use Models\Book;
use Models\User;

/**
 * CLI presenter
 *
 * @author     Patrik Votoček
 */
class CliPresenter extends BasePresenter
{
    protected function startup()
    {
        parent::startup();
        Environment::getCache('Doctrine')->clean(array('tags' => array("doctrine")));
    }

    public function actionCreateSchema($dump = FALSE)
    {
        $sTools = new \Doctrine\ORM\Tools\SchemaTool($this->em);

        echo "Loading metadata..." . PHP_EOL;

        try {
            $metadatas = $this->em->getMetadataFactory()->getAllMetadata();
            echo "DONE" . PHP_EOL;
        } catch (\Exception $e) {
            Debug::log($e);
            echo "Error: {$e->getMessage()}";
        }

        echo "Generating schema..." . PHP_EOL;

        if ($dump === TRUE)
            echo implode(';' . PHP_EOL, $sTools->getCreateSchemaSql($metadatas)) . PHP_EOL;
        elseif ($dump) {
            file_put_contents(((substr($dump, 0, 1) == "/" || substr($dump, 1, 1) == ":") ? "" : (WWW_DIR . "/")) . $dump,
                    implode(';' . PHP_EOL, $sTools->getCreateSchemaSql($metadatas)));
        } else {
            try {
                $sTools->updateSchema($metadatas);
                echo "DONE" . PHP_EOL;
            } catch (\Exception $e) {
                Debug::log($e);
                echo "Error: {$e->getMessage()}";
            }
        }

        echo "Inserting demo data..." . PHP_EOL;

        try {
                $this->em->persist(new Book(array("name"=>"Krakatit","author"=>"Karel Čapek")));
                $this->em->persist(new Book(array("name"=>"Báječná léta pod psa","author"=>"Michal Viewegh")));
                $this->em->persist(new Book(array("name"=>"Okna vesmíru dokořán","author"=>"Jiří Grygar")));

                $this->em->flush();

                echo "DONE" . PHP_EOL;
        } catch (\Exception $e) {
                Debug::log($e);
                echo "Error: {$e->getMessage()}";
        }

        $this->terminate();
    }
}