<?php

namespace App\UI\Post;

use App\Model\PostFacade;
use Nette;
use Nette\Application\UI\Form;

final class PostPresenter extends Nette\Application\UI\Presenter
{
	private PostFacade $facade;

	public function __construct(PostFacade $facade)
	{
		$this->facade = $facade;
	}

	public function renderShow(int $id): void
	{
		$post = $this->facade->getPostById($id, $this->getUser());
	
		if (!$post) {
			$this->template->notFound = true;
			return;
		}
	
		if ($post->status === 'ARCHIVED' && !$this->getUser()->isLoggedIn()) {
			$this->template->archived = true;
			return;
		}
		
		$this->facade->addView($id);
	
		$this->template->post = $post;
		$this->template->comments = $this->facade->getComments($id);
	}
	
	
	protected function createComponentCommentForm(): Form
	{
		$post = $this->facade->getPostById($this->getParameter('id'), $this->getUser());
	
		if (!$post || $post->status === 'ARCHIVED') {
			return new Form(); 
		}
	
		if ($post->status === 'CLOSED' && !$this->getUser()->isLoggedIn()) {
			return new Form();
		}
	
		$form = new Form;
		$form->addText('name', 'Jméno:')
			->setRequired();
	
		$form->addEmail('email', 'E-mail:');
	
		$form->addTextArea('content', 'Komentář:')
			->setRequired();
	
		$form->addSubmit('send', 'Publikovat komentář');
		$form->onSuccess[] = $this->commentFormSucceeded(...);
	
		return $form;
	}	

	private function commentFormSucceeded(\stdClass $data): void
	{
		$post_id = $this->getParameter('id');

		$this->facade->addComment( // Používáme metodu z facade
			$post_id,
			$data->name,
			$data->email,
			$data->content
		);

		$this->flashMessage('Děkuji za komentář', 'success');
		$this->redirect('this');
	}
	
	public function handleDeleteImage(int $postId): void
	{
		$post = $this->facade->getPostById($postId);
	
		if ($post && $post->image) {
			unlink($post->image);
	
			$this->facade->updatePost($postId, ['image' => null]);
	
			$this->flashMessage('Obrázek byl úspěšně smazán.', 'success');
		} else {
			$this->flashMessage('Žádný obrázek ke smazání.', 'error');
		}
	
		if($this->isAjax()) {
			$this->redrawControl('image');
			return;
		  
		  }
		  $this->redirect('this');
	}
	

	public function handleDeleteComment(int $commentId): void
{
	$comment = $this->facade->getCommentById($commentId);

	if (!$comment) {
		$this->flashMessage('Komentář nebyl nalezen.', 'error');
		$this->redirect('this');
	}

	$this->facade->deleteComment($commentId);

	$this->flashMessage('Komentář byl úspěšně smazán.', 'success');
	$this->redirect('this');
}
}