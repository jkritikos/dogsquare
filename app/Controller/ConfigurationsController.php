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
    
    /*Creates a new dog breed*/
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
}

?>
