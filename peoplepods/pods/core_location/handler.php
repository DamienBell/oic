<? 
	require_once("../../PeoplePods.php");
	$POD = new PeoplePod(array('authSecret'=>@$_COOKIE['pp_auth'],'debug'=>2));
	$POD->usecache(false);
	
	$POD->header('Location Test');
	
	$location = $POD->geocodeIP();
	
	echo "<h2>You are in " . $location->city . " at roughly " . $location->latitude . " x " . $location->longitude . "</h2>";
	
	
//	$content = $POD->getContentSortedByDistance(array(),$location->latitude,$location->longitude);
	$content = $POD->getContentNear(array(),$location->latitude,$location->longitude,100);
	
	echo "<h3>Nearby Content</h3>";

	
	foreach ($content as $c) {
		echo $c->headline . " @ " . $c->latitude . "x" . $c->longitude . "<br/>";		
	}
	
	echo "<h3>Nearby People</h3>";
	
	$people = $POD->getPeopleNear(array(),$location->latitude,$location->longitude,100);
	foreach ($people as $p) { 
		echo $p->nick. " @ " . $p->latitude . "x" . $p->longitude . "<br/>";		
	}		

	echo "<h3>Nearby Groups</h3>";
		
	$groups = $POD->getGroupsNear(array(),$location->latitude,$location->longitude,100);
	foreach ($groups as $p) { 
		echo $p->groupname. " @ " . $p->latitude . "x" . $p->longitude . "<br/>";		
	}		
			
	echo "<h3>Nearby Comments</h3>";

	$comments = $POD->getCommentsNear(array(),$location->latitude,$location->longitude,100);
	foreach ($comments as $p) { 
		echo $p->comment. " @ " . $p->latitude . "x" . $p->longitude . "<br/>";		
	}		


	echo "<h3>Nearby Files</h3>";
			
	$files = $POD->getFilesNear(array(),$location->latitude,$location->longitude,100);
	foreach ($files as $p) { 
		echo $p->file_name. " @ " . $p->latitude . "x" . $p->longitude . "<br/>";		
	}		
								
	$POD->footer();