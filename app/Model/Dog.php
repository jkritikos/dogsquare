<?php

class Dog extends AppModel {
    var $name = 'Dog';
     
    
    function getDogById($dogId){
        $sql = "SELECT d.id, d.name, db.name, d.age, d.gender, d.weight, p.path, dl.id";
        $sql .= " FROM dogs d";
        $sql .= " LEFT OUTER JOIN dog_breeds db on (d.breed_id = db.id)";
        $sql .= " LEFT OUTER JOIN photos p on (d.photo_id = p.id)";
        $sql .= " LEFT OUTER JOIN dog_likes dl on (d.id = dl.dog_id)";
        $sql .= " WHERE d.id = $dogId";
        $rs = $this->query($sql);
        $obj = array();
        
        if(is_array($rs)){
            foreach($rs as $i => $values){
                $obj['Dog']['id'] = $rs[$i]['d']['id'];
		$obj['Dog']['name'] = $name = $rs[$i]['d']['name'];
		$obj['Dog']['dog_breed'] = $rs[$i]['db']['name'];
                $obj['Dog']['age'] = $rs[$i]['d']['age'];
                $obj['Dog']['gender'] = $rs[$i]['d']['gender'];
                $obj['Dog']['weight'] = $rs[$i]['d']['weight'];
                $obj['Dog']['photo'] = $rs[$i]['p']['path'];
                $obj['Dog']['liked'] = $rs[$i]['dl']['id'];
                
		$data[] = $obj;
            }
        }
        
        return $data;
    }
}

?>