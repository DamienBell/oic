<?php
/***********************************************
* This file is part of PeoplePods
* (c) xoxco, inc  
* http://peoplepods.net http://xoxco.com
*
* theme/content/short_body.php
* Defines the body output as included by short.php
*
* Documentation for this pod can be found here:
* http://peoplepods.net/readme/themes
/**********************************************/
?>
		<article class="attributed_content content_body faq <? if(!$POD->odd){echo "even";}?>">
				<header>
					<h2><? $doc->write('headline'); ?></h2>
				</header>

				<? if ($img = $doc->files()->contains('file_name','img')) { ?>
					<p class="content_image"><a href="<? $doc->write('permalink'); ?>"><img src="<? $img->write('resized') ?>" /></a></p>
                                        <div class="clearer"></div>
				<? } ?>			

				<? if ($doc->get('body')) { 
					$doc->writeFormatted('body');
				} ?>
				
				<div class="clearer"></div>
		</article>
