<?php

App::uses('AppModel', 'Model');
class Feed extends AppModel {
    public $name = 'Feed';
     
    //new walk: user id, user_name, target_activity_id
    //checkin: user_id, user_name, target_place_id, target_place_name
    //new dog: user_id, user_name, target_dog_id, target_dog_name
    //friend new follower: user_id, user_name, target_user_id, target_username
    //friend like
    //friend comment
    //friend badges
}

?>