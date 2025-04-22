<?php

namespace App\UI\Home;

use App\Model\PostFacade;
use Nette;
use Nette\Application\UI\Presenter;

final class HomePresenter extends Presenter
{
    private PostFacade $postFacade;

    public function __construct(PostFacade $postFacade)
    {
        parent::__construct();
        $this->postFacade = $postFacade;
    }

    public function renderDefault(): void
    {
        $this->template->posts = $this->postFacade->getPublicArticles($this->getUser());
    }
}
