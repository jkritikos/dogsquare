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
    
    //Returns a count of activity likes for the specified user
     function countLikesForUser($id){
         $sql = "select count(*) as cnt from activity_likes a where a.user_id=$id";
        $rs = $this->query($sql);
        
        $count = 0;
        if(is_array($rs)){
            foreach($rs as $i => $values){
                $count = $rs[$i][0]['cnt'];
            }
        }
        
        return $count;
     }
     
     function getLikedActivities($userId){
        $sql = "select a.id, u.id, u.name, p.thumb, date_format(al.created, '%d/%m/%Y %H:%i' ) as creation_date ";
        $sql .= " from activity_likes al inner join activities a on (al.activity_id = a.id)";
        $sql .= " inner join users u on (a.user_id=u.id)";
        $sql .= " inner join photos p on (u.photo_id=p.id)";
        $sql .= " where al.user_id = $userId";
        
        $rs = $this->query($sql);
        
        $data = array();
        if(is_array($rs)){
            foreach($rs as $i => $values){
                $id = $rs[$i]['u']['id'];
                $name = $rs[$i]['u']['name'];
                $thumb = $rs[$i]['p']['thumb'];

                $obj['User']['activity_id'] = $rs[$i]['a']['id'];
                $obj['User']['id'] = $id;
                $obj['User']['name'] = $name;
                $obj['User']['creation_date'] = $rs[$i][0]['creation_date'];
                $obj['User']['thumb'] = $thumb;

                $data[] = $obj;
            }
        }
                
        return $data;
    }
     
}

?>