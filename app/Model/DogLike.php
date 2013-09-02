<?php

class DogLike extends AppModel {
    var $name = 'DogLike';
    
    function userLikesDog($user_id, $dog_id){
        $sql = "select dl.id from dog_likes dl where user_id=$user_id and dog_id=$dog_id";
        
        $response = false;
        $rs = $this->query($sql);
        foreach($rs as $i => $values){
            if($rs[$i]['dl']['id'] != null){
                $response = true;
            }
        }
        
        $this->log("DogLikes->userLikesDog() returns $response for user $user_id and dog_id $dog_id", LOG_DEBUG);
        return $response;
    }
    
    //Deletes a like
    function deleteLike($user_id, $dog_id){
        $sql = "delete from dog_likes where user_id=$user_id and dog_id=$dog_id";
        $rs = $this->query($sql);
        $deleted = $this->getAffectedRows();
        
        $this->log("DogLikes->deleteLike() deleted $deleted rows for user_id $user_id and dog_id $dog_id", LOG_DEBUG);
        return $deleted;
    }
     
}

?>