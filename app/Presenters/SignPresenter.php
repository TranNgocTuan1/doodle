<?php

declare(strict_types=1);

namespace App\Presenters;

use Nette;
use Nette\Application\UI\Form;
use Nette\Security\SimpleIdentity;
use App\Model\UserFacade;
use Nette\Security\Passwords;

/**
 * Docs
 */
final class SignPresenter extends Nette\Application\UI\Presenter
{

	private $database;
	private UserFacade $facade;
	private $passwords;

	public function __construct(Nette\Database\Explorer $database, Nette\Security\Passwords $passwords, UserFacade $facade)
	{
		$this->database = $database;
		$this->passwords = $passwords;
		$this->facade = $facade;
	}



	public function authenticate(string $username, string $password): SimpleIdentity
	{
		$row = $this->facade->get_user($username);

		if (!$row) {
			throw new Nette\Security\AuthenticationException('User not found.');
		}
		if (!$this->passwords->verify($password, $row->password)) {
			throw new Nette\Security\AuthenticationException('Invalid password.');
		}

		return new SimpleIdentity(
			$row->id,
			$row->email, // or array of roles
			['name' => $row->username],
		);
	}

	public function signInFormSucceeded(Form $form, \stdClass $data): void
	{

		try {
			$identity = $this->authenticate($data->username, $data->password);
			$this->getUser()->login($identity);
			$this->redirect('Home:default');
		} catch (Nette\Security\AuthenticationException $e) {
			$form->addError('Špatná kombinace hesla a jména ');
		}

	}

	public function actionOut(): void
	{
		$this->getUser()->logout();
		$this->flashMessage('You have been signed out.');
		$this->redirect('Sign:default');
	}

	/**
	 * Sign-in form factory.
	 */
	protected function createComponentSignInForm(): Form
	{
		$form = new Form;
		$form->addText('username', 'Username:')
			->setRequired('Please enter your username.');

		$form->addPassword('password', 'Password:')
			->setRequired('Please enter your password.');

		$form->addSubmit('send', 'Sign in');

		// call method signInFormSucceeded() on success
		$form->onSuccess[] = [$this, 'signInFormSucceeded'];
		return $form;
	}

	public function createComponentRegisterForm(): Form
    {
        $form = new Form;
        
        $form->addText('name', 'Name:')
            ->setRequired();
        
        $form->addEmail('email', 'Email:')
            ->setRequired()
            ->addRule(Form::EMAIL);
        
        $form->addPassword('password', 'Password:')
            ->setRequired();
        
        $form->addPassword('password_confirm', 'Confirm password:')
            ->setRequired()
            ->addRule(Form::EQUAL, 'Passwords do not match', $form['password']);
        
        $form->addSubmit('submit', 'Register');
        
        $form->onSuccess[] = [$this, 'processRegistrationForm'];
        
        return $form;
    }

	public function processRegistrationForm(Form $form, array $values): void
    {
		$passwordsObj = new Passwords();
        $username = $values['name'];
		$email = $values['email'];
		$password = $values['password'];
		
		$hash = $passwordsObj->hash($password);
		$this->facade->addUser($username, $email, $hash);
		$this->flashMessage('Account created successfuly.', 'success');
		$this->redirect('Sign:default');
    }
}
