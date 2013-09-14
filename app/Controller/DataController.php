<?php

class DataController extends AppController {
    
    //Loads the dog breeds data
    function breeds(){
        $targetPath = $_SERVER["DOCUMENT_ROOT"];
        $uploadfile = $targetPath ."/". "breeds.txt";
        
        $this->loadModel('DogBreed');
        $this->loadModel('DogfuelRule');
        
        $linePointer = 0;
        $file = fopen($uploadfile, "r");
          
        while(!feof($file)) {
            $linePointer++;
            $line = fgets($file);
            
            echo "DataController->load() looping for line $linePointer - $line";
            
            $lineData = explode("\t", $line);
            
            //skip the header line
            if($linePointer <= 2){
                echo "<li>DataController->load() skipping header line $line";
                //continue;
            }
            
            $breed = $lineData[0];
            $origin = $lineData[1];
            $weight_min = $lineData[2];
            $weight_max = $lineData[3];
            $walk = $lineData[4];
            $play = $lineData[5];
            $kennel = $lineData[6];
            
            $obj['DogBreed']['name'] = $breed;
            $obj['DogBreed']['origin'] = $origin;
            $obj['DogBreed']['weight_from'] = $weight_min;
            $obj['DogBreed']['weight_to'] = $weight_max;
            $obj['DogBreed']['kennel_club'] = $kennel;
            
            if($this->DogBreed->save($obj)){
                echo "<li>Saved $breed";
            } else {
                echo "<li>Error saving $breed";
            }
            
        }
        
        fclose($file);
        $this->layout = 'blank';
    }
    
    //Loads the places data
    function places(){
        
    }
    
}

?>