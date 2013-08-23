<?php

App::uses('AppModel', 'Model');
class DogBreed extends AppModel {
    public $name = 'DogBreed';
     
    function test(){
        echo "DOG BREED model";
    }
	 
    //Get dog breed names which don't have any dog fuel info
    function getDogBreedNames(){
        $sql= "SELECT db.name, db.id ";
        $sql .= " FROM dog_breeds db ";
        $sql .= " WHERE NOT EXISTS (SELECT * FROM dogfuel_rules dr WHERE db.id = dr.breed_id)";
        $sql .= " ORDER BY db.name";

        $rs = $this->query($sql);

        $data = array();
        if(is_array($rs)){
            foreach($rs as $i => $values){
                $name = $rs[$i]['db']['name'];
                $id = $rs[$i]['db']['id'];

                $obj['DogBreed']['name'] = $name;
                $obj['DogBreed']['id'] = $id;

                $data[] = $obj;
            }
        }

        $this->log("DogfuelRule->getDogBreedNames() returns ".count($data), LOG_DEBUG);
        return $data;
    }
}

?>