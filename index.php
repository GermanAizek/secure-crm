<?php

session_start();

include_once("catalog.php");
include_once("viewer.php");

echo '<html lang="en-GB">';
echo '<head>
	<meta http-equiv="Content-Type" content="text/html;charset=utf-8">
	<title>Защищенный просмотр</title>
	<meta name="author" content="GermanAizek">
	<meta name="keywords" content="защита, просмотр файлов, авторское право">
	<meta name="description" content="Защищенный просмотр документов. Разработчик: Герман Семенов https://germanaizek.github.io">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<meta name="robots" content="all">
	<meta name="copyright" content="Колхоз">
	
	<!--[if gte IE 9 ]><link rel="stylesheet" type="text/css" href="_styles.css" media="screen"><![endif]-->
	<!--[if !IE]>--><link rel="stylesheet" type="text/css" href="_styles.css" media="screen"><!--<![endif]-->

	<!-- Latest compiled and minified CSS -->
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.0/css/bootstrap.min.css">

	<!-- jQuery library -->
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>

	<!-- Latest compiled JavaScript -->
	<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.0/js/bootstrap.min.js"></script>';
echo '<style>
			p, body, head, h1, h2, h3, h4, div, iframe, img, form, button {
				-moz-user-select: none;
				-ms-user-select: none;
				-o-user-select: none;
				-webkit-user-select: none;
				user-select: none;
			}
		</style>';
echo '</head>';

// guarded document
echo '<body oncontextmenu="return false;" style="background-color: grey">';
echo '<div class="container-fluid">';
echo '<div class="row">';
echo '<div class="col-md-2">';
$files = scanCatalog('/docs');

printCatalog($files);

// foreach ($files as $key => $value) {
// 	echo $value;
// }
echo '</div>';
echo '<div class="col-md-10">';

if( strpos( $_GET['file'], '.pdf' ) !== false )
{
	$page = 0;
	echo $page;

	if (isset($_GET['next']))
	{
		$page++;
	}

	if (isset($_GET['prev']))
	{
		$page--;
	}

	if (isset($_GET['file']))
	{
	    generateWatermark('docs/' . $_GET['file'] . '[' . $page . ']', 0);
	}

	echo '<img oncontextmenu="return false;" src="temp/temp0.png" width="40%" height="95%" style="float:middle"></img>';

	if (isset($_GET['file']))
	{
	    generateWatermark('docs/' . $_GET['file'] . '[' . $page+1 . ']', 1);
	}
	echo '<img oncontextmenu="return false;" src="temp/temp1.png" width="40%" height="95%" style="float:middle"></img>';
} else {
	if (isset($_GET['file']))
	{
	    generateWatermark('docs/' . $_GET['file'], 0);
	}
	echo '<img oncontextmenu="return false;" src="temp/temp0.png" width="40%" height="95%" style="float:middle"></img>';

//
	if (isset($_GET['file']))
	{
	    generateWatermark('docs/doc2.png', 1);
	}
	echo '<img oncontextmenu="return false;" src="temp/temp1.png" width="40%" height="95%" style="float:middle"></img>';
//
}

echo '<form method="POST">
<button type="submit" name="prev" value="prev">< Предыдущая страница</button>
<button type="submit" name="next" value="next" style="float:right">Следующая страница ></button>
</form>';
echo '</div>';
echo '</div>';
echo '</div>';
echo '</body>';
echo '</html>';