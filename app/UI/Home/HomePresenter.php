<?php

namespace App\UI\Home;

use App\Model\PostFacade;
use Nette;
use Nette\Application\UI\Presenter;
use Nette\Utils\Paginator;

final class HomePresenter extends Presenter
{
    private PostFacade $facade;

    public function __construct(PostFacade $facade)
    {
        parent::__construct();
        $this->facade = $facade;
    }

    public function renderDefault(int $page = 1, $category = null): void
    {
        $categoryId = is_numeric($category) ? (int) $category : null;
    
        $result = $this->facade->getPaginatedPublicArticles(
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
        $this->template->categories = $this->facade->getCategories();
        $this->template->selectedCategory = $categoryId;
    }
     
}
