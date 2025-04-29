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
    
        $paginator = new Paginator;
        $paginator->setItemsPerPage(4);
        $paginator->setPage($page);
    
        $user = $this->getUser();
    
        $paginator->setItemCount($this->postFacade->getPublicArticlesCount($user, $categoryId));
    
        $this->template->posts = $this->postFacade->getPublicArticlesPage(
            $user,
            $paginator->getOffset(),
            $paginator->getLength(),
            $categoryId
        );
    
        $this->template->paginator = $paginator;
        $this->template->categories = $this->postFacade->getCategories();
        $this->template->selectedCategory = $categoryId;
    }    
}
