<?php

class Activity extends AppModel {
    var $name = 'Activity';
    
    //Returns a list of the activities by this user.
    function getActivityList($user_id){
        $sql = "select a.id, p.thumb, UNIX_TIMESTAMP(a.created) created, group_concat(d.name separator ', ') dogs from activities a ";
        $sql .= "inner join activity_dogs ad on (a.id = ad.activity_id) inner join dogs d ";
        $sql .= "on (d.id = ad.dog_id) inner join photos p on (p.id = d.photo_id) where a.user_id=$user_id group by a.id order by a.id desc";
        
        $rs = $this->query($sql);
        $data = array();
        if(is_array($rs)){
            foreach($rs as $i => $values){
                $obj['Activity']['id'] = $rs[$i]['a']['id'];
                $obj['Activity']['thumb'] = $rs[$i]['p']['thumb'];
                $obj['Activity']['created'] = $rs[$i][0]['created'];
                $obj['Activity']['dogs'] = $rs[$i][0]['dogs'];
                
                $data[] = $obj;
            }
        }
        
        return $data;
    }
    
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
        $sql = "select d.id, d.name, p.thumb, ad.dogfuel ";
        $sql .= " from dogs d";
        $sql .= " inner join photos p on (d.photo_id = p.id)";
        $sql .= " inner join activity_dogs ad on (d.id = ad.dog_id)";
        $sql .= " where ad.activity_id = $activityId";
        $rs = $this->query($sql);
        
        $data = array();
        if(is_array($rs)){
            foreach($rs as $i => $values){
                $obj['Dog']['id'] = $rs[$i]['d']['id'];
                $obj['Dog']['name'] = $rs[$i]['d']['name'];
                $obj['Dog']['thumb'] = $rs[$i]['p']['thumb'];
                $obj['Dog']['dogfuel'] = $rs[$i]['ad']['dogfuel'];

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
    
    //Returns the activities near the specified coordinates
    function getNearbyActivities($lat, $lon, $mutualFollowers){
        $sql = "select u.id, a.id, u.name, p.thumb, a.start_lat, a.start_lon, ";
        $sql .= "6371 * 2 * ASIN(SQRT(POWER(SIN(($lat - abs(a.start_lat)) * pi()/180 / 2), 2) +  COS($lat * pi()/180 ) * COS(abs(a.start_lat) * pi()/180) * POWER(SIN(($lon - a.start_lon) * pi()/180 / 2), 2) )) as distance ";
        $sql .= "from activities a inner join users u on (a.user_id = u.id) ";
        $sql .= "inner join photos p on (u.photo_id = p.id) ";
        $sql .= "where 1=1 ";
        $sql .= " and a.created > NOW() - INTERVAL 1 DAY ";
        $sql .= " and a.user_id in ($mutualFollowers) ";
        $sql .= "and a.created = (select max(a2.created) from activities a2 where a2.user_id = u.id) ";
        $sql .= "having distance <= ".NEARBY_DISTANCE ." order by distance ";
        
        $this->log("Activity->getNearbyActivities() sql $sql" , LOG_DEBUG);
        $data = array();
        $rs = $this->query($sql);
	if(is_array($rs)){
            foreach($rs as $i => $values){
		if($rs[$i]['0']['distance'] < 1){
                    $data[$i]['distance'] = 1000 * round($rs[$i][0]['distance'], 1) . 'm';
		} else {
                    $data[$i]['distance'] = round($rs[$i][0]['distance'], 1) . 'Km';
		}
                
                $data[$i]['id'] = $rs[$i]['u']['id'];
                $data[$i]['activity_id'] = $rs[$i]['a']['id'];
                $data[$i]['user_name'] = $rs[$i]['u']['name'];
                $data[$i]['lat'] = $rs[$i]['a']['start_lat'];
                $data[$i]['lon'] = $rs[$i]['a']['start_lon'];
                $data[$i]['thumb'] = $rs[$i]['p']['thumb'];
            }
	}

        return $data;
    }
}

?>