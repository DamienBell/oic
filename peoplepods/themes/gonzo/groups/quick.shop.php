<?php
/***********************************************
* This file is part of PeoplePods
* (c) xoxco, inc  
* http://peoplepods.net http://xoxco.com
*
* theme/groups/short.php
* Default short output template for group objects
* Used in lists of groups
*
* Documentation for this pod can be found here:
* http://peoplepods.net/readme/group-object
/**********************************************/

if($POD->isAuthenticated()){
    $user= $POD->currentUser();

    $fieldsArray= array('type'=>'group',
                        'name'=>'last_viewed',
                        'itemId'=>$group->id,
                        'userId'=>$user->id);

     $lastViewed= $POD->flagInfo($fieldsArray);//get the last time the use viewed this shop
     
     $lastViewed= $lastViewed['date'];

     //get the date of the most recent post in this shop
     $most_recent_post= $POD->getContents(array('type'=>'review',
                                                'groupId'=>$group->id), "date DESC", 1
                                         )->getNext()->date; 
     $stats= '';
     
     if($most_recent_post){
       if(strtotime($lastViewed) <= strtotime($most_recent_post)){
          $status= "<a href='$group->permalink'>New Posts!</a>";
       }
     }
}
?>
<li class="quick_shop rnd_outline">
	<header class="group_name">
		<h3><a href="<?=$group->permalink; ?>"><?=$POD->shorten($group->groupname, 22);?></a></h3>
	</header>
 <? if($POD->isAuthenticated() && $most_recent_post && $status){ ?>
    <div class="right">
        <span class='tiny green_grad padded outline'><?=$status; ?></span>
    </div>
<? } ?>
	<div class="clearer"></div>
</li>