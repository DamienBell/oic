//Globals
var map;
var homeMarker;
var extraMarkers= [];
var infoWindow;

// create a map
function createMap(id,h, w,lat, lng, callback){

  var mapDiv = document.getElementById(id);
  $("#"+id).height(h+"px");
  $("#"+id).width(w+"px");
 
  if(!lat || !lng){
      //Austin, TX
      lat= 30.27333052;
      lng= -97.7411789;
  }
 
  var latlng = new google.maps.LatLng(lat, lng);
  var bounds = new google.maps.LatLngBounds();
  var options= {

        center: latlng,
        zoom:   11,
        mapTypeId: google.maps.MapTypeId.HYBRID,
        navigationalControl: true,
        navigationalControlOptions: {
            style: google.maps.NavigationControlStyle.ZOOM_PAN
        },
        scaleControl: true,
        backgroundColor: '#e9fcf3'
      };
  map= new google.maps.Map(mapDiv, options);

  if(typeof(callback)== 'function'){
      google.maps.event.addListenerOnce(map, 'tilesloaded', callback);
  }
}


function createHomeMarker(callback){

  debug('creating home marker');
  var latlng= new google.maps.LatLng(map.getCenter());

  homeMarker= new google.maps.Marker({
              position: latlng,
              map: map,
              draggable: true,
              title: 'home',
              icon: 'http://gmaps-samples.googlecode.com/svn/trunk/markers/blue/blank.png'
    });

  if(typeof(callback)== 'function'){
      callback();
  }

}
////////********** Location Functions **********************/////////////////
//
//
//This function will check for hidden inputs latitude, longitude set by the server
//if those inputs are not set it will try google ClientLocation
//failing that it will use HTML5 location
function autoLocate(callback){
        debug('autoLocate');
        (function(){
            
            if (google && google.loader && google.loader.ClientLocation && google.loader.ClientLocation.latitude && google.loader.ClientLocation.longitude){

                   var latLng= new google.maps.LatLng(google.loader.ClientLocation.latitude,
                                                   google.loader.ClientLocation.longitude);

                   updateHomeMarker(LatLng);

                   if(typeof(callback)== 'function'){
                       callback();
                   }
                   debug(google.loader.ClientLocation.address.city);
            }
            else {
                debug('ClientLocation error: using HTML5');
                navigator.geolocation.getCurrentPosition(function(position) {
                
                    var latitude= position.coords.latitude;
                    var longitude= position.coords.longitude;

                    var latLng= new google.maps.LatLng(latitude, longitude);
                    updateHomeMarker(latLng);

                    if(typeof(callback)== 'function'){
                            callback();
                    }
                });
           }
        })();
}

function getCoordinatesByAddress(address, callback){

    var geocoder; 
    if(!geocoder){
        geocoder= new google.maps.Geocoder();
    }
    var geocoderRequest= {
            address: address
        };
    //actual call to retrieve address
    geocoder.geocode(geocoderRequest, function(results, status){
        
    if(status== google.maps.GeocoderStatus.OK){
       debug('found a location');
     
       if(typeof(callback)== 'function'){
                callback(results);
       }
       else{
           debug('no callback to getCoordinatesByAddress');
       }
    }
    else{
         debug('problem with geocoder request');
        }
    });

};

///******** Map Properties  ***********************////////////////////////////

//Determine the miles across represented on the map
function getWidthInMiles(){
    var bounds= map.getBounds();

    var east    = bounds.getNorthEast().lng();
    var north   = bounds.getNorthEast().lat();
    var west    = bounds.getSouthWest().lng();

    var ne= new google.maps.LatLng(north, east);
    var nw= new google.maps.LatLng(north, west);

    var meters= google.maps.geometry.spherical.computeDistanceBetween(ne, nw);
    var miles = Math.round(meters * 0.000621371192);

    if(miles < 1){
        miles= 1;
    }

    //divide miles by two since the radius is calculated from the center of the map
    return (miles/2);
}
///******* Marker Functions **********************////////////////////////////
//
//
//this is intended for general markers and will be placed in the extraMarkers array
function addMarker(LatLng){
    var marker= new google.maps.Marker({
                position: LatLng,
                map: map,
                title: "a title"
    });
    extraMarkers.push(marker);
};

//clearExtraMarkers
function clearExtraMarkers(){
 if(extraMarkers.length > 0){
     for(var m in extraMarkers){
        extraMarkers[m].setMap();
    }
    extraMarkers= [];
 }
}

//read in all markers, adjust bounds to fit those markers
function fitMapToMarkers(callback){

    var bounds= new google.maps.LatLngBounds();

    if(extraMarkers.length > 0){

        for(var m in extraMarkers){
            var lat= extraMarkers[m].getPosition().lat();
            var lng= extraMarkers[m].getPosition().lng();
            var pnt= new google.maps.LatLng(lat, lng);
           
            bounds.extend(pnt);
        }
        map.fitBounds(bounds);
    }

    if(typeof(callback)== 'function'){
        callback();
    }
}

/* additional functions for GonzoBeans */
/*****************************************************************************/
/*****************************************************************************/

function getShopsByGlobalLatLng(callback){

        clearExtraMarkers();

        var url= siteRoot+"/server/get_shops_nearby&lat="+map.center.lat()+"&lng="+map.center.lng();

        $.getJSON(url, function(json){

        if(json.results != '')
            for(var i in json){
                var latitude=  json[i].latitude;
                var longitude= json[i].longitude;
                var latLng= new google.maps.LatLng(latitude, longitude);
                plotShop(latLng, json[i]);
            }
            
            if(typeof(callback)== 'function'){
                callback();
            }
        });
}

//this function looks at the length of the map in miles and returns shops based
//with fall within those bounds
function getShopsByMapBounds(){

     var distance= getWidthInMiles();
     //var url= siteRoot+"/server/get_shops_within&lat="+map.getCenter().lat()+"&lng="+map.getCenter().lng()+"&distance="+distance;
	var url="/server/get_shops_within&lat="+map.getCenter().lat()+"&lng="+map.getCenter().lng()+"&distance="+distance;

     $.getJSON(url, function(json){
         
           if(json[0] != 'no results'){

            for(var i in json){

                var latitude=  json[i].latitude;
                var longitude= json[i].longitude;

                var found= false;
   
                //check markers array if not already present then plot it
                for(var checking in extraMarkers){
                        if(extraMarkers[checking].position.lat()== latitude && extraMarkers[checking].position.lng()){
                            found= true;
                        }
                }
                if(found== false){          
                        plotShop(json[i]);
                 }
            }
           }

            if(typeof(callback)== 'function'){
                callback();
            }
     });
}

//variation of addMarkers to add gonzobeans specific functionality
function plotShop(json){

    var LatLng= new google.maps.LatLng(json.latitude, json.longitude);
    
    var marker= new google.maps.Marker({
                position: LatLng,
                map: map,
                title: json.groupname
                //icon: icon
    });

    appendShopDescription(json);

    google.maps.event.addListener(marker, 'click', function(){

        google.maps.event.clearListeners(map, 'idle');
        
        if(!infoWindow){
            infoWindow= new google.maps.InfoWindow();
        }

        var content= "<h2><a href='"+json.permalink+"'>"+json.groupname+"</a></h2><p>"+json.address+"<br />"+json.city+", "+json.state+" "+json.zipcode+"</p>";


        infoWindow.setContent(content);
        infoWindow.open(map, marker);
        
        google.maps.event.addListenerOnce(map, 'idle', function(){
            google.maps.event.addListener(map, 'idle', getShopsByMapBounds);
        });
    });

    extraMarkers.push(marker);
}

