<?php
App::uses('AppModel', 'Model');
class PlaceCheckin extends AppModel {
     public $name = 'PlaceCheckin';
     
     //Returns a count of the place checkins for this place
     function countPlaceCheckins($id){
         $sql = "select count(*) as cnt from place_checkins a where a.place_id=$id";
        $rs = $this->query($sql);
        
        $count = 0;
        if(is_array($rs)){
            foreach($rs as $i => $values){
                $count = $rs[$i][0]['cnt'];
            }
        }
        
        return $count;
     }
     
     function getPlaceCheckins($id){
         $sql = "select u.id, u.name, date_format(pc.created, '%d/%m/%Y %H:%i' ) as creation_date from place_checkins pc inner join users u on (pc.user_id = u.id) where pc.place_id=$id";
         $rs = $this->query($sql);
        $data = array();
        
	if(is_array($rs)){
            foreach($rs as $i => $values){
                $obj['user_name'] = $rs[$i]['u']['name'];
                $obj['user_id'] = $rs[$i]['u']['id'];
                $obj['creation_date'] = $rs[$i][0]['creation_date'];
                
                $data[] = $obj;
            }
        }
        
        return $data;
     }
     
     //Returns a count of the place checkins for this user
     function countUserCheckins($id){
         $sql = "select count(*) as cnt from place_checkins a where a.user_id=$id";
        $rs = $this->query($sql);
        
        $count = 0;
        if(is_array($rs)){
            foreach($rs as $i => $values){
                $count = $rs[$i][0]['cnt'];
            }
        }
        
        return $count;
     }
     
     //Returns the checkins made by this user
     function getUserCheckins($id){
         $sql = "select p.name, pc.id, p.id, date_format(pc.created, '%d/%m/%Y %H:%i' ) as creation_date from place_checkins pc inner join places p on (pc.place_id = p.id) where pc.user_id=$id";
         $rs = $this->query($sql);
        $data = array();
        
	if(is_array($rs)){
            foreach($rs as $i => $values){
                $obj['place_name'] = $rs[$i]['p']['name'];
                $obj['place_id'] = $rs[$i]['p']['id'];
                $obj['creation_date'] = $rs[$i][0]['creation_date'];
                
                $data[] = $obj;
            }
        }
        
        return $data;
     }
     
     //Returns the nearby checkins for the specified coordinates
     function getNearbyCheckins($lat, $lon, $mutualFollowers){ 
        $sql = "select u.id, u.name, pl.id, pl.name, pl.lat, pl.lon, p.thumb, ";
        $sql .= "6371 * 2 * ASIN(SQRT(POWER(SIN(($lat - abs(pl.lat)) * pi()/180 / 2), 2) +  COS($lat * pi()/180 ) * COS(abs(pl.lat) * pi()/180) * POWER(SIN(($lon - pl.lon) * pi()/180 / 2), 2) )) as distance ";
        $sql .= "from place_checkins pc inner join places pl on (pc.place_id = pl.id) inner join users u on (pc.user_id = u.id) ";
        $sql .= "inner join photos p on (u.photo_id = p.id) ";
        $sql .= "where 1=1 ";
        $sql .= " and pc.created > NOW() - INTERVAL 1 DAY ";
        $sql .= "and pc.created = (select max(pc2.created) from place_checkins pc2 where pc2.user_id = u.id) ";
        //$sql .= " and pc.user_id in ($mutualFollowers) ";
        $sql .= "having distance <= ".NEARBY_DISTANCE_CHECKINS ." order by distance ";
        
        $this->log("PlaceCheckin->getNearbyCheckins() sql $sql" , LOG_DEBUG);
        $data = array();
        $counts = array();
        $rs = $this->query($sql);
	if(is_array($rs)){
            foreach($rs as $i => $values){
		if($rs[$i]['0']['distance'] < 1){
                    $data[$i]['distance'] = 1000 * round($rs[$i][0]['distance'], 1) . 'm';
		} else {
                    $data[$i]['distance'] = round($rs[$i][0]['distance'], 1) . 'Km';
		}
                
                $data[$i]['id'] = $rs[$i]['u']['id'];
                $data[$i]['user_name'] = $rs[$i]['u']['name'];
                $data[$i]['place_name'] = $rs[$i]['pl']['name'];
                $data[$i]['place_id'] = $rs[$i]['pl']['id'];
                $data[$i]['lat'] = $rs[$i]['pl']['lat'];
                $data[$i]['lon'] = $rs[$i]['pl']['lon'];
                $data[$i]['thumb'] = $rs[$i]['p']['thumb'];
                
                if(!isset($counts[$rs[$i]['pl']['id']])){
                    $counts[$rs[$i]['pl']['id']] = 1;
                } else {
                    $counts[$rs[$i]['pl']['id']] = $counts[$rs[$i]['pl']['id']] +1;
                }
                
            }
            
            //hack to get the total counts
            foreach($data as $i => $d){
                $total = $counts[$data[$i]['place_id']];
                $data[$i]['total_checkins'] = $total;
                //$this->log("PlaceCheckin->getNearbyCheckins() found $total checkins for place ".$data[$i]['place_id'] , LOG_DEBUG);
            }
	}

        return $data;
     }
}

?>