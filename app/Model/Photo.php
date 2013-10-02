<?php

App::uses('AppModel', 'Model');
class Photo extends AppModel {
     public $name = 'Photo';
     
     
     function getUserPhotos($user_id){
        $sql = "select p.thumb, p.path from photos p where p.user_id = $user_id and p.type_id = 3";
        $rs = $this->query($sql);
        $data = array();
        
	if(is_array($rs)){
            foreach($rs as $i => $values){
                $obj['thumb'] = $rs[$i]['p']['thumb'];
                $obj['path'] = $rs[$i]['p']['path'];
                
                $data[] = $obj;
            }
        }
        
        return $data;
    }
     
}

?>