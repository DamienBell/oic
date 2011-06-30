<?

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

<form id="add_shop" class="column_4" name="add_shop" method="post" action="<?$POD->siteRoot()?>/add_shop">
    <p><label for="shop">Shop Name     <input id="shop" type="text" name="shop" required/></label></p>
    <p><label for="state">State        <input id="state" type="text" name="state" required/></label></p>
    <p><label for="city">City          <input   id="city" type="text" name="city" /></label></p>
    <p><label for="address">address    <input id="address" type="text" name="address" /></label></p>
    <p><label for="zipcode">zipcode    <input id="zipcode" type="text" name="zipcode" /></label></p>
    <p><label for="latitude">latitude  <input id="latitude" type="text" name="latitude" required/></label></p>
    <p><label for="longitude">longitude<input id="longitude" type="text" name="longitude" required /></label></p>
    <p><label for="test_address">test_address<input type="text" name="test_address" required/></label></p>
    <input type="submit" />   
</form>
<div class="column_4">
    <div id="map"></div>
    <button id="test_address"><h2>Test Geo</h2></button>
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
    createMap('map', 300, 300, null, null, function(){
        createHomeMarker(function(){
            homeMarker.setPosition(map.getCenter());
        });

    });

     //Test address must be clicked prior to submission to set the lat/lng coordinates
    $('#test_address').click(function(){

        var state  = $('input[name="state"]').val();
        var city   = $('input[name="city"]').val();
        var address= $('input[name="address"]').val();
        var zip    = $('input[name="zipcode"]').val();

        var string= address+', '+city+', '+state+' '+zip;
        $('input[name="test_address"]').val(string);

        getCoordinatesByAddress(string, function(results){

            var latitude=results[0].geometry.location.lat();
            var longitude= results[0].geometry.location.lng();
            var latLng= new google.maps.LatLng(latitude, longitude);

            $('input[name="latitude"]').val(latitude);
            $('input[name="longitude"]').val(longitude);

            homeMarker.setPosition(latLng);
            map.setCenter(latLng);
        });
    });

});
</script>
