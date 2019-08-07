<?php

function scanCatalog($path)
{
	return scandir($_SERVER['DOCUMENT_ROOT'] . '/' . $path);
}

function printCatalog($files)
{
	echo '<ol class="tree">
			<li>';
	echo '<label>Ваш каталог</label> <input type="checkbox" checked />';
	echo '<ol>';

	foreach ($files as $key => $value)
	{
		echo '<li class="file label label-default"><a href="?file=' . $value . '">' . $value . '</a></li>';
	}

	echo '</ol>';
	echo '	</li>
		</ol>';
}