<?php

class Dog extends AppModel {
    var $name = 'Dog';
    
    function getProfilePhoto($dog_id){
        $sql = "select p.path, p.thumb from photos p inner join dogs d on (d.photo_id = p.id) where d.id=$dog_id";
        
        $rs = $this->query($sql);
        $data = array();
        if(is_array($rs)){
            foreach($rs as $i => $values){
                $data['photo'] = $rs[$i]['p']['path'];
                $data['thumb'] = $rs[$i]['p']['thumb'];
            }
        }
        
        return $data;
    }
    
    function getDogById($dogId){
        $sql = "SELECT d.id, d.name, db.name, d.size, d.mating, d.age, d.gender, d.weight, p.path, dl.id, count(dl.id) as likes";
        $sql .= " FROM dogs d";
        $sql .= " LEFT OUTER JOIN dog_breeds db on (d.breed_id = db.id)";
        $sql .= " LEFT OUTER JOIN photos p on (d.photo_id = p.id)";
        $sql .= " LEFT OUTER JOIN dog_likes dl on (d.id = dl.dog_id)";
        $sql .= " WHERE d.id = $dogId";
        $rs = $this->query($sql);
        
        $obj['id'] = $rs[0]['d']['id'];
        $obj['name'] = $name = $rs[0]['d']['name'];
        $obj['dog_breed'] = $rs[0]['db']['name'];
        $obj['mating'] = $rs[0]['d']['mating'];
        $obj['age'] = $rs[0]['d']['age'];
        $obj['gender'] = $rs[0]['d']['gender'];
        $obj['weight'] = $rs[0]['d']['weight'];
        $obj['photo'] = $rs[0]['p']['path'];
        $obj['liked'] = $rs[0]['dl']['id'];
        $obj['likes'] = $rs[0][0]['likes'];
        $obj['size'] = $rs[0]['d']['size'];
                
        return $obj;
    }
    
    function getUserDogs($userId){
        $sql = "select d.id, d.name, d.age, d.gender, d.mating, d.weight, p.thumb, p.path, db.name  ";
        $sql .= " from dogs d";
        $sql .= " inner join users u on (d.owner_id = u.id)";
        $sql .= " inner join photos p on (d.photo_id = p.id)";
        $sql .= " inner join dog_breeds db on (d.breed_id = db.id)";
        $sql .= " where u.id = $userId";
        $rs = $this->query($sql);
        $data = array();
        
        
        if(is_array($rs)){
            foreach($rs as $i => $values){
                $obj['Dog']['id'] = $rs[$i]['d']['id'];
                $obj['Dog']['name'] = $name = $rs[$i]['d']['name'];
                $obj['Dog']['dog_breed'] = $rs[$i]['db']['name'];
                $obj['Dog']['age'] = $rs[$i]['d']['age'];
                $obj['Dog']['gender'] = $rs[$i]['d']['gender'];
                $obj['Dog']['weight'] = $rs[$i]['d']['weight'];
                $obj['Dog']['photo'] = $rs[$i]['p']['path'];
                $obj['Dog']['thumb'] = $rs[$i]['p']['thumb'];
                $obj['Dog']['mating'] = $rs[$i]['d']['mating'];
                
                $data[] = $obj;
            }
        }
       
        return $data;
    }
    
    function getLikedUsers($dogId){
        $sql = "select u.id, u.name, p.thumb";
        $sql .= " from dog_likes dl";
        $sql .= " inner join users u on (dl.user_id=u.id)";
        $sql .= " inner join photos p on (u.photo_id=p.id)";
        $sql .= " where dl.dog_id = $dogId";
        
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