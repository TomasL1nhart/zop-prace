<?php

namespace App\UI\Register;

use App\Model\UserFacade;
use Nette;
use Nette\Application\UI\Form;
use Nette\Security\Passwords;

final class RegisterPresenter extends Nette\Application\UI\Presenter
{
    private UserFacade $userFacade;

    public function __construct(UserFacade $userFacade)
    {
        $this->userFacade = $userFacade;
    }

	protected function createComponentRegisterForm(): Form
    {
        $form = new Form;

        $form->addText('username', 'Uživatelské jméno:')
            ->setRequired('Zadejte uživatelské jméno.');

        $form->addEmail('email', 'E-mail:')
            ->setRequired('Zadejte platný e-mail.');

        $form->addPassword('password', 'Heslo:')
            ->setRequired('Zadejte heslo.');

        $form->addPassword('passwordVerify', 'Heslo znovu:')
            ->setRequired('Zadejte heslo znovu.')
            ->addRule($form::EQUAL, 'Hesla se neshodují.', $form['password']);

        $form->addSubmit('send', 'Registrovat');

        $form->onSuccess[] = [$this, 'RegisterFormSucceeded'];

        return $form;
    }

    public function RegisterFormSucceeded(Form $form, \stdClass $data): void
    {
        try {
            $this->userFacade->addUser(
                $data->username,
                $data->email,
                $data->password // hashování se děje v UserFacade
            );

            $this->flashMessage('Registrace byla úspěšná. Můžete se přihlásit.');
            $this->redirect('Sign:in'); // přesměrování na přihlašovací stránku

        } catch (\RuntimeException $e) {
            $form->addError($e->getMessage());
        } catch (\Exception $e) {
            $form->addError('Došlo k chybě při registraci.');
        }
    }
}