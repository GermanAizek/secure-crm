<?php
session_start();

function getCountRows()
{
	include("config.php");

	$conn = new PDO("mysql:host=$SERVER_DATABASE;dbname=$NAME_DATABASE;charset=utf8", $USERNAME_DATABASE, $PASSWORD_DATABASE);
	$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

	$stmt = $conn->prepare('SELECT COUNT(*) FROM users');
	$stmt->execute();
	return $stmt->fetchColumn();

	
	//return mysqli_query($conn, 'SELECT COUNT(*) FROM users');
	//$access = mysqli_query($conn, 'SELECT access FROM users');
	//$count = mysqli_num_fields($access);
}

function js_str($s)
{
    return '"' . addcslashes($s, "\0..\37\"\\") . '"';
}

function js_array($array)
{
    $temp = array_map('js_str', $array);
    return '[' . implode(',', $temp) . ']';
}

if (isset($_SESSION['user_id'])) {
	include("catalog.php");
	include("config.php");

	// create user
	if (isset($_POST["usrReg"]) and isset($_POST["pwdReg"]) and isset($_POST["isAdmin"])) {
		try {
		    $conn = new PDO("mysql:host=$SERVER_DATABASE;dbname=$NAME_DATABASE;charset=utf8", $USERNAME_DATABASE, $PASSWORD_DATABASE);
		    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

		    $accessCatalogs = $_POST['accessCats'];

		    if ($_POST["isAdmin"] == "true") {
	    		$stmt = $conn->prepare('INSERT INTO users(login,password,access,role) VALUES(?,?,?,"admin")');
				$result = $stmt->execute(array(
					$_POST['usrReg'],
					$_POST['pwdReg'],
					json_encode($accessCatalogs, JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES)
				));
			} else {
				$stmt = $conn->prepare('INSERT INTO users(login,password,access,role) VALUES(?,?,?,"user")');
				$result = $stmt->execute(array(
					$_POST['usrReg'],
					$_POST['pwdReg'],
					json_encode($accessCatalogs, JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES)
				));
			}

		    if ($result == 0) {
		    	print json_encode('invalid');
		    } else {
		    	print json_encode('suckcess');
		    }
		}
		catch(PDOException $e) {
		    print json_encode($e);
		}
	}

	// delete user
	if (isset($_POST["usrDel"])) {
		try {
		    $conn = new PDO("mysql:host=$SERVER_DATABASE;dbname=$NAME_DATABASE;charset=utf8", $USERNAME_DATABASE, $PASSWORD_DATABASE);
		    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

			$stmt = $conn->prepare('DELETE FROM users WHERE login=?');
			$stmt->execute(array(
				$_POST['usrDel']
			));

			$result = $stmt->fetch();

		    if ($result == 0) {
		    	print json_encode('invalid');
		    } else {
		    	print json_encode('suckcess');
		    }
		}
		catch(PDOException $e) {
		    print json_encode($e);
		}
	}

	// edit user
	if (isset($_POST["usrEdit"]) and isset($_POST["pwdEdit"]) and isset($_POST["accessCats"]) and isset($_POST["isAdmin"]) and isset($_POST["usrSelect"])) {
		try {
		    $conn = new PDO("mysql:host=$SERVER_DATABASE;dbname=$NAME_DATABASE;charset=utf8", $USERNAME_DATABASE, $PASSWORD_DATABASE);
		    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

		    $accessCatalogs = $_POST['accessCats'];

		    if ($_POST["isAdmin"] == "true") {
	    		$stmt = $conn->prepare('UPDATE users SET login=?,password=?,access=?,role="admin" WHERE login=?');
				$stmt->execute(array(
					$_POST['usrEdit'],
					$_POST['pwdEdit'],
					json_encode($accessCatalogs, JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES),
					$_POST["usrSelect"]
				));
			} else {
				$stmt = $conn->prepare('UPDATE users SET login=?,password=?,access=?,role="user" WHERE login=?');
				$stmt->execute(array(
					$_POST['usrEdit'],
					$_POST['pwdEdit'],
					json_encode($accessCatalogs, JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES),
					$_POST["usrSelect"]
				));
			}

			$result = $stmt->fetch();

		    if ($result == 0) {
		    	print json_encode('invalid');
		    } else {
		    	print json_encode('suckcess');
		    }
		}
		catch(PDOException $e) {
		    print json_encode($e);
		}
	}

	// create catalog
	if (isset($_POST["catalogAddName"])) {
		try {
		    $result = createCatalog($_POST["catalogAddName"]);

		    if ($result == 0) {
		    	print json_encode('invalid');
		    } else {
		    	print json_encode('suckcess');
		    }
		}
		catch(PDOException $e) {
		    print json_encode($e);
		}
	}

	// delete catalog
	if (isset($_POST["catalogDelName"])) {
		try {
			// $count = getCountRows();
			// print json_encode($count);

		    deleteCatalog($_POST["catalogDelName"]);

			$conn = new PDO("mysql:host=$SERVER_DATABASE;dbname=$NAME_DATABASE;charset=utf8", $USERNAME_DATABASE, $PASSWORD_DATABASE);
			$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			$conn->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);

			$count = getCountRows();
			print json_encode($count);
			//	$count = +$count;

			// Creates $data Array[]
			// $data = array(); 

			// while($row = mysqli_fetch_array($result)){
			//     //inserts received data into "$data" and makes available by array[]index
			//     $data[] = $row;
			// }

			for ($i = 0; $i < $count-1; $i++) {
				$sql = 'SELECT access FROM users LIMIT :skip,:max';
				$stmt = $conn->prepare($sql);
				$stmt->bindValue(':skip', $i, PDO::PARAM_INT);
				$stmt->bindValue(':max', $i+1, PDO::PARAM_INT);

				// $stmt->execute(array(
				// 	$i-1,
				// 	$i
				// ));
				$stmt->execute();
				$stmt->setFetchMode(PDO::FETCH_ASSOC);
				//$result = mysqli_query($conn, 'SELECT access FROM users WHERE 1=1');
				$result = $stmt->fetch();

				if (! $result = json_decode($result['access'], true)) {
					continue;
				}
				//print json_encode($result);

				//var_dump($result);

				if (($key = array_search($_POST["catalogDelName"], $result)) !== false) {
					unset($result[$key]);
				} else {
					continue;
				}
				print json_encode($result);	

				$sql = 'UPDATE users SET access=:access WHERE id IN (SELECT id FROM (SELECT id FROM users ORDER BY id ASC LIMIT :skip,:max) tmp)';
				$stmt = $conn->prepare($sql);
				$stmt->bindValue(':access', js_array($result), PDO::PARAM_STR);
				$stmt->bindValue(':skip', $i, PDO::PARAM_INT);
				$stmt->bindValue(':max', $i, PDO::PARAM_INT);
				// $stmt->execute(array(
				// 	js_array($result),
				// 	$i-1,
				// 	$i
				// ));
				$stmt->execute();
			}

			// TODO: СДЕЛАТЬ УДАЛЕНИЕ КАТАЛОГА У ВСЕХ ПОЛЬЗОВАТЕЛЕЙ

			if ($result == 0) {
				print json_encode('invalid');
			} else {
		    	print json_encode('suckcess');
		    }
		}
		catch(PDOException $e) {
		    print json_encode($e);
		}
	}

	// delete file
	if (isset($_POST["fileDelName"])) {
		try {
		    $result = deleteFile($_POST["fileDelName"]);

			if ($result == 0) {
				print json_encode('invalid');
			} else {
		    	print json_encode('suckcess');
		    }
		}
		catch(PDOException $e) {
		    print json_encode($e);
		}
	}

	// upload file
	if (isset($_FILES['userfile']) and isset($_POST['catalogUploadFile'])) {
		try {
		    $uploaddir = $_SERVER['DOCUMENT_ROOT'] . '/docs/' . $_POST['catalogUploadFile'] . '/' . basename($_FILES['userfile']['name']);
		    if (move_uploaded_file($_FILES['userfile']['tmp_name'], $uploaddir)) {
			    header('Location: http://'.$_SERVER['HTTP_HOST'].'/main.html');
			} else {
			    print json_encode('Invalid. Error code: ' . $_FILES['userfile']['error']);
			}
		}
		catch(PDOException $e) {
		    print json_encode($e);
		}
	}
} else {
 	header("Location: /");
}
