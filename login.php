<?php
session_start();

if (isset($_SESSION['user_id'])) {
	header("Location: /main.html");
} else {
	include("config.php");

	if (isset($_POST["usr"]) and isset($_POST["pwd"])) {
		$user = htmlspecialchars($_POST["usr"], ENT_QUOTES);
		$pass = htmlspecialchars($_POST["pwd"], ENT_QUOTES);
		// connect to db
		try {
		    $conn = new PDO("mysql:host=$SERVER_DATABASE;dbname=$NAME_DATABASE;charset=utf8", $USERNAME_DATABASE, $PASSWORD_DATABASE);
		    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

		    $conn->beginTransaction();

			$stmt = $conn->prepare("SELECT * FROM users WHERE login=? and password=?");
			$stmt->execute(array(
				$user,
				$pass
			));
			$result = $stmt->fetch();

		    if ($result == 0) {
		    	print json_encode('invalid');
		    } else {
		    	$_SESSION['user_id'] = $result['id'];
		    	$_SESSION['user'] = $user;

		    	$stmt = $conn->prepare("UPDATE users SET last_date=now() WHERE login=? and password=?");
				$stmt->execute(array(
					$user,
					$pass
				));

		    	print json_encode('suckcess');
		    }

		    $conn->commit();
		}
		catch(PDOException $e) {
			$conn->rollBack();
		    print json_encode($e);
		}
	}
}
 