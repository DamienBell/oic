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
		<article class="attributed_content content_body teaser">
				<header>
                                        <h2 class="review_title"><?=$doc->headline; ?></h2>
					<span class="content_meta">
						<span class="content_author"><? $doc->author()->permalink(); ?></span>
                                                  <span><? $doc->author()->output('avatar'); ?></span>
                                                  <p><?$doc->write('timesince'); ?> </p>
                                        </span><div class="clearer"></div>
				</header>
                                <div class="clearer"></div>
			
				<? if ($img = $doc->files()->contains('file_name','img')) { ?>
					<p class="content_image"><a href="<? $doc->write('permalink'); ?>"><img src="<? $img->write('thumbnail') ?>" /></a></p>
				<? } ?>
                                    <? if ($doc->get('body')) {
                                            //echo substr($doc->body, 0, 200);
                                            $doc->writeFormatted('body');
                                    }?>
				<div class="clearer"></div>
		</article>
