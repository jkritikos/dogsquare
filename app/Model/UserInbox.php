<?php

class UserInbox extends AppModel {
    var $name = 'UserInbox';
    
    function getUnreadMessages($user_id){
        
    }
    
    function countUnreadMessages($user_id){
        return 0;
    }
}

?>