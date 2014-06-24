<?php

App::uses('AppModel', 'Model');
class DogfuelRule extends AppModel {
     public $name = 'DogfuelRule';
	
     function doMassUpdate($walk, $play){
         $rows = 0;
         
         if($walk != null && $walk != ''){
             $sql = "update dogfuel_rules set walk_distance = walk_distance $walk";
             $this->query($sql);
             $rows += $this->getAffectedRows();
         }
         
         if($play != null && $play != ''){
             $sql = "update dogfuel_rules set playtime = playtime $play";
             $this->query($sql);
             $rows += $this->getAffectedRows();
         }
         
         return $rows;
     }
     
    function getDogfuelAll(){
        $sql= "select dr.id, dr.walk_distance, dr.playtime, dr.active, db.name, dr.breed_id";
        $sql .= " from dogfuel_rules dr, dog_breeds db";
        $sql .= " where dr.breed_id = db.id order by db.id";

        $rs = $this->query($sql);

        $data = array();
        if(is_array($rs)){
            foreach($rs as $i => $values){
                $id = $rs[$i]['dr']['id'];
                $walkDistance = $rs[$i]['dr']['walk_distance'];
                $playtime = $rs[$i]['dr']['playtime'];
                $active = $rs[$i]['dr']['active'];
                $name = $rs[$i]['db']['name'];
                $breedId = $rs[$i]['dr']['breed_id'];

                $obj['Dogfuel']['id'] = $id;
                $obj['Dogfuel']['walkDistance'] = $walkDistance;
                $obj['Dogfuel']['playtime'] = $playtime;
                $obj['Dogfuel']['active'] = $active;
                $obj['Dogfuel']['name'] = $name;
                $obj['Dogfuel']['breedId'] = $breedId;

                $data[] = $obj;
            }
        }

        $this->log("DogfuelRule->getDogfuelAll() returns ".count($data), LOG_DEBUG);
        return $data;

    }
	 
    function getDogfuelById($id){
        $sql= "select dr.id, dr.walk_distance, dr.playtime, dr.active, db.name, dr.breed_id";
        $sql .= " from dogfuel_rules dr, dog_breeds db";
        $sql .= " where dr.breed_id = db.id";
        $sql .= " and dr.id = $id";

        $rs = $this->query($sql);

        $data = array();
        if(is_array($rs)){
            foreach($rs as $i => $values){
                $walkDistance = $rs[$i]['dr']['walk_distance'];
                $playtime = $rs[$i]['dr']['playtime'];
                $active = $rs[$i]['dr']['active'];
                $name = $rs[$i]['db']['name'];
                $breedId = $rs[$i]['dr']['breed_id'];

                $data['Dogfuel']['walkDistance'] = $walkDistance;
                $data['Dogfuel']['playtime'] = $playtime;
                $data['Dogfuel']['active'] = $active;
                $data['Dogfuel']['name'] = $name;
                $data['Dogfuel']['breedId'] = $breedId;

            }
        }

        $this->log("DogfuelRule->getDogfuelById() returns ".count($data), LOG_DEBUG);
        return $data;

    }
}

?>