<?
/*
  /map
 * This pod
 */
	// include the peoplepods library and instantiate a pod object
	require_once("../../PeoplePods.php");
	$POD = new PeoplePod(array('authSecret'=>@$_COOKIE['pp_auth'],'debug'=>0));

        //$POD->cacheflush();
        $featured= $POD->getGroups(array('description'=>'coffee_shop', 'featured'=>'featuring'))->getNext();

	$POD->header();?>

  <div id="shop_list" class="column_9">
      <img class="banner" src="<?$POD->templateDir()?>/img/banner.png" width="700" height="100" />
      <ul id="short_shop_description"></ul>
  </div>
  <div class="column_3"><?$POD->output('map', dirname(__FILE__)."/templates");?></div>


	<? $POD->footer(); ?>

<script type="text/javascript" src="http://maps.google.com/maps/api/js?sensor=false"></script>
<script type="text/javascript" src="http://www.google.com/jsapi"></script>
<script type="text/javascript" src="http://maps.google.com/maps/api/js?libraries=geometry&sensor=false"></script>
<script type="text/javascript" src="<?$POD->templateDir(); ?>/map.js"></script>

<script type="text/javascript">
$(document).ready(function(){
    startLoadWait();

    if($('input[name="latitude"]').val() && $('input[name="longitude"]').val()){
        var lat= $('input[name="latitude"]').val();
        var lng= $('input[name="longitude"]').val();
        var user_has_loc= true;
    }else{
        var user_has_loc= false;
    }

    //default to Austin for visitors
    if(!user_has_loc){
        createMap('map', 300, 300,null,null, function(){

        var initialLocation= {"city": "Austin", "st": "TX"};
        getShopsbyAddressThen(initialLocation, function(json){
            for(var i in json){
                plotShop(json[i]);
            }
            stopLoadWait();
            fitMapToMarkers();
            google.maps.event.addListener(map, 'idle', getShopsByMapBounds);
       });
    });
    }else{
        createMap('map', 300, 300,lat,lng, function(){
            google.maps.event.addListener(map, 'idle', getShopsByMapBounds);
            stopLoadWait();
        });
    }

    $('#manual_locate').click(function(){

         var address= $('input[name="manual_locate"]').val();
         clearExtraMarkers();
         $('#short_shop_description').html('');

            getCoordinatesByAddress(address, function(returnedGeo){
              
            var obj= {"city":"", "st":"", "zip":""}

            var result= returnedGeo[0].address_components;

            for(var index in result){

                var type= result[index].types[0];

                if(type== 'postal_code'){
                    obj.zip= result[index].long_name;
                }
                if(type== 'locality'){
                    obj.city= result[index].long_name;

                }
                if(type== 'administrative_area_level_1'){
                    obj.st= result[index].short_name; //we want the abbreviation here exp: TX vs Texas
                }
            }
            getShopsbyAddressThen(obj, function(json){
               for(var i in json){
                   plotShop(json[i]);
               }
               fitMapToMarkers();
            });
       });
    });

//the user use the <select> option to find shops
   $('#jumper').change(function(){
        clearExtraMarkers();
        $('#short_shop_description').html('');
        var city=  $('#jumper :selected').attr('data-city');
        var state= $('#jumper :selected').attr('data-state');

        var obj= {"city": city, "st": state};
        getShopsbyAddressThen(obj, function(json){
            for(var i in json){
                   plotShop(json[i]);
            }
            fitMapToMarkers();
        });
   });
   //when a user clicks the shop description it zooms incrmentally towards the
   //shop on the map.  It also opens the address bubble
    $('#short_shop_description li').live('click', function(){
        var index= $(this).index();
        var zoom = map.getZoom();

        if(zoom < 16){

         map.setZoom(zoom + 2);
         map.setCenter(extraMarkers[index].getPosition());
       }
       google.maps.event.trigger(extraMarkers[index], 'click');
       
    }); 
});

function upDateReviewsByShop(id){

    var url= siteRoot+"/server/get_shop_reviews?id="+id;
    $.getJSON(url, function(json){
        
        for(var i in json){

            appendTeaserReview(json[i]);
        }
    });
}

function appendTeaserReview(json){
    
        //recreate the teaser output
        var headline= "<h2 class='review_title'>${headline}</h2>";
        var avatar  = "<span><a href=${author_link}><img src='${avatar}' /></a></span>"
        var header  = "<header>"+headline+"<span class='content_meta'><span class='content_author'></span></span>"+avatar+"<p>${timesince}</p><div class='clearer'></div></header>";

        if(json.img){
            var img     = "<p class='content_image'><a href='${permalink}'><img src='${img}' /></a></p>";
        }
        else var img= '';
        var inner   = header+"<div class='clearer'></div>"+img+"${body}";
        var body    = "<article class='attributed_content content_body teaser'>"+inner+"<div class='clearer'></div></article>";
        var teaser  = "<li class='content_short content_review' id='content${id}'>"+body+"<div class='clearer'></div></li>";

         var t= $.template(teaser);
        $('#teaser_reviews').append(t, json);
}

//pass a json of the following values {'city': 'val', 'st': 'val', 'zip': 'val'}
function getShopsbyAddressThen(addressObj, callback){

    var q= "";
    //var url= siteRoot+"/server/get.shops_city_state";
	var url= "/server/get.shops_city_state";
    if(addressObj['city'] && addressObj['city']){
        q= q+"&city="+ addressObj['city'];
    }
     if(addressObj['st']){
        q= q+"&st="+ addressObj['st'];
    }
     if(addressObj['zip'] && addressObj['zip']){
        q= q+"&zip="+ addressObj['zip'];
    }

    $.getJSON(url+q, function(json){

        if(typeof(callback)== 'function'){
            callback(json);
        }
    });
}

//plot short description of shops on the map
function appendShopDescription(json){

    var atmos_avg=  json.atmos_avg * 10;
    var comm_avg=   json.comm_avg  * 10;
    var coff_avg=   json.coff_avg  * 10;

    if(atmos_avg && comm_avg && coff_avg){
        var pot_avg = Math.round((atmos_avg + comm_avg + coff_avg)/30);
        var pot_pic= "pot"+pot_avg+".png";
    }
    else { var pot_pic= "nopot.png"};

    var graph   = '<canvas id="canvas${id}" width="160" height="70" class="outline"></canvas>';
    var avg_data= 'data-coff_avg="${coff_avg}" data-comm_avg="${comm_avg}" data-atmos_avg="${atmos_avg}"';
    var pot     = '<img class="pot" src="'+themeRoot+'/img/pots/'+pot_pic+'" width="40" height="40"/>';
    var reviews = "<br /><span class='review_link'><a href='${permalink}'>${reviews} reviews</a></span>";
    var data    = avg_data+'data-id="${id}" data-link="${permalink}" data-address="${address}" data-city="${city}" data-state="${state}" data-zip="${zip}" data-shop="${groupname}"';
    var rating  = '<span>${rating}</span>';
    var shopinfo= '<li '+data+'><div class="info_container"><a href="${permalink}"><h2>'+shorten(json.groupname, 25)+'</h2></a> <br />'+graph+pot+reviews+'</div></li>';

    var t= $.template(shopinfo);
    $('#short_shop_description').append(t, json);

     var canvas = document.getElementById('canvas'+json.id);
     var context = canvas.getContext('2d');

     var atmos_color="rgb(63,10,219)";
     var comm_color ="rgb(0,255,106)";
     var coff_color ="rgb(155,117,12)";

     context.font= "12px";
     context.strokeStyle= "#888888";

    if(atmos_avg && comm_avg && coff_avg){

        context.fillStyle =atmos_color;
        var show_atmos= context.fillRect(0,10, atmos_avg, 10);

        context.strokeText("Atmosphere", 101, 20);

        //context.strokeStyle= comm_color;
        context.fillStyle = comm_color;
        var show_comm= context.fillRect(0,30, comm_avg, 10);
        context.strokeText("Community", 101, 40);

        //context.strokeStyle= coff_color;
        context.fillStyle = coff_color;
        var show_coff= context.fillRect(0, 50 , coff_avg, 10);
        context.strokeText("Coffee", 101, 60);
    }
    else{
        context.strokeText("No reviews yet", 50, 40);
    }
}

function scrollAnimate(index){
    var top_pic= $('#scroll li').eq(index);

    var next_pic= top_pic.nextAll();

    var distX= top_pic.width();
    var distY= top_pic.height();

    var topAnimation={"left": "+="+distX+"px",
                      "opacity": "0.5"
                     };
    top_pic.animate(topAnimation, "slow");
    next_pic.animate({"bottom": "+="+distY+"px"}, "slow", function(){
    
    });
    
}

</script>


