<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
$parent= $comment->parent();
?>
<a name="<? $comment->write('id'); ?>"></a>
<div class="comment <? if ($comment->get('isOddItem')) {?>comment_odd<? } ?> <? if ($comment->get('isEvenItem')) {?>comment_even<? } ?> <? if ($comment->get('isLastItem')) {?>comment_last<? } ?> <? if ($comment->get('isFirstItem')) {?>comment_first<? } ?>" id="comment<? $comment->write('id'); ?>">
	<? $comment->author()->output('avatar'); ?>
	<div class="attributed_content comment_body">
		<span class="byline">
			<? if ($comment->POD->isAuthenticated() && ($comment->parent('userId') == $comment->POD->currentUser()->get('id') || $comment->get('userId') == $comment->POD->currentUser()->get('id'))) { ?>
				<span class="gray remove_comment"><a href="#deleteComment" data-comment="<? $comment->write('id'); ?>">Remove Comment</a></span>
			<? } ?>
			<span class="author"><? $comment->author()->permalink(); ?></span> commented, on <a href="<?=$parent->permalink; ?>"><?=$parent->headline; ?></a> (<span class="post_time"><? echo $this->POD->timesince($comment->get('minutes')); ?></span>)
		</span>
		<? $comment->writeFormatted('comment') ?>
	</div>
	<div class="clearer"></div>
</div>