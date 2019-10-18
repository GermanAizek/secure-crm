<?php
session_start();

include("config.php");

if (isset($_POST["usr"]) and isset($_POST["pwd"])) {
	// connect to db
	try {
	    $conn = new PDO("mysql:host=$SERVER_DATABASE;dbname=$NAME_DATABASE;charset=utf8", $USERNAME_DATABASE, $PASSWORD_DATABASE);
	    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

	    $conn->beginTransaction();

		$stmt = $conn->prepare("SELECT * FROM users WHERE login=? and password=?");
		$stmt->execute(array(
			$_POST['usr'],
			$_POST['pwd']
		));
		$result = $stmt->fetch();

	    if ($result == 0) {
	    	print json_encode('invalid');
	    } else {
	    	$_SESSION['user_id'] = $result['id'];
	    	$_SESSION['user'] = $_POST['usr'];

	    	$stmt = $conn->prepare("UPDATE users SET last_date=now() WHERE login=? and password=?");
			$stmt->execute(array(
				$_POST['usr'],
				$_POST['pwd']
			));

	    	print json_encode('suckcess');
	    }

	    $conn->commit();
	}
	catch(PDOException $e) {
	    print json_encode($e);
	    $conn->rollBack();
	}
}
 