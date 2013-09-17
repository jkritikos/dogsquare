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
   
    //Returns the nearby places for the specified coordinates
    function getPlacesNearby($lat, $lon, $categoryId=null){
        $sql = "select p.name, p.lat, p.lon, p.id, ph.thumb,";
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
                
            }
	}

        return $data;
   }
}

?>