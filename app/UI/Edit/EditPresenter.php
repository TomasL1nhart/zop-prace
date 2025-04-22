<?php

namespace App\UI\Edit;

use App\Model\PostFacade;
use Nette;
use Nette\Application\UI\Form;

final class EditPresenter extends Nette\Application\UI\Presenter
{
    public function __construct(private PostFacade $facade) {}

    protected function createComponentPostForm(): Form
    {
        $form = new Form;

        $form->addText('title', 'Titulek:')
            ->setRequired();

        $form->addTextArea('content', 'Obsah:')
            ->setRequired();

            $form->addSelect('category_id', 'Kategorie:', $this->facade->getCategories()->fetchPairs('id', 'name'))
            ->setPrompt('Vyberte kategorii')
            ->setRequired();

        $form->addSelect('status', 'Stav:', [
            'OPENED' => 'Otevřený',
            'CLOSED' => 'Uzavřený',
            'ARCHIVED' => 'Archivovaný',
        ]);

        $form->addUpload('image', 'Obrázek')
            ->addRule(Form::IMAGE, 'Obrázek musí být JPEG, PNG nebo GIF');

        $form->addSubmit('send', 'Uložit');

        $form->onSuccess[] = $this->postFormSucceeded(...);

        return $form;
    }

    public function renderEdit(?int $id = null): void
    {
        if ($id) {
            $post = $this->facade->getPostById($id, $this->getUser());
            if (!$post) {
                $this->error('Příspěvek nebyl nalezen.');
            }
            $this['postForm']->setDefaults($post->toArray());
            $this->template->post = $post;
        }
    }

    private function postFormSucceeded(Form $form, \stdClass $values): void
    {
        $id = $this->getParameter('id');
        $data = (array) $values;

        if ($values->image->isOk()) {
            $filename = 'www/uploads/' . $values->image->getSanitizedName();
            $values->image->move($filename);
            $data['image'] = str_replace('www/', '', $filename); // ukládáme relativní cestu
        } else {
            unset($data['image']);
        }

        if ($id) {
            $this->facade->updatePost($id, $data);
            $this->flashMessage('Příspěvek byl aktualizován.', 'success');
        } else {
            $post = $this->facade->createPost($data);
            $id = $post->id;
            $this->flashMessage('Příspěvek byl vytvořen.', 'success');
        }

        $this->redirect('Post:show', $id);
    }

    public function startup(): void
    {
        parent::startup();
        if (!$this->getUser()->isLoggedIn()) {
            $this->redirect('Sign:in');
        }
    }
}
