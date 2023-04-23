<?php

declare(strict_types=1);

namespace App\Model;
use Nette;
use Nette\Application\UI\Form;
use Nette\Security\SimpleIdentity;


final class UserFacade
{
	use Nette\SmartObject;

	private Nette\Database\Explorer $database;


	public function __construct(Nette\Database\Explorer $database)
	{
		$this->database = $database;
	}

	public function get_user(string $username)
	{
		return $this->database
			->table('users') 
			->where('username', $username) 
			->fetch();
	}

	public function getEvents(bool $prihlasen){
		if ($prihlasen) {
			$events = $this->database->table('events')
				->order('date DESC')
				->limit(20)
				->fetchAll();
	
			return $events;
		} else {
			return [];
		}
	}

	public function insertVote($postId, $vote, $user_id){	
		if($vote == "yes"){
			$statement = $this->database->query('INSERT INTO votes(events_id, user_id, vote) VALUES(? , ? , 1)', intval($postId), $user_id);
		}else{
			$statement = $this->database->query('INSERT INTO votes(events_id, user_id, vote) VALUES(? , ? , 0)', $postId, $user_id);
		}
	}

	public function checkVote($postId, $user_id){
		return $row = $this->database
			->table('votes')
			->where('events_id', $postId)
			->where('user_id =  ', $user_id)
			->fetch();
	}

	public function render_event($id,$prihlasen){
		if ($prihlasen){
			return $test =  $this->database
			->table('events')
			->where('id = ', $id)->fetch();
			
		}
	}

	public function revote($postId, $vote, $userId){
		if($vote == "yes"){
			$statement = $this->database->query('UPDATE votes set vote = 1 where events_id = ? and user_id = ?', intval($postId), $userId);
		}else{
			$statement = $this->database->query('UPDATE votes set vote = 0 where events_id = ? and user_id = ?', $postId, $userId);
		}
	}

	public function addEvent($prihlasen, $title, $date, $description, $userId){
		if($prihlasen){
			$statement = $this->database->query('INSERT INTO events (creator_id, date, description, title) values(?, ?, ?, ?)', intval($userId), $date, $description, $title);
		}
	}

	public function addUser($username, $email, $password){
		$statement = $this->database->query('INSERT INTO users (username, email, password) values(?, ?, ?)', $username, $email, $password);
	}

}
