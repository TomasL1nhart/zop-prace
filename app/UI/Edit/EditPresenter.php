<?php

namespace App\UI\Edit;

use App\Model\PostFacade;
use Nette;
use Nette\Application\UI\Form;

final class EditPresenter extends Nette\Application\UI\Presenter
{
    private PostFacade $facade;

    public function __construct(PostFacade $facade)
    {
        $this->facade = $facade;
    }

    protected function createComponentPostForm(): Form
    {
        $form = new Form;
        $form->addText('title', 'Titulek:')
            ->setRequired();
        $form->addTextArea('content', 'Obsah:')
            ->setRequired();
    
        $categories = $this->facade->getCategories();
        $categoryOptions = [];
        foreach ($categories as $category) {
            $categoryOptions[$category->id] = $category->name;
        }
        
        $form->addSelect('category_id', 'Kategorie:', $categoryOptions)
            ->setPrompt('Vyberte kategorii')
            ->setRequired();
    
        $statuses = [
            'OPEN' => 'OTEVŘENÝ',
            'CLOSED' => 'UZAVŘENÝ',
            'ARCHIVED' => 'ARCHIVOVANÝ'
        ];
        $form->addSelect('status', 'Stav:', $statuses)
            ->setDefaultValue('OPEN');
    
        $form->addUpload('image', 'Soubor')
            ->addRule(Form::IMAGE, 'Thumbnail must be JPEG, PNG or GIF');
    
        $form->addSubmit('send', 'Uložit a publikovat');
        $form->onSuccess[] = $this->postFormSucceeded(...);
    
        return $form;
    }    

    private function postFormSucceeded(array $data): void
    {
        $id = $this->getParameter('id');
    
        if (!empty($data['image']) && $data['image']->isOk()) {
            $image = $data['image'];
            $imagePath = 'upload/' . $image->getSanitizedName();
            $image->move($imagePath);
            $data['image'] = $imagePath;
        } else {
            unset($data['image']);
        }
    
        if ($id) {
            $this->facade->updatePost($id, $data);
            $this->flashMessage('Příspěvek byl úspěšně aktualizován.', 'success');
        } else {
            $post = $this->facade->createPost($data);
            $id = $post->id;
            $this->flashMessage('Příspěvek byl úspěšně publikován.', 'success');
        }
    
        $this->redirect('Post:show', $id);
    }    

    public function renderEdit(int $id): void
    {
        $post = $this->facade->getPostById($id, $this->getUser());
    
        if (!$post) {
            $this->error('Post not found');
        }
    
        if ($post->status === 'ARCHIVED' && !$this->getUser()->isLoggedIn()) {
            $this->template->archived = true;
            return;
        }
    
        $this->getComponent('postForm')->setDefaults($post->toArray());
        $this->template->post = $post;
    }
    

    public function startup(): void
    {
        parent::startup();

        if (!$this->getUser()->isLoggedIn()) {
            $this->redirect('Sign:in');
        }
    }
}
