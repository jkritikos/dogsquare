<?php

class UserBadge extends AppModel {
    var $name = 'UserBadge';
    var $useTable = 'user_badges';
    
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
}

?>