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
<li class="rnd_outline quick_friend">

	<a href="<? $user->write('permalink'); ?>"><img src="<?= $user->avatar(); ?>"  alt="<? $user->htmlspecialwrite('nick'); ?>" align="absmiddle" /></a>
 <h3><?=$POD->shorten($user->nick, 10); ?></h3>
  <div class="status rnd_outline right">

 </div>
 <div class="clearer"></div>
</li>
