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
        return $deleted;
    }
    
    //Returns the followers of user with id $id
    function getFollowers($id){
        $sql = "select u.id, u.name, p.thumb from users u inner join user_follows uf on (u.id=uf.user_id) inner join photos p on (u.photo_id=p.id) where uf.follows_user=$id";
        $rs = $this->query($sql);
        $data = array();
        
        if(is_array($rs)){
            foreach($rs as $i => $values){
                $obj['User']['name'] = $rs[$i]['u']['name'];
		$obj['User']['thumb'] = $name = $rs[$i]['p']['thumb'];
		$obj['User']['id'] = $rs[$i]['u']['id'];

		$data[] = $obj;
            }
        }
        
        return $data;
    }
    
    //Returns a count of the followers for the specified user
    function countFollowers($id){
        $sql = "select count(*) cnt from user_follows uf where follows_user=$userId";
        $rs = $this->query($sql);
        
        $count = 0;
        if(is_array($rs)){
            foreach($rs as $i => $values){
                $count = $rs[$i][0]['cnt'];
            }
        }
        
        return $count;
    }
    
    //Reutns the users that user $id is following
    function getFollowing($id){
        $sql = "select u.id, u.name, p.thumb from users u inner join user_follows uf on (u.id=uf.follows_user) inner join photos p on (u.photo_id=p.id) where uf.user_id=$id";
        $rs = $this->query($sql);
        $data = array();
        
        if(is_array($rs)){
            foreach($rs as $i => $values){
                $obj['User']['name'] = $rs[$i]['u']['name'];
		$obj['User']['thumb'] = $name = $rs[$i]['p']['thumb'];
		$obj['User']['id'] = $rs[$i]['u']['id'];

		$data[] = $obj;
            }
        }
        
        return $data;
    }
    
    //Returns the number of follows/followers for this user
    function getFollowStats($userId){
        $sql = "select count(*) cnt from user_follows uf where user_id=$userId";
        $rs = $this->query($sql);
        
        $data = array();
        if(is_array($rs)){
            foreach($rs as $i => $values){
                $data['following'] = $rs[$i][0]['cnt'];
            }
        }
        
        $sql = "select count(*) cnt from user_follows uf where follows_user=$userId";
        $rs = $this->query($sql);
        if(is_array($rs)){
            foreach($rs as $i => $values){
                $data['followers'] = $rs[$i][0]['cnt'];
            }
        }
        
        return $data;
    }
    
    //Returns an array with the users that are mutually followed by user $id
    function getMutualFollowers($id){
        $sql = "";
    }
}

?>