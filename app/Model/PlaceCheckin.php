<?php
App::uses('AppModel', 'Model');
class PlaceCheckin extends AppModel {
     public $name = 'PlaceCheckin';
     
     //Returns the nearby checkins for the specified coordinates
     function getNearbyCheckins($lat, $lon){
         
     }
}

?>