<?php
/**
 * Demo - knihovní systém
 */

use Nette\Application\BadRequestException,
    Nette\Application\AppForm,
    Nette\Forms\Form;
use Models\Book;

/**
 * Book presenter.
 *
 * @author     Tomáš Penc
 */
class BookPresenter extends BasePresenter
{
    public function actionAdd()
    {
        if(!$this->getUser()->isLoggedIn())
            throw new BadRequestException();
    }

    /** @param int $id */
    public function renderDetail($id)
    {
        $this->template->book = $this->em->find("Models\Book",$id);
        if(!$this->template->book) {
            throw new BadRequestException("Book ID $id not found.");
        }
    }

    public function renderDefault()
    {
        $this->template->books = $this->em->getRepository("Models\Book")->findAll();
    }

    /** @param int $id */
    public function handleChangeState($id)
    {
        $book = $this->em->find("Models\Book",$id);
        if(!$book) throw new BadRequestException("Book ID $id not found.");
        $book->state = $book->state == Book::STATE_FREE ?
                            Book::STATE_LENT : Book::STATE_FREE;
        $this->em->flush();
        

        if($this->isAjax()) {
            $this->invalidateControl("table");
        } else {
            $this->flashMessage("Stav změněn.");
            $this->redirect("this");
        }
    }

    protected function createComponentBookForm($name)
    {
        $form = new AppForm($this, $name);

        $form->addProtection();

        $form->addText("name","Název:")
                ->setRequired("Vyplňte název knihy.");

        $form->addText("author","Autor:")
                ->setRequired("Vyplňte autora");

        $items = array(
            0 => "-> vyberte",
            Book::STATE_FREE => "volná",
            Book::STATE_LENT => "půjčená",
        );
        $form->addSelect("state", "Stav:", $items)
                ->skipFirst()
                ->setRequired("Vyberte stav knihy.");

        $form->addTextArea("stateDescription","Popis stavu:");

        $form->addSubmit("save", "Uložit");

        $presenter = $this;

        $form->addSubmit("storno", "Storno")
                ->setValidationScope(false)
                ->onClick[] = function() use ($presenter) {
                    $presenter->redirect("Book:");
                };

        // edit
        if(!is_null($this->getParam("id"))) {
            $book = $this->em->find("Models\Book",$this->getParam("id"));
            $form->addHidden("id",$book->id);
            $form->setDefaults($book);
        }
        
        $form->onSubmit[] = function(AppForm $form) use($presenter) {
            $values = $form->getValues();
            if(isset($values["id"])) {
                $book = $presenter->em->find("Models\Book",$values["id"]);
                unset($values["id"]);
            } else {
                $book = new Book();
                $presenter->em->persist($book);
            }
            $book->loadValues($values);

            $presenter->em->flush();
            $presenter->flashMessage("Uloženo.");
            $presenter->redirect("Book:");
        };
    }

    protected function createComponentRemoveBook($name)
    {
        $form = new AppForm($this, $name);
        $form->addProtection();
        $form->addHidden("id");
        $form->addSubmit("send","Smazat")
                ->getControlPrototype()
                    ->onclick("return confirm('Opravdu smazat?');");
        $presenter = $this;
        $form->onSubmit[] = function (AppForm $form) use($presenter) {
            $book = $presenter->em->find("Models\Book",$form["id"]->getValue());
            $presenter->em->remove($book);
            $presenter->em->flush();

            $presenter->flashMessage("Kniha byla odstraněna.");
            $presenter->redirect("this");
        };
    }
}