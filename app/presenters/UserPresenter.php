<?php
/**
 * Demo - knihovní systém
 */

use Nette\Application\AppForm,
    Nette\Application\BadRequestException;

/**
 * User presenter.
 *
 * @author     Tomáš Penc
 */
class UserPresenter extends BasePresenter
{
    protected function startup()
    {
        parent::startup();
        if(!$this->getUser()->isLoggedIn())
            throw new BadRequestException();
    }
    
    public function renderDefault()
    {
        $this->template->users = $this->em->getRepository("Models\User")->findAll();
    }

    protected function createComponentRemoveUser($name)
    {
        $form = new AppForm($this, $name);
        $form->addProtection();
        $form->addHidden("id");
        $form->addSubmit("send","Smazat")
                ->getControlPrototype()
                    ->onclick("return confirm('Opravdu smazat?');");
        $presenter = $this;
        $user = $this->getUser();
        $form->onSubmit[] = function (AppForm $form) use($presenter, $user) {
            $userId = $form["id"]->getValue();
            if($userId == $user->getId()) {
                $presenter->flashMessage("Nemůžete smazat uživatele, pod kterým jste přihlášený.");
                $presenter->redirect("this");
            }
            $user = $presenter->em->find("Models\User",$userId);
            $presenter->em->remove($user);
            $presenter->em->flush();
            $presenter->flashMessage("Uživatel byl odstraněn.");
            $presenter->redirect("this");
        };
    }
}