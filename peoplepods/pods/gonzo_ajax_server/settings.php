<?

	$POD->registerPOD(
		'gonzo_ajax',									// this is the name of the pod. it should match the folder name.
		'server for ajax calls',		// this is the description of the pod. it shows up in the command center.
		array(
			'^server$'=>'gonzo_ajax_server/handler.php',		// set up the /sample url to handle requets
			'^server/(.*)'=>'gonzo_ajax_server/handler.php?command=$1',	// set up handle requests for individual shops
		),
		array(),
                dirname(__FILE__).'/methods.php'   // if this pod is enabled, value can be accessed via $POD->libOptions('sample_pod_variable'))								// tells PeoplePods to call this function when the pod is turned off.
	);