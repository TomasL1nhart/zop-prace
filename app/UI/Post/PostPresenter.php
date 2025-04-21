<?php
namespace App\UI\Post;

use App\Model\PostFacade;
use Nette\Application\UI\Presenter;
use Nette\Application\UI\Form;
use Nette\Security\AuthenticationException;
use Nette\Utils\Strings;
use Nette\Utils\Validators;
use Nette\Http\FileUpload;

final class PostPresenter extends Presenter
{
    public function __construct(
        private PostFacade $postFacade,
        private CommentFacade $commentFacade,
        private ImageFacade $imageFacade,
        private CategoryFacade $categoryFacade
    ) {}

    public function renderDefault(): void
    {
        $this->template->posts = $this->postFacade->getVisiblePosts($this->getUser());
    }

    public function renderShow(int $id): void
    {
        $post = $this->postFacade->getById($id);
        if (!$post) {
            $this->error('Příspěvek nebyl nalezen.');
        }

        $user = $this->getUser();
        $isOwner = $user->isLoggedIn() && $user->getId() === $post->author_id;
        $isAdmin = $user->isInRole('admin');

        if ($post->status === 'ARCHIVED' && !$isOwner && !$isAdmin) {
            $this->error('Tento příspěvek je archivovaný.');
        }

        if ($post->status === 'CLOSED' && !$isOwner && !$isAdmin) {
            $this->template->commentsAllowed = false;
        } elseif ($post->status === 'OPENED') {
            $this->template->commentsAllowed = $user->isLoggedIn() && $user->isInRole('user') || $user->isInRole('producer') || $isAdmin;
        } else {
            $this->template->commentsAllowed = $isOwner || $isAdmin;
        }

        $this->template->canEdit = $isAdmin || ($user->isInRole('producer') && $isOwner);
        $this->template->post = $post;
        $this->template->comments = $this->commentFacade->getByPost($id);
        $this->template->images = $this->imageFacade->getByPost($id);
    }

    public function renderEdit(int $id): void
    {
        $post = $this->postFacade->getById($id);
        if (!$post) {
            $this->error('Příspěvek nebyl nalezen.');
        }

        $user = $this->getUser();
        $isAdmin = $user->isInRole('admin');
        $isOwner = $user->isLoggedIn() && $user->getId() === $post->author_id;

        if (!$isAdmin && !($user->isInRole('producer') && $isOwner)) {
            $this->error('Nemáte oprávnění upravit tento příspěvek.');
        }

        $this['postForm']->setDefaults($post);
        $this->template->images = $this->imageFacade->getByPost($id);
        $this->template->postId = $id;
    }

    protected function createComponentPostForm(): Form
    {
        $form = new Form;
        $form->addText('title', 'Nadpis')->setRequired();
        $form->addTextArea('content', 'Obsah')->setRequired();
        $form->addSelect('category_id', 'Kategorie', $this->categoryFacade->getPairs())->setPrompt('Vyberte kategorii');
        $form->addSelect('status', 'Stav', [
            'OPENED' => 'Otevřený',
            'CLOSED' => 'Uzavřený',
            'ARCHIVED' => 'Archivovaný'
        ])->setRequired();
        $form->addUpload('images[]', 'Obrázky')->setHtmlAttribute('multiple', true);
        $form->addSubmit('save', 'Uložit');

        $form->onSuccess[] = function (Form $form, array $values): void {
            $user = $this->getUser();
            $id = $this->getParameter('id');

            if ($id) {
                $this->postFacade->update($id, $values);
                $this->imageFacade->handleUpload($id, $form['images']->getValue());
                $this->flashMessage('Příspěvek upraven.', 'success');
            } else {
                if (!$user->isLoggedIn() || !$user->isInRole('producer') && !$user->isInRole('admin')) {
                    $this->error('Nemáte oprávnění vytvořit příspěvek.');
                }
                $postId = $this->postFacade->create($values, $user->getId());
                $this->imageFacade->handleUpload($postId, $form['images']->getValue());
                $this->flashMessage('Příspěvek vytvořen.', 'success');
            }
            $this->redirect('default');
        };

        return $form;
    }

    public function handleDeleteImage(int $id): void
    {
        $this->imageFacade->delete($id);
        $this->flashMessage('Obrázek byl smazán.');
        $this->redirect('this');
    }
}
