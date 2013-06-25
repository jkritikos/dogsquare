<?php


class PlaceCategoriesController extends AppController{
		
	/*Executed before all functions*/
    function beforeFilter() {	
		parent::beforeFilter();
		$this->set('headerTitle', "Place Category Management");
		$this->set('activeTab', "configurations");
    }
    
    function index(){
        $currentUser = $this->Session->read('userID');
		if($currentUser != null){
	
		} else {
	            $this->requireLogin('/configurations/index');
		}
    }	
		
	function createPlaceCategory(){
	    $currentUser = $this->Session->read('userID');
		if($currentUser != null){
			if (!empty($this->request->data)){
	            $this->loadModel('PlaceCategory');
	            if($this->PlaceCategory->save($this->request->data)){
	                $this->set('notification', 'New place category successfully created.');
	            } else {
	                $this->set('error', 'Unable to create the new place category - please try again.');
	            } 
	        }
		} else {
	            $this->requireLogin('/configurations/index');
		}
    }
	
	/*AJAX validator for place category validations*/
    function validatePlaceCategory(){
        if(isset($_REQUEST['place_category'])) $place_category = $_REQUEST['place_category'];
		if(isset($_REQUEST['edit_place_category'])) {
			$edit_place_category = $_REQUEST['edit_place_category'];
		} else{
			$edit_place_category = "";
		}
        $this->log("Configurations->validatePlaceCategory() called for $place_category and $edit_place_category", LOG_DEBUG);
        
        if((!empty($place_category) && !isset($edit_place_category)) || 
         	(!empty($place_category) && isset($edit_place_category) && $place_category != $edit_place_category)){
            $this->loadModel('PlaceCategory');
            
            $dd = $this->PlaceCategory->findByName($place_category);
            if($dd != null && isset($dd['PlaceCategory']['name'])){
                $data['data[PlaceCategory][name]'] = "Place category already defined";

            } else {
                $data = true;
            }
			} else {
        	$data = true;
        }
        
        $this->layout = 'blank';
        echo json_encode(compact('data', $data));
    }

	function viewPlaceCategory(){
        $currentUser = $this->Session->read('userID');
		if($currentUser != null){
            $this->loadModel('PlaceCategory');
            $placeCategories = $this->PlaceCategory->find('all');
            $this->set('placeCategories', $placeCategories);
		} else {
	        $this->requireLogin('/configurations/index');
		}
    }
	
	function editPlaceCategory($id){
        $currentUser = $this->Session->read('userID');
		if($currentUser != null){
            $this->loadModel('PlaceCategory');
            
            //on submit
            if (!empty($this->request->data)){
            	$this->PlaceCategory->id = $id;
                if($this->PlaceCategory->save($this->request->data)){
                    $this->set('notification', 'Place category successfully modified.');
                } else {
                    $this->set('error', 'Unable to modify the place category - please try again.');
                }
            }
            
            //load place category object
            $placeCategory = $this->PlaceCategory->findById($id);
            if($placeCategory != null){
                $this->set('placeCategory', $placeCategory);
            }
            
            //return the place category id to the view
            $this->set('id',$id);
	            
		} else {
	        $this->requireLogin('/configurations/index');
		}
	    }
}

?>