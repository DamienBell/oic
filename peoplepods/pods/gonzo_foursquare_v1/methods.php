<?

	function fourSquareSearchVenues($POD,$lat,$lon,$q=null) {
	
		if ($q!=null && $q !='') {
			$command = 'http://api.foursquare.com/v1/venues.json?geolat='. $lat . '&geolong='. $lon . '&q='. $q;
		} else {
			$command = 'http://api.foursquare.com/v1/venues.json?geolat='. $lat . '&geolong='. $lon;		
		}
		$json= file_get_contents($command);
		return $json;
	}
	
	function fourSquareVenue($POD,$vid) { 
	
		$command = 'http://api.foursquare.com/v1/venue.json?vid='.$vid;
		$json = file_get_contents($command);
		return $json;
	
	}
	
	PeoplePod::registerMethod('fourSquareSearchVenues');
	PeoplePod::registerMethod('fourSquareVenue');