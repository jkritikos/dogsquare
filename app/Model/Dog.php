<?php

class Dog extends AppModel {
    var $name = 'Dog';
     
    
    function getDogById($dogId){
        $sql = "SELECT d.id, d.name, db.name, d.age, d.gender, d.weight, p.path, dl.id, count(dl.id) as likes";
        $sql .= " FROM dogs d";
        $sql .= " LEFT OUTER JOIN dog_breeds db on (d.breed_id = db.id)";
        $sql .= " LEFT OUTER JOIN photos p on (d.photo_id = p.id)";
        $sql .= " LEFT OUTER JOIN dog_likes dl on (d.id = dl.dog_id)";
        $sql .= " WHERE d.id = $dogId";
        $rs = $this->query($sql);
        $obj = array();
        
        
        $obj['id'] = $rs[0]['d']['id'];
        $obj['name'] = $name = $rs[0]['d']['name'];
        $obj['dog_breed'] = $rs[0]['db']['name'];
        $obj['age'] = $rs[0]['d']['age'];
        $obj['gender'] = $rs[0]['d']['gender'];
        $obj['weight'] = $rs[0]['d']['weight'];
        $obj['photo'] = $rs[0]['p']['path'];
        $obj['liked'] = $rs[0]['dl']['id'];
        $obj['likes'] = $rs[0][0]['likes'];
                
        return $obj;
    }
}

?>