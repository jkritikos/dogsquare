<?php

App::uses('AppModel', 'Model');
class Feed extends AppModel {
    public $name = 'Feed';
     
    //DONE new walk: user id, target_activity_id
    //DONE new dog: user_id, user_name, target_dog_id, target_dog_name
    //DONE friend new follower: user_id, user_name, target_user_id, target_username
    //DONE friend like dog
    
    //checkin: user_id, user_name, target_place_id, target_place_name
    
    //friend like
    //friend comment
    //friend badges
    
    //Returns the latest 20 feed entries from all the users followed by $user_id
    function getFeed($user_id){
        $sql = "select unix_timestamp(f.created) created, f.user_from, f.user_from_name, f.target_user_id, f.target_user_name, f.type_id, ";
        $sql .= "f.target_dog_id, f.target_dog_name, f.target_place_id, f.target_place_name, f.activity_id, ";
        $sql .= "p.thumb from feeds f inner join users u on (u.id = f.user_from) inner join photos p on (u.photo_id = p.id) ";
        $sql .= " where f.user_from in (select uf.follows_user from user_follows uf where uf.user_id=$user_id) ";
        $sql .= "order by f.created desc limit 20";
        
        $rs = $this->query($sql);

        $data = array();
        if(is_array($rs)){
            foreach($rs as $i => $values){
               
                $obj['Feed']['type_id'] = $rs[$i]['f']['type_id'];
                $obj['Feed']['user_from'] = $rs[$i]['f']['user_from'];
                $obj['Feed']['user_from_name'] = $rs[$i]['f']['user_from_name'];
                $obj['Feed']['target_user_id'] = $rs[$i]['f']['target_user_id'];
                $obj['Feed']['target_user_name'] = $rs[$i]['f']['target_user_name'];
                $obj['Feed']['target_dog_id'] = $rs[$i]['f']['target_dog_id'];
                $obj['Feed']['target_dog_name'] = $rs[$i]['f']['target_dog_name'];
                $obj['Feed']['target_place_id'] = $rs[$i]['f']['target_place_id'];
                $obj['Feed']['target_place_name'] = $rs[$i]['f']['target_place_name'];
                $obj['Feed']['activity_id'] = $rs[$i]['f']['activity_id'];
                $obj['Feed']['user_from_thumb'] = $rs[$i]['p']['thumb'];
                $obj['Feed']['created'] = $rs[$i][0]['created'] * 1000;

                $data[] = $obj;
            }
        }
        
        return $data;
    }
}

?>