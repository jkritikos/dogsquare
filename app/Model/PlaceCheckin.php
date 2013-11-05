<?php
App::uses('AppModel', 'Model');
class PlaceCheckin extends AppModel {
     public $name = 'PlaceCheckin';
     
     //Returns the nearby checkins for the specified coordinates
     function getNearbyCheckins($lat, $lon, $mutualFollowers){ 
        $sql = "select u.id, u.name, pl.id, pl.name, pl.lat, pl.lon, p.thumb, ";
        $sql .= "6371 * 2 * ASIN(SQRT(POWER(SIN(($lat - abs(pl.lat)) * pi()/180 / 2), 2) +  COS($lat * pi()/180 ) * COS(abs(pl.lat) * pi()/180) * POWER(SIN(($lon - pl.lon) * pi()/180 / 2), 2) )) as distance ";
        $sql .= "from place_checkins pc inner join places pl on (pc.place_id = pl.id) inner join users u on (pc.user_id = u.id) ";
        $sql .= "inner join photos p on (u.photo_id = p.id) ";
        $sql .= "where 1=1 ";
        $sql .= " and pc.created > NOW() - INTERVAL 1 DAY ";
        $sql .= "and pc.created = (select max(pc2.created) from place_checkins pc2 where pc2.user_id = u.id and pc2.place_id=pc.place_id) ";
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