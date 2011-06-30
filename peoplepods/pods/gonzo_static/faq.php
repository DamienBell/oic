<?

	// include the peoplepods library and instantiate a pod object
	require_once("../../PeoplePods.php");
	$POD = new PeoplePod(array('authSecret'=>@$_COOKIE['pp_auth'],'debug'=>0));

 $faqs= $POD->getContents(array('type'=>'faq'));

 //print_r($faqs);

 $POD->header();?>

<div class="column_6">
    <?
     $i= 0;
     while($f= $faqs->getNext()){

         if($i%2== 0){
             $POD->odd= false;
         }else{$POD->odd= true;}

         $f->output('faq');
         $i++;
     }?>
</div>
<?	$POD->footer();?>
