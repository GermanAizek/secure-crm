<?php

function generateWatermark($file, $number = 0)
{
	//$image = new Imagick($_SERVER['DOCUMENT_ROOT'] . '/localfiles/' . $file);
	$image = new Imagick();
	//echo 'Текущий путь: ' . $file;
	$image->readImage($_SERVER['DOCUMENT_ROOT'] . '/localfiles/' . $file);

	$text = md5($_SERVER['REMOTE_ADDR'] . ' ' . $_SERVER['HTTP_USER_AGENT']);

	$draw = new ImagickDraw();
	$draw->setGravity( Imagick::GRAVITY_CENTER );
	//$draw->setFont( "./WCManoNegraBta.ttf" );
	$draw->setFontSize( 20 );
	//$draw->rotate(45);

	$watermark = new Imagick();
	$properties = $watermark->queryFontMetrics( $draw, $text );

	$wmark['w'] = (int)$properties["textWidth"] + 50;
	$wmark['h'] = (int)$properties["textHeight"] + 50;
	
	$watermark->newImage( $wmark['w'], $wmark['h'], new ImagickPixel( "transparent" ) );
	
	$it = $image->getPixelRegionIterator( 0, 0, $wmark['w'], $wmark['h'] );
	 
	$luminosity = 0;
	$i = 0;
	
	while($row = $it->getNextIteratorRow())
	{
		foreach ($row as $pixel)
		{
			$hsl = $pixel->getHSL();
			$luminosity += $hsl['luminosity'];
			$i++;
		}
	}
	
	$textColor = (($luminosity / $i) > 0.5) ? new ImagickPixel("black") : new ImagickPixel("white");
	
	$draw->setFillColor( $textColor );
	$draw->setFillAlpha( 0.2 );
	$watermark->setImageFormat( "png" );
	$watermark->annotateImage( $draw, 0, 0, 0, $text );
	$watermark = $watermark->clone();
	$watermark->setImageBackgroundColor( $textColor );
	$watermark->shadowImage( 80, 1, 1, 2 );
	$watermark->compositeImage( $watermark, Imagick::COMPOSITE_OVER, 0, 0 );
	$image->compositeImage( $watermark, Imagick::COMPOSITE_OVER, 0, 0 );
	//header( "Content-Type: image/png" );
	$image->writeImage($_SERVER['DOCUMENT_ROOT'] . '/localfiles/temp/temp' . $number . '.png');

	return $image;
}