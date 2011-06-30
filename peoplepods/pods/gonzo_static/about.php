<?

	// include the peoplepods library and instantiate a pod object
	require_once("../../PeoplePods.php");
	$POD = new PeoplePod(array('authSecret'=>@$_COOKIE['pp_auth'],'debug'=>0));

 $about= $POD->getContent(array('stub'=>'the-story'));

 $POD->header();
?>
<div class="column_12">
    <img width="860" height="300" />
</div>
<article class="paper column_8">
    <header>
        <h2 class="center_align"><?=$about->headline;?></h2>
    </header>
    <section class="review">
        <? $about->writeFormatted('body')?>
    </section>
</article>

<?
	$POD->footer();
?>