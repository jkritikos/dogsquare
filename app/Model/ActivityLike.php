<?php

class ActivityLike extends AppModel {
    var $name = 'ActivityLike';
    
    function userLikesActivity($user_id, $activity_id){
        $sql = "select al.id from activity_likes al where user_id=$user_id and activity_id=$activity_id";
        
        $response = false;
        $rs = $this->query($sql);
        foreach($rs as $i => $values){
            if($rs[$i]['al']['id'] != null){
                $response = true;
            }
        }
        
        $this->log("ActivityLike->userLikesActivity() returns $response for user $user_id and activity_id $activity_id", LOG_DEBUG);
        return $response;
    }
    
    //Deletes a like
    function deleteLike($user_id, $activity_id){
        $sql = "delete from activity_likes where user_id=$user_id and activity_id=$activity_id";
        $rs = $this->query($sql);
        $deleted = $this->getAffectedRows();
        
        $this->log("ActivityLike->deleteLike() deleted $deleted rows for user_id $user_id and activity_id $activity_id", LOG_DEBUG);
        return $deleted;
    }
     
}

?>