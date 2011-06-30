<? 
/***********************************************
* This file is part of PeoplePods
* (c) xoxco, inc  
* http://peoplepods.net http://xoxco.com
*
* core_groups/index.php
* Handles requests to /groups
* Creates new groups
*
* Documentation for this pod can be found here:
* http://peoplepods.net/readme
/**********************************************/

	include_once("../../PeoplePods.php");
	if ($_POST) { 
		$lockdown = 'verified';
	} else {
		$lockdown = ''; // anyone can see the groups homepage.
	}

	$POD = new PeoplePod(array('debug'=>0,'lockdown'=>$lockdown,'authSecret'=>@$_COOKIE['pp_auth']));	
	
	$max = 10;
	$offset = 0;
	if (isset($_GET['offset'])) { $offset = $_GET['offset']; }
	
	if ($_POST) { 
                //creating a group
		$group = $POD->getGroup();
		$group->set('groupname',$_POST['groupname']);
		$group->set('description',$_POST['description']);				
		$group->set('type',$_POST['type']);
		$group->save();
		
		if (!$group->success()) { 
			$POD->addMessage("Your group was not created! " . $group->error());
		} else {
			$POD->addMessage("Your new group, " . $group->permalink('groupname',true) . " is ready! ");
		}
	
	}
	if (isset($_GET['q'])) { 
		$header = "Shop Search";
		$groups = $POD->getGroups(array('or'=>array('groupname:like'=>'%' . $_GET['q'] . '%','description:like'=>'%' . $_GET['q'] . '%')),'g.date DESC',$max,$offset);
	} else {

		$groups = $POD->getGroups(array(),null,$max,$offset);
	}
	if (!$groups->success()) { 
		$POD->addMessage($groups->error());
	}
	
	$POD->header('All Shops');
	
	?>
	<div class="column_8">	
		<? $groups->output('short.shop','header','pager',$header,'Sorry We couldn\'t find that'); ?>
	</div>

	<div class="column_4 structure_only">
	
		<? $POD->output('sidebars/group_search'); ?>
		<? //$POD->output('sidebars/create_group'); ?>
		<? //$POD->output('sidebars/recent_groups'); ?>
		
	</div>
		
<?	$POD->footer(); ?>
<script type="text/javascript" >
    $(document).ready(function(){
        paintRatings();
    });
</script>