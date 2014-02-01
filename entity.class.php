<?php

class entity { 

	private $files; 
	private $name;
	private $mails;
	private $id;
	
	public function __construct() {

		$this->files=array();
		$this->name=null;
		$this->mails=array();
		$this->id=null;
    }  
	
	public function setFiles($f)
	{
		$this->files=$f; 
	}
	
	public function setName($n)
	{
		$this->name=$n; 
	}
	
	public function setId($i)
	{
		$this->id=$i;
	}
	
	public function setMails($m)
	{
		$this->mails=$m; 
	}
	
	public function getId()
	{
		return $this->id;
	}
	
	public function getName()
	{
		return $this->name;
	}
	
	public function getFiles()
	{
		return $this->files;
	}
	
	public function getMails()
	{
		return $this->mails;
	}
}

?>