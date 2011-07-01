<?php 

	$POD->registerPOD(
		"user_agent", // name of pod
		"Determine features of a users browser and OS", // description of pod for settings menu
		array( // rewrite rules
			'^user_agent'=>'user_agent/index.php'
		),
		array(),
                dirname(__FILE__) . "/methods.php"				// tells PeoplePods to add custom methods included in the methods.php file
	);

?>