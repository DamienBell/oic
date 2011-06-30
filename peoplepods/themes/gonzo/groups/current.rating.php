<?
    if($group->getAvgRating('coffee')){
        $pot_avg= "pot".round((($group->getAvgRating('atmosphere') + $group->getAvgRating('community') + $group->getAvgRating('coffee'))/3), 0);
    }
    else $pot_avg= "nopot";
?>
<div class="rating_box" data-boxID="<?=$group->id; ?>">
        <canvas id="canvas<?=$group->id; ?>" width="160" height="70" class="outline">

        </canvas><img src="<?$POD->templateDir(); ?>/img/pots/<?=$pot_avg; ?>.png" width="50" height="50"/>
</div>
<input type="hidden" name="atmos_avg<?=$group->id; ?>" value="<?=$group->getAvgRating('atmosphere'); ?>" />
<input type="hidden" name="comm_avg<?=$group->id; ?>" value="<?=$group->getAvgRating('community'); ?>" />
<input type="hidden" name="coff_avg<?=$group->id; ?>" value="<?=$group->getAvgRating('coffee'); ?>" />

