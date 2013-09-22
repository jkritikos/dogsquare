<?php

class UserInbox extends AppModel {
    var $name = 'UserInbox';
    var $useTable = 'user_inbox';
    
    function getUnreadMessages($user_id){
        $sql = "select ui.id, unix_timestamp(ui.created) created, ui.user_from, ui.message, u.name from user_inbox ui inner join users u on (ui.user_from = u.id) where ui.read=0 and ui.user_to=$user_id";
        
        $rs = $this->query($sql);
        $data = array();
        
        if(is_array($rs)){
            foreach($rs as $i => $values){
                $obj['UserInbox']['id'] = $rs[$i]['ui']['id'];
                $obj['UserInbox']['user_from_id'] = $rs[$i]['ui']['user_from'];
		$obj['UserInbox']['message'] = $name = $rs[$i]['ui']['message'];
		$obj['UserInbox']['user_from_name'] = $rs[$i]['u']['name'];
                $obj['UserInbox']['created'] = $rs[$i][0]['created'] * 1000;

		$data[] = $obj;
            }
        }
        
        return $data;
    }
    
    function setMessagesToRead($list){
        $sql = "update user_inbox ui set ui.read = 1 where id in ($list)";
        $rs = $this->query($sql);
        
        $rows = $this->getAffectedRows();
        return $rows;
    }
    
    function countUnreadMessages($user_id){
        return 0;
    }
}

?>