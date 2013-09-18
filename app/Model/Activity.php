<?php

class Activity extends AppModel {
    var $name = 'Activity';
     
    function getActivityById($userId, $activityId){
        $sql = "select a.id, a.type_id, a.temperature, a.pace, a.distance, ";
        $sql .= " (select al2.id from activity_likes al2 where al2.activity_id = $activityId and al2.user_id = $userId) as liked,";
        $sql .= " count(al2.id) as likes";
        $sql .= " from activities a";
        $sql .= " left outer join activity_likes al2 on (a.id = al2.activity_id)";
        $sql .= " where a.id = $activityId";
        $sql .= " and a.user_id = $userId";
        $rs = $this->query($sql);
        
        $obj['id'] = $rs[0]['a']['id'];
        $obj['type_id'] = $rs[0]['a']['type_id'];
        $obj['temperature'] = $rs[0]['a']['temperature'];
        $obj['pace'] = $rs[0]['a']['pace'];
        $obj['distance'] = $rs[0]['a']['distance'];
        $obj['liked'] = $rs[0][0]['liked'];
        $obj['likes'] = $rs[0][0]['likes'];
                
        return $obj;
    }
    
    
    function getActivityDogs($activityId){
        $sql = "select d.id, d.name, p.thumb";
        $sql .= " from dogs d";
        $sql .= " inner join photos p on (d.photo_id = p.id)";
        $sql .= " inner join activity_dogs ad on (d.id = ad.dog_id)";
        $sql .= " where ad.activity_id = $activityId";
        $rs = $this->query($sql);
        
        $data = array();
        if(is_array($rs)){
            foreach($rs as $i => $values){
                $id = $rs[$i]['d']['id'];
                $name = $rs[$i]['d']['name'];
                $thumb = $rs[$i]['p']['thumb'];

                $obj['Dog']['id'] = $id;
                $obj['Dog']['name'] = $name;
                $obj['Dog']['thumb'] = $thumb;

                $data[] = $obj;
            }
        }
                
        return $data;
    }
    
    function getActivityComments($activityId){
        $sql = "select ad.id, ad.comment, ad.user_id, u.name, ad.created";
        $sql .= " from activity_comments ad";
        $sql .= " inner join users u on (ad.user_id=u.id)";
        $sql .= " where ad.activity_id = $activityId order by ad.created desc";
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

                $data[] = $obj;
            }
        }
                
        return $data;
    }
    
    function getActivityCoordinates($activityId){
        $sql = "select ad.lat, ad.lon";
        $sql .= " from activity_coordinates ad ";
        $sql .= " where ad.activity_id = $activityId order by ad.id asc";
        $rs = $this->query($sql);
        
        $data = array();
        if(is_array($rs)){
            foreach($rs as $i => $values){
                $latitude = $rs[$i]['ad']['lat'];
                $longitude = $rs[$i]['ad']['lon'];

                $obj['latitude'] = $latitude;
                $obj['longitude'] = $longitude;

                $data[] = $obj;
            }
        }
                
        return $data;
    }
    
    function getLikedUsers($activityId){
        $sql = "select u.id, u.name, p.thumb";
        $sql .= " from activity_likes al";
        $sql .= " inner join users u on (al.user_id=u.id)";
        $sql .= " inner join photos p on (u.photo_id=p.id)";
        $sql .= " where al.activity_id = $activityId";
        
        $rs = $this->query($sql);
        
        $data = array();
        if(is_array($rs)){
            foreach($rs as $i => $values){
                $id = $rs[$i]['u']['id'];
                $name = $rs[$i]['u']['name'];
                $thumb = $rs[$i]['p']['thumb'];

                $obj['User']['id'] = $id;
                $obj['User']['name'] = $name;
                $obj['User']['thumb'] = $thumb;

                $data[] = $obj;
            }
        }
                
        return $data;
    }
}

?>