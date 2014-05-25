<?php

class DogsController extends AppController {
    /*Executed before all functions*/
    var $components = array('Cookie', 'RequestHandler','PhpThumb');
    var $helpers = array('Js','Time');
    	
    function beforeFilter() {	
        parent::beforeFilter();
        $this->set('headerTitle', "Dog Management");
        $this->set('activeTab', "dogs");
    }
    
    function index(){
        $currentUser = $this->Session->read('userID');
	if($currentUser != null){

	} else {
            $this->requireLogin('/Dogs/index');
	}
    }
    
    //Searches for dogs according to the specified criteria
    function search(){
        $currentUser = $this->Session->read('userID');
	if($currentUser != null){
            $this->set('headerTitle', "Search dogs");
            
            $this->loadModel('Country');
            $countries = $this->Country->find('list');
            $this->set('countries', $countries);
            
            $this->loadModel('DogBreed');
            $breeds = $this->DogBreed->find('list', array('order'=> 'name')); 
            $this->set('breeds', $breeds);
            
            if (!empty($this->request->data)){
                $name = $this->request->data['Dog']['name'];
                $breed = $this->request->data['Dog']['breed_id'];
                $country = $this->request->data['User']['country_id'];
                $mating = $this->request->data['Dog']['mating'];
                $results = $this->Dog->websearch($name, $breed, $country, $mating);
                $this->set('results', $results);
            }
            
	} else {
            $this->requireLogin('/Dogs/index');
	}
    }
    
    //Edits the specified dog
    function edit($id){
        $currentUser = $this->Session->read('userID');
	if($currentUser != null){
            
            //load data for the menu
            $photos = 0;
            $activities = 0;
            $likes = 0;
            
            $this->set('photos', $photos);
            $this->set('activities', $activities);
            $this->set('likes', $likes);
            
            $this->set('id', $id);
            $dog = $this->Dog->findById($id);
            $this->set('dog', $dog);
            
            $this->loadModel('DogBreed');
            $breeds = $this->DogBreed->find('list', array('order'=> 'name')); 
            $this->set('breeds', $breeds);
            
	} else {
            $this->requireLogin("/Dogs/edit/$id");
	}
    }
    
}

?>