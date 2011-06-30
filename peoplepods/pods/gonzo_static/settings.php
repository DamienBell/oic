<?

	$POD->registerPOD(
		'gonzo_static',									// this is the name of the pod. it should match the folder name.
		'manage static pages, such as about, FAQ, blog, ect',		// this is the description of the pod. it shows up in the command center.
		array(
			'^faq$'=>'gonzo_static/faq.php',
   '^about$'=>'gonzo_static/about.php',
   '^blog$'=>'gonzo_static/blog.php'
		),
		array(),
    dirname(__FILE__).'/methods.php'   // if this pod is enabled, value can be accessed via $POD->libOptions('sample_pod_variable'))								// tells PeoplePods to call this function when the pod is turned off.
	);