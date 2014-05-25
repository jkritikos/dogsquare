<?php
App::uses('AppModel', 'Model');
class ActivityComment extends AppModel {
     public $name = 'ActivityComment';
     
     //Returns a count of activity comments for the specified user
     function countCommentsForUser($id){
         $sql = "select count(*) as cnt from activity_comments a where a.user_id=$id";
        $rs = $this->query($sql);
        
        $count = 0;
        if(is_array($rs)){
            foreach($rs as $i => $values){
                $count = $rs[$i][0]['cnt'];
            }
        }
        
        return $count;
     }
}

?>