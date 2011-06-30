<?

	$POD->registerPOD(
		'core_location',
		'add location based functionality to content, people and groups',
		array(),
		array(),
		dirname(__FILE__).'/methods.php',
		'locationSetup',
		'locationInstall',
		'locationUninstall'
	);


	$POD->registerPOD(
		'core_location_demo',
		'demonstrates core location features by displaying content,people,groups,comments and files near the current user. View at <i>mysite.com</i>/locationTest',
		array('^locationTest'=>'core_location/handler.php'),
		array()
	);