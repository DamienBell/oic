<?php
/***********************************************
* This file is part of PeoplePods
* (c) xoxco, inc  
* http://peoplepods.net http://xoxco.com
*
* theme/people/output.php
* Default output template for a person object. 
* Defines what a user profile looks like
*
* Documentation for this pod can be found here:
* http://peoplepods.net/readme/person-object
/**********************************************/
if($POD->isAuthenticated() && $user->id== $POD->currentUser()->id){
    $own_profile= true;
}else{
    $own_profile= false;
}

$my_shops = $POD->getGroups(array('mem.userId'=>$user->id));
$my_shops->sortBy('last_review');

$user_content= $user->getContents(array());
$recent_reactions= $POD->getComments(array('contentId'=>$user_content->extract('id')));
$recent_reactions->sortBy('date');

$tester= $user_content->asArray();

?>
	<div id="faves_n_such">
      <div id="my_profile" class="column_4">
          <div>
           <h1><? $user->write('nick'); ?></h1>
           <? if ($img = $user->files()->contains('file_name','img')) { ?>
            <img src="<? $img->write('resized'); ?>"  />
           <? } ?>
          </div>

          <div id="profile_actions">
            <? if ($user->POD->isAuthenticated()) {
             if (!$own_profile) {  ?>
                  <a href="#toggleFlag" data-flag="friends" data-person="<?= $user->id; ?>" data-active="Stop Following" data-inactive="Follow" class="person_output_follow_button <? if ($user->hasFlag('friends',$POD->currentUser())){?>active<? } ?>">Follow</a><br />
                  <? if ($user->POD->libOptions('enable_core_private_messaging')) { ?>
                   <a href="<? $user->POD->siteRoot(); ?><? echo $user->POD->libOptions('messagePath') ?>/<? $user->write('stub'); ?>" class="person_output_send_message_button">Send Message</a>
                  <? } ?>
             <? }else { ?>
                    <a href="<? $user->POD->siteRoot(); ?>/editprofile" title="Edit My Profile">Edit My Profile</a>
             <? } ?>
            <? } else { ?>
                <div id="addFriend<? $user->write('id'); ?>"><a href="<? $user->POD->siteRoot(); ?>/join" class="person_output_follow_button person_output_follow_button_start">Join up to follow <? $user->write('nick'); ?></a></div>
            <? } ?>
          </div>

          <div id="profile_about">
           <? if ($user->get('aboutme')) { ?>
            <? echo $user->formatText('aboutme'); ?>
           <? } ?>
          </div>
      </div><div class="clearer"></div>
     <div id="profile_friends" class="profile_control column_6">
       <h2>Following <? echo $user->friends()->totalCount(); echo $POD->pluralize($user->friends()->totalCount(),' Author',' Authors'); ?></h2>
       <div class="">
            <? $user->friends()->output('quick.friend'); ?>
       </div>
     </div>
     <div id="my_shops" class="profile_control column_6">
         <div class="">
             <h2>Your Shops</h2>
              <? while($shop= $my_shops->getNext()){
                    $shop->output('quick.shop');
              }?>
         </div>
     </div>
           <?if($own_profile){ ?>
      <div class="column_12">
        <div id="comment_on_reviews" class="profile_control">
                <h2>Recent Comments on your Reviews</h2>
                <? while($r= $recent_reactions->getNext()){
                        $r->output('short.comment');
                }?>
        </div>
     </div>
     <? }?>
</div>
<div class="clearer"></div>
<div class="column_12 last" id="profile_content">
		<? 	
			$offset = 0;
			if (isset($_GET['offset'])) {
				$offset = $_GET['offset'];
			}
			$docs = $user->POD->getContents(array('userId'=>$user->get('id')),null,20,$offset); 
			if ($user->get('tagline')) { 
				$tagline = $user->get('tagline');
			} else {
				$tagline = $user->get('nick') . "'s Posts";
			}
			$docs->output('short','header','pager',$tagline,$user->get('nick') . " hasn't posted anything yet.");
		?>	
	</div>
