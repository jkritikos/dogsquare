<?php

App::uses('AppModel', 'Model');
class Photo extends AppModel {
     public $name = 'Photo';
     
     //Returns a count of the photos uploaded by the specified user
     function countPhotosByUser($id){
        $sql = "select count(*) as cnt from photos a where a.user_id=$id";
        $rs = $this->query($sql);
        
        $count = 0;
        if(is_array($rs)){
            foreach($rs as $i => $values){
                $count = $rs[$i][0]['cnt'];
            }
        }
        
        return $count;
     }
     
     function getUserPhotos($user_id){
        $sql = "select p.thumb, p.path from photos p where p.user_id = $user_id and p.type_id in(1,3)";
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
    
    //Returns the photos for the specified place
    function getPlacePhotos($place_id){
        $sql = "select p.thumb, p.path from photos p where p.place_id = $place_id and p.type_id = ".PLACE_PHOTO_TYPE;
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