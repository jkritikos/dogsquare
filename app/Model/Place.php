<?php

App::uses('AppModel', 'Model');
class Place extends AppModel {
     public $name = 'Place';
	 
	 

    function search($name, $category, $status){
        $sql = "select p.id,  p.name, pc.name, date_format(p.created, '%d/%m/%Y %H:%i') as created ";
	$sql .= " from places p, place_categories pc  where pc.id = p.category_id ";
		
        if($name != ''){
            $sql .= " and p.name like '%$name%' ";
        }

        if($category != ''){
            $sql .= " and pc.name like '%$category%' ";
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

                $obj['Place']['name'] = $name;
                $obj['Place']['category'] = $category;
                $obj['Place']['created'] = $created;
                $obj['Place']['id'] = $id;

                $data[] = $obj;
            }
        }

        $this->log("Place->search() returns ".count($data), LOG_DEBUG);
        return $data;
    }
        
    function getPlaceById($placeId){
        $sql = "select p.id, p.name, p.lon, p.lat, pc.name, ph.path, pl.id";
        $sql .= " from places p";
        $sql .= " left outer join place_categories pc on (p.category_id = pc.id)";
        $sql .= " left outer join photos ph on (p.photo_id = ph.id)";
        $sql .= " left outer join place_likes pl on (p.id = pl.place_id)";
        $sql .= " where p.id = $placeId";
        $rs = $this->query($sql);

        $obj['id'] = $rs[0]['p']['id'];
        $obj['name'] = $name = $rs[0]['p']['name'];
        $obj['longitude'] = $rs[0]['p']['lon'];
        $obj['latitude'] = $rs[0]['p']['lat'];
        $obj['category'] = $rs[0]['pc']['name'];
        $obj['photo'] = $rs[0]['ph']['path'];
        $obj['liked'] = $rs[0]['pl']['id'];

        return $obj;
   }
   
   //Returns the nearby dogs who are mating for the specified coordinates
   function getMatingDogsNearby($lat, $lon){
        $sql = "select d.id, d.name, p.thumb, a.start_lat, a.start_lon, ";
        $sql .= "6371 * 2 * ASIN(SQRT(POWER(SIN(($lat - abs(a.start_lat)) * pi()/180 / 2), 2) +  COS($lat * pi()/180 ) * COS(abs(a.start_lat) * pi()/180) * POWER(SIN(($lon - a.start_lon) * pi()/180 / 2), 2) )) as distance ";
        $sql .= "from dogs d inner join activity_dogs ad  on (d.id = ad.dog_id) ";
        $sql .= "inner join activities a  on (ad.activity_id = a.id) ";
        $sql .= "inner join photos p on (d.photo_id = p.id) ";
        $sql .= "where d.mating = 2 ";
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
        $sql = "select d.id, db.name, d.name, p.thumb, a.start_lat, a.start_lon, ";
        $sql .= "6371 * 2 * ASIN(SQRT(POWER(SIN(($lat - abs(a.start_lat)) * pi()/180 / 2), 2) +  COS($lat * pi()/180 ) * COS(abs(a.start_lat) * pi()/180) * POWER(SIN(($lon - a.start_lon) * pi()/180 / 2), 2) )) as distance ";
        $sql .= "from dogs d inner join activity_dogs ad  on (d.id = ad.dog_id) ";
        $sql .= "inner join activities a  on (ad.activity_id = a.id) ";
        $sql .= "inner join dog_breeds db  on (d.breed_id = db.id) ";
        $sql .= "inner join photos p on (d.photo_id = p.id) ";
        $sql .= "where d.breed_id in ($breeds) ";
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
   function getPlacesNearby($lat, $lon, $categoryId=null){
        $sql = "select p.name, p.lat, p.lon, p.id, ph.thumb, c.name, ";
        $sql .= "6371 * 2 * ASIN(SQRT(POWER(SIN(($lat - abs(p.lat)) * pi()/180 / 2), 2) +  COS($lat * pi()/180 ) * COS(abs(p.lat) * pi()/180) * POWER(SIN(($lon - p.lon) * pi()/180 / 2), 2) )) as distance ";
        $sql .= "from places p left join photos ph on (ph.id = p.photo_id) ";
        $sql .= "left join place_categories c on (p.category_id=c.id) ";
        $sql .= " where p.active=1 ";
        
        //add category if needed
        if($categoryId != null){
            $sql .= " and p.category_id=$categoryId";
        }
        
        $sql .= " having distance <= ".NEARBY_DISTANCE ." order by distance ";
        
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
                
            }
	}

        return $data;
   }
   
   function getPlaceComments($placeId){
        $sql = "select pc.id, pc.comment, pc.user_id, u.name, pc.created";
        $sql .= " from place_comments pc";
        $sql .= " inner join users u on (pc.user_id=u.id)";
        $sql .= " where pc.place_id = $placeId order by pc.created desc";
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
}

?>