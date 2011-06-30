<?

	function getAvgRating($group, $field) {
                    $POD = $group->POD;
                    $id  = $group->id;
                    $query= "select avg(value) from flags where name='$field' and itemId='$id'";

                    $result= $POD->executeSql($query);
                    $avg= mysql_fetch_row($result);

                    return $avg[0];
	}
	Group::registerMethod('getAvgRating');


//given a shopId ("groupId") this function will return the average
//rating of each field of the shop, as well as the number of written reviews
//on the shop
//returns array('coff_avg', 'comm_avg', 'atmos_avg', 'review_count')
function getShopReviewInfo($POD, $shopId){

         $q="select tab1.coff_avg, tab2.comm_avg, tab3.atmos_avg, tab4.review_count
             From(
                  (select avg(value) as coff_avg from flags where type='group' and name='coffee'  and itemId='$shopId')as tab1,
                  (select avg(value) as comm_avg from flags where type='group' and name='community' and itemId='$shopId') as tab2,
                  (select avg(value) as atmos_avg from flags where type='group' and name='atmosphere' and itemId='$shopId')as tab3,
                  (select count(*) as review_count from content where type='review' and groupId='$shopId') as tab4
             )";

         $results= $POD->executeSQL($q);

         return mysql_fetch_assoc($results);
       
}
PeoplePod::registerMethod('getShopReviewInfo');

/*
function getCitiesStates($POD){

}
PeoplePod::registerMethod('getCitiesStates');*/