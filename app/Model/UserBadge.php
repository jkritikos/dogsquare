<?php

class UserBadge extends AppModel {
    var $name = 'UserBadge';
    var $useTable = 'user_badges';
    
    //Returns all the user badges for the specified user id
    function getUserBadges($user_id){
        $sql = "select b.id, b.title, ub.user_id from badges b left join user_badges ub on (b.id = ub.badge_id and ub.user_id=$user_id)";
        
        $rs = $this->query($sql);

	$data = array();
	if(is_array($rs)){
            foreach($rs as $i => $values){
                $id = $rs[$i]['b']['id'];
                $value = $rs[$i]['ub']['user_id'] != null ? true : false;
                
                $obj['id'] = $id;
                $obj['flag'] = $value;
                
                $data[] = $obj; 
            }
        }
        
        return $data;
    }
    
    //Returns true if the specified user/badge combo exists, false otherwise
    function userHasBadge($user_id, $badge_id){
        $sql = "select count(*) cnt from user_badges ub where ub.user_id=$user_id and ub.badge_id=$badge_id";
        $rs = $this->query($sql);
        $exists = $rs[0][0]['cnt'] > 0 ? true : false;
        
        return $exists;
    }
    
    //Awards the specified badge to the specified user
    function awardBadge($user_id, $badge_id){
        $obj['UserBadge']['user_id'] = $user_id;
        $obj['UserBadge']['badge_id'] = $badge_id;
        
        if($this->save($obj)){
            return true;
        } else {
            return false;
        }
    }
}

?>