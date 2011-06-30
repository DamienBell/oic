<?php
/***********************************************
* This file is part of PeoplePods
* (c) xoxco, inc  
* http://peoplepods.net http://xoxco.com
*
* theme/groups/output.php
* Default output page for a group object
*
*
* Documentation for this pod can be found here:
* http://peoplepods.net/readme/group-object
/**********************************************/
?>
<?
	//variables
        $shop       = $group->groupname;
        $address    = $group->address;
        $state      = $group->state;
        $city       = $group->city;
        $zip        = $group->zipcode;
        $groupId    = $group->id;

        if($_GET['offset']){
            $offset= $_GET['offset'];
        }else{
            $offset= 0;
        }
        //stacks
        $members= $group->members();

        if($POD->isAuthenticated()){
            $new_review= $POD->getContent(array('type'=>'review', 'groupId'=>$groupId));
            $group->addFlag('last_viewed', $POD->currentUser(), date('Y-m-d H:i:s'));
        }
        $reviews= $POD->getContents(array('type'=>'review', 'groupId'=>$groupId), null, 10, $offset);
        $all_titles= $group->getAllReviewTitles();
?>
<div id="shop_output">

    <div class="outline"></div>
    <div id="shop_info" class="column_4">
            <span class="large"><?=$shop; ?></span><br />
                <?=$address; ?>
                <?=$city; ?>, <?=$state;?>     
    </div>

    <div class="column_4">
         <? $group->output('current.rating') ?>
    </div>
    
    <div class="column_3">
        <? $group->output('rating'); ?>
    </div>
  
    <div class="clearer"></div>

    <div id="add_review" class="green_grad">
    <?
       if($POD->isAuthenticated()){
           $new_review->output('add.review');
       }
    ?>
    </div>
    <div id="shop_table" class="column_12">
            <h2><a href="#" class="joinGroup" data-person="<?=$POD->currentUser()->id;?>" data-group="<?=$groupId; ?>">Join</a> the <?=$group->groupname;?> table</h2>
            <?
                while($m= $members->getNext()){
                    $m->output('table');
                }
            ?>
         <div class="clearer"></div>
    </div>

    <div id="recent_reviews" class="column_8">
    <? while($review= $reviews->getNext()){
            $review->output('review');
       }
    ?>
        <div id="pager">
        <?
           if($reviews->hasNextPage()){
                echo "<a href='?offset=".$reviews->nextPage()."' >Next<a/>";
           }
           if($reviews->hasPreviousPage()){
                echo "<a href='?offset=".$reviews->previousPage()."' >Previous<a/>";
           }
        ?>
        </div>
    </div>
    <div class="column_4">
        <div id="all_reviews">
            <h2>All reviews for <?=$shop;?></h2>
            <ul>
            <?
                $i;
                foreach($all_titles as $title){
                     if($i%2== 0){
                       $class= 'even';
                    }
                    else $class= '';

                    echo "<li class='$class'><p><a href='".$POD->siteRoot(false)."/show/".$title['stub']."'>".$POD->shorten($title['headline'], 30)."</a></p></li>";
                  
                    $i++;
                }
            ?>
            </ul>
        </div>
    </div>
</div>