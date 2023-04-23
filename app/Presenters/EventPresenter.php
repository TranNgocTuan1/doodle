<?php

declare(strict_types=1);

namespace App\Presenters;

use App\Model\UserFacade;
use Nette\Application\UI\Form;
use Nette;
use Nette\Schema\Expect;

final class EventPresenter extends Nette\Application\UI\Presenter
{
	private UserFacade $facade;
    private $id_post = null;

	public function __construct(UserFacade $facade)
	{
		$this->facade = $facade;
	}

    public function renderDefault(int $postId): void
	{
		$this->id_post = $postId;
		$this->template->event = $this->facade->render_event($postId, $this->getUser()->isLoggedIn());
        $row = $this->facade->checkVote($postId, $this->getUser()->getId());
        if(isset($row)){
            $this->template->voted = "yes";
            $this->template->row = $this->facade->checkVote($postId, $this->getUser()->getId());
            
        }else{
            $this->template->voted = "no";
        }
        

	}

    public function createComponentVoteForm(): Form
    {
        $form = new Form();
        
        $form->addRadioList('vote', 'Vote', [
            'yes' => 'Yes',
            'no' => 'No',
        ]);
        $form->addSubmit('submit', 'Submit');
        $form->onSuccess[] = [$this, 'voteFormSucceeded'];
        return $form;
    }

    public function voteFormSucceeded(Form $form, \stdClass $values): void
    {
        $user_id = $this->getUser()->getId();
        $vote = $values->vote;
        $postId = isset($_GET['postId']) ? $_GET['postId'] : null;
        $row = $this->facade->checkVote($postId, $this->getUser()->getId());
        if(isset($row)){
            $this->facade->revote(intval($postId), $vote, $user_id);
        }else{
            $this->facade->insertVote(intval($postId), $vote, $user_id);
        }
        $this->redirect('Home:default');
    }

    
}
