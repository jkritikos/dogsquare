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
        
        $json = '{"a":1,"b":2,"c":3,"d":4,"e":5}';
        $obj = json_decode($json,true);
        
        $json2 = "[{\"lat\":37.333892822265625,\"lon\":-122.07197570800781,\"log_time\":1379496134374},{\"lat\":37.33382797241211,\"lon\":-122.07235717773438,\"log_time\":1379496135371},{\"lat\":37.33375930786133,\"lon\":-122.0727310180664,\"log_time\":1379496136369},{\"lat\":37.33369827270508,\"lon\":-122.0730972290039,\"log_time\":1379496137356},{\"lat\":37.33363723754883,\"lon\":-122.0734634399414,\"log_time\":1379496138355}]";
        $obj2 = json_decode($json2,true);
        
        echo "<li> json: is array returns ". is_Array($obj);
        echo "<li> json2: is array returns ". is_Array($obj2);
        
        foreach($obj2 as $key => $val){

            echo "<li> lat " .$obj2[$key]['lat'];
        }
        
        //var_dump(json_decode($json));
        
        phpinfo();
        
        $data['test'] = 'edd';
        
        $this->layout = 'blank';
        //echo json_encode(compact('data', $data));
    }

    function login(){
        if(isset($_REQUEST['email'])) $email = $_REQUEST['email'];
        if(isset($_REQUEST['password'])) $password = $_REQUEST['password'];
        
        $response = null;
        
        if($email != '' && $password != ''){
            $this->loadModel('User');
            $user_id = $this->User->validateClientCredentials($email, $password);
            
            if($user_id != null){
                
                //Dog breeds
                $this->loadModel('DogBreed');
                $breeds = $this->DogBreed->find('all');
                $data['breeds'] = $breeds;
                
                //Get user
                $this->loadModel('User');
                $user = $this->User->getOtherUserById($user_id, $user_id);
                $data['user'] = $user;
                
                //Get dogs
                $this->loadModel('Dog');
                $dogs = $this->Dog->getUserDogs($user_id);
                $data['dogs'] = $dogs;
                
                //Count unread notifications
                $this->loadModel('UserNotification');
                $count_notifications = $this->UserNotification->countUnreadNotifications($user_id);
                $data['count_notifications'] = $count_notifications;

                //Count followers
                $this->loadModel('UserFollows');
                $count_followers = $this->UserFollows->countFollowers($user_id);
                $data['count_followers'] = $count_followers;
                
                //Mutual followers
                $mutual_followers = $this->UserFollows->getMutualFollowers($user_id);
                $data['mutual_followers'] = $mutual_followers;
                
                //Count inbox
                $this->loadModel('UserInbox');
                $count_inbox = $this->UserInbox->countUnreadMessages($user_id);
                $data['count_inbox'] = $count_inbox;
                
                $response = REQUEST_OK;
            } else {
                $response = REQUEST_FAILED;
            }
            
        } else {
            $response = REQUEST_FAILED;
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
        
        $this->log("API->addDog() called for user $userID with dog name $name breed $breed" , LOG_DEBUG);
        
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
            
            //Check for valid extension
            $dateString = Security::hash(time().rand(1, 10), 'md5');
            $fileExtension = ".jpeg";
            $fileName = $dateString.$fileExtension;
            $uploadfile = UPLOAD_PATH.DOG_PATH."/". "$fileName";
            
            //Thumbnail
            $filenameThumb = "thumb_".$dateString.$fileExtension;
            $uploadfileThumb = UPLOAD_PATH . DOG_PATH . "/". "$filenameThumb";
            
            $this->log("API->addDog() uploadfile is $uploadfile AND thumb is $uploadfileThumb" , LOG_DEBUG);
            
            if(is_uploaded_file($_FILES['photo']['tmp_name']) && move_uploaded_file($_FILES['photo']['tmp_name'], $uploadfile)){
                if(is_uploaded_file($_FILES['thumb']['tmp_name']) && move_uploaded_file($_FILES['thumb']['tmp_name'], $uploadfileThumb)){
                    $this->log("API->addDog() uploading succeeded" , LOG_DEBUG);

                    //Save photo info to the db
                    $this->loadModel('Photo');
                    $obj = array();
                    $obj['Photo']['path'] = $fileName;
                    $obj['Photo']['thumb'] = $filenameThumb;
                    $obj['Photo']['user_id'] = $userID;
                    
                    if($this->Photo->save($obj)){
                        $photoID = $this->Photo->getLastInsertID();
                        
                        $data['thumb'] = $filenameThumb;
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
                    $this->log("API->addDog() thumb uploading failed" , LOG_DEBUG);
                    $response = REQUEST_FAILED;
                    $errorMessage = ERROR_DOG_PHOTO_UPLOAD;
                }
                
            } else {
                $this->log("API->addDog() photo uploading failed" , LOG_DEBUG);
                $response = REQUEST_FAILED;
                $errorMessage = ERROR_DOG_PHOTO_UPLOAD;
            }
        } else {
            $this->log("API->addDog() no photo found" , LOG_DEBUG);
        }
       
        $this->log("API->addDog() returns: response $response error $errorMessage" , LOG_DEBUG);
        
        //Load additional data with this request
        if($response == REQUEST_OK){
        
            //Count unread notifications
            $this->loadModel('UserNotification');
            $count_notifications = $this->UserNotification->countUnreadNotifications($userID);
            $data['count_notifications'] = $count_notifications;

            //Count followers
            $this->loadModel('UserFollows');
            $count_followers = $this->UserFollows->countFollowers($userID);
            $data['count_followers'] = $count_followers;
            
            //Count inbox
            $this->loadModel('UserInbox');
            $count_inbox = $this->UserInbox->countUnreadMessages($user_id);
            $data['count_inbox'] = $count_inbox;
        }
        
        //Feed entry
        if($response == REQUEST_OK){
            $this->loadModel('User');
            $this->loadModel('Feed');
            
            $user = $this->User->findById($userID);
            $user_name = $user['User']['name'];
            
            $feed['Feed']['user_from'] = $userID;
            $feed['Feed']['user_from_name'] = $user_name;
            $feed['Feed']['target_dog_id'] = $dogID;
            $feed['Feed']['target_dog_name'] = $name;
            $feed['Feed']['type_id'] = FEED_NEW_DOG;
            
            $feedOK = $this->Feed->save($feed);
            
            if(!$feedOK){
                $this->log("API->addDog() error creating feed", LOG_DEBUG);
                $response = ERROR_FEED_CREATION;
            } else {
                $this->log("API->addDog() saved feed ", LOG_DEBUG);
            }
        }
        
        $data['response'] = $response;
        $data['dog_id'] = $dogID;
        $data['error'] = $errorMessage;
        
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
        
        //Load additional data with this request
        if($response == REQUEST_OK){
        
            //Count unread notifications
            $this->loadModel('UserNotification');
            $count_notifications = $this->UserNotification->countUnreadNotifications($userID);
            $data['count_notifications'] = $count_notifications;

            //Count followers
            $this->loadModel('UserFollows');
            $count_followers = $this->UserFollows->countFollowers($userID);
            $data['count_followers'] = $count_followers;
            
            //Count inbox
            $this->loadModel('UserInbox');
            $count_inbox = $this->UserInbox->countUnreadMessages($user_id);
            $data['count_inbox'] = $count_inbox;
        }
        
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
                $uploadfile = UPLOAD_PATH . USER_PATH . "/". "$fileName";

                //Thumbnail
                $filenameThumb = "thumb_".$dateString.$fileExtension;
                $uploadfileThumb = UPLOAD_PATH . USER_PATH . "/". "$filenameThumb";
                
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
                            
                            $data['thumb'] = $filenameThumb;
                            $data['photo'] = $fileName;
                            
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
    
    //add a comment for a given place
    function addPlaceComment(){
        if(isset($_REQUEST['user_id'])) $userId = $_REQUEST['user_id'];
        if(isset($_REQUEST['comment'])) $comment = $_REQUEST['comment'];
        if(isset($_REQUEST['place_id'])) $placeId = $_REQUEST['place_id'];
        
        $this->log("API->addPlaceComment() called for palce: $placeId from user: $userId", LOG_DEBUG);
        
        $commentId = null;
        $response = null;
        $errorMessage = null;
        
        //Save place comment object
        $this->loadModel('PlaceComment');
        $com = array();
        $com['PlaceComment']['user_id'] = $userId;
        $com['PlaceComment']['comment'] = $comment;
        $com['PlaceComment']['place_id'] = $placeId;
        $this->log("API->addPlaceComment() called ", LOG_DEBUG);
        if($this->PlaceComment->save($com)){
            
            $response = REQUEST_OK;
            $commentId = $this->PlaceComment->getLastInsertID();
        } else {
            $response = REQUEST_FAILED;
            $errorMessage = ERROR_COMMENT_CREATION;
        }
        
        $this->log("API->addPlaceComment() returns: response $response error $errorMessage" , LOG_DEBUG);
        
        //Load additional data with this request
        if($response == REQUEST_OK){
        
            //Count unread notifications
            $this->loadModel('UserNotification');
            $count_notifications = $this->UserNotification->countUnreadNotifications($userId);
            $data['count_notifications'] = $count_notifications;

            //Count followers
            $this->loadModel('UserFollows');
            $count_followers = $this->UserFollows->countFollowers($userId);
            $data['count_followers'] = $count_followers;
            
            //Count inbox
            $this->loadModel('UserInbox');
            $count_inbox = $this->UserInbox->countUnreadMessages($user_id);
            $data['count_inbox'] = $count_inbox;
        }
        
        $data['response'] = $response;
        $data['comment_id'] = $commentId;
        $data['error'] = $errorMessage;
        
        $this->layout = 'blank';
        echo json_encode(compact('data', $data));
    }
    
    //add a comment for a given activity
    function addActivityComment(){
        if(isset($_REQUEST['user_id'])) $userId = $_REQUEST['user_id'];
        if(isset($_REQUEST['comment'])) $comment = $_REQUEST['comment'];
        if(isset($_REQUEST['activity_id'])) $activityId = $_REQUEST['activity_id'];
        
        $this->log("API->addActivityComment() called for palce: $activityId from user: $userId", LOG_DEBUG);
        
        $commentId = null;
        $response = null;
        $errorMessage = null;
        
        //Obtain activity info
        $this->loadModel('Activity');
        $activity_obj = $this->Activity->findById($activity_id);
        
        //Proceed if this is activity exists
        if($activity_obj != null){
            
            //Save activity comment object
            $this->loadModel('ActivityComment');
            $com = array();
            $com['ActivityComment']['user_id'] = $userId;
            $com['ActivityComment']['comment'] = $comment;
            $com['ActivityComment']['activity_id'] = $activityId;
            $this->log("API->addActivityComment() called ", LOG_DEBUG);
            if($this->ActivityComment->save($com)){
                $this->loadModel('UserNotification');

                $not['UserNotification']['user_from'] = $userId;
                $not['UserNotification']['activity_id'] = $activityId;
                $not['UserNotification']['type_id'] = NOTIFICATION_COMMENT_ACTIVITY;

                if($this->UserNotification->save($not)){
                    $response = REQUEST_OK;
                    $commentId = $this->ActivityComment->getLastInsertID();
                }else{
                    $response = REQUEST_FAILED;
                }
            } else {
                $response = REQUEST_FAILED;
                $errorMessage = ERROR_COMMENT_CREATION;
            }

            $this->log("API->addActivityComment() returns: response $response error $errorMessage" , LOG_DEBUG);

            //Load additional data with this request
            if($response == REQUEST_OK){

                //Count unread notifications
                $count_notifications = $this->UserNotification->countUnreadNotifications($userId);
                $data['count_notifications'] = $count_notifications;

                //Count followers
                $this->loadModel('UserFollows');
                $count_followers = $this->UserFollows->countFollowers($userId);
                $data['count_followers'] = $count_followers;
                
                //Count inbox
                $this->loadModel('UserInbox');
                $count_inbox = $this->UserInbox->countUnreadMessages($user_id);
                $data['count_inbox'] = $count_inbox;
            }
        } else {
            $response = REQUEST_INVALID;
        }
        
        $data['response'] = $response;
        $data['comment_id'] = $commentId;
        $data['error'] = $errorMessage;
        
        $this->layout = 'blank';
        echo json_encode(compact('data', $data));
    }
    
    //Checks if the specified list of emails maps to dogsquare users
    function areUsers(){
        if(isset($_REQUEST['user_id'])) $user_id = $_REQUEST['user_id'];
        if(isset($_REQUEST['list'])) $list = $_REQUEST['list'];
        
        $list = json_decode(urldecode($list));
        $listToString = '';
        
        for($i=0;$i<sizeof($list);$i++){
            
            if($list[$i] != ''){
                if(sizeof($list) - 1 == $i){
                    $listToString.= "'" . $list[$i] . "'";
                }else{
                    $listToString.= "'" . $list[$i] . "',";
                }
            }
        }
        
        $this->log("API->areUsers() called to convert list to : $listToString", LOG_DEBUG);
        $this->loadModel('User');
        $userData = $this->User->areUsers($listToString, $user_id);
        
        //Count unread notifications
        $this->loadModel('UserNotification');
        $count_notifications = $this->UserNotification->countUnreadNotifications($user_id);
        $data['count_notifications'] = $count_notifications;

        //Count followers
        $this->loadModel('UserFollows');
        $count_followers = $this->UserFollows->countFollowers($user_id);
        $data['count_followers'] = $count_followers;
        
        //Count inbox
        $this->loadModel('UserInbox');
        $count_inbox = $this->UserInbox->countUnreadMessages($user_id);
        $data['count_inbox'] = $count_inbox;
        
        $data['response'] = REQUEST_OK;
        $data['results'] = $userData;
        
        $this->layout = 'blank';
        echo json_encode(compact('data', $data));
    }
    
    function searchUser(){
        if(isset($_REQUEST['user_id'])) $user_id = $_REQUEST['user_id'];
        if(isset($_REQUEST['name'])) $name = $_REQUEST['name'];
        
        
        $this->loadModel('User');
        $userData = $this->User->search($name, null, null, $user_id);
        
        //Count unread notifications
        $this->loadModel('UserNotification');
        $count_notifications = $this->UserNotification->countUnreadNotifications($user_id);
        $data['count_notifications'] = $count_notifications;

        //Count followers
        $this->loadModel('UserFollows');
        $count_followers = $this->UserFollows->countFollowers($user_id);
        $data['count_followers'] = $count_followers;
        
        //Count inbox
        $this->loadModel('UserInbox');
        $count_inbox = $this->UserInbox->countUnreadMessages($user_id);
        $data['count_inbox'] = $count_inbox;
        
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
                $this->loadModel('UserNotification');
                
                $obj2['UserNotification']['user_from'] = $user_id;
                $obj2['UserNotification']['user_id'] = $follow_user;
                $obj2['UserNotification']['type_id'] = NOTIFICATION_NEW_FOLLOWER;
                
                if($this->UserNotification->save($obj2)){
                    $response = REQUEST_OK;
                }else{
                    $response = REQUEST_FAILED;
                }
            } else {
                $response = REQUEST_FAILED;
            }
        } else {
            $response = ERROR_USER_ALREADY_FOLLOWING;
        }
        
        //Load additional data with this request
        if($response == REQUEST_OK){
        
            //Count unread notifications
            $count_notifications = $this->UserNotification->countUnreadNotifications($user_id);
            $data['count_notifications'] = $count_notifications;

            //Count followers
            $count_followers = $this->UserFollows->countFollowers($user_id);
            $data['count_followers'] = $count_followers;
            
            //Count inbox
            $this->loadModel('UserInbox');
            $count_inbox = $this->UserInbox->countUnreadMessages($user_id);
            $data['count_inbox'] = $count_inbox;
        }
        
        //Feed entry
        if($response == REQUEST_OK){
            $this->loadModel('User');
            $this->loadModel('Feed');
            
            $user = $this->User->findById($user_id);
            $user_name = $user['User']['name'];
            
            $userTarget = $this->User->findById($follow_user);
            $target_user_id = $userTarget['User']['id'];
            $target_user_name = $userTarget['User']['name'];
            
            $feed['Feed']['user_from'] = $user_id;
            $feed['Feed']['user_from_name'] = $user_name;
            $feed['Feed']['target_user_id'] = $target_user_id;
            $feed['Feed']['target_user_name'] = $target_user_name;
            $feed['Feed']['type_id'] = FEED_FRIEND_NEW_FOLLOWER;
            
            $feedOK = $this->Feed->save($feed);
            
            if(!$feedOK){
                $this->log("API->followUser() error creating feed", LOG_DEBUG);
                $response = ERROR_FEED_CREATION;
            } else {
                $this->log("API->followUser() saved feed ", LOG_DEBUG);
            }
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
        
        //Load additional data with this request
        if($response == REQUEST_OK){
        
            //Count unread notifications
            $this->loadModel('UserNotification');
            $count_notifications = $this->UserNotification->countUnreadNotifications($user_id);
            $data['count_notifications'] = $count_notifications;

            //Count followers
            $count_followers = $this->UserFollows->countFollowers($user_id);
            $data['count_followers'] = $count_followers;
            
            //Count inbox
            $this->loadModel('UserInbox');
            $count_inbox = $this->UserInbox->countUnreadMessages($user_id);
            $data['count_inbox'] = $count_inbox;
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
        
        //Count unread notifications
        $this->loadModel('UserNotification');
        $count_notifications = $this->UserNotification->countUnreadNotifications($user_id);
        $data['count_notifications'] = $count_notifications;

        //Count followers
        $count_followers = $this->UserFollows->countFollowers($user_id);
        $data['count_followers'] = $count_followers;
        
        //Count inbox
        $this->loadModel('UserInbox');
        $count_inbox = $this->UserInbox->countUnreadMessages($user_id);
        $data['count_inbox'] = $count_inbox;
        
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
        
        //Count unread notifications
        $this->loadModel('UserNotification');
        $count_notifications = $this->UserNotification->countUnreadNotifications($user_id);
        $data['count_notifications'] = $count_notifications;

        //Count followers
        $count_followers = $this->UserFollows->countFollowers($user_id);
        $data['count_followers'] = $count_followers;
        
        //Count inbox
        $this->loadModel('UserInbox');
        $count_inbox = $this->UserInbox->countUnreadMessages($user_id);
        $data['count_inbox'] = $count_inbox;
        
        $data['response'] = REQUEST_OK;
        $data['users'] = $users;
        
        $this->layout = 'blank';
        echo json_encode(compact('data', $data));
    }
    
    //Returns the users that are mutually followed for user $user_id
    function getMutualFollowers(){
        if(isset($_REQUEST['user_id'])) $user_id = $_REQUEST['user_id'];
        if(isset($_REQUEST['target_id'])) $target_id = $_REQUEST['target_id'];
        
        $this->loadModel('UserFollows');
        $mutual_followers = $this->UserFollows->getMutualFollowers($target_id);
        $data['mutual_followers'] = $mutual_followers;
        $data['response'] = REQUEST_OK;
        
        $this->layout = 'blank';
        echo json_encode(compact('data', $data));
    }
    
    //Returns the unread notifications of the specified user
    function getNotifications(){
        if(isset($_REQUEST['user_id'])) $user_id = $_REQUEST['user_id'];
        $this->log("API->getNotifications() called for user $user_id" , LOG_DEBUG);
        
        $this->loadModel('UserNotification');
        $notifications = $this->UserNotification->getUnreadNotifications($user_id);
        
        //Count unread notifications
        $count_notifications = $this->UserNotification->countUnreadNotifications($user_id);
        $data['count_notifications'] = $count_notifications;

        //Count followers
        $this->loadModel('UserFollows');
        $count_followers = $this->UserFollows->countFollowers($user_id);
        $data['count_followers'] = $count_followers;
        
        //Count inbox
        $this->loadModel('UserInbox');
        $count_inbox = $this->UserInbox->countUnreadMessages($user_id);
        $data['count_inbox'] = $count_inbox;
        
        $data['response'] = REQUEST_OK;
        $data['notifications'] = $notifications;
        
        $this->layout = 'blank';
        echo json_encode(compact('data', $data));
    }
    
    //Returns all the activity related data for the specified activity id
    function getActivity(){
        if(isset($_REQUEST['user_id'])) $user_id = $_REQUEST['user_id'];
        if(isset($_REQUEST['activity_id'])) $activity_id = $_REQUEST['activity_id'];
        
        $this->log("API->getActivity() called for user $user_id and activity $activity_id" , LOG_DEBUG);
        
        $this->loadModel('Activity');
        $activity = $this->Activity->getActivityById($user_id, $activity_id);
        $dogs = $this->Activity->getActivityDogs($activity_id);
        $coordinates = $this->Activity->getActivityCoordinates($activity_id);
        $comments = $this->Activity->getActivityComments($activity_id);
        
        //Count unread notifications
        $this->loadModel('UserNotification');
        $count_notifications = $this->UserNotification->countUnreadNotifications($user_id);
        $data['count_notifications'] = $count_notifications;

        //Count followers
        $this->loadModel('UserFollows');
        $count_followers = $this->UserFollows->countFollowers($user_id);
        $data['count_followers'] = $count_followers;
        
        //Count inbox
        $this->loadModel('UserInbox');
        $count_inbox = $this->UserInbox->countUnreadMessages($user_id);
        $data['count_inbox'] = $count_inbox;
        
        $data['response'] = REQUEST_OK;
        $data['activity'] = $activity;
        $data['dogs'] = $dogs;
        $data['coordinates'] = $coordinates;
        $data['comments'] = $comments;
        
        $this->layout = 'blank';
        echo json_encode(compact('data', $data));
    }
    
    //Saves an activity (properties, dogs, coordinates)
    function saveActivity(){
        if(isset($_REQUEST['user_id'])) $user_id = $_REQUEST['user_id'];
        if(isset($_REQUEST['coordinates'])) $coordinates = $_REQUEST['coordinates'];
        if(isset($_REQUEST['dogs'])) $dogs = $_REQUEST['dogs'];
        if(isset($_REQUEST['start_date'])) $start_date = $_REQUEST['start_date'];
        if(isset($_REQUEST['start_time'])) $start_time = $_REQUEST['start_time'];
        if(isset($_REQUEST['end_time'])) $end_time = $_REQUEST['end_time'];
        if(isset($_REQUEST['duration'])) $duration = $_REQUEST['duration'];
        if(isset($_REQUEST['temperature'])) $temperature = $_REQUEST['temperature'];
        if(isset($_REQUEST['pace'])) $pace = $_REQUEST['pace'];
        if(isset($_REQUEST['distance'])) $distance = $_REQUEST['distance'];
        
        $this->log("API->saveActivity() called for user $user_id with coordinates $coordinates" , LOG_DEBUG);
        
        $coordinates = json_decode($coordinates, true);
        $dogs = json_decode($dogs, true);
        
        $response = REQUEST_OK;
        
        //activity
        $this->loadModel('Activity');
        $obj['Activity']['user_id'] = $user_id;
        $obj['Activity']['start_date'] = $start_date;
        $obj['Activity']['start_time'] = $start_time;
        $obj['Activity']['end_time'] = $end_time;
        $obj['Activity']['type_id'] = ACTIVITY_WALK;
        $obj['Activity']['temperature'] = $temperature;
        $obj['Activity']['pace'] = $pace;
        $obj['Activity']['distance'] = $distance;
        
        if($this->Activity->save($obj)){
            
            //coordinates
            $activity_id = $this->Activity->getLastInsertID();
            
            $this->loadModel('ActivityCoordinate');
            if(is_array($coordinates)){
                foreach($coordinates as $key => $val){

                    $this->log("API->saveActivity() looping coordinate ".$coordinates[$key]['lat'], LOG_DEBUG);
                    
                    $this->ActivityCoordinate->create();
                    $obj2['ActivityCoordinate']['activity_id'] = $activity_id;
                    $obj2['ActivityCoordinate']['lat'] = $coordinates[$key]['lat'];
                    $obj2['ActivityCoordinate']['lon'] = $coordinates[$key]['lon'];
                    $obj2['ActivityCoordinate']['logtime'] = $coordinates[$key]['log_time'];

                    if($this->ActivityCoordinate->save($obj2)){
                        //carry on
                        //$this->log("API->saveActivity() saved coordinate ", LOG_DEBUG);
                    } else {
                        $this->log("API->saveActivity() error saving coordinate ", LOG_DEBUG);
                        $response = ERROR_ACTIVITY_COORDINATE_CREATION;
                    }
                }
            }
            
            //dogs
            if($response == REQUEST_OK){
                $this->loadModel('ActivityDog');
                
                if(is_array($dogs)){
                    foreach($dogs as $key => $val){
                        $this->ActivityDog->create();
                        $obj3['ActivityDog']['activity_id'] = $activity_id;
                        $obj3['ActivityDog']['dog_id'] = $dogs[$key]['dog_id'];

                        if($this->ActivityDog->save($obj3)){
                            //carry on
                            $this->log("API->saveActivity() saved dog ", LOG_DEBUG);
                        } else {
                            $this->log("API->saveActivity() error saving dog info ", LOG_DEBUG);
                            $response = ERROR_ACTIVITY_DOG_CREATION;
                        }
                    }
                }
            }
        } else {
            $response = ERROR_ACTIVITY_CREATION;
        }
        
        //Feed entry
        if($response == REQUEST_OK){
            $this->loadModel('User');
            $this->loadModel('Feed');
            
            $user = $this->User->findById($user_id);
            $user_name = $user['User']['name'];
            $dog_names = "";

            $activityDogs = $this->ActivityDog->findAllByActivityId($activity_id);
            foreach($activityDogs as $key => $val){
                $this->log("API->saveActivity() feed: found dog ".$activityDogs[$key]['Dog']['name'], LOG_DEBUG);
                $dog_names .= $activityDogs[$key]['Dog']['name'] . ", ";
            }

            //remove trailing comma
            $dog_names = substr($dog_names, 0, strlen($dog_names)-2);
            
            $feed['Feed']['user_from'] = $user_id;
            $feed['Feed']['user_from_name'] = $user_name;
            $feed['Feed']['activity_id'] = $activity_id;
            $feed['Feed']['target_dog_name'] = $dog_names;
            $feed['Feed']['type_id'] = FEED_NEW_WALK;
            
            $feedOK = $this->Feed->save($feed);
            
            if(!$feedOK){
                $this->log("API->saveActivity() error creating feed", LOG_DEBUG);
                $response = ERROR_FEED_CREATION;
            } else {
                $this->log("API->saveActivity() saved feed ", LOG_DEBUG);
            }
        }
        
        $this->log("API->saveActivity() returns activity id $activity_id ", LOG_DEBUG);
        
        $data['activity_id'] = $activity_id;
        $data['response'] = $response;  
        $this->layout = 'blank';
        echo json_encode(compact('data', $data));
    }
    
    function getUser(){
        
    }
    
    //Returns the newsfeed for the specified user
    function getFeed(){
        if(isset($_REQUEST['user_id'])) $user_id = $_REQUEST['user_id'];
        
        $this->loadModel('Feed');
        $feed = $this->Feed->getFeed($user_id);
        $response = REQUEST_OK;
        
        $data['response'] = $response;  
        $data['feed'] = $feed;
        
        //Count unread notifications
        $this->loadModel('UserNotification');
        $count_notifications = $this->UserNotification->countUnreadNotifications($user_id);
        $data['count_notifications'] = $count_notifications;

        //Count followers
        $this->loadModel('UserFollows');
        $count_followers = $this->UserFollows->countFollowers($user_id);
        $data['count_followers'] = $count_followers;
        
        //Count inbox
        $this->loadModel('UserInbox');
        $count_inbox = $this->UserInbox->countUnreadMessages($user_id);
        $data['count_inbox'] = $count_inbox;
        
        $this->layout = 'blank';
        echo json_encode(compact('data', $data));
    }
    
    function getDog(){
        if(isset($_REQUEST['dog_id'])) $dog_id = $_REQUEST['dog_id'];
        if(isset($_REQUEST['user_id'])) $user_id = $_REQUEST['user_id'];
        
        $this->loadModel('Dog');
        $dog = $this->Dog->getDogById($dog_id);
        
        //Count unread notifications
        $this->loadModel('UserNotification');
        $count_notifications = $this->UserNotification->countUnreadNotifications($user_id);
        $data['count_notifications'] = $count_notifications;

        //Count followers
        $this->loadModel('UserFollows');
        $count_followers = $this->UserFollows->countFollowers($user_id);
        $data['count_followers'] = $count_followers;
        
        //Count inbox
        $this->loadModel('UserInbox');
        $count_inbox = $this->UserInbox->countUnreadMessages($user_id);
        $data['count_inbox'] = $count_inbox;
        
        $data['response'] = REQUEST_OK;
        $data['dog'] = $dog;
        
        $this->layout = 'blank';
        echo json_encode(compact('data', $data));
    }
    
    function getPlace(){
        if(isset($_REQUEST['place_id'])) $place_id = $_REQUEST['place_id'];
        if(isset($_REQUEST['user_id'])) $user_id = $_REQUEST['user_id'];
        
        $this->loadModel('Place');
        $place = $this->Place->getPlaceById($place_id);
        
        //Count unread notifications
        $this->loadModel('UserNotification');
        $count_notifications = $this->UserNotification->countUnreadNotifications($user_id);
        $data['count_notifications'] = $count_notifications;

        //Count followers
        $this->loadModel('UserFollows');
        $count_followers = $this->UserFollows->countFollowers($user_id);
        $data['count_followers'] = $count_followers;
        
        //Count inbox
        $this->loadModel('UserInbox');
        $count_inbox = $this->UserInbox->countUnreadMessages($user_id);
        $data['count_inbox'] = $count_inbox;
        
        $data['response'] = REQUEST_OK;
        $data['place'] = $place;
        
        $this->layout = 'blank';
        echo json_encode(compact('data', $data));
    }
    
    function getOtherUser(){
        if(isset($_REQUEST['user_id'])) $user_id = $_REQUEST['user_id'];
        if(isset($_REQUEST['target_id'])) $target_id = $_REQUEST['target_id'];
        
        $this->loadModel('User');
        $otherUser = $this->User->getOtherUserById($user_id, $target_id);
        
        $this->loadModel('Dog');
        $dogs = $this->Dog->getUserDogs($target_id);
        
        //Count unread notifications
        $this->loadModel('UserNotification');
        $count_notifications = $this->UserNotification->countUnreadNotifications($user_id);
        $data['count_notifications'] = $count_notifications;

        //Count followers
        $this->loadModel('UserFollows');
        $count_followers = $this->UserFollows->countFollowers($user_id);
        $data['count_followers'] = $count_followers;
        
        //Count inbox
        $this->loadModel('UserInbox');
        $count_inbox = $this->UserInbox->countUnreadMessages($user_id);
        $data['count_inbox'] = $count_inbox;
        
        $data['response'] = REQUEST_OK;
        $data['user'] = $otherUser;
        $data['dogs'] = $dogs;
        
        $this->layout = 'blank';
        echo json_encode(compact('data', $data));
    }
    
    //Sends a message from $user_id to $target_id
    function sendMessage(){
        if(isset($_REQUEST['user_id'])) $user_id = $_REQUEST['user_id'];
        if(isset($_REQUEST['target_id'])) $target_id = $_REQUEST['target_id'];
        if(isset($_REQUEST['message'])) $message = $_REQUEST['message'];
        
        $this->loadModel('UserInbox');
        $obj = array();
        $obj['UserInbox']['user_from'] = $user_id;
        $obj['UserInbox']['user_to'] = $target_id;
        $obj['UserInbox']['message'] = $message;
        
        if($this->UserInbox->save($obj)){
            $response = REQUEST_OK;
        } else {
            $response = REQUEST_FAILED;
        }
        
        //Load additional data with this request
        if($response == REQUEST_OK){
        
            //Count unread notifications
            $this->loadModel('UserNotification');
            $count_notifications = $this->UserNotification->countUnreadNotifications($user_id);
            $data['count_notifications'] = $count_notifications;

            //Count followers
            $this->loadModel('UserFollows');
            $count_followers = $this->UserFollows->countFollowers($user_id);
            $data['count_followers'] = $count_followers;
            
            //Count inbox
            $this->loadModel('UserInbox');
            $count_inbox = $this->UserInbox->countUnreadMessages($user_id);
            $data['count_inbox'] = $count_inbox;
        }
        
        $data['response'] = $response;
        
        $this->layout = 'blank';
        echo json_encode(compact('data', $data));
    }
    
    //Gets all unread messages
    function getMessages(){
        if(isset($_REQUEST['user_id'])) $user_id = $_REQUEST['user_id'];
        
        $this->loadModel('UserInbox');
    }
    
    //Searches for users and places
    function search(){
        if(isset($_REQUEST['user_id'])) $user_id = $_REQUEST['user_id'];
        if(isset($_REQUEST['name'])) $name = $_REQUEST['name'];
        
        $this->loadModel('User');
        $userData = $this->User->search($name, null, null, $user_id);
        
        $this->loadModel('Place');
        $placeData = $this->Place->search($name, null, null);
        
        //Count unread notifications
        $this->loadModel('UserNotification');
        $count_notifications = $this->UserNotification->countUnreadNotifications($user_id);
        $data['count_notifications'] = $count_notifications;

        //Count followers
        $this->loadModel('UserFollows');
        $count_followers = $this->UserFollows->countFollowers($user_id);
        $data['count_followers'] = $count_followers;
        
        //Count inbox
        $this->loadModel('UserInbox');
        $count_inbox = $this->UserInbox->countUnreadMessages($user_id);
        $data['count_inbox'] = $count_inbox;
        
        $data['response'] = REQUEST_OK;
        $data['users'] = $userData;
        $data['places'] = $placeData;
        $this->layout = 'blank';
        echo json_encode(compact('data', $data));
    }
    
    //Searches for nearby places
    function getPlaces(){
        if(isset($_REQUEST['user_id'])) $user_id = $_REQUEST['user_id'];
        if(isset($_REQUEST['lat'])) $lat = $_REQUEST['lat'];
        if(isset($_REQUEST['lon'])) $lon = $_REQUEST['lon'];
        
        $category_id = null;
        if(isset($_REQUEST['category_id'])){
            $category_id = $_REQUEST['category_id'];
        }
        
        $this->loadModel('Place');
        $places = $this->Place->getPlacesNearby($lat, $lon, $category_id);
        $data['places'] = $places;
        $response = is_array($places) ? REQUEST_OK : REQUEST_FAILED;
        
        //Load additional data with this request
        if($response == REQUEST_OK){
        
            //Count unread notifications
            $this->loadModel('UserNotification');
            $count_notifications = $this->UserNotification->countUnreadNotifications($user_id);
            $data['count_notifications'] = $count_notifications;

            //Count followers
            $this->loadModel('UserFollows');
            $count_followers = $this->UserFollows->countFollowers($user_id);
            $data['count_followers'] = $count_followers;
            
            //Count inbox
            $this->loadModel('UserInbox');
            $count_inbox = $this->UserInbox->countUnreadMessages($user_id);
            $data['count_inbox'] = $count_inbox;
        }
        
        $data['response'] = $response;
        
        $this->layout = 'blank';
        echo json_encode(compact('data', $data));
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
        
        //Obtain activity info
        $this->loadModel('Activity');
        $activity_obj = $this->Activity->findById($activity_id);
        
        //Only proceed if this activity exists
        if($activity_obj != null){
            
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

            //Create user notification
            $this->loadModel('UserNotification');
            $obj2['UserNotification']['user_from'] = $user_id;
            $obj2['UserNotification']['user_id'] = $activity_obj['Activity']['user_id'];
            $obj2['UserNotification']['activity_id'] = $activity_id;
            $obj2['UserNotification']['type_id'] = NOTIFICATION_LIKE_ACTIVITY;

            if($this->UserNotification->save($obj2)){
                $response = REQUEST_OK;
            } else{
                $response = REQUEST_FAILED;
            }

            //Load additional data with this request
            if($response == REQUEST_OK){

                //Count unread notifications
                $this->loadModel('UserNotification');
                $count_notifications = $this->UserNotification->countUnreadNotifications($user_id);
                $data['count_notifications'] = $count_notifications;

                //Count followers
                $this->loadModel('UserFollows');
                $count_followers = $this->UserFollows->countFollowers($user_id);
                $data['count_followers'] = $count_followers;
                
                //Count inbox
                $this->loadModel('UserInbox');
                $count_inbox = $this->UserInbox->countUnreadMessages($user_id);
                $data['count_inbox'] = $count_inbox;
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
        
        //Load additional data with this request
        if($response == REQUEST_OK){
        
            //Count unread notifications
            $this->loadModel('UserNotification');
            $count_notifications = $this->UserNotification->countUnreadNotifications($user_id);
            $data['count_notifications'] = $count_notifications;

            //Count followers
            $this->loadModel('UserFollows');
            $count_followers = $this->UserFollows->countFollowers($user_id);
            $data['count_followers'] = $count_followers;
            
            //Count inbox
            $this->loadModel('UserInbox');
            $count_inbox = $this->UserInbox->countUnreadMessages($user_id);
            $data['count_inbox'] = $count_inbox;
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
        
        //Load additional data with this request
        if($response == REQUEST_OK){
        
            //Count unread notifications
            $this->loadModel('UserNotification');
            $count_notifications = $this->UserNotification->countUnreadNotifications($user_id);
            $data['count_notifications'] = $count_notifications;

            //Count followers
            $this->loadModel('UserFollows');
            $count_followers = $this->UserFollows->countFollowers($user_id);
            $data['count_followers'] = $count_followers;
            
            //Count inbox
            $this->loadModel('UserInbox');
            $count_inbox = $this->UserInbox->countUnreadMessages($user_id);
            $data['count_inbox'] = $count_inbox;
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
        
        //Load additional data with this request
        if($response == REQUEST_OK){
        
            //Count unread notifications
            $this->loadModel('UserNotification');
            $count_notifications = $this->UserNotification->countUnreadNotifications($user_id);
            $data['count_notifications'] = $count_notifications;

            //Count followers
            $this->loadModel('UserFollows');
            $count_followers = $this->UserFollows->countFollowers($user_id);
            $data['count_followers'] = $count_followers;
            
            //Count inbox
            $this->loadModel('UserInbox');
            $count_inbox = $this->UserInbox->countUnreadMessages($user_id);
            $data['count_inbox'] = $count_inbox;
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
        
        //Load additional data with this request
        if($response == REQUEST_OK){
        
            //Count unread notifications
            $this->loadModel('UserNotification');
            $count_notifications = $this->UserNotification->countUnreadNotifications($user_id);
            $data['count_notifications'] = $count_notifications;

            //Count followers
            $this->loadModel('UserFollows');
            $count_followers = $this->UserFollows->countFollowers($user_id);
            $data['count_followers'] = $count_followers;
            
            //Count inbox
            $this->loadModel('UserInbox');
            $count_inbox = $this->UserInbox->countUnreadMessages($user_id);
            $data['count_inbox'] = $count_inbox;
        }
        
        //Feed entry
        if($response == REQUEST_OK){
            $this->loadModel('User');
            $this->loadModel('Feed');
            $this->loadModel('Dog');
            
            $user = $this->User->findById($user_id);
            $user_name = $user['User']['name'];
            
            $dogObject = $this->Dog->findById($dog_id);
            $dog_name = $dogObject['Dog']['name'];
            
            $feed['Feed']['user_from'] = $user_id;
            $feed['Feed']['user_from_name'] = $user_name;
            $feed['Feed']['target_dog_id'] = $dog_id;
            $feed['Feed']['target_dog_name'] = $dog_name;
            $feed['Feed']['type_id'] = FEED_FRIEND_LIKE_DOG;
            
            $feedOK = $this->Feed->save($feed);
            
            if(!$feedOK){
                $this->log("API->likeDog() error creating feed", LOG_DEBUG);
                $response = ERROR_FEED_CREATION;
            } else {
                $this->log("API->likeDog() saved feed ", LOG_DEBUG);
            }
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
        
        //Load additional data with this request
        if($response == REQUEST_OK){
        
            //Count unread notifications
            $this->loadModel('UserNotification');
            $count_notifications = $this->UserNotification->countUnreadNotifications($user_id);
            $data['count_notifications'] = $count_notifications;

            //Count followers
            $this->loadModel('UserFollows');
            $count_followers = $this->UserFollows->countFollowers($user_id);
            $data['count_followers'] = $count_followers;
            
            //Count inbox
            $this->loadModel('UserInbox');
            $count_inbox = $this->UserInbox->countUnreadMessages($user_id);
            $data['count_inbox'] = $count_inbox;
        }
        
        $data['response'] = $response;
        
        $this->layout = 'blank';
        echo json_encode(compact('data', $data));
    }
}

?>
