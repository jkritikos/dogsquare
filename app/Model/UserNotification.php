<?php

class UserNotification extends AppModel {
    var $name = 'UserNotification';
    
    function getUnreadNotifications($user_id){
        $sql = "select un.id, un.user_from, un.type_id, un.activity_id, p.thumb, u.name, un.created from user_notifications un ";
        $sql .= "inner join users u on (u.id=un.user_from) inner join photos p on (p.id=u.photo_id) where un.user_id=$user_id and un.read=0";
        $rs = $this->query($sql);
        $data = array();
        
	if(is_array($rs)){
            foreach($rs as $i => $values){
                $obj['id'] = $rs[$i]['un']['id'];
                $obj['created'] = $rs[$i]['un']['created'];
                $obj['name'] = $rs[$i]['u']['name'];
                $obj['user_from'] = $rs[$i]['un']['user_from'];
                $obj['type_id'] = $rs[$i]['un']['type_id'];
                $obj['activity_id'] = $rs[$i]['un']['activity_id'];
                $obj['thumb'] = $rs[$i]['p']['thumb'];
                
                $data[] = $obj;
            }
        }
        
        return $data;
    }
     
}

?>