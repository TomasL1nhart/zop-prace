<?php

namespace App\UI\Home;

use App\Model\PostFacade;
use Nette\Application\UI\Presenter;

class HomePresenter extends Presenter
{
    public function __construct(
        private PostFacade $posts
    ) {}

    public function renderDefault(): void
    {
        $this->template->posts = $this->posts->getAll();
    }
}