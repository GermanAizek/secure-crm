<?php

function scanCatalog($path)
{
	return scandir($_SERVER['DOCUMENT_ROOT'] . '/localfiles/' . $path);
}

function printCatalog($files)
{
	echo '<p>Каталог документов</p>';
	echo '<ol class="tree">
			<li>';
	echo '<label for="folder1">docs</label> <input type="checkbox" checked disabled id="folder1" />';
	echo '<ol>';

	foreach ($files as $key => $value)
	{
		echo '<li class="file"><a href="?file=' . $value . '">' . $value . '</a></li>';
	}

	echo '</ol>';
	echo '	</li>
		</ol>';
}