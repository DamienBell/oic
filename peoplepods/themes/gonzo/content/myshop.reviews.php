<?php
/***********************************************
* This file is part of PeoplePods
* (c) xoxco, inc  
* http://peoplepods.net http://xoxco.com
*
* theme/content/short.php
* Default short template for content.
* Used by core_usercontent/list.php
*
* Documentation for this pod can be found here:
* http://peoplepods.net/readme/themes
/**********************************************/
?>	
    <? $doc->author()->output('avatar'); ?>
    <h3><a href="<?=$doc->permalink; ?>"><?=$doc->headline; ?></a></h3>
    <p>by <? $doc->author()->permalink(); ?></p>
    <div class="clearer"></div>
    <p class="tiny"><a href="<?=$doc->group()->permalink; ?>"><?=$doc->group()->groupname; ?></a></p>
		<div class="clearer"></div>

