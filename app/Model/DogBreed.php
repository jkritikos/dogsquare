<?php

App::uses('AppModel', 'Model');
class DogBreed extends AppModel {
     public $name = 'DogBreed';
     
     function test(){
         echo "DOG BREED model";
     }
	 
	 function getDogBreedName(){
	 	$sql = "select db.name, db.id ";
		$sql .= " from dog_breeds db ORDER BY db.name";
		
		$rs = $this->query($sql);
	
		$data = array();
		if(is_array($rs)){
            foreach($rs as $i => $values){
            	$id = $rs[$i]['db']['id'];
                $name = $rs[$i]['db']['name'];
		                
				$obj['DogBreed']['id'] = $id;
				$obj['DogBreed']['name'] = $name;
		
				$data[] = $obj;
            }
		}
		
		$this->log("DogBreed->getDogBreedName() returns ".count($data), LOG_DEBUG);
		return $data;
	 }
}

?>