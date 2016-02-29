<?php

	//$file	=	'C:\Users\federico.stange\apf-dev\boot.php';

//require $file;

	require "/usr/share/apf-dev/boot.php";
	
	$ui	=	\apf\core\Project::configure();
	$ui->render();
	die();
