<?

	$POD->registerPOD(
		'gonzo_search_4square',									// this is the name of the pod. it should match the folder name.
		'search for coffee shops via four square',		// this is the description of the pod. it shows up in the command center.
		array(
			'^via4$'=>'gonzo_search_4square/handler.php',		// set up the /sample url to handle requets
                                                                	// set up handle requests for individual shops
		),
		array(),
                dirname(__FILE__).'/methods.php'   // if this pod is enabled, value can be accessed via $POD->libOptions('sample_pod_variable'))								// tells PeoplePods to call this function when the pod is turned off.
	);