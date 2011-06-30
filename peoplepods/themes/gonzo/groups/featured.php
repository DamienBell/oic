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

$reviews= $group->content(array('type'=>'review'));

$count = $reviews->totalCount();

if($count== 0){
    $review_msg= "no reviews yet, be the first!";
}
else{
    $review_msg= $count." reviews";
}
?>
<div id="featured">

	<header>
	<h1><? $group->permalink(); ?></h1>
            <span class="count_msg"><?=$review_msg; ?></span>
            <span class="rating">Avg Rating: </span>
	</header>
        
        <ul id="teaser_reviews">
            <?
              if($count > 0){
                  while($review= $reviews->getNext()){
                      $review->output('teaser');
                  }
              }
            ?>
        </ul>
	<div class="clearer"></div>
</div>
