<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of ApiController
 *
 * @author jace
 */
class ApiController extends AppController{
    
    var $components = array('Cookie', 'RequestHandler');
    var $helpers = array('Js','Time');
    //put your code here
    
    function hello(){
        
        $data['test'] = 'edd';
        
        $this->layout = 'blank';
        echo json_encode(compact('data', $data));
    }

    function login(){
        if(isset($_REQUEST['user_id'])) $userID = $_REQUEST['user_id'];
        if(isset($_REQUEST['token'])) $token = $_REQUEST['token'];
        
        $response = false;
        
        if($userID != '' && $token != ''){
            $this->loadModel('User');
            $serverToken = $this->User->generateToken($userID);
            if($serverToken == $token){
                $response = true;
            }
        }
        
        $data['response'] = $response;
        $this->layout = 'blank';
        echo json_encode(compact('data', $data));
    }
    
    function addDog(){
        if(isset($_REQUEST['user_id'])) $userID = $_REQUEST['user_id'];
        if(isset($_REQUEST['name'])) $name = $_REQUEST['name'];
        if(isset($_REQUEST['breed_id'])) $breed = $_REQUEST['breed_id'];
        if(isset($_REQUEST['age'])) $age = $_REQUEST['age'];
        if(isset($_REQUEST['weight'])) $weight = $_REQUEST['weight'];
        if(isset($_REQUEST['gender'])) $gender = $_REQUEST['gender'];
        if(isset($_REQUEST['mating'])) $mating = $_REQUEST['mating'];
        
        $this->log("API->addDog() called for $name breed $breed and photo ".$_FILES['photo'] , LOG_DEBUG);
        
        $dogCreated = false;
        $dogID = null;
        $response = null;
        $errorMessage = null;
        $securityToken = null;
        
        //Save dog object
        $this->loadModel('Dog');
        $dog = array();
        $dog['Dog']['breed_id'] = $breed;
        $dog['Dog']['owner_id'] = $userID;
        $dog['Dog']['name'] = $name;
        $dog['Dog']['gender'] = $gender;
        $dog['Dog']['mating'] = $mating;
        $dog['Dog']['weight'] = $weight;
        $dog['Dog']['age'] = $age;
        $this->log("API->addDog() called ", LOG_DEBUG);
        if($this->Dog->save($dog)){
            
            $dogCreated = true;
            $dogID = $this->Dog->getLastInsertID();
        } else {
            $response = REQUEST_FAILED;
            $errorMessage = ERROR_DOG_CREATION;
        }
        
        //handle photo if dog was created OK
        if($dogCreated && isset($_FILES['photo'])){
            //$targetPath = FILE_PATH;
            
            //Check for valid extension
            $dateString = Security::hash(time().rand(1, 10), 'md5');
            $fileExtension = ".jpeg";
            $fileName = $dateString.$fileExtension;
            //$filePath = FILE_PATH ."/". $fileName;
           
            $uploadfile = UPLOAD_PATH.DOG_PATH."/". "$fileName";
            
            $this->log("API->addDog() uploadfile is $uploadfile" , LOG_DEBUG);
            
            if(is_uploaded_file($_FILES['photo']['tmp_name']) && move_uploaded_file($_FILES['photo']['tmp_name'], $uploadfile)){
                $this->log("API->addDog() uploading succeeded" , LOG_DEBUG);
                
                //Save photo info to the db
                $this->loadModel('Photo');
                $obj = array();
                $obj['Photo']['path'] = $fileName;
                $obj['Photo']['user_id'] = $userID;
                if($this->Photo->save($obj)){
                    $photoID = $this->Photo->getLastInsertID();
                    
                    $this->log("API->addDog() saved photo $photoID to db" , LOG_DEBUG);
                    
                    //Update dog with profile photo
                    $dog['Dog']['id'] = $dogID;
                    $dog['Dog']['photo_id'] = $photoID;
                    if(!$this->Dog->save($dog)){
                        $this->log("API->addDog() failed to set profile image for dog $dogID" , LOG_DEBUG);
                        
                        $response = REQUEST_FAILED;
                        $errorMessage = ERROR_DOG_PHOTO_UPLOAD;
                    } else {
                        $response = REQUEST_OK;
                    }
                    
                } else {
                    $this->log("API->addDog() saving photo to db failed" , LOG_DEBUG);
                    $response = REQUEST_FAILED;
                    $errorMessage = ERROR_DOG_PHOTO_UPLOAD;
                }
                
            } else {
                $this->log("API->addDog() uploading failed" , LOG_DEBUG);
                $response = REQUEST_FAILED;
                $errorMessage = ERROR_DOG_PHOTO_UPLOAD;
            }
        } else {
            $this->log("API->addDog() no photo found" , LOG_DEBUG);
        }
       
        $this->log("API->addDog() returns: response $response error $errorMessage" , LOG_DEBUG);
        
        $data['response'] = $response;
        $data['dog_id'] = $dogID;
        $data['error'] = $errorMessage;
        $data['dog_id'] = $dogID;
        
        $this->layout = 'blank';
        echo json_encode(compact('data', $data));
    }
    
    //Creates a new place
    function addPlace(){
        if(isset($_REQUEST['user_id'])) $userID = $_REQUEST['user_id'];
        if(isset($_REQUEST['name'])) $name = $_REQUEST['name'];
        if(isset($_REQUEST['category_id'])) $categoryId = $_REQUEST['category_id'];
        if(isset($_REQUEST['longitude'])) $longitude = $_REQUEST['longitude'];
        if(isset($_REQUEST['latitude'])) $latitude = $_REQUEST['latitude'];
        
        $this->log("API->addPlace() called for $name and with photo ".$_FILES['photo'] , LOG_DEBUG);
        
        $placeCreated = false;
        $placeID = null;
        $response = null;
        $errorMessage = null;
        
        //Save place object
        $this->loadModel('Place');
        $place = array();
        $place['Place']['user_id'] = $userID;
        $place['Place']['name'] = $name;
        $place['Place']['category_id'] = $categoryId;
        $place['Place']['lon'] = $longitude;
        $place['Place']['lat'] = $latitude;
        
        if($this->Place->save($place)){
            
            $placeCreated = true;
            $placeID = $this->Place->getLastInsertID();
        } else {
            $response = REQUEST_FAILED;
            $errorMessage = ERROR_PLACE_CREATION;
        }
        
        //handle photo if place was created OK
        if($placeCreated && isset($_FILES['photo'])){
            
            //Check for valid extension
            $dateString = Security::hash(time().rand(1, 10), 'md5');
            $fileExtension = ".jpeg";
            $fileName = $dateString.$fileExtension;
           
            $uploadfile = UPLOAD_PATH.PLACE_PATH."/". "$fileName";
            
            $this->log("API->addPlace() uploadfile is $uploadfile" , LOG_DEBUG);
            
            if(is_uploaded_file($_FILES['photo']['tmp_name']) && move_uploaded_file($_FILES['photo']['tmp_name'], $uploadfile)){
                $this->log("API->addPlace() uploading succeeded" , LOG_DEBUG);
                
                //Save photo info to the db
                $this->loadModel('Photo');
                $obj = array();
                $obj['Photo']['path'] = $fileName;
                $obj['Photo']['user_id'] = $userID;
                if($this->Photo->save($obj)){
                    $photoID = $this->Photo->getLastInsertID();
                    
                    $this->log("API->addPlace() saved photo $photoID to db" , LOG_DEBUG);
                    
                    //Update place with profile photo
                    $place['Place']['id'] = $placeID;
                    $place['Place']['photo_id'] = $photoID;
                    if(!$this->Place->save($place)){
                        $this->log("API->addPlace() failed to set profile image for dog $placeID" , LOG_DEBUG);
                        
                        $response = REQUEST_FAILED;
                        $errorMessage = ERROR_PLACE_PHOTO_UPLOAD;
                    } else {
                        $response = REQUEST_OK;
                    }
                    
                } else {
                    $this->log("API->addPlace() saving photo to db failed" , LOG_DEBUG);
                    $response = REQUEST_FAILED;
                    $errorMessage = ERROR_DOG_PHOTO_UPLOAD;
                }
                
            } else {
                $this->log("API->addPlace() uploading failed" , LOG_DEBUG);
                $response = REQUEST_FAILED;
                $errorMessage = ERROR_PLACE_PHOTO_UPLOAD;
            }
        } else {
            $this->log("API->addPlace() no photo found" , LOG_DEBUG);
        }
       
        $this->log("API->addPlace() returns: response $response error $errorMessage" , LOG_DEBUG);
        
        $data['response'] = $response;
        $data['place_id'] = $placeID;
        $data['error'] = $errorMessage;
        
        $this->layout = 'blank';
        echo json_encode(compact('data', $data));
    }
    
    function signup(){
        if(isset($_REQUEST['name'])) $name = $_REQUEST['name'];
        if(isset($_REQUEST['email'])) $email = $_REQUEST['email'];
        if(isset($_REQUEST['password'])) $password = $_REQUEST['password'];
        if(isset($_REQUEST['age'])) $age = $_REQUEST['age'];
        if(isset($_REQUEST['facebook_id'])) $facebook_id = $_REQUEST['facebook_id'];
	if(isset($_REQUEST['gender'])) $gender = $_REQUEST['gender'];
        
        $this->log("API->signup() called for $name and photo ".$_FILES['photo'] , LOG_DEBUG);
        
        $userCreated = false;
        $userID = null;
        $response = null;
        $errorMessage = null;
        $securityToken = null;
        
        $this->loadModel('User');
        
        //Check if email exists
        if($this->User->findAllByEmail($email) != null){
            $this->log("API->signup() email $email is already in use" , LOG_DEBUG);
            $response = ERROR_EMAIL_TAKEN;
        } else {
            //Save user object
            $user = array();
            $user['User']['name'] = $name;
            $user['User']['email'] = $email;
            $user['User']['password'] = $this->User->hashPassword($password);

            if(isset($facebook_id) && $facebook_id != ''){
                $user['User']['facebook_id'] = $facebook_id;
            }

            if(isset($age) && $age != ''){
                $user['User']['age'] = $age;
            }

            if(isset($gender) && $gender != ''){
                if($gender == 'male') $gender = 1;
                else if($gender == 'female') $gender = 2;
                $user['User']['gender'] = $gender;
            }

            if($this->User->save($user)){
                $userCreated = true;
                $userID = $this->User->getLastInsertID();
                $securityToken = $this->User->generateToken($userID);
            } else {
                $response = REQUEST_FAILED;
                $errorMessage = ERROR_USER_CREATION;
            }
        
            //handle photo if user was created OK
            if($userCreated && isset($_FILES['photo'])){
                //$targetPath = FILE_PATH;
                
                //Check for valid extension
                $dateString = Security::hash(time().rand(1, 10), 'md5');
                $fileExtension = ".jpeg";
                $fileName = $dateString.$fileExtension;
                //$filePath = FILE_PATH ."/". $fileName;
                $uploadfile = UPLOAD_PATH.USER_PATH."/". "$fileName";

                //Thumbnail
                $filenameThumb = "thumb_".$dateString.$fileExtension;
                //$filePathThumb = FILE_PATH ."/". $filenameThumb;
                $uploadfileThumb = UPLOAD_PATH.USER_PATH."/". "$filenameThumb";
                
                $this->log("API->signup() uploadfile is $uploadfile AND thumb is $uploadfileThumb" , LOG_DEBUG);

                if(is_uploaded_file($_FILES['photo']['tmp_name']) && move_uploaded_file($_FILES['photo']['tmp_name'], $uploadfile)){
                    if(is_uploaded_file($_FILES['thumb']['tmp_name']) && move_uploaded_file($_FILES['thumb']['tmp_name'], $uploadfileThumb)){
                        $this->log("API->signup() uploading succeeded" , LOG_DEBUG);

                        //Save photo info to the db
                        $this->loadModel('Photo');
                        $obj = array();
                        $obj['Photo']['path'] = $fileName;
                        $obj['Photo']['thumb'] = $filenameThumb;
                        $obj['Photo']['user_id'] = $userID;
                        if($this->Photo->save($obj)){
                            $photoID = $this->Photo->getLastInsertID();

                            $this->log("API->signup() saved photo $photoID to db" , LOG_DEBUG);

                            //Update user with profile photo
                            $user['User']['id'] = $userID;
                            $user['User']['photo_id'] = $photoID;
                            if(!$this->User->save($user)){
                                $this->log("API->signup() failed to set profile image for user $userID" , LOG_DEBUG);

                                $response = REQUEST_FAILED;
                                $errorMessage = ERROR_USER_PHOTO_UPLOAD;
                            } else {
                                $response = REQUEST_OK;
                            }

                        } else {
                            $this->log("API->signup() saving photo to db failed" , LOG_DEBUG);
                            $response = REQUEST_FAILED;
                            $errorMessage = ERROR_USER_PHOTO_UPLOAD;
                        }

                    } else {
                        $this->log("API->signup() uploading failed" , LOG_DEBUG);
                        $response = REQUEST_FAILED;
                        $errorMessage = ERROR_USER_PHOTO_UPLOAD;
                    }
                } else {
                    $this->log("API->signup() uploading failed" , LOG_DEBUG);
                    $response = REQUEST_FAILED;
                    $errorMessage = ERROR_USER_PHOTO_UPLOAD;
                }
            } else {
                $this->log("API->signup() no photo found" , LOG_DEBUG);
            }
        }
        
        $this->log("API->signup() returns: response $response error $errorMessage token $securityToken" , LOG_DEBUG);
        
        $data['response'] = $response;
        $data['error'] = $errorMessage;
        $data['token'] = $securityToken;
        $data['user_id'] = $userID;
        
        
        $this->layout = 'blank';
        echo json_encode(compact('data', $data));
    }
    
    //Checks if the specified list of emails maps to dogsquare users
    function areUsers(){
        if(isset($_REQUEST['user_id'])) $user_id = $_REQUEST['user_id'];
        if(isset($_REQUEST['list'])) $list = $_REQUEST['list'];
        
        $this->loadModel('User');
        $userData = $this->User->areUsers($list);
        
        $data['response'] = REQUEST_OK;
        $data['results'] = $userData;
        
        $this->layout = 'blank';
        echo json_encode(compact('data', $data));
    }
    
    //TODO Alex TERRIBLE bug here, find it..
    function searchUser(){
        if(isset($_REQUEST['name'])){
            $name = $_REQUEST['name'];
        } else {
            $name = "";
        }
        
        $this->loadModel('User');
        $userData = $this->User->search($name, null, null);
        
        $data['response'] = REQUEST_OK;
        $data['users'] = $userData;
        $this->layout = 'blank';
        echo json_encode(compact('data', $data));
    }
    
    //Follows the specified user
    function followUser(){
        if(isset($_REQUEST['user_id'])) $user_id = $_REQUEST['user_id'];
        if(isset($_REQUEST['follow_user'])) $follow_user = $_REQUEST['follow_user'];
        
        $this->loadModel('UserFollows');
        $obj = array();
        $obj['UserFollows']['user_id'] = $user_id;
        $obj['UserFollows']['follows_user'] = $follow_user;
        
        if(!$this->UserFollows->isUserFollowing($user_id, $follow_user)){
            if($this->UserFollows->save($obj)){
                $response = REQUEST_OK;
            } else {
                $response = REQUEST_FAILED;
            }
        } else {
            $response = ERROR_USER_ALREADY_FOLLOWING;
        }
        
        $data['response'] = $response;
        
        $this->layout = 'blank';
        echo json_encode(compact('data', $data));
    }
    
    //Unfollows the specified user
    function unfollowUser(){
        if(isset($_REQUEST['user_id'])) $user_id = $_REQUEST['user_id'];
        if(isset($_REQUEST['follow_user'])) $follow_user = $_REQUEST['follow_user'];
        
        $this->loadModel('UserFollows');
        if($this->UserFollows->isUserFollowing($user_id, $follow_user)){
            $rows = $this->UserFollows->deleteUserFollow($user_id, $follow_user);
            
            if($rows > 0){
                $response = REQUEST_OK;
            } else {
                $response = REQUEST_FAILED;
            }
            
        } else {
            $response = ERROR_USER_NOT_FOLLOWING;
        }
        
        $data['response'] = $response;
        
        $this->layout = 'blank';
        echo json_encode(compact('data', $data));
    }
    
    //Returns the followers of user $target_id
    function getFollowers(){
        if(isset($_REQUEST['user_id'])) $user_id = $_REQUEST['user_id'];
        if(isset($_REQUEST['target_id'])) $target_id = $_REQUEST['target_id'];
        
        $this->loadModel('UserFollows');
        $users = $this->UserFollows->getFollowers($target_id);
        
        $data['response'] = REQUEST_OK;
        $data['users'] = $users;
        
        $this->layout = 'blank';
        echo json_encode(compact('data', $data));
    }
    
    //Returns the users following user $target_id
    function getFollowing(){
        if(isset($_REQUEST['user_id'])) $user_id = $_REQUEST['user_id'];
        if(isset($_REQUEST['target_id'])) $target_id = $_REQUEST['target_id'];
        
        $this->loadModel('UserFollows');
        $users = $this->UserFollows->getFollowing($target_id);
        
        $data['response'] = REQUEST_OK;
        $data['users'] = $users;
        
        $this->layout = 'blank';
        echo json_encode(compact('data', $data));
    }
    
    //Returns the unread notifications of the specified user
    function getNotifications(){
        if(isset($_REQUEST['user_id'])) $user_id = $_REQUEST['user_id'];
        $this->log("API->getNotifications() called for user $user_id" , LOG_DEBUG);
        
        $this->loadModel('UserNotification');
        $notifications = $this->UserNotification->getUnreadNotifications($user_id);
        
        $data['response'] = REQUEST_OK;
        $data['notifications'] = $notifications;
        
        $this->layout = 'blank';
        echo json_encode(compact('data', $data));
    }
    
    function saveActivity(){
        
    }
    
    function getUser(){
        
    }
    
    function getInbox(){
        
    }
    
    function getDog(){
        if(isset($_REQUEST['dog_id'])) $dog_id = $_REQUEST['dog_id'];
        
        $this->loadModel('Dog');
        $dog = $this->Dog->getDogById($dog_id);
        
        $data['response'] = REQUEST_OK;
        $data['dog'] = $dog;
        
        $this->layout = 'blank';
        echo json_encode(compact('data', $data));
    }
    
    function getPlace(){
        if(isset($_REQUEST['place_id'])) $place_id = $_REQUEST['place_id'];
        
        $this->loadModel('Place');
        $place_id = $this->Place->getPlaceById($place_id);
        
        $data['response'] = REQUEST_OK;
        $data['place'] = $place_id;
        
        $this->layout = 'blank';
        echo json_encode(compact('data', $data));
    }
    
    function sendMessage(){
        
    }
    
    //Searches for users and places
    function search(){
        
    }
    
    //Searches for nearby places
    function getPlaces(){
        
    }
    
    //Performs a checkin
    function checkin(){
        
    }
    
    //Sets the user's current location
    function saveLocation(){
        
    }
    
    //Sets a like for an activity
    function likeActivity(){
        if(isset($_REQUEST['user_id'])) $user_id = $_REQUEST['user_id'];
        if(isset($_REQUEST['activity_id'])) $activity_id = $_REQUEST['activity_id'];
        
        $this->loadModel('ActivityLike');
        $obj = array();
        $obj['ActivityLike']['user_id'] = $user_id;
        $obj['ActivityLike']['activity_id'] = $activity_id;
        
        if(!$this->ActivityLike->userLikesActivity($user_id, $activity_id)){
            if($this->ActivityLike->save($obj)){
                $response = REQUEST_OK;
            } else {
                $response = REQUEST_FAILED;
            }
        } else {
            $response = REQUEST_INVALID;
        }
        
        $data['response'] = $response;
        
        $this->layout = 'blank';
        echo json_encode(compact('data', $data));
    }
    
    //Deletes a like for an activity
    function unlikeActivity(){
        if(isset($_REQUEST['user_id'])) $user_id = $_REQUEST['user_id'];
        if(isset($_REQUEST['activity_id'])) $activity_id = $_REQUEST['activity_id'];
        
        $this->loadModel('ActivityLike');
        
        if($this->ActivityLike->userLikesActivity($user_id, $activity_id)){
            $rows = $this->ActivityLike->deleteLike($user_id, $activity_id);
            
            if($rows > 0){
                $response = REQUEST_OK;
            } else {
                $response = REQUEST_FAILED;
            }
        } else {
            $response = REQUEST_INVALID;
        }
        
        $data['response'] = $response;
        
        $this->layout = 'blank';
        echo json_encode(compact('data', $data));
    }
    
    //Sets a like for a place
    function likePlace(){
        if(isset($_REQUEST['user_id'])) $user_id = $_REQUEST['user_id'];
        if(isset($_REQUEST['place_id'])) $place_id = $_REQUEST['place_id'];
        
        $this->loadModel('PlaceLike');
        $obj = array();
        $obj['PlaceLike']['user_id'] = $user_id;
        $obj['PlaceLike']['place_id'] = $place_id;
        
        if(!$this->PlaceLike->userLikesPlace($user_id, $place_id)){
            if($this->PlaceLike->save($obj)){
                $response = REQUEST_OK;
            } else {
                $response = REQUEST_FAILED;
            }
        } else {
            $response = REQUEST_INVALID;
        }
        
        $data['response'] = $response;
        
        $this->layout = 'blank';
        echo json_encode(compact('data', $data));
    }
    
    //Deletes a like for a place
    function unlikePlace(){
        if(isset($_REQUEST['user_id'])) $user_id = $_REQUEST['user_id'];
        if(isset($_REQUEST['place_id'])) $place_id = $_REQUEST['place_id'];
        
        $this->loadModel('PlaceLike');
        
        if($this->PlaceLike->userLikesPlace($user_id, $place_id)){
            $rows = $this->PlaceLike->deleteLike($user_id, $place_id);
            
            if($rows > 0){
                $response = REQUEST_OK;
            } else {
                $response = REQUEST_FAILED;
            }
        } else {
            $response = REQUEST_INVALID;
        }
        
        $data['response'] = $response;
        
        $this->layout = 'blank';
        echo json_encode(compact('data', $data));
    }
    
    //Sets a like for a dog
    function likeDog(){
        if(isset($_REQUEST['user_id'])) $user_id = $_REQUEST['user_id'];
        if(isset($_REQUEST['dog_id'])) $dog_id = $_REQUEST['dog_id'];
        
        $this->loadModel('DogLike');
        $obj = array();
        $obj['DogLike']['user_id'] = $user_id;
        $obj['DogLike']['dog_id'] = $dog_id;
        
        if(!$this->DogLike->userLikesDog($user_id, $dog_id)){
            if($this->DogLike->save($obj)){
                $response = REQUEST_OK;
            } else {
                $response = REQUEST_FAILED;
            }
        } else {
            $response = REQUEST_INVALID;
        }
        
        $data['response'] = $response;
        
        $this->layout = 'blank';
        echo json_encode(compact('data', $data));
    }
    
    //Deletes a like for a dog
    function unlikeDog(){
        if(isset($_REQUEST['user_id'])) $user_id = $_REQUEST['user_id'];
        if(isset($_REQUEST['dog_id'])) $dog_id = $_REQUEST['dog_id'];
        
        $this->loadModel('DogLike');
        
        if($this->DogLike->userLikesDog($user_id, $dog_id)){
            $rows = $this->DogLike->deleteLike($user_id, $dog_id);
            
            if($rows > 0){
                $response = REQUEST_OK;
            } else {
                $response = REQUEST_FAILED;
            }
        } else {
            $response = REQUEST_INVALID;
        }
        
        $data['response'] = $response;
        
        $this->layout = 'blank';
        echo json_encode(compact('data', $data));
    }
}

?>
