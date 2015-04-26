<?php

class App_Password
{

	private function __construct()
	{
	}

	public static function getInstance() {
		static $instance = false;
		if(!$instance) 
			$instance = new App_Password;
		return $instance;
	}

	public function salt($pwd) {
		$result = new stdClass;
		$result->salt = substr(md5(time() . rand(0,9999)), 0, 10);
		$result->pass = sha1($pwd . $result->salt);
		return $result;
	}

	public function saltWith($pwd, $salt) {
		$result = new stdClass;
		$result->salt = $salt;
		$result->pass = sha1($pwd . $result->salt);
		return $result;
	}
}

?>