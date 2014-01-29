<?php

class UserFollows extends AppModel {
    var $name = 'UserFollows';
    
    //Returns true if user $id is mutual follower with user $followingId, false otherwise
    function isMutualFollower($id, $followingId){
        $sql = "select 1 as follows from user_follows uf inner join user_follows uf2 on (uf.user_id=uf2.follows_user) where uf.user_id=$id and uf.follows_user=$followingId and uf2.user_id=$followingId and uf2.follows_user=$id";
        
        $response = false;
        $rs = $this->query($sql);
        foreach($rs as $i => $values){
            if($rs[$i][0]['follows'] != null){
                $response = true;
            }
        }
        
        $this->log("UserFollows->isMutualFollower() returns $response for id $id and followingId $followingId", LOG_DEBUG);
        return $response;
    }
    
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
    function countFollowers($userId){
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
    
    //Returns the facebook ids of the users that the current user is following
    function getFacebookFollowing($id){
        $sql = "select u.facebook_id from users u inner join user_follows uf on (u.id=uf.follows_user) where uf.user_id=$id and u.facebook_id is not null";
        $rs = $this->query($sql);
        $data = array();
        
        if(is_array($rs)){
            foreach($rs as $i => $values){
		$data[] = $rs[$i]['u']['facebook_id'];
            }
        }
        
        return $data;
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
        //IDs only
        //$sql = "select uf.follows_user from user_follows uf where uf.user_id=$id and exists (select uf2.follows_user from user_follows uf2 where uf2.follows_user=$id and uf2.user_id=uf.follows_user)";
        $sql = "select uf.follows_user,u.name, p.thumb from user_follows uf inner join users u on (uf.follows_user=u.id) ";
        $sql .= "inner join photos p on (u.photo_id = p.id) where uf.user_id=$id and exists ";
        $sql .= "(select uf2.follows_user from user_follows uf2 where uf2.follows_user=$id and uf2.user_id=uf.follows_user)";
        
        $rs = $this->query($sql);
        $data = array();
        
        if(is_array($rs)){
            foreach($rs as $i => $values){
                $obj['User']['name'] = $rs[$i]['u']['name'];
		$obj['User']['thumb'] = $name = $rs[$i]['p']['thumb'];
		$obj['User']['id'] = $rs[$i]['uf']['follows_user'];

		$data[] = $obj;
            }
        }
        
        return $data;
    }
    
    //Returns a list of the user IDs of the mutual followers of the specified user id, or null if none
    function getMutualFollowersList($id){
        $sql = "select uf.follows_user from user_follows uf inner join users u on (uf.follows_user=u.id) ";
        $sql .= "inner join photos p on (u.photo_id = p.id) where uf.user_id=$id and exists ";
        $sql .= "(select uf2.follows_user from user_follows uf2 where uf2.follows_user=$id and uf2.user_id=uf.follows_user)";
        
        $rs = $this->query($sql);
        $data = array();
        
        if(is_array($rs)){
            foreach($rs as $i => $values){
		$data[] = $rs[$i]['uf']['follows_user'];
            }
        }
        
        if(!empty($data)){
            return implode(",", $data);
        } else {
            return null;
        }
       
    }
}

?>