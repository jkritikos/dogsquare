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
     
     function countGalleryPhotosByUser($id){
        $sql = "select count(*) as cnt from photos a where a.user_id=$id and a.type_id = 3";
        $rs = $this->query($sql);
        
        $count = 0;
        if(is_array($rs)){
            foreach($rs as $i => $values){
                $count = $rs[$i][0]['cnt'];
            }
        }
        
        return $count;
     }
     
     function getGalleryUserPhotos($user_id){
        $sql = "select date_format(p.created, '%d/%m/%Y %H:%i' ) as creation_date, p.id, p.active, p.thumb, p.path from photos p where p.user_id = $user_id and p.type_id in(3)";
        $rs = $this->query($sql);
        $data = array();
        
	if(is_array($rs)){
            foreach($rs as $i => $values){
                $obj['thumb'] = $rs[$i]['p']['thumb'];
                $obj['path'] = $rs[$i]['p']['path'];
                $obj['id'] = $rs[$i]['p']['id'];
                $obj['creation_date'] = $rs[$i][0]['creation_date'];
                $obj['active'] = $rs[$i]['p']['active'];
                
                $data[] = $obj;
            }
        }
        
        return $data;
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
        $sql = "select p.id, u.id, u.name, p.active, p.thumb, p.path, date_format(p.created, '%d/%m/%Y %H:%i' ) as creation_date from photos p inner join users u on (p.user_id = u.id) where p.place_id = $place_id and p.type_id = ".PLACE_PHOTO_TYPE;
        $rs = $this->query($sql);
        $data = array();
        
	if(is_array($rs)){
            foreach($rs as $i => $values){
                $obj['thumb'] = $rs[$i]['p']['thumb'];
                $obj['path'] = $rs[$i]['p']['path'];
                $obj['creation_date'] = $rs[$i][0]['creation_date'];
                $obj['active'] = $rs[$i]['p']['active'];
                $obj['user_id'] = $rs[$i]['u']['id'];
                $obj['user_name'] = $rs[$i]['u']['name'];
                $obj['id'] = $rs[$i]['p']['id'];
                
                $data[] = $obj;
            }
        }
        
        return $data;
    }
    
    //Returns a count of the photos uploaded by the specified user
     function countPlacePhotos($id){
        $sql = "select count(*) as cnt from photos p where p.place_id = $id and p.type_id = ".PLACE_PHOTO_TYPE;
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