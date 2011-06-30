<?
/*
 * the intention here is to pull shops via foursquare and then sort through them
 * to ensure they are acceptable before saving them.  This can be used to initiate
 * a city.
 */

	// include the peoplepods library and instantiate a pod object
	require_once("../../PeoplePods.php");
	$POD = new PeoplePod(array('authSecret'=>@$_COOKIE['pp_auth'],'debug'=>0));

        if($_POST){
            //print_r($_POST);

            $shop= $POD->getGroup();
            $shop->set('groupname', $_POST['shop']);
            $shop->set('description', "coffee_shop");//shop groups are differentiated from other groups by this description
            $shop->save();
            if($shop->success()){
                $shop->setLocation($_POST['latitude'], $_POST['longitude']);
                
                if($shop->success()){
                    $POD->addMessage('successfully save shop with location data');
                    
                    if($_POST['state']){
                        $shop->state= $_POST['state'];
                    }
                    if($_POST['city']){
                        $shop->city= $_POST['city'];
                    }
                    if($_POST['address']){
                        $shop->address= $_POST['address'];
                    }
                    if($_POST['zipcode']){
                        $shop->zipcode= $_POST['zipcode'];
                    }
                }
                else{
                    $POD->addMessage("Error with location save");
                }
            }
            else{
                $POD->addMessage("Error with initial save");
            }
        }
	$POD->header();
?>

<form id="add_shop" class="column_4" name="add_shop" method="post" action="<?$POD->siteRoot()?>/via4">
    <p><label for="shop">Shop Name     <input id="shop" type="text" name="shop" required/></label></p>
    <p><label for="state">State        <input id="state" type="text" name="state" required/></label></p>
    <p><label for="city">City          <input   id="city" type="text" name="city" /></label></p>
    <p><label for="address">address    <input id="address" type="text" name="address" /></label></p>
    <p><label for="zipcode">zipcode    <input id="zipcode" type="text" name="zipcode" /></label></p>
    <p><label for="latitude">latitude  <input id="latitude" type="text" name="latitude" required/></label></p>
    <p><label for="longitude">longitude<input id="longitude" type="text" name="longitude" required /></label></p>
    <p><label for="test_address">test_address<input type="text" name="test_address"/></label></p>
    <input type="submit" />   
</form>
<div class="column_4">
    <div id="map"></div>
    <button id="test_address"><h2>Go To</h2></button>
    <button id="get_squares"><h2>Get Squares</h2></button>
</div>
<div class="column_4">
    <ul id="fourlist"></ul>
</div>

<?
	$POD->footer();
?>

<script type="text/javascript" src="http://maps.google.com/maps/api/js?sensor=false"></script>
<script type="text/javascript" src="http://www.google.com/jsapi"></script>
<script type="text/javascript" src="<? $POD->templateDir(); ?>/map.js"></script>

<script type="text/javascript">
    $(document).ready(function (){

    //style and map
    $('input[name="test_address"]').width('300px')

    //in this case fill the lat/lng fields because they are relevant every time
    createMap('map', 300, 300, function(){
       $('input[name="latitude"]').val(cur_lat);
       $('input[name="longitude"]').val(cur_lng);

       createHomeMarker();
    });

    google.maps.event.addListener(homeMarker, 'dragend', function(){
        
        updateHomeMarker(homeMarker.position, function(){
             $('input[name="latitude"]').val(cur_lat);
             $('input[name="longitude"]').val(cur_lng);
             getShopsBySquare(cur_lat, cur_lng);
        });

    });
     //Test address must be clicked prior to submission to set the lat/lng coordinates
    $('#test_address').click(function(){

        var state  = $('input[name="state"]').val();
        var city   = $('input[name="city"]').val();
        var address= $('input[name="address"]').val();
        var zip    = $('input[name="zipcode"]').val();

        var string= address+' '+city+' '+state+' '+zip;
        $('input[name="test_address"]').val(string);

        getCoordinatesByAddress(string, function(results){

            var latitude=results[0].geometry.location.lat();
            var longitude= results[0].geometry.location.lng();

            updateHomeMarker(results[0].geometry.location, function(){
                $('input[name="latitude"]').val(cur_lat);
                $('input[name="longitude"]').val(cur_lng);
                //var latLng= new google.maps.LatLng(cur_lat, cur_lng);
                map.setCenter(results[0].geometry.location);
                homeMarker.setPosition(results[0].geometry.location);
            });
            
        });
    });

    //bind clicks to
    $('a[href="#fillFourValues"]').live('click', function(){

        var data= $(this).parent();//this is the list item with the data-values

        var shop   = data.attr('data-shop');
        var lat    = data.attr('data-lat');
        var lng    = data.attr('data-long');
        var address= data.attr('data-address');
        var city   = data.attr('data-city');
        var state  = data.attr('data-state');
        var zip    = data.attr('data-zip');

        $('input[name="shop"]').val(shop);
        $('input[name="latitude"]').val(lat);
        $('input[name="longitude"]').val(lng);
        $('input[name="address"]').val(address);
        $('input[name="city"]').val(city);
        $('input[name="state"]').val(state);
        $('input[name="zipcode"]').val(zip);

        if(lat && lng){
            var latLng= new google.maps.LatLng(lat, lng);
            map.setCenter(latLng);
        }


    });

    $('#get_squares').click(function(){

       var lat= $('input[name="latitude"]').val();
       var lng= $('input[name="longitude"]').val();

       if(lat && lng){
         getShopsBySquare(lat, lng)
       }else{
           debug('no lat or lng values');
       }
    });
});



function getShopsBySquare(lat, lng){

    var lat= cur_lat;
    var lng= cur_lng;
    clearExtraMarkers();
    $('#fourlist').html('');

    var url= siteRoot+"/server/via_4square&lat="+lat+"&lng="+lng;

    //get foursquare shops, plot them
    $.getJSON(url, function(json){
        var venues= json.groups[0].venues;
        //n= venues;
        var four_template= '<li data-id="${id}" data-lat="${geolat}" data-long="${geolong}" data-address="${address}" data-city="${city}" data-state="${state}" data-zip="${zip}" data-shop="${name}"><a href="#fillFourValues">${name}</a></li>';
        var t = $.template(four_template);

        for(var g in venues){
            
            var venue= venues[g];
            $('#fourlist').append(t, venue);
            var latLng= new google.maps.LatLng(venue.geolat, venue.geolong);
            addMarker(latLng);
        }
        fitMapToMarkers();
    });

}
</script>
