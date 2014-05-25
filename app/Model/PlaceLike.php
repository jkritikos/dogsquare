<?php

class PlaceLike extends AppModel {
    var $name = 'PlaceLike';
    
    function userLikesPlace($user_id, $place_id){
        $sql = "select count(*) cnt from place_likes pl where user_id=$user_id and place_id=$place_id";
        
        $response = false;
        $rs = $this->query($sql);
        foreach($rs as $i => $values){
            if($rs[$i]['0']['cnt'] > 0){
                $response = true;
            }
        }
        
        $this->log("PlaceLike->userLikesPlace() returns $response for user $user_id and place_id $place_id", LOG_DEBUG);
        return $response;
    }
    
    //Deletes a like
    function deleteLike($user_id, $place_id){
        $sql = "delete from place_likes where user_id=$user_id and place_id=$place_id";
        $rs = $this->query($sql);
        $deleted = $this->getAffectedRows();
        
        $this->log("PlaceLike->deleteLike() deleted $deleted rows for user_id $user_id and place_id $place_id", LOG_DEBUG);
        return $deleted;
    }
    
    //Returns a count of place likes for the specified user
     function countLikesForUser($id){
         $sql = "select count(*) as cnt from place_likes a where a.user_id=$id";
        $rs = $this->query($sql);
        
        $count = 0;
        if(is_array($rs)){
            foreach($rs as $i => $values){
                $count = $rs[$i][0]['cnt'];
            }
        }
        
        return $count;
     }
     
}

?>