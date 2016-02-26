<?php

	$file	=	'C:\Users\federico.stange\apf-dev\boot.php';

	require $file;
	require 'test.php';


	$form = new TestForm();
	$form->render();
