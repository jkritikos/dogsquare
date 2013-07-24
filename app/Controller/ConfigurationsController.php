<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of ConfigurationsController
 *
 * @author jace
 */

//App::uses('Core', 'l10n', 'Sanitize');

class ConfigurationsController extends AppController{
    
    var $components = array('Cookie', 'RequestHandler');
    var $helpers = array('Js','Time');
    
    /*Executed before all functions*/
    function beforeFilter() {

        parent::beforeFilter();
	$this->set('headerTitle', "Configuration Management");
	$this->set('activeTab', "configurations");
    }
    
    function index(){
        $currentUser = $this->Session->read('userID');
	if($currentUser != null){

	} else {
            $this->requireLogin('/configurations/index');
	}
    }
    
    /*Creates a new dog s*/
    function breedCreate(){
        $currentUser = $this->Session->read('userID');
	if($currentUser != null){
            
            //on submit
            if (!empty($this->request->data)){
                
                $this->loadModel('DogBreed');
                if($this->DogBreed->save($this->request->data)){
                    $this->set('notification', 'New breed successfully created.');
                } else {
                    $this->set('error', 'Unable to create the new breed - please try again.');
                }
                
            }
            
	} else {
            $this->requireLogin('/configurations/breedCreate');
	}
    }
    
    /*AJAX validator for dog breed validations*/
    function validateBreed(){
        if(isset($_REQUEST['breed'])) $breed = $_REQUEST['breed'];
		if(isset($_REQUEST['edit_breed'])) {
			$edit_breed = $_REQUEST['edit_breed'];
		} else{
			$edit_breed = "";
		}
        $this->log("Configurations->validateBreed() called for $breed and $edit_breed", LOG_DEBUG);
        
        //If we are creating a new breed, or editing an existing one (and have edited the name)
        if((!empty($breed) && !isset($edit_breed)) || (!empty($breed) && isset($edit_breed) && $breed != $edit_breed)){
            $this->loadModel('DogBreed');
            
            $dd = $this->DogBreed->findByName($breed);
            if($dd != null && isset($dd['DogBreed']['name'])){
                $data['data[DogBreed][name]'] = "Breed already defined";

            } else {
                $data = true;
            }
        } else {
        	$data = true;
        }
        
        $this->layout = 'blank';
        echo json_encode(compact('data', $data));
    }
    
    /*Returns all breeds*/
    function breedView(){
        $currentUser = $this->Session->read('userID');
	if($currentUser != null){
            
            $this->loadModel('DogBreed');
            $breeds = $this->DogBreed->find('all');
            $this->set('breeds', $breeds);
            
	} else {
            $this->requireLogin('/configurations/breedView');
	}
    }
    
    function breedEdit($id){
        $currentUser = $this->Session->read('userID');
	if($currentUser != null){
            
            $this->loadModel('DogBreed');
            
            //on submit
            if (!empty($this->request->data)){
                $this->DogBreed->id = $id;
                if($this->DogBreed->save($this->request->data)){
                    $this->set('notification', 'Breed successfully modified.');
                } else {
                    $this->set('error', 'Unable to modify the breed - please try again.');
                }
            }
            
            //load breed object
            $breed = $this->DogBreed->findById($id);
            if($breed != null){
                $this->set('breed', $breed);
            }
            
            //return the breed id to the view
            $this->set('id',$id);
            
	} else {
            $this->requireLogin("/configurations/breedEdit/$id");
	}
    }

	
	function dogfuelView(){
        $currentUser = $this->Session->read('userID');
	if($currentUser != null){
		$this->set('currentUser', $currentUser);
		
        $this->loadModel('DogfuelRule');
        
		$this->loadModel('DogBreed');
		$breed = $this->DogBreed->find('all', array('order' => 'DogBreed.name'));
		$this->set('breed', $breed);
		
		$dogfuel = $this->DogfuelRule->getDogfuelAll();
		$this->set('dogfuel', $dogfuel);
		
        //on submit
        if (!empty($this->request->data)){
            if($this->DogfuelRule->save($this->request->data)){
                $this->set('notification', 'Dog fuel successfully created.');
				$this->redirect(array('action' => 'dogfuelView'));
            } else {
                $this->set('error', 'Unable to create the Dog fuel - please try again.');
            }
        }
	} else {
            $this->requireLogin("/configurations/dogfuelView");
	}
    }
	
	function validateDogfuel(){
        if(isset($_REQUEST['dogfuel'])) $dogfuel = $_REQUEST['dogfuel'];
		
        $this->log("Configurations->validateDogfuel() called for $dogfuel", LOG_DEBUG);
        
        //If we are creating a new dog fuel, or editing an existing one (and have edited the name)
        if(!empty($dogfuel)){
            
            $this->loadModel('DogfuelRule');
            
            $dd = $this->DogfuelRule->findByBreedId($dogfuel);
            if($dd != null && isset($dd['DogfuelRule']['breed_id'])){
                $data['data[DogfuelRule][breed_id]'] = "Breed already defined";
            } else {
                $data = true;
            }
        } else {
        	$data = true;
        }
        
        $this->layout = 'blank';
        echo json_encode(compact('data', $data));
    }


	function dogfuelEdit($id){
		$currentUser = $this->Session->read('userID');
	if($currentUser != null){
            
            $this->loadModel('DogfuelRule');
		
            //on submit
            if (!empty($this->request->data)){
                $this->DogfuelRule->id = $id;
                if($this->DogfuelRule->save($this->request->data)){
                    $this->set('notification', 'Dog fuel rule successfully modified.');
                } else {
                    $this->set('error', 'Unable to modify the dog fuel rule - please try again.');
                }
            }
			
			//load dog fuel object
            $dogfuel = $this->DogfuelRule->getDogfuelById($id);
			
            if($dogfuel != null){
                $this->set('dogfuel', $dogfuel);
            }
            
            //return the dog fuel id to the view
            $this->set('id',$id);
            
	} else {
            $this->requireLogin("/configurations/dogfuelEdit/$id");
	}
	}
}

?>
