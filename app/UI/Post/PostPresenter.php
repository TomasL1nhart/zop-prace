<?php

namespace App\UI\Post;

use App\Model\PostFacade;
use Nette;
use Nette\Application\UI\Form;

final class PostPresenter extends Nette\Application\UI\Presenter
{
    public function __construct(private PostFacade $facade) {}

    public function renderShow(int $id): void
    {
        $post = $this->facade->getPostById($id, $this->getUser());
    
        if ($post && $post->status === 'ARCHIVED' && !$this->getUser()->isLoggedIn()) {
            $this->template->isArchivedAndRestricted = true;
            return;
        }
    
        if (!$post) {
            $this->template->notFound = true;
            return;
        }
    
        $this->template->post = $post;
        $this->template->comments = $this->facade->getComments($id);
    }
    
    public function renderCreate(): void
    {
        $this->template->categories = $this->facade->getCategories();
    }    

    protected function createComponentPostForm(): Form
    {
        $form = new Form;

        $form->addText('title', 'Nadpis:')
            ->setRequired();

        $form->addTextArea('content', 'Obsah:')
            ->setRequired();

            $form->addSelect('status', 'Stav:', [
                'OPENED' => 'Otevřený',
                'CLOSED' => 'Uzavřený',
            ])->setRequired();
            

        $form->addSelect('category_id', 'Kategorie:', $this->facade->getCategories()->fetchPairs('id', 'name'))
            ->setPrompt('Vyberte kategorii')
            ->setRequired();

        $form->addUpload('image', 'Obrázek:')
            ->setRequired(true)
            ->addRule($form::IMAGE, 'Soubor musí být obrázek.');

        $form->addSubmit('save', 'Vytvořit příspěvek');

        $form->onSuccess[] = $this->postFormSucceeded(...);
        return $form;
    }

    private function postFormSucceeded(Form $form, \stdClass $values): void
    {
        $imageName = null;
    
        if ($values->image && $values->image->isOk() && $values->image->isImage()) {
            $imageName = uniqid() . '_' . $values->image->getSanitizedName();
            $values->image->move(__DIR__ . '/../../../www/uploads/' . $imageName);
        }
    
        $this->facade->createPost([
            'title' => $values->title,
            'content' => $values->content,
            'status' => $values->status,
            'category_id' => $values->category_id,
            'image' => $imageName,
            'user_id' => $this->getUser()->getId(),
        ]);
    
        $this->flashMessage('Příspěvek byl úspěšně vytvořen.', 'success');
        $this->redirect('Home:default');
    }

    protected function createComponentCommentForm(): Form
    {
        if (!$this->getUser()->isLoggedIn()) {
            throw new Nette\Application\ForbiddenRequestException('Musíte být přihlášeni, abyste mohli komentovat.');
        }
    
        $form = new Form;
        $form->addTextArea('comment', 'Komentář:')
            ->setRequired('Zadejte prosím komentář.');
    
        $form->addSubmit('send', 'Odeslat komentář');
    
        $form->onSuccess[] = $this->commentFormSucceeded(...);
        return $form;
    }
    
    private function commentFormSucceeded(Form $form, \stdClass $values): void
    {
    $postId = (int) $this->getParameter('id');
    $userId = $this->getUser()->getId();

    $post = $this->facade->getPostById($postId, $this->getUser());

    if (!$post) {
        $this->flashMessage('Příspěvek nebyl nalezen.', 'error');
        $this->redirect('this');
    }

    if (
        $post->status === 'CLOSED' &&
        $post->user_id !== $userId &&
        !$this->getUser()->isInRole('admin')
    ) {
        $this->flashMessage('Do uzavřeného příspěvku nemůžete přidávat komentáře.', 'error');
        $this->redirect('this');
    }

    $this->facade->addComment($postId, $userId, $values->comment);

    $this->flashMessage('Komentář byl přidán.', 'success');
    $this->redirect('this');
    }

    public function handleDeleteComment(int $commentId): void
    {
        $comment = $this->facade->getCommentById($commentId);
        if (!$comment) {
            $this->flashMessage('Komentář nebyl nalezen.', 'error');
            $this->redirect('this');
        }
    
        if ($comment['user_id'] !== $this->getUser()->getId() && !$this->getUser()->isInRole('admin')) {
            $this->flashMessage('Nemáte oprávnění smazat tento komentář.', 'error');
            $this->redirect('this');
        }
    
        $this->facade->deleteComment($commentId);
        $this->flashMessage('Komentář byl smazán.', 'success');
        $this->redirect('this');
    }
    
    public function handleDelete(int $id): void
    {
    $post = $this->facade->getPostById($id, $this->getUser());
    if (!$post) {
        $this->error('Příspěvek nebyl nalezen.');
    }

    if ($post->user_id !== $this->getUser()->getId() && !$this->getUser()->isInRole('admin')) {
        $this->flashMessage('Nemáte oprávnění smazat tento příspěvek.', 'error');
        $this->redirect('this');
    }
    if ($post->image && file_exists(__DIR__ . '/../../../www/uploads/' . $post->image)) {
    unlink(__DIR__ . '/../../../www/uploads/' . $post->image);
    }


    $this->facade->deletePost($id);
    $this->flashMessage('Příspěvek byl smazán.', 'success');
    $this->redirect('Home:default');
    }   

    protected function createComponentDeleteForm(): \Nette\Application\UI\Form
    {
    $form = new \Nette\Application\UI\Form();
    $form->addSubmit('delete', 'Smazat');
    $form->onSuccess[] = function () {
        $this->handleDelete((int) $this->getParameter('id'));
    };
    return $form;
    }

    public function handleDeleteCategory(int $categoryId): void
    {
    $this->postFacade->deleteCategory($categoryId);
    $this->flashMessage('Kategorie byla smazána.');
    $this->redirect('this');
    }

}
