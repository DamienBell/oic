<?php

?>
<div  id="map_hold" class="right">
     <div id="map"></div>
     <div id="manual_loc">
        <input type="search" name="manual_locate" placeholder="San Marcos, TX"/>
        <button id="manual_locate" >GO</button>
        <div class="clearer"></div>
        <span class="tiny">*scroll and zoom the map to find more awesome coffee shops</span>
        <?
           if($POD->isAuthenticated()){
                $user= $POD->currentUser();
           if($user->latitude && $user->longitude){ ?>
                <input type="hidden" name="latitude" value="<?=$user->latitude;?>" />
                <input type="hidden" name="longitude" value="<?=$user->longitude;?>" />
           <?}
           }?>
        <select id="jumper">
            <option>Jump to... </option>
            <option data-city="Austin" data-state="TX">Austin, TX </option>
            <option data-city="San Marcos" data-state="TX">San Marcos, TX </option>
            <option data-city="New Braunfels" data-state="TX">New Braunfels, TX</option>
        </select>
     </div>
     <div id="hideMap">
        <h2><a href="#hide_map" data-showmap="Hide Map" data-hiddenmap="Show Map">Hide Map</a></h2>
     </div>
</div>
<script type="text/javascript" >
$(document).ready(function(){
    $('#hideMap').click(function(){
        $('#map').toggle('slow', function(){
            if(($(this).css('display'))== 'none'){
                $('a[href="#hide_map"]').html('Show Map');
            }
            else{
                $('a[href="#hide_map"]').html('Hide Map');
            }
        });
    });
});
</script>