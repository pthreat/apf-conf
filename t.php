<?php

	$route	=	Array(
							'name'			=>	'configure project directories',
							'description'	=>	'This route points to the project module for configuring directories',
							'path'			=> 'configure:project:directories',
							'module'			=>	'project',
							'sub'				=>	'directories',
							'controller'	=>	'index'
	);


	echo json_encode($route);
