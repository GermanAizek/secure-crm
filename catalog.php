<?php

function getAccessCatalogs($userid)
{
	include('config.php');

	try {
	    $conn = new PDO("mysql:host=$SERVER_DATABASE;dbname=$NAME_DATABASE;charset=utf8", $USERNAME_DATABASE, $PASSWORD_DATABASE);
	    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

		$stmt = $conn->prepare("SELECT * FROM users WHERE id=?");
		$stmt->execute(array($userid));
		$result = $stmt->fetch();

		return $result['access'];
	}
	catch(PDOException $e) {
	    print json_encode($e);
	}
}

function createCatalog($path)
{
	try {
		if (!is_dir($_SERVER['DOCUMENT_ROOT'] . '/docs/' . $path)) {
			if (mkdir($_SERVER['DOCUMENT_ROOT'] . '/docs/' . $path)) {
				return true;
			} else {
				return false;
			}
		} else {
			return false;
		}
	}
	catch(PDOException $e) {
	    print json_encode($e);
	}
}

function deleteFile($path)
{
	$fulldir = $_SERVER['DOCUMENT_ROOT'] . '/docs/' . $path;
    if (unlink($fulldir)) {
    	return true;
    } else {
    	return false;
    }
}

function deleteCatalog($path)
{
	$fulldir = $_SERVER['DOCUMENT_ROOT'] . '/docs/' . $path;

	$files = array_diff(scandir($fulldir), array('.','..'));
    foreach ($files as $file) {
      (is_dir("$fulldir/$file")) ? delTree("$fulldir/$file") : unlink("$fulldir/$file");
    }
    if (rmdir($fulldir)) {
    	return true;
    } else {
    	return false;
    }
}

function scanCatalog($path)
{
	return array_filter(scandir($_SERVER['DOCUMENT_ROOT'] . '/docs/' . $path), function($item) {
	    return $item[0] !== '.';
	});
}

function printCatalog($files, $cat, $id)
{
	echo '<ol class="tree">
			<li>';
	echo '<label id="catalog' . $id . '">' . $cat . '</label> <input type="checkbox" checked />';
	echo '<ol>';

	foreach ($files as $key => $value)
	{
		echo '<li class="file label label-default"><a href="?file=' . $cat . '/' . $value . '">' . $value . '</a></li>';
	}

	echo '</ol>';
	echo '	</li>
		</ol>';
}

function getFolders($path)
{
	return glob($_SERVER['DOCUMENT_ROOT'] . $path, GLOB_ONLYDIR);
}

function getFiles($path)
{
	return glob($_SERVER['DOCUMENT_ROOT'] . $path . ".{*}", GLOB_BRACE);
}

function uploadFile($path)
{
	if (!is_dir($_SERVER['DOCUMENT_ROOT'] . '/docs/' . $path)) {
		if (mkdir($_SERVER['DOCUMENT_ROOT'] . '/docs/' . $path)) {
			return true;
		} else {
			return false;
		}
	} else {
		return false;
	}
}

function getDateTimeUploadFile($filename)
{
	$fulldir = $_SERVER['DOCUMENT_ROOT'] . '/docs/' . $filename;

	if (file_exists($fulldir)) {
		return date("Y-m-d H:i:s", filemtime($fulldir));
	}
}

?>