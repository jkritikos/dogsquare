<?php

class UserNotification extends AppModel {
    var $name = 'UserNotification';
    
    //Returns the unread notifications for this user
    function getUnreadNotifications($user_id){
        $sql = "select un.id, un.user_from, un.type_id, un.activity_id, p.thumb, u.name, UNIX_TIMESTAMP(un.created) created, un.badge_id from user_notifications un ";
        $sql .= "inner join users u on (u.id=un.user_from) inner join photos p on (p.id=u.photo_id) where un.user_id=$user_id and un.read=0 order by id desc";
        $rs = $this->query($sql);
        $data = array();
        
	if(is_array($rs)){
            foreach($rs as $i => $values){
                $obj['id'] = $rs[$i]['un']['id'];
                $obj['created'] = $rs[$i][0]['created'] * 1000;
                $obj['name'] = $rs[$i]['u']['name'];
                $obj['user_from'] = $rs[$i]['un']['user_from'];
                $obj['type_id'] = $rs[$i]['un']['type_id'];
                $obj['activity_id'] = $rs[$i]['un']['activity_id'];
                $obj['badge_id'] = $rs[$i]['un']['badge_id'];
                $obj['thumb'] = $rs[$i]['p']['thumb'];
                
                $data[] = $obj;
            }
        }
        
        return $data;
    }
    
    //Returns the number of unread notifications for this user
    function countUnreadNotifications($user_id){
        $sql = "select count(*) cnt from user_notifications un where un.user_id=$user_id and un.read=0";
  
        $rs = $this->query($sql);
        $count = 0;
        
	if(is_array($rs)){
            foreach($rs as $i => $values){
                $count = $rs[$i][0]['cnt'];
                
            }
        }
        
        $this->log("API->countUnreadNotifications() returns is $count", LOG_DEBUG);
        return $count;
    }
    
    function setNotificationsToRead($list){
        $sql = "update user_notifications un set un.read = 1 where un.id in ($list)";
        $rs = $this->query($sql);
        
        $rows = $this->getAffectedRows();
        
        return $rows;
    }
     
}

?>