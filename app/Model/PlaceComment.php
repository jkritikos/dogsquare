<?php
App::uses('AppModel', 'Model');
class PlaceComment extends AppModel {
     public $name = 'PlaceComment';
     
     //Returns a count of place comments for the specified user
     function countCommentsForUser($id){
         $sql = "select count(*) as cnt from place_comments a where a.user_id=$id";
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