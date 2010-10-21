<?php
/**
 * Demo - knihovní systém
 */

use Nette\Application\AppForm,
    Nette\Forms\Form,
    Nette\Security\AuthenticationException;
use Models\User;


/**
 * Sign in/out presenters.
 *
 * @author     Tomáš Penc
 */
class SignPresenter extends BasePresenter
{
    /**
     * Sign in form component factory.
     * @return Nette\Application\AppForm
     */
    protected function createComponentSignInForm()
    {
        $form = new AppForm;
        $form->addText('username', 'Uživatel:')
                ->addRule(AppForm::FILLED, 'Prosím zadejte uživatelské jméno.');

        $form->addPassword('password', 'Heslo:')
                ->addRule(AppForm::FILLED, 'Prosím zadejte heslo.');

        $form->addCheckbox('remember', 'pamatovat si přihlášení');

        $form->addSubmit('send', 'Přihlásit');

        $presenter = $this;
        $form->onSubmit[] = function (AppForm $form) use($presenter) {
            try {
                $values = $form->getValues();
                if ($values['remember']) {
                        $presenter->getUser()->setExpiration('+ 14 days', FALSE);
                } else {
                        $presenter->getUser()->setExpiration('+ 20 minutes', TRUE);
                }
                $presenter->getUser()->login($values['username'], $values['password']);
                $presenter->redirect('Book:');

            } catch (AuthenticationException $e) {
                $form->addError($e->getMessage());
            }
        };
        
        return $form;
    }

    public function actionOut()
    {
        $this->getUser()->logout();
        $this->flashMessage('Byl jste odhlášen.');
        $this->redirect('in');
    }

    /**
     * Register form component factory.
     * @return Nette\Application\AppForm
     */
    protected function createComponentRegisterForm($name)
    {
        $form = new AppForm($this, $name);

        $form->addText("username", "Uživatelské jméno:")
                ->setRequired("Vyplňte uživatelské jméno.");

        $form->addText("email", "Email:")
                ->setRequired("Vyplňte email.")
                ->addRule(Form::EMAIL,"Nesprávný formát emailu.");

        $form->addPassword("password","Heslo:")
                ->setRequired("Vyplňte heslo")
                ->addRule(Form::MIN_LENGTH, "Minimální délka hesla je %d znaků.", 6);

        $form->addPassword("password2","Heslo znovu:")
                ->setRequired("Vyplňte heslo pro kontrolu.")
                ->addConditionOn($form["password"], Form::VALID)
                    ->addRule(Form::EQUAL, "Hesla se neshodují.", $form["password"]);

        $form->addSubmit("save","Uložit");

        $presenter = $this;
        $form->onSubmit[] = function(AppForm $form) use ($presenter) {
            try {
                $values = $form->getValues();
                unset($values["password2"]);
                $presenter->em->persist(new User($values));
                $presenter->em->flush();

                $presenter->flashMessage("Uživatel byl vytvořen.");
                $presenter->redirect("Book:default");
            } catch(PDOException $ex) {
                if (strpos($ex->getMessage(), "user_username_uniq") !== false) {
                    $form->addError("Zadané uživatelské jméno již existuje.");
                } elseif (strpos($ex->getMessage(), "user_email_uniq") !== false) {
                    $form->addError("Zadaný email již existuje.");
                } else {
                    $form->addError($ex->getMessage());
                }
            }
        };

        return $form;
    }
}
