<?php
/***********************************************
* This file is part of PeoplePods
* (c) xoxco, inc  
* http://peoplepods.net http://xoxco.com
*
* theme/people/short.php
* Default tempalte for short output of person object
* Used to create lists of people
*
* Documentation for this pod can be found here:
* http://peoplepods.net/readme/person-object
/**********************************************/
?>
<li class="person_table">
  <a href="<? $user->write('permalink'); ?>"><img src="<?= $user->avatar(); ?>"  alt="<? $user->htmlspecialwrite('nick'); ?>" /></a>
</li>
