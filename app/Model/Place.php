<?php

App::uses('AppModel', 'Model');
class Place extends AppModel {
     public $name = 'Place';
	 
	 

    function search($name, $category, $status){
        $sql = "select p.id, ph.thumb, p.name, pc.name, date_format(p.created, '%d/%m/%Y %H:%i') as created ";
	$sql .= " from places p";
        $sql .= " left outer join photos ph on (p.photo_id = ph.id)";
        $sql .= " inner join place_categories pc on (pc.id = p.category_id)";
		
        if($name != ''){
            $sql .= " and p.name like '%$name%' ";
        }

        if($category != ''){
            $sql .= " and p.category_id = $category ";
        }

        if($status != ''){
            $sql .= " and p.active=$status ";
        }
	
        $rs = $this->query($sql);

        $data = array();
        if(is_array($rs)){
            foreach($rs as $i => $values){
                $id = $rs[$i]['p']['id'];
                $name = $rs[$i]['p']['name'];
                $category = $rs[$i]['pc']['name'];
                $created = $rs[$i]['0']['created'];
                $thumb = $rs[$i]['ph']['thumb'];

                $obj['Place']['name'] = $name;
                $obj['Place']['category'] = $category;
                $obj['Place']['created'] = $created;
                $obj['Place']['id'] = $id;
                $obj['Place']['thumb'] = $thumb;

                $data[] = $obj;
            }
        }

        $this->log("Place->search() returns ".count($data), LOG_DEBUG);
        return $data;
    }
        
    function getPlaceById($placeId, $userId){
        $sql = "select p.id, p.name, p.lon, p.lat, pc.name, ph.path, pl.id, p.url";
        $sql .= " from places p";
        $sql .= " left outer join place_categories pc on (p.category_id = pc.id)";
        $sql .= " left outer join photos ph on (p.photo_id = ph.id)";
        $sql .= " left outer join place_likes pl on (p.id = pl.place_id and pl.user_id=$userId)";
        $sql .= " where p.id = $placeId";
        $rs = $this->query($sql);

        $obj['id'] = $rs[0]['p']['id'];
        $obj['url'] = $rs[0]['p']['url'];
        $obj['name'] = $name = $rs[0]['p']['name'];
        $obj['longitude'] = $rs[0]['p']['lon'];
        $obj['latitude'] = $rs[0]['p']['lat'];
        $obj['category'] = $rs[0]['pc']['name'];
        $obj['photo'] = $rs[0]['ph']['path'];
        $obj['liked'] = $rs[0]['pl']['id'];

        return $obj;
   }
   
   function getPlaceLikes($placeId){
        $sql = "select count(*) cnt from place_likes pl where pl.place_id=$placeId";
        $rs = $this->query($sql);
        $count = $rs[0][0]['cnt'];
        
        return $count;
   }
   
   function getPlaceCheckins($placeId){
        $sql = "select count(*) cnt from place_checkins pc where pc.place_id=$placeId";
        $rs = $this->query($sql);
        $count = $rs[0][0]['cnt'];
        
        return $count;
   }
   
   //Returns the last checkin for the specified user/place combo
   function getLastCheckin($user_id, $place_id){
       $sql = "select unix_timestamp(pc.created) created from place_checkins pc where user_id=$user_id and place_id=$place_id order by created desc limit 1";
       $rs = $this->query($sql);
       $checkinTimestamp = null;
       
       if(is_array($rs) && count($rs) > 0){
           $checkinTimestamp = $rs[0][0]['created'] * 1000;
       }
       
       return $checkinTimestamp;
   }
   
   //Returns the nearby dogs who are mating for the specified coordinates
   function getMatingDogsNearby($lat, $lon){
        $sql = "select d.id, d.name, p.thumb, a.start_lat, a.start_lon, d.owner_id, u.name,";
        $sql .= "6371 * 2 * ASIN(SQRT(POWER(SIN(($lat - abs(a.start_lat)) * pi()/180 / 2), 2) +  COS($lat * pi()/180 ) * COS(abs(a.start_lat) * pi()/180) * POWER(SIN(($lon - a.start_lon) * pi()/180 / 2), 2) )) as distance ";
        $sql .= "from dogs d inner join activity_dogs ad  on (d.id = ad.dog_id) ";
        $sql .= "inner join activities a  on (ad.activity_id = a.id) ";
        $sql .= "inner join photos p on (d.photo_id = p.id) ";
        $sql .= "inner join users u on (d.owner_id = u.id) ";
        $sql .= "where d.mating = 1 and d.active=1 ";
        $sql .= "and ad.created = (select max(ad2.created) from activity_dogs ad2 where ad2.dog_id = ad.dog_id) ";
        $sql .= "having distance <= ".NEARBY_DISTANCE ." order by distance ";
        
        $this->log("Place->getMatingDogsNearby() sql $sql" , LOG_DEBUG);
        $data = array();
        $rs = $this->query($sql);
	if(is_array($rs)){
            foreach($rs as $i => $values){
		if($rs[$i]['0']['distance'] < 1){
                    $data[$i]['distance'] = 1000 * round($rs[$i][0]['distance'], 1) . 'm';
		} else {
                    $data[$i]['distance'] = round($rs[$i][0]['distance'], 1) . 'Km';
		}
                
                $data[$i]['user_name'] = $rs[$i]['u']['name'];
                $data[$i]['user_id'] = $rs[$i]['d']['owner_id'];
                $data[$i]['id'] = $rs[$i]['d']['id'];
                $data[$i]['name'] = $rs[$i]['d']['name'];
                $data[$i]['lat'] = $rs[$i]['a']['start_lat'];
                $data[$i]['lon'] = $rs[$i]['a']['start_lon'];
                $data[$i]['thumb'] = $rs[$i]['p']['thumb'];
            }
	}

        return $data;
   }
   
   //Returns the nearby dogs who have the same breed with user's dogs for the specified coordinates
   function getSameBreedDogsNearby($lat, $lon, $breeds){
        $sql = "select d.id, db.name, d.name, p.thumb, a.start_lat, a.start_lon, d.owner_id, u.name, ";
        $sql .= "6371 * 2 * ASIN(SQRT(POWER(SIN(($lat - abs(a.start_lat)) * pi()/180 / 2), 2) +  COS($lat * pi()/180 ) * COS(abs(a.start_lat) * pi()/180) * POWER(SIN(($lon - a.start_lon) * pi()/180 / 2), 2) )) as distance ";
        $sql .= "from dogs d inner join activity_dogs ad  on (d.id = ad.dog_id) ";
        $sql .= "inner join activities a  on (ad.activity_id = a.id) ";
        $sql .= "inner join dog_breeds db  on (d.breed_id = db.id) ";
        $sql .= "inner join photos p on (d.photo_id = p.id) ";
        $sql .= "inner join users u on (d.owner_id = u.id) ";
        $sql .= "where d.breed_id in ($breeds) and d.active=1 ";
        $sql .= "and ad.created = (select max(ad2.created) from activity_dogs ad2 where ad2.dog_id = ad.dog_id) ";
        $sql .= "having distance <= ".NEARBY_DISTANCE ." order by distance ";
        
        $this->log("Place->getMatingDogsNearby() sql $sql" , LOG_DEBUG);
        $data = array();
        $rs = $this->query($sql);
	if(is_array($rs)){
            foreach($rs as $i => $values){
		if($rs[$i]['0']['distance'] < 1){
                    $data[$i]['distance'] = 1000 * round($rs[$i][0]['distance'], 1) . 'm';
		} else {
                    $data[$i]['distance'] = round($rs[$i][0]['distance'], 1) . 'Km';
		}
                
                $data[$i]['user_name'] = $rs[$i]['u']['name'];
                $data[$i]['user_id'] = $rs[$i]['d']['owner_id'];
                $data[$i]['id'] = $rs[$i]['d']['id'];
                $data[$i]['breed'] = $rs[$i]['db']['name'];
                $data[$i]['name'] = $rs[$i]['d']['name'];
                $data[$i]['lat'] = $rs[$i]['a']['start_lat'];
                $data[$i]['lon'] = $rs[$i]['a']['start_lon'];
                $data[$i]['thumb'] = $rs[$i]['p']['thumb'];
                $data[$i]['test'] = strtotime('+1 day');
            }
	}

        return $data;
   }
   
   //Returns the recently opened nearby places for the specified coordinates
   function getRecentlyOpenedPlacesNearby($lat, $lon){
        $sql = "select p.name, p.lat, p.lon, p.id, ph.thumb, c.name, ";
        $sql .= "6371 * 2 * ASIN(SQRT(POWER(SIN(($lat - abs(p.lat)) * pi()/180 / 2), 2) +  COS($lat * pi()/180 ) * COS(abs(p.lat) * pi()/180) * POWER(SIN(($lon - p.lon) * pi()/180 / 2), 2) )) as distance ";
        $sql .= "from places p left join photos ph on (ph.id = p.photo_id) ";
        $sql .= "left join place_categories c on (p.category_id=c.id) ";
        $sql .= " where p.active=1 ";
        $sql .= " and p.created > NOW() - INTERVAL 100 DAY ";
        $sql .= " having distance <= ".NEARBY_DISTANCE ." order by distance ";
        
        $this->log("Place->getRecentlyOpenedPlacesNearby() sql $sql" , LOG_DEBUG);
        $data = array();
        $rs = $this->query($sql);
	if(is_array($rs)){
            foreach($rs as $i => $values){
		if($rs[$i]['0']['distance'] < 1){
                    $data[$i]['distance'] = 1000 * round($rs[$i][0]['distance'], 1) . 'm';
		} else {
                    $data[$i]['distance'] = round($rs[$i][0]['distance'], 1) . 'Km';
		}
                
                $data[$i]['id'] = $rs[$i]['p']['id'];
                $data[$i]['name'] = $rs[$i]['p']['name'];
                $data[$i]['lat'] = $rs[$i]['p']['lat'];
                $data[$i]['lon'] = $rs[$i]['p']['lon'];
                $data[$i]['thumb'] = $rs[$i]['ph']['thumb'];
                $data[$i]['category'] = $rs[$i]['c']['name'];
                
            }
	}

        return $data;
   }
   
   //Returns the nearby places for the specified coordinates
   function getPlacesNearby($name, $lat, $lon, $categoryId=null){
        $sql = "select p.name, p.lat, p.lon, p.id, p.user_id, u.name, ph.thumb, c.name, p.category_id, p.dog_id, p.user_id, p.weight, p.color,p.url,";
        $sql .= "6371 * 2 * ASIN(SQRT(POWER(SIN(($lat - abs(p.lat)) * pi()/180 / 2), 2) +  COS($lat * pi()/180 ) * COS(abs(p.lat) * pi()/180) * POWER(SIN(($lon - p.lon) * pi()/180 / 2), 2) )) as distance ";
        $sql .= "from places p left join photos ph on (ph.id = p.photo_id) ";
        $sql .= "left join place_categories c on (p.category_id=c.id) ";
        $sql .= "left join users u on (p.user_id=u.id)  ";
        $sql .= " where p.active=1 ";
        
        //add category if needed
        if($categoryId != null){
            $sql .= " and p.category_id=$categoryId";
        }
        
        //add name if needed
        if($name != null){
            $sql .= " and p.name like '%$name%' ";
        }
        
        $sql .= " having distance <= ".NEARBY_DISTANCE ." order by p.weight desc,distance ";
        
        $this->log("Place->getPlacesNearby() sql $sql" , LOG_DEBUG);
        $data = array();
        $rs = $this->query($sql);
	if(is_array($rs)){
            foreach($rs as $i => $values){
		if($rs[$i]['0']['distance'] < 1){
                    $data[$i]['distance'] = 1000 * round($rs[$i][0]['distance'], 1) . 'm';
		} else {
                    $data[$i]['distance'] = round($rs[$i][0]['distance'], 1) . 'Km';
		}
                
                $data[$i]['id'] = $rs[$i]['p']['id'];
                $data[$i]['name'] = $rs[$i]['p']['name'];
                $data[$i]['lat'] = $rs[$i]['p']['lat'];
                $data[$i]['lon'] = $rs[$i]['p']['lon'];
                $data[$i]['thumb'] = $rs[$i]['ph']['thumb'];
                $data[$i]['category'] = $rs[$i]['c']['name'];
                $data[$i]['category_id'] = $rs[$i]['p']['category_id'];
                $data[$i]['user_id'] = $rs[$i]['p']['user_id'];
                $data[$i]['user_name'] = $rs[$i]['u']['name'];
                $data[$i]['user_id'] = $rs[$i]['p']['user_id'];
                $data[$i]['dog_id'] = $rs[$i]['p']['dog_id'];
                //weighted data for sponsored places
                $data[$i]['weight'] = $rs[$i]['p']['weight'];
                $data[$i]['url'] = $rs[$i]['p']['url'];
                $data[$i]['color'] = $rs[$i]['p']['color'];
            }
	}

        return $data;
   }
   
   function getPlaceComments($placeId, $onlyActive){
        $sql = "select pc.id, pc.comment, pc.user_id, u.name, pc.created";
        $sql .= " from place_comments pc";
        $sql .= " inner join users u on (pc.user_id=u.id)";
        $sql .= " where pc.place_id = $placeId ";
        
        if($onlyActive){
            $sql .= " and pc.active=1 ";
        }
        
        $sql .= " order by pc.created desc";
        
        $rs = $this->query($sql);
        
        $data = array();
        if(is_array($rs)){
            foreach($rs as $i => $values){
                $id = $rs[$i]['pc']['id'];
                $comment = $rs[$i]['pc']['comment'];
                $userId = $rs[$i]['pc']['user_id'];
                $userName = $rs[$i]['u']['name'];
                
                $date = $rs[$i]['pc']['created'];
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
    
    //Returns the number of places added by this user - uses $category_id if specified
    function countPlacesByUser($user_id, $category_id){
        $sql = "select count(*) cnt from places p where p.user_id=$user_id and p.category_id=$category_id";
        $rs = $this->query($sql);
        $count = $rs[0][0]['cnt'];
        
        return $count;
    }
    
    function getLikedUsers($placeId){
        $sql = "select u.id, u.name, p.thumb";
        $sql .= " from place_likes pl";
        $sql .= " inner join users u on (pl.user_id=u.id)";
        $sql .= " inner join photos p on (u.photo_id=p.id)";
        $sql .= " where pl.place_id = $placeId";
        
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
    
    function getCheckinUsers($placeId){
        $sql = "select u.id, u.name, p.thumb, UNIX_TIMESTAMP(pc.created) created";
        $sql .= " from place_checkins pc";
        $sql .= " inner join users u on (pc.user_id=u.id)";
        $sql .= " inner join photos p on (u.photo_id=p.id)";
        $sql .= " where pc.place_id = $placeId";
        $sql .= " order by pc.created desc";
        
        $rs = $this->query($sql);
        
        $data = array();
        if(is_array($rs)){
            foreach($rs as $i => $values){
                $id = $rs[$i]['u']['id'];
                $name = $rs[$i]['u']['name'];
                $thumb = $rs[$i]['p']['thumb'];
                $time = $rs[$i][0]['created'] * 1000;

                $obj['User']['id'] = $id;
                $obj['User']['name'] = $name;
                $obj['User']['thumb'] = $thumb;
                $obj['User']['time'] = $time;

                $data[] = $obj;
            }
        }
                
        return $data;
    }
}

?>