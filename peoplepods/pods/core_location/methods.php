<?

/* -------------------------------------------------------------------------
core_location adds location based queries to PeoplePods.

When enabled, content, people, groups, files and comments will have an additional "location" field added to the database.
The location field uses MySQLs built in geometry and GIS functions to provide native support for location.
PeoplePods translates this field into a latitude and a longitude field, so you should never access the actual location field directly.

DETERMINE LOCATION:

$POD->geocodeIP() returns an object with geocoding information for the remote user's IP address


READ LOCATION OF OBJECT:

$content->latitude, $content->longitude
$person->latitude, $person->longitude
$group->latitude, $group->longitude
$file->latitude, $file->longitude
$comment->latitude, $comment->longitude


SET LOCATION OF OBJECT:

$content->setLocation($lat,$lon) sets the location and saves it to the database immediately
$person->setLocation($lat,$lon) sets the location and saves it to the database immediately
$group->setLocation($lat,$lon) sets the location and saves it to the database immediately
$file->setLocation($lat,$lon) sets the location and saves it to the database immediately
$comment->setLocation($lat,$lon) sets the location and saves it to the database immediately


QUERY BASED ON LOCATION:

$POD->getContentNear($query,$lat,$lon,$distance,$sort,$count,$offset) 
returns content matching $query within $distance miles of $lat, $lon 

$POD->getContentSortedByDistance($query,$lat,$lon,$sort,$count,$offset)
returns content matching $query, sorted its distance from $lat,$lon

$POD->getPeopleNear($query,$lat,$lon,$distance,$sort,$count,$offset) 
returns people matching $query within $distance miles of $lat, $lon 

$POD->getPeopleSortedByDistance($query,$lat,$lon,$sort,$count,$offset)
returns people matching $query, sorted its distance from $lat,$lon

$POD->getGroupsNear($query,$lat,$lon,$distance,$sort,$count,$offset) 
returns groups matching $query within $distance miles of $lat, $lon 

$POD->getGroupsSortedByDistance($query,$lat,$lon,$sort,$count,$offset)
returns groups matching $query, sorted its distance from $lat,$lon

$POD->getCommentsNear($query,$lat,$lon,$distance,$sort,$count,$offset) 
returns comments matching $query within $distance miles of $lat, $lon 

$POD->getCommentsSortedByDistance($query,$lat,$lon,$sort,$count,$offset)
returns comments matching $query, sorted its distance from $lat,$lon

$POD->getFilesNear($query,$lat,$lon,$distance,$sort,$count,$offset) 
returns files matching $query within $distance miles of $lat, $lon 

$POD->getFilesSortedByDistance($query,$lat,$lon,$sort,$count,$offset)
returns files matching $query, sorted its distance from $lat,$lon

------------------------------------------------------------------------- */

/* ----------------- */
/* Setter Functions  */
/* ----------------- */

function setLocation($obj,$latitude=null,$longitude=null) { 
	$obj->latitude = $latitude;
	$obj->longitude = $longitude;
	$obj->save();
}
Content::registerMethod('setLocation');
Person::registerMethod('setLocation');
File::registerMethod('setLocation');
Comment::registerMethod('setLocation');
Group::registerMethod('setLocation');


// Use FreeGeoIP.net to geocode the remote user's IP address
// $geo = $POD->geocodeIP();
// Returns an object with the following fields, which MAY BE BLANK
// latitude
// longitude
// city
// region_code (short state = TX)
// region_name (long state = Texas)
// country_name
// country_code
// metrocode
// zipcode
function geocodeIP($POD) {
	$ip = getenv('REMOTE_ADDR');

	// on a mac  with ipv6 turned on, localhost returns ::1 instead of the local ip or even 127.0.0.1
	if ($ip=='::1') {
		$ip = '';
	}
	return json_decode(file_get_contents("http://freegeoip.net/json/$ip"));
}
PeoplePod::registerMethod('geocodeIP');


function mysqlNeedsAsBinary($POD) {
	return $POD->libOptions('location_asbinary_required');
}
PeoplePod::registerMethod('mysqlNeedsAsBinary');



/* ----------------- */
/* Content Functions */
/* ----------------- */

// Get content objects that are within $distance miles of $lat,$lon
// $query is a standard set of stack parameters
// Usage:
// $content = $POD->getContentNear($query,$lat,$lon,$distance);
// Optionally include a different sort, count and offset:
// $content = $POD->getContentNear($query,$lat,$lon,$distance,'date desc',5,10);
function getContentNear($POD,$query,$lat,$lon,$distance,$sort='distance asc',$count=20,$offset=0) {
	
	list($south,$north,$west,$east) = $POD->getBoundingBox($lat,$lon,$distance);

	$poly = "Polygon(({$north} {$east},{$north} {$west},{$south} {$west},{$south} {$east},{$north} {$east}))";

	$query["d.location,MBRWithin(d.location,GeomFromText('${poly}'))"] = 1;
	

	return $POD->getContentSortedByDistance($query,$lat,$lon,$sort,$count,$offset);

}
PeoplePod::registerMethod('getContentNear');


function getContentSortedByDistance($POD,$query,$lat,$lon,$sort='distance asc',$count=20,$offset=0) { 
	

	$select = "SELECT DISTINCT d.*,(TIME_TO_SEC(TIMEDIFF(NOW(),d.date)) / 60) as minutes,";
	if ($POD->mysqlNeedsAsBinary()) { 
		$select .= "(GLength(LineStringFromWKB(LineString(AsBinary(d.location),AsBinary(GeomFromText('Point({$lat} {$lon})')))))) AS distance";
	} else { 
		$select .= "(GLength(LineStringFromWKB(LineString(d.location,GeomFromText('Point({$lat} {$lon})'))))) AS distance";
	
	}
	return new Stack($POD,'content',$query,$sort,$count,$offset,null,$select);

}
PeoplePod::registerMethod('getContentSortedByDistance');

/* ----------------- */
/* People Functions  */
/* ----------------- */

// Get content objects that are within $distance miles of $lat,$lon
// $query is a standard set of stack parameters
// Usage:
// $content = $POD->getContentNear($query,$lat,$lon,$distance);
// Optionally include a different sort, count and offset:
// $content = $POD->getContentNear($query,$lat,$lon,$distance,'date desc',5,10);
function getPeopleNear($POD,$query,$lat,$lon,$distance,$sort='distance asc',$count=20,$offset=0) {
	
	list($south,$north,$west,$east) = $POD->getBoundingBox($lat,$lon,$distance);

	$poly = "Polygon(({$north} {$east},{$north} {$west},{$south} {$west},{$south} {$east},{$north} {$east}))";

	$query["u.location,MBRWithin(u.location,GeomFromText('${poly}'))"] = 1;
	

	return $POD->getPeopleSortedByDistance($query,$lat,$lon,$sort,$count,$offset);

}
PeoplePod::registerMethod('getPeopleNear');


function getPeopleSortedByDistance($POD,$query,$lat,$lon,$sort='distance asc',$count=20,$offset=0) { 
	

	$select = "SELECT DISTINCT u.*,(TIME_TO_SEC(TIMEDIFF(NOW(),u.date)) / 60) as minutes,";
	if ($POD->mysqlNeedsAsBinary()) { 
		$select .= "(GLength(LineStringFromWKB(LineString(AsBinary(u.location),AsBinary(GeomFromText('Point({$lat} {$lon})')))))) AS distance";
	} else {
		$select .= "(GLength(LineStringFromWKB(LineString(u.location,GeomFromText('Point({$lat} {$lon})'))))) AS distance";
	}	
	return new Stack($POD,'user',$query,$sort,$count,$offset,null,$select);

}
PeoplePod::registerMethod('getPeopleSortedByDistance');



/* ----------------- */
/* Group  Functions  */
/* ----------------- */

// Get group objects that are within $distance miles of $lat,$lon
// $query is a standard set of stack parameters
// Usage:
// $content = $POD->getGroupsNear($query,$lat,$lon,$distance);
// Optionally include a different sort, count and offset:
// $content = $POD->getGroupsNear($query,$lat,$lon,$distance,'date desc',5,10);
function getGroupsNear($POD,$query,$lat,$lon,$distance,$sort='distance asc',$count=20,$offset=0) {
	
	list($south,$north,$west,$east) = $POD->getBoundingBox($lat,$lon,$distance);

	$poly = "Polygon(({$north} {$east},{$north} {$west},{$south} {$west},{$south} {$east},{$north} {$east}))";

	$query["g.location,MBRWithin(g.location,GeomFromText('${poly}'))"] = 1;
	

	return $POD->getGroupsSortedByDistance($query,$lat,$lon,$sort,$count,$offset);

}
PeoplePod::registerMethod('getGroupsNear');


function getGroupsSortedByDistance($POD,$query,$lat,$lon,$sort='distance asc',$count=20,$offset=0) { 
	

	$select = "SELECT DISTINCT g.*,(TIME_TO_SEC(TIMEDIFF(NOW(),g.date)) / 60) as minutes,";
	if ($POD->mysqlNeedsAsBinary()) { 
		$select .= "(GLength(LineStringFromWKB(LineString(AsBinary(g.location),AsBinary(GeomFromText('Point({$lat} {$lon})')))))) AS distance";
	} else {
		$select .= "(GLength(LineStringFromWKB(LineString(g.location,GeomFromText('Point({$lat} {$lon})'))))) AS distance";
	}
	return new Stack($POD,'group',$query,$sort,$count,$offset,null,$select);

}
PeoplePod::registerMethod('getGroupsSortedByDistance');



/* ----------------- */
/* Comment Functions */
/* ----------------- */

// Get comment objects that are within $distance miles of $lat,$lon
// $query is a standard set of stack parameters
// Usage:
// $content = $POD->getCommentsNear($query,$lat,$lon,$distance);
// Optionally include a different sort, count and offset:
// $content = $POD->getCommentsNear($query,$lat,$lon,$distance,'date desc',5,10);
function getCommentsNear($POD,$query,$lat,$lon,$distance,$sort='distance asc',$count=20,$offset=0) {
	
	list($south,$north,$west,$east) = $POD->getBoundingBox($lat,$lon,$distance);

	$poly = "Polygon(({$north} {$east},{$north} {$west},{$south} {$west},{$south} {$east},{$north} {$east}))";

	$query["c.location,MBRWithin(c.location,GeomFromText('${poly}'))"] = 1;
	

	return $POD->getCommentsSortedByDistance($query,$lat,$lon,$sort,$count,$offset);

}
PeoplePod::registerMethod('getCommentsNear');


function getCommentsSortedByDistance($POD,$query,$lat,$lon,$sort='distance asc',$count=20,$offset=0) { 
	

	$select = "SELECT DISTINCT c.*,(TIME_TO_SEC(TIMEDIFF(NOW(),c.date)) / 60) as minutes,";
	if ($POD->mysqlNeedsAsBinary()) { 
		$select .= "(GLength(LineStringFromWKB(LineString(AsBinary(c.location),AsBinary(GeomFromText('Point({$lat} {$lon})')))))) AS distance";
	} else {
		$select .= "(GLength(LineStringFromWKB(LineString(c.location,GeomFromText('Point({$lat} {$lon})'))))) AS distance";
	
	}
	return new Stack($POD,'comment',$query,$sort,$count,$offset,null,$select);

}
PeoplePod::registerMethod('getCommentsSortedByDistance');



/* ----------------- */
/* File Functions    */
/* ----------------- */

// Get file objects that are within $distance miles of $lat,$lon
// $query is a standard set of stack parameters
// Usage:
// $content = $POD->getFilesNear($query,$lat,$lon,$distance);
// Optionally include a different sort, count and offset:
// $content = $POD->getFilesNear($query,$lat,$lon,$distance,'date desc',5,10);
function getFilesNear($POD,$query,$lat,$lon,$distance,$sort='distance asc',$count=20,$offset=0) {
	
	list($south,$north,$west,$east) = $POD->getBoundingBox($lat,$lon,$distance);

	$poly = "Polygon(({$north} {$east},{$north} {$west},{$south} {$west},{$south} {$east},{$north} {$east}))";

	$query["f.location,MBRWithin(f.location,GeomFromText('${poly}'))"] = 1;
	

	return $POD->getFilesSortedByDistance($query,$lat,$lon,$sort,$count,$offset);

}
PeoplePod::registerMethod('getFilesNear');


function getFilesSortedByDistance($POD,$query,$lat,$lon,$sort='distance asc',$count=20,$offset=0) { 
	

	$select = "SELECT DISTINCT f.*,(TIME_TO_SEC(TIMEDIFF(NOW(),f.date)) / 60) as minutes,";
	if ($POD->mysqlNeedsAsBinary()) { 
		$select .= "(GLength(LineStringFromWKB(LineString(AsBinary(f.location),AsBinary(GeomFromText('Point({$lat} {$lon})')))))) AS distance";
	} else {
		$select .= "(GLength(LineStringFromWKB(LineString(f.location,GeomFromText('Point({$lat} {$lon})'))))) AS distance";	
	
	}
	return new Stack($POD,'file',$query,$sort,$count,$offset,null,$select);

}
PeoplePod::registerMethod('getFilesSortedByDistance');






// getBoundingBox
// hacked out by ben brown <ben@xoxco.com>
// http://xoxco.com/clickable/php-getboundingbox

// given a latitude and longitude in degrees (40.123123,-72.234234) and a distance in miles
// calculates a bounding box with corners $distance_in_miles away from the point specified.
// returns $min_lat,$max_lat,$min_lon,$max_lon 
function getBoundingBox($POD,$lat_degrees,$lon_degrees,$distance_in_miles) {

	$radius = 3963.1; // of earth in miles

	// bearings	
	$due_north = 0;
	$due_south = 180;
	$due_east = 90;
	$due_west = -90;

	// convert latitude and longitude into radians 
	$lat_r = deg2rad($lat_degrees);
	$lon_r = deg2rad($lon_degrees);
		
	// find the northmost, southmost, eastmost and westmost corners $distance_in_miles away
	// original formula from
	// http://www.movable-type.co.uk/scripts/latlong.html

	$northmost  = asin(sin($lat_r) * cos($distance_in_miles/$radius) + cos($lat_r) * sin ($distance_in_miles/$radius) * cos($due_north));
	$southmost  = asin(sin($lat_r) * cos($distance_in_miles/$radius) + cos($lat_r) * sin ($distance_in_miles/$radius) * cos($due_south));
	
	$eastmost = $lon_r + atan2(sin($due_east)*sin($distance_in_miles/$radius)*cos($lat_r),cos($distance_in_miles/$radius)-sin($lat_r)*sin($lat_r));
	$westmost = $lon_r + atan2(sin($due_west)*sin($distance_in_miles/$radius)*cos($lat_r),cos($distance_in_miles/$radius)-sin($lat_r)*sin($lat_r));
		
		
	$northmost = rad2deg($northmost);
	$southmost = rad2deg($southmost);
	$eastmost  = rad2deg($eastmost);
	$westmost  = rad2deg($westmost);
		
	// sort the lat and long so that we can use them for a between query		
	if ($northmost > $southmost) { 
		$lat1 = $southmost;
		$lat2 = $northmost;
	
	} else {
		$lat1 = $northmost;
		$lat2 = $southmost;
	}


	if ($eastmost > $westmost) { 
		$lon1 = $westmost;
		$lon2 = $eastmost;
	
	} else {
		$lon1 = $eastmost;
		$lon2 = $westmost;
	}
	
	return array($lat1,$lat2,$lon1,$lon2);
}

PeoplePod::registerMethod('getBoundingBox');




/* -------------------------------------------------------------------------
These functions tell PeoplePods how to handle the extended schema, in terms of storing and retrieving the values.

a new REAL field called location is added to content, people, groups, comments and files.
2 "fake" fields called latitude and longitude are added to all the same objects.

a "select helper" is added to each object that tells PeoplePods to split the location field into 2 values (lat and lon)
an "insert helper" is added to each object that tells PeoplePods to combine lat and lon into the location field in the db.
------------------------------------------------------------------------- */

Content::addDatabaseFields(array('location'=>array('select'=>'locationToLatLon','insert'=>'latLonToLocation')));
Content::addIgnoreFields(array('distance','latitude','longitude'));

Person::addDatabaseFields(array('location'=>array('select'=>'locationToLatLon','insert'=>'latLonToLocation')));
Person::addIgnoreFields(array('distance','latitude','longitude'));

Group::addDatabaseFields(array('location'=>array('select'=>'locationToLatLon','insert'=>'latLonToLocation')));
Group::addIgnoreFields(array('distance','latitude','longitude'));

Comment::addDatabaseFields(array('location'=>array('select'=>'locationToLatLon','insert'=>'latLonToLocation')));
Comment::addIgnoreFields(array('distance','latitude','longitude'));

File::addDatabaseFields(array('location'=>array('select'=>'locationToLatLon','insert'=>'latLonToLocation')));
File::addIgnoreFields(array('distance','latitude','longitude'));


// return the select modifiers for the location field
function locationToLatLon($obj,$field) {
	return array(
		'X(' . $obj->table_shortname() . '.location) as latitude',
		'Y(' . $obj->table_shortname() . '.location) as longitude',
	);
}



// return the insert modifier for the location field
function latLonToLocation($obj,$field,$value) {

	if (isset($obj->latitude) && isset($obj->longitude)) {
	
		return array(
			"location=GeomFromText('Point(" . $obj->latitude . " " . $obj->longitude . ")')",
		);
	} else {
		// NOTE:
		// this is INCOMPATIBLE with a spatial index
		// if a spatial index has been created, SOME real geometry point must be used!
		return array(
			"location=''"
		);
	}
}



/* -------------------------------------------------------------------------
These functions tell PeoplePods how to turn the location functionality on.

locationInstall() is called when this pod is turned on. It causes the location fields to be added to the schema.

locationUninstall() is called when the pod is turned off.  It does nothing - we don't want to delete data automatically.

locationSetup() returns a list of global variable names that will be made available in the settings menu for this plugin within in PeoplePods.
------------------------------------------------------------------------- */


function locationSetup() { 

	return array(
		'location_asbinary_required'=>'If you are using a version of MySql < 5.5, put a 1 in this field. Otherwise, leave blank.',
	);

}

function locationInstall($POD) {

	$messages = array();
		
	$queries = array(
		"ALTER TABLE content ADD location point not null",
		"ALTER TABLE users ADD location point not null",
		"ALTER TABLE groups ADD location point not null",
		"ALTER TABLE comments ADD location point not null",
		"ALTER TABLE files ADD location point not null",
// Indexes are NOT automatically created, because they require every row to have a real value
// and there is no good way to set a default value that would not theoretically cause problems
// IE, setting latitude/longitude to 0x0 has a real meaning (middle of the ocean) that interferes with mapping
//		"ALTER TABLE content ADD spatial index(location)",
//		"ALTER TABLE users ADD spatial index(location)",
//		"ALTER TABLE groups ADD spatial index(location)",
//		"ALTER TABLE comments ADD spatial index(location)",
//		"ALTER TABLE files ADD spatial index(location)",

	);

	foreach ($queries as $sql) {
		$res = $POD->executeSQL($sql);
		if (!$res) {
			$messages[] = 'core_location: ' . mysql_error($POD->DATABASE);
		} 
	}
	
	if (sizeof($messages) == 0) {		
		$messages[] = 'Successfully enabled on core_location and completed schema updates.';
	} 
	
	return implode("<BR />",$messages) ."<Br />";

}

function locationUninstall() {
	return "core_location disabled.  Location fields are still present in the database! You may want to manually remove them.<Br />";

}


