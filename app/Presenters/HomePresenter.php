<?php

declare(strict_types=1);

namespace App\Presenters;

use Nette;
use App\Model\UserFacade;
use Nette\Application\UI\Form;
use Nette\Schema\Expect;

final class HomePresenter extends Nette\Application\UI\Presenter
{
    private UserFacade $facade;
    private int $user_id;
    public function __construct(UserFacade $facade)
	{
		$this->facade = $facade;
        $user_id = 0;
    }

    public function beforeRender()
    {
        parent::beforeRender();
        if (!$this->getUser()->isLoggedIn()) {
            $this->redirect('Sign:default');
        }
    }

    /*public function renderDeafault() : void {
        $this->template->posts = $this->facade
			->getEvents($this->getUser()->isLoggedIn());
    }*/

    public function renderDefault(): void
    {
        $this->template->posts = $this->facade->getEvents($this->getUser()->isLoggedIn());
        $this->user_id = $this->getUser()->getId();
    }
    
    


}
