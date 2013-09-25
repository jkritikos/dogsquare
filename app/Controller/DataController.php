<?php

class DataController extends AppController {
    
    //Loads the dog breeds data
    function breeds(){
        $targetPath = $_SERVER["DOCUMENT_ROOT"];
        $uploadfile = $targetPath ."/". "j2.txt";
        
        $this->loadModel('DogBreed');
        $this->loadModel('DogfuelRule');
        
        $linePointer = 0;
        $file = fopen($uploadfile, "r");
          
        while(!feof($file)) {
            $linePointer++;
            $line = fgets($file);
            
            $lineData = explode("\t", $line);
            
            //skip the header line
            if($linePointer <= 1){
                echo "<li>DataController->load() skipping header line $linePointer";
                continue;
            }
            
            echo "<pre>";var_dump($lineData);echo "</pre>";
            
            $breed = trim($lineData[0]);
            $origin = trim($lineData[1]);
            $weight_min = trim($lineData[2]);
            $weight_max = trim($lineData[3]);
            $walk = trim($lineData[4]);
            $play = trim($lineData[5]);
            $kennel = trim($lineData[6]);
            
            //echo "<li>DataController->load() looping for line $linePointer - breed $breed walk $walk play $play";
            
            $this->DogBreed->create();
            $obj['DogBreed']['name'] = $breed;
            $obj['DogBreed']['origin'] = $origin;
            $obj['DogBreed']['weight_from'] = $weight_min;
            $obj['DogBreed']['weight_to'] = $weight_max;
            //$obj['DogBreed']['kennel_club'] = $kennel;
            
            if($this->DogBreed->save($obj)){
                $breeID = $this->DogBreed->getLastInsertID();
                $this->DogfuelRule->create();
                $obj2['DogfuelRule']['user_id'] = 0;
                $obj2['DogfuelRule']['walk_distance'] = $walk;
                $obj2['DogfuelRule']['playtime'] = $play;
                $obj2['DogfuelRule']['active'] = 1;
                $obj2['DogfuelRule']['breed_id'] = $breeID;
                
                if($this->DogfuelRule->save($obj2)){
                    echo "<li>Saved $breed";
                } else {
                    echo "<li>Error saving rule for $breed";
                }
                
            } else {
                echo "<li>Error saving $breed";
            }
        }
        
        fclose($file);
        $this->layout = 'blank';
    }
    
    //Loads the places data
    function places(){
        $targetPath = $_SERVER["DOCUMENT_ROOT"];
        $uploadfile = $targetPath ."/". "places2.txt";
        
        $this->loadModel('Place');
        $this->loadModel('DogfuelRule');
        
        $linePointer = 0;
        $file = fopen($uploadfile, "r");
          
        while(!feof($file)) {
            $linePointer++;
            $line = fgets($file);
            
            $lineData = explode("\t", $line);
            
            //skip the header line
            if($linePointer <= 1){
                echo "<li>DataController->load() skipping header line $linePointer";
                continue;
            }
            
            echo "<pre>";var_dump($lineData);echo "</pre>";
            
             $name = trim($lineData[0]);
             $lat = trim($lineData[4]);
             $lon = trim($lineData[5]);
             
             $this->Place->create();
             $obj['Place']['name'] = $name;
             $obj['Place']['lon'] = $lon;
             $obj['Place']['lat'] = $lat;
             $obj['Place']['active'] = 1;
             
             if($name != ''){
                 if($this->Place->save($obj)){
                    echo "<li>Saved $name";
                } else {
                    echo "<li>Error saving $name";
                }
             } else {
                 echo "<li>Ignoring line coz it is empty.";
             }
             
             
            
        }
        
        fclose($file);
        $this->layout = 'blank';
    }
    
}

?>