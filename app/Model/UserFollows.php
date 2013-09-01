<?php

class UserFollows extends AppModel {
    var $name = 'UserFollows';
    
    //Returns true if user $id is following user $followingId, false otherwise
    function isUserFollowing($id, $followingId){
        $sql = "select uf.id from user_follows uf where user_id=$id and follows_user=$followingId";
        
        $response = false;
        $rs = $this->query($sql);
        foreach($rs as $i => $values){
            if($rs[$i]['uf']['id'] != null){
                $response = true;
            }
        }
        
        $this->log("UserFollows->isUserFollowing() returns $response for id $id and followingId $followingId", LOG_DEBUG);
        return $response;
    }
    
    //Deletes a user follow
    function deleteUserFollow($id, $followingId){
        $sql = "delete from user_follows where user_id=$id and follows_user=$followingId";
        $rs = $this->query($sql);
        $deleted = $this->getAffectedRows();
        
        $this->log("UserFollows->deleteUserFollow() deleted $deleted rows for id $id and followingId $followingId", LOG_DEBUG);
    }
    
}

?>