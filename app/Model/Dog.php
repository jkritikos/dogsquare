<?php

class Dog extends AppModel {
    var $name = 'Dog';
    
    function websearch($name, $breed, $country, $mating){
        $sql = "select u.id, u.name, d.id, d.name, db.name, c.name from dogs d inner join dog_breeds db on (d.breed_id = db.id) inner join users u on (d.owner_id = u.id) inner join countries c on (u.country_id = c.id) where 1=1 ";
        
        if($name != ''){
            $sql .= " and d.name like '%$name%' ";
        }
        
        if($breed != ''){
            $sql .= " and d.breed_id = $breed ";
        }
        
        if($country != ''){
            $sql .= " and u.country_id = $country ";
        }
        
        if($mating != ''){
            $sql .= " and d.mating = $mating";
        }
        
        $rs = $this->query($sql);
        $data = array();
        if(is_array($rs)){
            foreach($rs as $i => $values){
                $obj['Dog']['id'] = $rs[$i]['d']['id'];
                $obj['Dog']['name'] = $rs[$i]['d']['name'];
                $obj['Dog']['owner'] = $rs[$i]['u']['name'];
                $obj['Dog']['owner_id'] = $rs[$i]['u']['id'];
                $obj['Dog']['breed'] = $rs[$i]['db']['name'];
                $obj['Dog']['country'] = $rs[$i]['c']['name'];
                
                $data[] =$obj;
            }
        }
        
        return $data;
    }
    
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
    
    //Clears all lost dog instances related to the specified dog
    function deleteLostDogs($dog_id){
        $sql = "update places set dog_id=null where dog_id=$dog_id";
        $rs = $this->query($sql);
        $deleted = $this->getAffectedRows();
        
        $this->log("Dog->deleteLostDogs() cleared $deleted places with lost dog $dog_id", LOG_DEBUG);
        return $deleted;
    }
    
    function getDogById($dogId){
        $sql = "SELECT d.id, d.name, db.name, d.size, d.mating, d.age, d.gender, d.weight, p.path, pl.id, d.owner_id ";
        $sql .= " FROM dogs d";
        $sql .= " LEFT OUTER JOIN dog_breeds db on (d.breed_id = db.id)";
        $sql .= " LEFT OUTER JOIN photos p on (d.photo_id = p.id)";
        $sql .= " LEFT OUTER JOIN places pl on (d.id = pl.dog_id)";
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
        $obj['likes'] = $this->countDogLikes($dogId);
        $obj['size'] = $rs[0]['d']['size'];
        $obj['lost'] = $rs[0]['pl']['id'];
        $obj['owner_id'] = $rs[0]['d']['owner_id'];
                
        return $obj;
    }
    
    //Returns a count of the dogs that belong to the specified user
    function countUserDogs($user_id){
        $sql = "select count(*) cnt from dogs d where d.owner_id=$user_id and d.active=1";
        $rs = $this->query($sql);
        $count = $rs[0][0]['cnt'];
        
        return $count;
    }
    
    //Returns the dog ids that the specified user owns
    function getUserDogIDs($user_id){
        $sql = "select d.id from dogs d where d.owner_id=$user_id and d.active=1";
        $rs = $this->query($sql);
        
        $data = array();
        if(is_array($rs)){
            foreach($rs as $i => $values){
                
                $data[] = $rs[$i]['d']['id'];
            }
        }
       
        return $data;
    }
    
    //Returns the latest dogfuel value for the specified dog
    function getLatestDogfuel($dog_id, $fromDate, $toDate, $timezone){
        $sql = "select sum(ad.dogfuel) as fuel from activity_dogs ad where ad.dog_id=$dog_id and CONVERT_TZ(ad.created, 'SYSTEM', '$timezone')  >= '$fromDate' and CONVERT_TZ(ad.created, 'SYSTEM', '$timezone') <= '$toDate'";
        $rs = $this->query($sql);
        
        $value = null;
        if(is_array($rs)){
            foreach($rs as $i => $values){
                $value = $rs[$i]['0']['fuel'] >= 100 ? 100 : $rs[$i]['0']['fuel'];
            }
        }
        
        return $value;
    }
    
    //Returns the latest dogfuel value for all dogs belonging to the specified user
    function getDogfuelValues($user_id,$fromDate, $toDate, $timezone){
        //$sql = "select COALESCE(SUM(ad.dogfuel),0) as fuel , d.id from dogs d left join activity_dogs ad on (d.id=ad.dog_id) where d.owner_id=$user_id and ad.created > NOW() - INTERVAL 24 HOUR group by d.id";
        $sql = "select COALESCE(SUM(ad.dogfuel),0) as fuel , d.id from dogs d left join activity_dogs ad on (d.id=ad.dog_id) where d.owner_id=$user_id and CONVERT_TZ(ad.created, 'SYSTEM', '$timezone')  >= '$fromDate' and CONVERT_TZ(ad.created, 'SYSTEM', '$timezone') <= '$toDate' group by d.id";
        $this->log("Dog->getDogfuelValues() sql is $sql", LOG_DEBUG);
        $rs = $this->query($sql);
        
        $data = array();
        if(is_array($rs)){
            foreach($rs as $i => $values){
                $value = $rs[$i]['0']['fuel'] >= 100 ? 100 : $rs[$i]['0']['fuel'];
                $data[$i]['dog_id'] = $rs[$i]['d']['id'];
                $data[$i]['dogfuel'] = $value;
            }
        }
        
        return $data;
    }
    
    //Returns a list of all the dogs that belong to the specified user
    function getUserDogs($userId, $fromDate, $toDate, $timezone){
        $sql = "select d.id, d.name, d.age, d.gender, d.mating, d.weight, p.thumb, p.path, db.name, d.size, d.breed_id  ";
        $sql .= " from dogs d";
        $sql .= " inner join users u on (d.owner_id = u.id)";
        $sql .= " inner join photos p on (d.photo_id = p.id)";
        $sql .= " inner join dog_breeds db on (d.breed_id = db.id)";
        $sql .= " where u.id = $userId and d.active=1";
        $rs = $this->query($sql);
        $data = array();
        
        
        if(is_array($rs)){
            foreach($rs as $i => $values){
                $obj['Dog']['id'] = $rs[$i]['d']['id'];
                $obj['Dog']['breed_id'] = $rs[$i]['d']['breed_id'];
                $obj['Dog']['name'] = $name = $rs[$i]['d']['name'];
                $obj['Dog']['dog_breed'] = $rs[$i]['db']['name'];
                $obj['Dog']['age'] = $rs[$i]['d']['age'];
                $obj['Dog']['gender'] = $rs[$i]['d']['gender'];
                $obj['Dog']['weight'] = $rs[$i]['d']['weight'];
                $obj['Dog']['photo'] = $rs[$i]['p']['path'];
                $obj['Dog']['thumb'] = $rs[$i]['p']['thumb'];
                $obj['Dog']['mating'] = $rs[$i]['d']['mating'];
                $obj['Dog']['size'] = $rs[$i]['d']['size'];
                $obj['Dog']['dogfuel'] = $this->getLatestDogfuel($rs[$i]['d']['id'], $fromDate, $toDate, $timezone);
                $data[] = $obj;
            }
        }
       
        return $data;
    }
    
    //Returns the count of likes for the specified dog
    function countDogLikes($dog_id){
        $sql = "select count(*) cnt from dog_likes dl where dog_id=$dog_id";
        $rs = $this->query($sql);
        $count = $rs[0][0]['cnt'];
        
        return $count;
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