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

function printCatalogs($cat)
{
	echo '<a class="collapse-item" href="catalog.html?catalog=' . $cat . '">' . $cat . '</a>';
}

function printFiles($files, $cat)
{
	foreach ($files as $key => $value)
	{
		echo '<tr>';
		echo '<td><a href="viewer.html?file=' . $cat . '/' . $value . '">' . $value . '</a></td>';
		echo '<td>' . getDateTimeModifiedFile($cat . '/' . $value) . '</td>';
		echo '<td>' . getDateTimeCreateFile($cat . '/' . $value) . '</td>';
		echo '<td>' . getProgramFormat($cat . '/' . $value) . '</td>';
		echo '</tr>';
	}
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

function getDateTimeModifiedFile($filename)
{
	$fulldir = $_SERVER['DOCUMENT_ROOT'] . '/docs/' . $filename;

	if (file_exists($fulldir)) {
		return date("d.m.y H:i", filemtime($fulldir));
	}
}

function getDateTimeCreateFile($filename)
{
	$fulldir = $_SERVER['DOCUMENT_ROOT'] . '/docs/' . $filename;

	if (file_exists($fulldir)) {
		return date("d.m.Y H:i", filectime($fulldir));
	}
}

function getProgramFormat($filename)
{
	$fulldir = $_SERVER['DOCUMENT_ROOT'] . '/docs/' . $filename;

	if (file_exists($fulldir)) {
		$ext = substr($filename, strrpos($filename, '.')+1);
		if ($ext == 'doc' or $ext == 'docx')
		{
			return $ext . ' (Microsoft Word)';
		}
		elseif ($ext == 'pdf')
		{
			return $ext . ' (Любой браузер)';
		}
	}
}

?>