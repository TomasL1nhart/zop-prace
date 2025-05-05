<?php

namespace App\UI\Register;

use App\Model\UserFacade;
use Nette\Application\UI\Form;
use Nette\Application\UI\Presenter;
use Nette\Utils\Validators;

final class RegisterPresenter extends Presenter
{
    public function __construct(
        private UserFacade $users,
    ) {}

    public function renderDefault(): void {}

    protected function createComponentRegisterForm(): Form
    {
        $form = new Form;
        $form->addText('username', 'Uživatelské jméno:')->setRequired('Zadejte prosím své uživatelské jméno.');
        $form->addText('email', 'Email:')->setRequired()->addRule($form::EMAIL);
        $form->addPassword('password', 'Heslo:')->setRequired()->addRule($form::MIN_LENGTH, 'Heslo musí mít alespoň 6 znaků', 6);
        $form->addSubmit('send', 'Registrovat');
        $form->onSuccess[] = [$this, 'registerFormSucceeded'];
        return $form;
    }

    public function registerFormSucceeded(Form $form, array $values): void
    {
        try {
            $this->users->register($values['username'], $values['email'], $values['password']);
            $this->flashMessage('Registrace byla úspěšná. Můžete se nyní přihlásit.', 'success');
        } catch (\Exception $e) {
            $form->addError($e->getMessage());
        }
    }
}
