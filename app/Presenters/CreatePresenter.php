<?php

declare(strict_types=1);

namespace App\Presenters;

use App\Model\UserFacade;
use Nette\Application\UI\Form;
use Nette;
use Nette\Schema\Expect;
use Nette\Forms\Controls\DateInput;
use Nette\Utils\DateTime;

final class CreatePresenter extends Nette\Application\UI\Presenter
{
	private UserFacade $facade;
    private $id_post = null;

	public function __construct(UserFacade $facade)
	{
		$this->facade = $facade;
	}

    public function renderDefault(): void
	{

	}

    protected function createComponentAddEventForm(): Form
    {
        $form = new Form();

    $form->addText('title', 'Title:')
        ->setRequired('Please enter the event title.');

    $form->addTextArea('description', 'Description:')
        ->setRequired('Please enter the event description.');

    $form->addText('date', 'Date')->setType('date');//setAttribute('class', 'datepicker');

    $form->addSubmit('submit', 'Add event');

    $form->onSuccess[] = [$this, 'addEventFormSucceeded'];


    return $form;
    }

    public function addEventFormSucceeded(Form $form, \stdClass $values): void
    {
        $title = $values->title;
        $date = $values->date;
        $description = $values->description;
        $userId = $this->getUser()->getId();
        $prihlasen = $this->getUser()->isLoggedIn();
        if (empty($title) || empty($description) || empty($date)) {
            $form->addError('Please fill in all fields.');
            return;
        }
        $this->facade->addEvent($prihlasen, $title, $date, $description, $userId);
        $this->flashMessage('Event added successfully.', 'success');
        $this->redirect('Home:default');
    }

    
}
