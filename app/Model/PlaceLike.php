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
     
     function getLikedPlaces($userId){
        $sql = "select pp.name, pp.id, u.id, u.name, p.thumb, date_format(pl.created, '%d/%m/%Y %H:%i' ) as creation_date";
        $sql .= " from place_likes pl";
        $sql .= " inner join users u on (pl.user_id=u.id)";
        $sql .= " inner join photos p on (u.photo_id=p.id)";
        $sql .= " inner join places pp on (pp.id = pl.place_id) ";
        $sql .= " where pl.user_id = $userId";
        
        $rs = $this->query($sql);
        
        $data = array();
        if(is_array($rs)){
            foreach($rs as $i => $values){
                $id = $rs[$i]['u']['id'];
                $name = $rs[$i]['u']['name'];
                $thumb = $rs[$i]['p']['thumb'];

                $obj['User']['place_id'] = $rs[$i]['pp']['id'];
                $obj['User']['id'] = $id;
                $obj['User']['name'] = $rs[$i]['pp']['name'];
                $obj['User']['thumb'] = $thumb;
                $obj['User']['creation_date'] = $rs[$i][0]['creation_date'];

                $data[] = $obj;
            }
        }
                
        return $data;
    }
}

?>