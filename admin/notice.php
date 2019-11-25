<?php
session_start();

$typeNotices = array(
	'forgot' => 'Внимание!<br>Вы давно не посещали систему документооборота.<br>Зайдите и ознакомьтесь с новыми документами.<br><button><a href="http://77.232.61.125">Зайти в систему</a></button>',
	'addFile' => 'Внимание!<br>В систему добавлены новые документы для вашего ознакомления.<br>Пройдите и ознакомьтесь с новыми документами.<br><button><a href="http://77.232.61.125">Прочитать документ</a></button>',
	'updateFile' => '',
	'deleteFile' => ''
);

function getNotices($userid)
{
	include("../config.php");

	try {
		$conn = new PDO("mysql:host=$SERVER_DATABASE;dbname=$NAME_DATABASE;charset=utf8", $USERNAME_DATABASE, $PASSWORD_DATABASE);
		$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

		$stmt = $conn->prepare("SELECT * FROM users WHERE id=?");
		$stmt->execute(array($userid));
		$result = $stmt->fetch();

		return unserialize(base64_decode(gzuncompress($result['notices'])));
	}
	catch(PDOException $e) {
		print json_encode($e);
	}
}

function getEmail($userid)
{
	include("../config.php");

	try {
		$conn = new PDO("mysql:host=$SERVER_DATABASE;dbname=$NAME_DATABASE;charset=utf8", $USERNAME_DATABASE, $PASSWORD_DATABASE);
		$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

		$stmt = $conn->prepare("SELECT * FROM users WHERE id=?");
		$stmt->execute(array($userid));
		$result = $stmt->fetch();

		return $result['email'];
	}
	catch(PDOException $e) {
		print json_encode($e);
	}
}

function addNotice($userid, $text, $typeNotice)
{
	include("../mail.php");

	// define 'struct' notice
	$notices = array('date' => "", 'icon' => "", 'iconColor' => "", 'text' => "", 'attachment' => "");
	// TODO: Customize params notice pls
	array_push($notices, array('date' => date("F j, Y"), 'icon' => "fa-file-alt", 'iconColor' => "bg-success", 'text' => $text, 'attachment' => ""));

	try {
	    $conn = new PDO("mysql:host=$SERVER_DATABASE;dbname=$NAME_DATABASE;charset=utf8", $USERNAME_DATABASE, $PASSWORD_DATABASE);
	    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

		$stmt = $conn->prepare('INSERT INTO users(notices) VALUES(?) WHERE id=?');
		$result = $stmt->execute(array(
			gzcompress(base64_encode(serialize($notices))),
			$userid
		));

		sendMail('documentsystem@kolhoz.spb.ru', getEmail($userid), $typeNotice);

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

function printNotice($notice)
{
	echo '<a class="dropdown-item d-flex align-items-center" href="#">';
    echo '<div class="mr-3">';
    echo '<div class="icon-circle ' . $notice['iconColor'] . '">';
    echo '<i class="fas ' . $notice['icon'] . ' text-white"></i>';
    echo '</div>';
    echo '</div>';
    echo '<div>';
    echo '<div class="small text-gray-500">' . $notice['date'] . '</div>';
    echo '<span class="font-weight-bold">' . $notice['text'] . '</span>';
    echo $notice['attachment'];
    echo '</div>';
    echo '</a>';
}

function printEmptyNotice()
{
	echo '<a class="dropdown-item d-flex align-items-center" href="#">';
    echo '<div><span class="font-weight-bold">Новых уведомлений нет</span></div>';
    echo '</a>';
}

// функция вызывается только при обновлении, удалении, добавлении файла
function noticeHandler($accessCatalogs)
{
	include("../config.php");
	include("admin.php");

	try {
	    $conn = new PDO("mysql:host=$SERVER_DATABASE;dbname=$NAME_DATABASE;charset=utf8", $USERNAME_DATABASE, $PASSWORD_DATABASE);
	    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

		$stmt = $conn->prepare("SELECT * FROM users WHERE id=?");

		for ($i = 0; $i < getCountRows(); $i++) { 
			$stmt->execute(array($i));
			$result = $stmt->fetch();

			$catalogs = json_decode($result['access']);
			foreach ($catalogs as $cat) {
				if (in_array($cat, $accessCatalogs)) {
					addNotice($result['id'], $typeNotices['forgot']);
			    } 
			}
		}
	}
	catch(PDOException $e) {
		print json_encode($e);
	}
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
	// guard for unauth user
} else {
	header("Location: /");
}
