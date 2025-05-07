<?php

namespace App\UI\Admin;

use Nette;
use App\Model\PostFacade;
use App\Model\UserFacade;

final class DashboardPresenter extends Nette\Application\UI\Presenter
{
    private PostFacade $postFacade;
    private UserFacade $userFacade;

    public function __construct(
        PostFacade $postFacade,
        UserFacade $userFacade,
    ) {
        parent::__construct();
        $this->postFacade = $postFacade;
        $this->userFacade = $userFacade;
    }
}