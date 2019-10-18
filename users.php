<?php

function isAdmin($userid)
{
	include("config.php");

	try {
	    $conn = new PDO("mysql:host=$SERVER_DATABASE;dbname=$NAME_DATABASE;charset=utf8", $USERNAME_DATABASE, $PASSWORD_DATABASE);
	    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

		$stmt = $conn->prepare("SELECT * FROM users WHERE id=?");
		$stmt->execute(array($userid));
		$result = $stmt->fetch();

		return $result['role'];
	}
	catch(PDOException $e) {
		print json_encode($e);
	}
}

function getUserList()
{
	include("config.php");
	// connect to db
	try {
	    $conn = new PDO("mysql:host=$SERVER_DATABASE;dbname=$NAME_DATABASE;charset=utf8", $USERNAME_DATABASE, $PASSWORD_DATABASE);
	    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

		$sql = 'SELECT login FROM users';
		$stmt = $conn->query($sql);

		$array = [];
		while ($row = $stmt->fetch())
		{
		    array_push($array, $row['login']);
		}
		
		return $array;
	}
	catch(PDOException $e) {
	    print json_encode($e);
	}
}

function getTimeLogin($userid)
{
	include('config.php');

	try {
	    $conn = new PDO("mysql:host=$SERVER_DATABASE;dbname=$NAME_DATABASE;charset=utf8", $USERNAME_DATABASE, $PASSWORD_DATABASE);
	    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

		$stmt = $conn->prepare("SELECT * FROM users WHERE id=?");
		$stmt->execute(array($userid));
		$result = $stmt->fetch();

		return $result['last_date'];
	}
	catch(PDOException $e) {
	    print json_encode($e);
	}
}
 