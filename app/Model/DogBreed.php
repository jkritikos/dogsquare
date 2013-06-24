<?php

App::uses('AppModel', 'Model');
class DogBreed extends AppModel {
     public $name = 'DogBreed';
     
     function test(){
         echo "DOG BREED model";
     }
}

?>