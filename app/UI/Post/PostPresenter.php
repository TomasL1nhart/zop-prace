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
    
        // Pokud je archivovaný a uživatel není přihlášený
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
                'ARCHIVED' => 'Archivovaný',
            ])->setRequired();
            

        $form->addSelect('category_id', 'Kategorie:', $this->facade->getCategories()->fetchPairs('id', 'name'))
            ->setPrompt('Vyberte kategorii')
            ->setRequired();

        $form->addUpload('image', 'Obrázek:')
            ->setRequired(false)
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
    
        $this->facade->addComment($postId, $userId, $values->comment);
    
        $this->flashMessage('Komentář byl přidán.', 'success');
        $this->redirect('this');
    }
    

    public function handleDeleteImage(int $postId): void
    {
        $post = $this->facade->getPostById($postId);
        if ($post && $post->image && file_exists('www/' . $post->image)) {
            unlink('www/' . $post->image);
            $this->facade->updatePost($postId, ['image' => null]);
            $this->flashMessage('Obrázek byl smazán.', 'success');
        }

        $this->redirect('this');
    }

    public function handleDeleteComment(int $commentId): void
    {
        $this->facade->deleteComment($commentId);
        $this->flashMessage('Komentář smazán.', 'success');
        $this->redirect('this');
    }

    public function handleDelete(int $id): void
    {
    $post = $this->facade->getPostById($id);
    if (!$post) {
        $this->error('Příspěvek nebyl nalezen.');
    }

    // Kontrola oprávnění – např. vlastník nebo admin
    if ($post->user_id !== $this->getUser()->getId()) {
        $this->error('Nemáte oprávnění mazat tento příspěvek.');
    }

    // Smazání příspěvku i případného obrázku
    if ($post->image && file_exists("www/uploads/{$post->image}")) {
        unlink("www/uploads/{$post->image}");
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


}
