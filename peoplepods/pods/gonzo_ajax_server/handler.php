<?
	/* This is the JSON server nonjudgment
	/* Lives at /server
	/* Access by /server/methodname
	*/

	require_once("../../PeoplePods.php");
	$POD = new PeoplePod(array('authSecret'=>@$_COOKIE['pp_auth']));


	$method = clean($_GET['command']);
        //clean($_GET);
	switch ($method) {

/******************************************************************************************************
/* Return all shops */
    case 'get_shops_nearby';

    if($_GET['lat']){
        $lat= $_GET['lat'];
    }
    if($_GET['lng']){
        $lng= $_GET['lng'];
    }
    $distance= 10;
    //$shops= $POD->getGroups($query);
    $shops= $POD->getGroupsNear(array(),$lat,$lng,$distance,$sort='distance asc',$count=20,$offset=0);
    $order= array();

    if($shops->count() > 0){

        $order= $shops->asArray();
    }
    else{
        $order[]='no results';
    }

    echo results($order);
    exit;

exit;

// get shops by the width of the map
    case 'get_shops_within';

    if($_GET['lat']){
        $lat= $_GET['lat'];
    }
    if($_GET['lng']){
        $lng= $_GET['lng'];
    }
    $distance= $_GET['distance'];

    $shops= $POD->getGroupsNear(array(),$lat,$lng,$distance,$sort='distance asc',$count=20,$offset=0);
    $order= array();

      if($shops->count() > 0){
        $i=0;
        $order= $shops->asArray();
        foreach($order as $shop){

            $ratings_avgs= $POD->getShopReviewInfo($shop['id']);

            $order[$i]['reviews']  = $ratings_avgs['review_count'];
            $order[$i]['coff_avg'] = $ratings_avgs['coff_avg'];
            $order[$i]['comm_avg'] = $ratings_avgs['comm_avg'];
            $order[$i]['atmos_avg']= $ratings_avgs['atmos_avg'];

            $i++;
        }
    }
    else{
        $order[]='no results';
    }

    echo results($order);
    exit;

exit;

// get shops by city and/or state
    case 'get.shops_city_state';

    $query= array('description'=>'coffee_shop');
    
    if($_GET['city']){
        $query['city']= $_GET['city'];
    }
    if($_GET['st']){
       $query['state']= $_GET['st'];
    }
    if(@$_GET['zip']){
        $query['zipcode']= $_GET['zip'];
    }
    
    $sort = "groupname asc";

    $shops= $POD->getGroups($query, $sort, 100);

    $order= array();

    if($shops->count() > 0){
        $i=0;
        $order= $shops->asArray();
        foreach($order as $shop){

            $ratings_avgs= $POD->getShopReviewInfo($shop['id']);

            $order[$i]['reviews']  = $ratings_avgs['review_count'];
            $order[$i]['coff_avg'] = $ratings_avgs['coff_avg'];
            $order[$i]['comm_avg'] = $ratings_avgs['comm_avg'];
            $order[$i]['atmos_avg']= $ratings_avgs['atmos_avg'];
      
            $i++;
        }
    }
    else{
        $order[]='no results';
    }

    echo results($order);
    exit;

exit;

//update a users location preferences
case 'updateloc';
    $data= array('msg'=>'?');

    if($POD->isAuthenticated()){
        if($_GET['lat'] && $_GET['lng']){
            $user= $POD->currentUser();
            $user->setLocation($_GET['lat'], $_GET['lng']);

            if($user->success()){
                $data['msg']= 'location saved!';
            }
            else{$data['msg']= 'could not save location';}
        }else $data['msg']= 'no location';
    }else $data['msg']= 'no auth';

    echo results($data);
exit;
// return the reviews for one shop
    case 'get_shop_reviews';

    if($_GET['id']){

     $shop= $POD->getGroup(array('id'=>$_GET['id']));
     $reviews= $shop->content(array('type'=>'review'));

     if($reviews->count() > 0){
         $reviews_array= $reviews->asArray();

         foreach($reviews_array as $key=>$value){
             $contentId= $reviews_array[$key]['id'];
            
             $doc= $POD->getContent(array('id'=>$contentId));

             $avatarFile= $doc->author()->files()->contains('file_name', 'img');

             $author= $doc->author()->permalink;

             if($avatarFile){
                $avatar= $avatarFile->get('thumbnail');
             }
             else{
                 $avatar= $POD->templateDir(false)."/img/noimage.png";
             }
             
             
             $reviews_array[$key]['avatar']=$avatar;
             $reviews_array[$key]['author_link']= $author;


              if ($img = $doc->files()->contains('file_name','img')){
                $reviews_array[$key]['img']= $img->get('thumbnail') ;
              }
            
         }
     }
     else $reviews[]= 'no results';

     echo results($reviews_array);
    }
    
    exit;

    /////////////////////////////////////////////////
    case 'rate_shop';

     $msg= array();
     $msg['msg']= 'success';
     
     if($_GET['groupId']){

        $group= $POD->getGroup(array('id'=>$_GET['groupId']));
        $user = $POD->currentUser();

        $flagArray= array('atmosphere', 'community', 'coffee');
        
        foreach($flagArray as $flag){

            if(!is_null($group->hasFlag($flag, $user))){
                $group->removeFlag($flag, $user);
            }
            if($_GET[$flag] > 0){//don't let a zero in there it will ruin the averages
                $group->addFlag($flag, $user, $_GET[$flag]);
            }
            if(!$group->success()){
                $msg['msg']= 'error';
                $msg[$flag]= "could not add flag: $flag";
            }
        }

     }
     else {$msg['msg']= "no id";}

        echo results($msg);
    exit;

}

//////// other functions //////////////////////////
function results($data) {
	return json_encode($data);
}

function mysql_fix_string($string){

    if(get_magic_quotes_gpc ()){
        $string= stripslashes($string);
    }
    return mysql_real_escape_string($string);
}

function clean($string){
    return htmlentities(mysql_fix_string($string));
}
