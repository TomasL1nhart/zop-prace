<?php

namespace App\UI\Sign;

use Nette;
use Nette\Application\UI\Form;

final class SignPresenter extends Nette\Application\UI\Presenter
{
	protected function createComponentSignInForm(): Form
	{
		$form = new Form;

		$form->addText('username', 'Uživatelské jméno:')
			->setRequired('Prosím vyplňte své uživatelské jméno.');

		$form->addPassword('password', 'Heslo:')
			->setRequired('Prosím vyplňte své heslo.');

		$form->addSubmit('send', 'Přihlásit');

		// Oprava: správné přiřazení callbacku (bez volání metody)
		$form->onSuccess[] = [$this, 'signInFormSucceeded'];

		return $form;
	}

	public function signInFormSucceeded(Form $form, \stdClass $data): void
	{
		try {
			$this->getUser()->login($data->username, $data->password);
			$this->redirect('Home:'); // nebo kamkoliv chceš po přihlášení

		} catch (Nette\Security\AuthenticationException $e) {
			$form->addError('Nesprávné přihlašovací jméno nebo heslo.');
		}
	}

	public function actionOut(): void
	{
		$this->getUser()->logout();
		$this->flashMessage('Odhlášení bylo úspěšné.');
		$this->redirect('Home:'); // nebo kam chceš po odhlášení
	}
}
