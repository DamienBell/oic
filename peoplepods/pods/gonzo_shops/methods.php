<?
function getAllReviewTitles($group) {

    $titles= array();
    $POD= $group->POD;
    $q= "select headline, date, stub from content where groupId='$group->id' order by date ASC;";
    $results= $POD->executeSQL($q);

    while($r= mysql_fetch_assoc($results)){
        $titles[]= $r;
    }

    return $titles;
	}

	Group::registerMethod('getAllReviewTitles');


 /*
  fieldsArray
  //should have type, userId, itemId, and name to ensure one result
  *
  */
 function flagInfo($POD, $fieldsArray){
   
     $q= "SELECT * FROM flags WHERE ";

     $size= sizeof($fieldsArray);
     $i= 0;

     foreach($fieldsArray as $field=>$value){
         $i++;
         $q .= "$field = '$value' ";
         if($i < $size){
             $q .= " AND ";
         }
     }
     $results= $POD->executeSQL($q);

     $result= mysql_fetch_assoc($results);

     return $result;
 }

 PeoplePod::registerMethod('flagInfo');