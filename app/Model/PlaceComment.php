<?php
App::uses('AppModel', 'Model');
class PlaceComment extends AppModel {
     public $name = 'PlaceComment';
     
     //Returns a count of place comments for the specified place
     function countPlaceComments($id){
         $sql = "select count(*) as cnt from place_comments a where a.place_id=$id";
        $rs = $this->query($sql);
        
        $count = 0;
        if(is_array($rs)){
            foreach($rs as $i => $values){
                $count = $rs[$i][0]['cnt'];
            }
        }
        
        return $count;
     }
     
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
     
     function getPlaceCommentsByUser($userId, $onlyActive){
        $sql = "select ad.active, ad.id, ad.comment, ad.user_id, u.name, ad.created, date_format(ad.created, '%d/%m/%Y %H:%i' ) as creation_date";
        $sql .= " from place_comments ad";
        $sql .= " inner join users u on (ad.user_id=u.id)";
        $sql .= " where ad.user_id = $userId ";
        
        if($onlyActive){
            $sql .= " and ad.active=1 ";
        }
        
        $sql .= "order by ad.created desc";
        
        $rs = $this->query($sql);
        
        $data = array();
        if(is_array($rs)){
            foreach($rs as $i => $values){
                $id = $rs[$i]['ad']['id'];
                $comment = $rs[$i]['ad']['comment'];
                $userId = $rs[$i]['ad']['user_id'];
                $userName = $rs[$i]['u']['name'];
                
                $date = $rs[$i]['ad']['created'];
                $timestamp = strtotime($date);

                $obj['comm']['id'] = $id;
                $obj['comm']['text'] = $comment;
                $obj['comm']['user_id'] = $userId;
                $obj['comm']['name'] = $userName;
                $obj['comm']['date'] = $timestamp;
                $obj['comm']['creation_date'] = $rs[$i][0]['creation_date'];
                $obj['comm']['active'] = $rs[$i]['ad']['active'];

                $data[] = $obj;
            }
        }
                
        return $data;
    }
}

?>