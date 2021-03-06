<?php

class allThreads{
	private $nrOfThreads = 0;
	private $threads = array();
	private $connect;
	private $user;
	public function __construct($connection, $user){
		$this->connect = $connection;
		$this->user = $user;
		foreach ($connection->query('SELECT * FROM forum_threads') as $row){
			$this->nrOfThreads += 1;
			$thread = new thread();
			$thread->setId($row['forumid']);
			$thread->setText($row['text']);
			$thread->setTimestamp($row['timestamp']);
			$thread->setTitle($row['title']);
			$thread->setUserId($row['userid']);
			$thread->setTopTopic($row['toptopic']);
			$thread->setUsername(usertools::getUsernameById($row['userid'], $connection));
			array_push($this->threads, $thread);
		}
	}
	/**
	 * This method return a list of toptopics.. could be used to make a overview
	 * @return array with toptopics
	 */
	public function getAllTopThreads(){
		$filteredList = array();
		foreach($this->threads as $thread){
			if($thread->getTopTopic()==-1){
				array_push($filteredList, $thread);
			}
		}
		return $filteredList;
	}

	public function getSubThreads($topicid,$position = 10, $recursive = true){
		$filteredList = array();
		// go through every thread
		foreach($this->threads as $thread){
			if($thread->getTopTopic()==$topicid){
				$thread->setPosition($position);
				array_push($filteredList, $thread);
				if($recursive){
					$filteredList = array_merge($filteredList, $this->getSubThreads($thread->getId(), $position + 20));
				}
			}
		}
		return $filteredList;
	}

	public function getTopThreadId($subthreadid){
		// get the thread
		$subThread = $this->getThreadById($subthreadid);
		$currentId;
		foreach($this->threads as $thread){
			// check, if a thread got a id which is the toptopic from the subthread..
			if($thread->getId()==$subThread->getTopTopic()){
				if($thread->getTopTopic()!=-1){
					$currentId = $this->getTopThreadId($thread->getId());
				}
				else{
					return $thread->getId();
				}
			}
			else if($subThread->getTopTopic()==-1){
				return $subThread->getId();
			}
		}
		return $currentId;
	}
	
	public function getThreadById($id){
		foreach($this->threads as $thread){
			if($thread->getId()==$id){
				return $thread;
			}
		}
		return $filteredList;
	}
	public function createNewThread($title, $text, $toptopic = -1){
		$this->connect->exec("INSERT INTO `learncards`.`forum_threads` (`forumid`, `userid`, `title`, `text`, `timestamp`, `toptopic`) VALUES (NULL, '".$this->user->getId()."', '".$title."', '".$text."', CURRENT_TIMESTAMP, '".$toptopic."');");
		$this->nrOfThreads += 1;
		$thread = new thread();
		$thread->setId($this->connect->lastInsertId());
		$thread->setText($text);
		$thread->setTimestamp("");
		$thread->setTitle($title);
		$thread->setUserId($this->user->getId());
		$thread->setTopTopic($toptopic);
		$thread->setUsername(usertools::getUsernameById($this->user->getId(), $this->connect));
		array_push($this->threads, $thread);
		return $this->connect->lastInsertId();
	}

}

class thread{
	private $id;
	private $userid;
	private $title;
	private $text;
	private $timestamp;
	private $topTopic;
	private $username;
	private $position;
	public function setId($id){
		$this->id = $id;
	}
	public function getId(){
		return $this->id;
	}
	public function getPosition(){
		return $this->position;
	}
	public function setPosition($position){
		$this->position = $position;
	}
	public function setUserId($userid){
		$this->userid = $userid;
	}
	public function getUserId(){
		return $this->userid;
	}
	public function getUsername(){
		return $this->username;
	}
	public function setUsername($username){
		$this->username = $username;
	}
	public function setTitle($title){
		$this->title = $title;
	}
	public function getTitle(){
		return $this->title;
	}
	public function setText($text){
		$this->text = $text;
	}
	public function getText(){
		return $this->text;
	}
	public function setTimestamp($timestamp){
		$this->timestamp = $timestamp;
	}
	public function getTimestamp(){
		return $this->timestamp;
	}
	public function setTopTopic($topTopic){
		$this->topTopic = $topTopic;
	}
	public function getTopTopic(){
		return $this->topTopic;
	}

}



?>