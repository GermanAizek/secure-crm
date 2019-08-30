<?php

include("../config.php");

if (isset($_GET["key"])) {
    if ($_GET["key"] == $PASSWORD_DATABASE) {
		try {
		    $conn = new PDO("mysql:host=$SERVER_DATABASE;dbname=$NAME_DATABASE", $USERNAME_DATABASE, $PASSWORD_DATABASE);
		    // set the PDO error mode to exception
		    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

		    // sql to create table
		    $sql = "CREATE TABLE users (id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY, login VARCHAR(30) NOT NULL, password VARCHAR(30) NOT NULL, access VARCHAR(20), last_date DATETIME, role VARCHAR(5))";

		    // use exec() because no results are returned
		    $conn->exec($sql);
		    echo "Таблица 'users' создана успешно";
		}
		catch(PDOException $e) {
		    echo $sql . "<br>" . $e->getMessage();
		}

		$conn = null;
    } else {
		echo 'Введите в параметр key пароль от базы данных!';
	}
}
