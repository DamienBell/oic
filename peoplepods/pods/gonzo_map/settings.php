<?

	$POD->registerPOD(
		'gonzo_map',									// this is the name of the pod. it should match the folder name.
		'map coffee shops within a given radius',		// this is the description of the pod. it shows up in the command center.
		array(
			'^$'=>'gonzo_map/handler.php',		// set up the /sample url to handle requets
			'^map/(.*)'=>'gonzo_map/handler.php?shop=$1',	// set up handle requests for individual shops
		),
		array(),
                dirname(__FILE__).'/methods.php'   // if this pod is enabled, value can be accessed via $POD->libOptions('sample_pod_variable'))								// tells PeoplePods to call this function when the pod is turned off.
	);