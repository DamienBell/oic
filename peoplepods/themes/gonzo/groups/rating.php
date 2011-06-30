<? $user= $POD->currentUser();

   $atmos_flag= $group->hasFlag('atmosphere', $user);
   $comm_flag = $group->hasFlag('community', $user);
   $coff_flag = $group->hasFlag('coffee', $user);
?>

<? if($POD->isAuthenticated()){ ?>
    <h3><a href="#showrate">Rate this Shop</a></h3>

    <div class="rating_form">
        <p>
            <label for="atmosphere">Atmosphere</label>
            <input id="atmosphere" name="atmosphere" type="range" min="1"  max="10" step="1" value="<? if($atmos_flag){echo $atmos_flag;}else{ ?>5<? } ?>"><span class="littlebox atmosphere_box"><? if($atmos_flag){echo $atmos_flag;}else{ ?>5<? } ?></span>
        </p>

        <p>
            <label for="community">Community</label>
            <input id="community" name="community" type="range" min="1"  max="10" step="1" value="<? if($comm_flag){echo $comm_flag;}else{ ?>5<? } ?>"><span class="littlebox community_box"><? if($comm_flag){echo $comm_flag;}else{ ?>5<? } ?></span>
        </p>

        <p>
            <label for="coffee">Coffee</label>
            <input id="coffee" name="coffee" type="range" min="1"  max="10" step="1" value="<? if($coff_flag){echo $coff_flag;}else{ ?>5<? } ?>"><span class="littlebox coffee_box"><? if($coff_flag){echo $coff_flag;}else{ ?>5<? } ?></span>

            <button id="rateshop">Vote</button>
        <p/>
    </div>
<? }else{ ?>
    <h3><a href="<?$POD->siteRoot();?>/login">Login</a> or<a href="<?$POD->siteRoot(); ?>/login">Join</a> to rate this Shop</h3>
     <? } ?>
<script type="text/javascript">

$(document).ready(function(){
        //hide form initially
        $('.rating_form').hide();

        //show form
        $('a[href="#showrate"]').click(function(){

            $('.rating_form').slideToggle('medium');
        });
        //adjust value boxes
        $('input[name="atmosphere"], input[name="community"], input[name="coffee"]' ).change(function(){
            var box= $(this).attr('name')+"_box";
            $('.'+box).html($(this).val());
        });

        $('#rateshop').click(function(){

            var data= $('input[name="atmosphere"], input[name="community"], input[name="coffee"], input[name="groupId"]' ).serialize();
            var url= siteRoot+"/server/rate_shop&"+data;

            $.getJSON(url, function(json){
                debug(json);
                if(json.msg== 'success'){
                    complain("Thanks!", 'success');
                    $('.rating_form').hide('medium');
                }
            });

        });
});

</script>