<?php

	$file	=	'C:\Users\federico.stange\apf-dev\boot.php';

	require $file;
	require 'test.php';

	use \apf\core\Log;

	$input	=	new InputWidget(
											Array(
													'decorator' =>	new Log(),
													'validator'	=>	new InputValidator()
											)
	);

	$input->render();

	$select	=	new SelectWidget(
											Array(
													  'options'=>Array(
																			  Array(
																					  'name'				=>'a',
																					  'description'	=>'Set value of A'
																			  )
													  )
											)
	);

	$select->render();

