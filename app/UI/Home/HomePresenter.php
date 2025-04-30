<?php

namespace App\UI\Home;

use App\Model\PostFacade;
use Nette;
use Nette\Application\UI\Presenter;
use Nette\Utils\Paginator;

final class HomePresenter extends Presenter
{
    private PostFacade $postFacade;

    public function __construct(PostFacade $postFacade)
    {
        parent::__construct();
        $this->postFacade = $postFacade;
    }

    public function renderDefault(int $page = 1, $category = null): void
    {
        $categoryId = is_numeric($category) ? (int) $category : null;
    
        $result = $this->postFacade->getPaginatedPublicArticles(
            $this->getUser(),
            $page,
            4,
            $categoryId
        );
    
        /** @var Paginator $paginator */
        $paginator = $result['paginator'];
    
        if ($paginator->getPage() > $paginator->getLastPage()) {
            $this->redirect('this', ['page' => $paginator->getLastPage(), 'category' => $category]);
        }
    
        $this->template->posts = $result['posts'];
        $this->template->paginator = $paginator;
        $this->template->categories = $this->postFacade->getCategories();
        $this->template->selectedCategory = $categoryId;
    }
     
}
