<?php
session_start();

// get access catalogs for search viewer
if (isset($_POST['getAccessCatalogs'])) {
	include_once('catalog.php');
	$access = json_decode(getAccessCatalogs($_SESSION['user_id']));
	$files = array();
	foreach ($access as $cat) {
		foreach (scanCatalog($cat) as $file) {
			array_push($files, $file);
		}
	}

	print json_encode($files);
}

function generateWatermark()
{
	$image = new Imagick();
	$image->newImage(420, 50, new ImagickPixel( "transparent" ));

	$datauser = $_SERVER['REMOTE_ADDR'] . ' ' . $_SERVER['HTTP_USER_AGENT'];

	$text = md5($datauser);

	// write in admin logs
	file_put_contents('logs/access.log', '[' . date("Y-m-d H:i") . '] ' . $datauser . ' -> ' . $text . "\n", FILE_APPEND);

	$draw = new ImagickDraw();
	$draw->setGravity( Imagick::GRAVITY_CENTER );
	//$draw->setFont( "./WCManoNegraBta.ttf" );
	$draw->setFontSize( 20 );
	//$draw->rotate(45);

	$watermark = new Imagick();
	$properties = $watermark->queryFontMetrics( $draw, $text );

	$wmark['w'] = (int)$properties["textWidth"];
	$wmark['h'] = (int)$properties["textHeight"] + 30;
	
	$watermark->newImage( $wmark['w'], $wmark['h'], new ImagickPixel( "transparent" ) );
	
	$it = $image->getPixelRegionIterator( 0, 0, $wmark['w'], $wmark['h'] );
	
	$textColor = new ImagickPixel("black");
	
	$draw->setFillColor( $textColor );
	//$draw->setFillAlpha( 0.2 );
	$watermark->setImageFormat( "png" );
	$watermark->annotateImage( $draw, 0, 0, 0, $text );
	//$watermark = $watermark->clone();
	$watermark->setImageBackgroundColor( $textColor );
	$watermark->shadowImage( 80, 1, 1, 2 );
	$watermark->compositeImage( $watermark, Imagick::COMPOSITE_OVER, 0, 0 );
	$image->compositeImage( $watermark, Imagick::COMPOSITE_OVER, 0, 0 );
	//header( "Content-Type: image/png" );
	$image->writeImage($_SERVER['DOCUMENT_ROOT'] . '/temp/temp' . date("Y-m-d_H") . '.png');

	return '/temp/temp' . date("Y-m-d_H") . '.png';
}
