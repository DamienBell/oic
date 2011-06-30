<?php
/***********************************************
* This file is part of PeoplePods
* (c) xoxco, inc
* http://peoplepods.net http://xoxco.com
*
* theme/content/editform.php
* Default content add/edit form used by the core_usercontent module
* Customizing the fields in this form will alter the information stored!
* Use this file as the basis for new content type forms
*
* Documentation for this pod can be found here:
* http://peoplepods.net/readme/new-content-type
/**********************************************/
?>

<section id="editform" class="green">
    <h2 class="green_grad"><a href="#showForm">Click Here to add your Review</a></h2>
	<form class="valid" action="<? $doc->write('editpath'); ?>" method="post" id="post_something"  enctype="multipart/form-data">
            <div id="showForm">
		<? if ($doc->get('id')) { ?>
			<input type="hidden" name="id" value="<? $doc->write('id'); ?>" />
			<input type="hidden" name="redirect" value="<? $doc->write('permalink'); ?>" />
		<? } else if ($doc->get('groupId')) { ?>
			<input type="hidden" name="redirect" value="<? $this->group()->write('permalink'); ?>" />
		<? } ?>
		<? if ($doc->get('groupId')) { ?>
			<input type="hidden" name="groupId" value="<? $doc->write('groupId'); ?>" />
		<? } ?>
		<? if ($doc->get('type')) { ?>
			<input type="hidden" name="type" value="<? $doc->write('type'); ?>" />
		<? } ?>

			<label for="headline" id="edit_form_title">Title your review:</label>
			<ul id="post_options">
				<li class="post_option">
					<a href="#"  id="add_tags" onclick="return togglePostOption('tags');">+ Tags</a>
				</li>
			</ul>
			<textarea name="headline" id="headline" class="text required" required><? $doc->htmlspecialwrite('headline'); ?></textarea>

		<div class="clearer"></div>
		<p id="post_body">
			<label for="body">Review:</label>
			<textarea name="body" id="body" class="htmlarea text"><? $doc->htmlspecialwrite('body'); ?></textarea>
		</p>
		<p id="post_photo">
			<label for="photo">Image:</label>
			<input type="file" name="img" id="img" />
			<? if ($img = $doc->files()->contains('file_name','img')) { ?>
			<div id="file<?= $img->id; ?>" class="file">
				<a href="<?= $img->original_file; ?>"><img src="<? $img->write('thumbnail'); ?>" /></a>
				<a href="#deleteFile" data-file="<?= $img->id;?>">Delete</a>
			</div>
			<? } ?>
               </p>
		<p class="post_extra" id="post_tags"><label for="tags">Tags:</label>
		<input name="tags" id="tags" value="<? echo $doc->tagsAsString(); ?>" class="text" />
		(Separate tags with a space: monkeys robots ninjas)
		</p>

		<p>
			<input type="submit" id="editform_save" value="Save" />
		</p>
		<div class="clearer"></div>
            </div>
	</form>
	<div class="clearer"></div>
        
</section> <!-- end editform -->

<script type="text/javascript">
// display the appropriate fields in the edit form.
  	<? if ($doc->get('id') && $doc->tags()->count() > 0) { ?>
		togglePostOption('tags');
	<? } ?>

     $('a[href="#showForm"]').click(function(){
         $('#showForm').toggle('medium', function(){

             if($('#showForm').css('display') == 'block'){
                 $('textarea#headline').focus();
             }
         });
     });
        
</script>