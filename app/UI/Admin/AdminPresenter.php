<?php

namespace App\UI\Admin;

use App\Model\PostFacade;
use App\Model\UserFacade;
use Nette\Application\UI\Presenter;
use Nette\Security\User;
use Nette\Application\UI\Form;
use Nette\Application\UI\Multiplier;

final class AdminPresenter extends Presenter
{
    public function __construct(
        private PostFacade $facade,
        private UserFacade $userFacade,
        private User $user
    ) {}

    public function startup(): void
    {
        parent::startup();
        $identity = $this->user->getIdentity();

        if (!$this->user->isLoggedIn() || $identity->username !== 'admin' || $identity->email !== 'admin@admin.admin') {
            $this->flashMessage('Přístup zamítnut!', 'error');
            $this->redirect('Home:default');
        }
    }

    public function renderDefault(): void
    {
        $this->template->posts = $this->facade->findAll();
        $this->template->users = $this->userFacade->getAll();
        $this->template->categories = $this->facade->findAllCategories();
    }
    
    public function handleDeletePost(int $postId): void
    {
    $post = $this->facade->getPostById($postId);
    
    $imagePath = __DIR__ . '/../../../www/uploads/' . $post->image;
    if ($post->image && file_exists($imagePath)) {
        unlink($imagePath);
    }

    $this->facade->deletePost($postId);
    $this->flashMessage('Příspěvek byl smazán.');
    $this->redirect('this');
    }

    public function handleDeleteUser(int $userId): void
    {
        $this->userFacade->deleteUser($userId); 
        $this->flashMessage('Uživatel byl smazán.');
        $this->redirect('this');
    }

    protected function createComponentChangePasswordForms(): Multiplier
    {
    return new Multiplier(function (string $userId): Form {
        $form = new Form;
        $form->addPassword('newPassword', 'Nové heslo:')
            ->setRequired()
            ->addRule(Form::MIN_LENGTH, 'Heslo musí mít alespoň %d znaků.', 6);
        $form->addSubmit('change', 'Změnit heslo');

        $form->onSuccess[] = function (Form $form, array $values) use ($userId): void {
            $this->userFacade->changePassword((int) $userId, $values['newPassword']);
            $this->flashMessage('Heslo změněno.');
            $this->redirect('this');
        };
        return $form;
    });
    }

    protected function createComponentAddCategoryForm(): Form
    {
        $form = new Form;
        $form->addText('name', 'Category name:')
            ->setRequired();
        $form->addSubmit('add', 'Add');
        $form->onSuccess[] = function (Form $form, array $values): void {
            $this->facade->addCategory($values['name']);
            $this->flashMessage('Category added.');
            $this->redirect('this');
        };
        return $form;
    }

    public function handleDeleteCategory(int $categoryId): void
    {
    $this->facade->deleteCategory($categoryId);
    $this->flashMessage('Kategorie byla smazána.');
    $this->redirect('this');
    }

}
