<?php

class PlacesController extends AppController {
    /*Executed before all functions*/
    var $components = array('Cookie', 'RequestHandler','PhpThumb');
    var $helpers = array('Js','Time');
    	
    function beforeFilter() {	
        parent::beforeFilter();
        $this->set('headerTitle', "Configuration Management");
        $this->set('activeTab', "configurations");
    }
	
    function createPlace(){
        $currentUser = $this->Session->read('userID');
        
        if($currentUser != null){
            if (!empty($this->request->data)){
                
                $path_info = pathinfo(basename($_FILES['photo']['name']));
    		$fileExtension = $path_info['extension'];
                
                $dateString = Security::hash(time().rand(1, 10), 'md5');
                $fileName = $dateString.".".$fileExtension;
                
                //echo "<pre>"; var_dump($_FILES); echo "</pre>";
                
                $thumbInput = "/uploaded_files/places/$fileName";
                
                $uploadfile = UPLOAD_PATH.PLACE_PATH."/". "$fileName";
                if(is_uploaded_file($_FILES['photo']['tmp_name']) && move_uploaded_file($_FILES['photo']['tmp_name'], $uploadfile)){
                    $res = $this->PhpThumb->generateThumbnail($thumbInput, array(
                        'w' => 60, 'h' => 60, 'f' => 'png', 'q' => 95, 'fltr' => "ric|30|30"
                    ), PLACE_PHOTO_TYPE);
                
                    //If all went well with the thumbnail generation
                    if($res['error'] == 0){
                        $thumbFileName = $res['src'];
                        
                        //Save photo
                        $this->loadModel('Photo');
                        $obj = array();
                        $obj['Photo']['path'] = $fileName;
                        $obj['Photo']['thumb'] = $thumbFileName;
                        $obj['Photo']['user_id'] = $currentUser;
                        $obj['Photo']['type_id'] = PLACE_PHOTO_TYPE;
                        
                        if($this->Photo->save($obj)){
                            $photoID = $this->Photo->getLastInsertID();
                            
                            //Save place
                            $this->loadModel('Place');
                            
                            $this->request->data['Place']['photo_id'] = $photoID;
                            
                            if($this->Place->save($this->request->data)){
                                $this->set('notification', 'New place successfully created.');
                            } else {
                                $this->set('error', 'Unable to create the new place - please try again.');
                            }
                        }
                        
                        
                        
                        
                        
                    }
                    
                    
                    echo "<pre>"; var_dump($res); echo "</pre>";
                    //echo "<Br>using input $thumbInput";
                    
                }
                
                
                
                
            }

            $this->loadModel('PlaceCategory');
            $categoryNames = $this->PlaceCategory->find('all', array('fields' => array('PlaceCategory.id','PlaceCategory.name') ));
            $this->set('categoryNames', $categoryNames);

            $this->loadModel('User');
            $userNames = $this->User->find('all', array('fields' => array( 'User.id', 'User.name') ));
            $this->set('userNames', $userNames);
        } else {
            $this->requireLogin('/Places/createPlace');
        }
    }
	
    /*AJAX validator for place category validations*/
    function validatePlace(){
        if(isset($_REQUEST['place'])) $place = $_REQUEST['place'];
		if(isset($_REQUEST['edit_place'])) {
			$edit_place = $_REQUEST['edit_place'];
		} else{
			$edit_place = "";
		}
        $this->log("Configurations->validatePlace() called for $place and $edit_place", LOG_DEBUG);
        
        if((!empty($place) && !isset($edit_place)) || 
         	(!empty($place) && isset($edit_place) && $place != $edit_place)){
            $this->loadModel('Place');
            $dd = $this->Place->findByName($place);
	            if($dd != null && isset($dd['Place']['name'])){
	                $data['data[Place][name]'] = "Place already defined";
					
	            } else {
	                $data = true;
					
	            }
			} else {
        		$data = true;
        }
        
        $this->layout = 'blank';
        echo json_encode(compact('data', $data));
    }
	
	function searchPlace(){
        $currentUser = $this->Session->read('userID');
		if($currentUser != null){
	        $this->set('headerTitle', "Search places");
			
	        if (!empty($this->request->data)){
	        	$name = $this->request->data['Place']['name'];
	            $category = $this->request->data['Place']['category'];
	            $status = $this->request->data['Place']['active'];
				
				$data = $this->Place->search($name, $category, $status);
				$this->set('results', $data);
	        }
	
		} else {
	        $this->requireLogin('/Places/searchPlace');
		}
    }

	function editPlace($id){
        $currentUser = $this->Session->read('userID');
		if($currentUser != null){
            $this->set('headerTitle', "Edit place");
            $this->set('targetPlaceId', $id);
            
            if (!empty($this->request->data)){
                if($this->Place->save($this->request->data)){
                    $this->set('notification', 'Place details updated successfully.');
				} else {
		            $this->set('errorMsg', 'Unable to update the place - please try again.');
				}
            }

            $placeObj = $this->Place->findById($id);
            if($placeObj != null){
                $this->set('place', $placeObj);
            }
			
			$catId = $placeObj['Place']['category_id'];
			
			$this->loadModel('PlaceCategory');
			$placeCategoryById = $this->PlaceCategory->findById($catId);
        	if($placeCategoryById != null){
            	$this->set('placeCategoryById', $placeCategoryById);
       	 	}
			
			$categoryNames = $this->PlaceCategory->find('all', array('fields' => array('PlaceCategory.id','PlaceCategory.name') ));
			if($categoryNames != null){
				$this->set('categoryNames', $categoryNames);
			}

		} else {
	            $this->requireLogin("/Places/editPlace/$id");
		}
    }
}

?>