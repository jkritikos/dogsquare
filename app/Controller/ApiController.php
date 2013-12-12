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
App::uses('CakeEmail', 'Network/Email');
class ApiController extends AppController{
    
    var $components = array('Cookie', 'RequestHandler');
    var $helpers = array('Js','Time');
    //put your code here
    
    function test(){
        echo $_SERVER['HTTP_HOST'];
        
        $Email = new CakeEmail('smtp');
        
        /*
        $Email->emailFormat('html')
                ->subject('Welcome to Dogsquare!')
            ->template('welcome')
            ->to('jkritikos@gmail.com')
            ->send();
         */
        
        $emailUser = "Jason Kritikos";
        $followerUser = "Mitsos Mwraitis";
        $followers = 2;
        $following = 4;
        $dogs = 5;
        $Email->emailFormat('html')
                ->subject('New follow Dogsquare')
            ->template('follow')
            ->viewVars(array('emailUser' => $emailUser, 'followerUser' => $followerUser, 'followers' => $followers, 'following' => $following, 'dogs' => $dogs))    
            ->to('jkritikos@gmail.com')
            ->send();
    }
    
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
        if(isset($_REQUEST['facebook_id'])) $facebook_id = $_REQUEST['facebook_id'];
        if(isset($_REQUEST['f'])) $fb_dummy_pwd = $_REQUEST['f'];
        if(isset($_REQUEST['timezone'])) $timezone = $_REQUEST['timezone'];
        if(isset($_REQUEST['month'])) $month = $_REQUEST['month'];
        if(isset($_REQUEST['day'])) $day = $_REQUEST['day'];
        if(isset($_REQUEST['year'])) $year = $_REQUEST['year'];
        
        $response = null;
        
        $this->loadModel('User');
        
        //Retrieve user id based on actual dogsquare or dummy FB credentials
        if($email != '' && $password != ''){
            $user_id = $this->User->validateClientCredentials($email, $password);
            //Generate token
            $token = $this->User->generateToken($user_id,$password);
        } else if($facebook_id != '' && $fb_dummy_pwd != ''){
            $user_id = $this->User->validateDummyFacebookCredentials($facebook_id, $fb_dummy_pwd);
            //Generate token
            $token = $this->User->generateToken($user_id,$fb_dummy_pwd);
        } else {
            $user_id = null;
        }
            
        if($user_id != null){
            $data['token'] = $token;

            //Dog breeds 
            //TODO cakephp doesnt seem to properly encode utf8 chars, so we get back NULL
            $this->loadModel('DogBreed');
            //$breeds = $this->DogBreed->find('all');
            $breeds = $this->DogBreed->find('all', array(
                'conditions' => array('DogBreed.active' => '1')
            ));
            $data['breeds'] = $breeds;
            
            //dogfuel rules
            $this->loadModel('DogfuelRule');
            $rules = $this->DogfuelRule->find('all', array(
                'conditions' => array('DogfuelRule.active' => '1')
            ));
            $data['rules'] = $rules;
            
            //passport notes
            $this->loadModel('UserPassport');
            $notes = $this->UserPassport->getNotes($user_id);
            $data['notes'] = $notes;

            //Place Categories
            $this->loadModel('PlaceCategory');
            $categories = $this->PlaceCategory->find('all', array(
                'conditions' => array('PlaceCategory.visible' => '1')
            ));
            $data['categories'] = $categories;
            
            //Get user
            $this->loadModel('User');
            $user = $this->User->getOtherUserById($user_id, $user_id);
            $data['user'] = $user;

            //Get dogs
            $this->loadModel('Dog');
            $fromDate = "$year-$month-$day 00:00:01";
            $toDate = "$year-$month-$day 23:59:59";
            $dogs = $this->Dog->getUserDogs($user_id, $fromDate, $toDate, $timezone);
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
            $response = REQUEST_UNAUTHORISED;
        }
        
        $data['response'] = $response;
        $this->layout = 'blank';
        echo json_encode(compact('data', $data));
    }
    
    //Returns the latest dogfuel value for all dogs belonging to the specified user
    function getDogfuel(){
        if(isset($_REQUEST['user_id'])) $userID = $_REQUEST['user_id'];
        if(isset($_REQUEST['token'])) $token = $_REQUEST['token'];
        if(isset($_REQUEST['timezone'])) $timezone = $_REQUEST['timezone'];
        if(isset($_REQUEST['month'])) $month = $_REQUEST['month'];
        if(isset($_REQUEST['day'])) $day = $_REQUEST['day'];
        if(isset($_REQUEST['year'])) $year = $_REQUEST['year'];
        
        $this->log("API->getDogfuel() called for user $userID with timezone $timezone and date $day/$month/$year" , LOG_DEBUG);
        
        //no security here, just return the data ASAP
        $this->loadModel('Dog');
        //$fromDate = date("Y/n/j H:i:s", mktime(0, 1, 0, $month, $day, $year));
        //$toDate = date("Y/n/j H:i:s", mktime(23, 59, 0, $month, $day, $year));
        $fromDate = "$year-$month-$day 00:00:01";
        $toDate = "$year-$month-$day 23:59:59";
        $values = $this->Dog->getDogfuelValues($userID, $fromDate, $toDate, $timezone);
        $response = REQUEST_OK;
        
        //Dog breeds 
        //TODO cakephp doesnt seem to properly encode utf8 chars, so we get back NULL
        $this->loadModel('DogBreed');
        //$breeds = $this->DogBreed->find('all');
        $breeds = $this->DogBreed->find('all', array(
            'conditions' => array('DogBreed.active' => '1')
        ));
        $data['breeds'] = $breeds;

        //dogfuel rules
        $this->loadModel('DogfuelRule');
        $rules = $this->DogfuelRule->find('all', array(
            'conditions' => array('DogfuelRule.active' => '1')
        ));
        $data['rules'] = $rules;

        //Place Categories
        $this->loadModel('PlaceCategory');
        $categories = $this->PlaceCategory->find('all', array(
            'conditions' => array('PlaceCategory.visible' => '1')
        ));
        $data['categories'] = $categories;
        
        $data['values'] = $values;
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
        if(isset($_REQUEST['size'])) $size = $_REQUEST['size'];
        if(isset($_REQUEST['token'])) $token = $_REQUEST['token'];
        
        $this->log("API->addDog() called for user $userID with dog name $name breed $breed" , LOG_DEBUG);
        
        //Authorise user
        $this->loadModel('User');
        $authorised = $this->User->authorise($userID,$token);
        if($authorised){
        
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
            $dog['Dog']['size'] = $size;
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
                        $obj['Photo']['type_id'] = DOG_PHOTO_TYPE;

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
        
            //Feed entry
            if($response == REQUEST_OK){
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
            
            //Badge handling
            if($response == REQUEST_OK){
                $this->loadModel('UserBadge');
                $this->loadModel('UserNotification');
                
                if($age <= 2 && !$this->UserBadge->userHasBadge($userID, BADGE_PUPPY)){
                    //Award badge and notification
                    if($this->UserBadge->awardBadge($userID, BADGE_PUPPY)){
                        $obj2['UserNotification']['user_from'] = $userID;
                        $obj2['UserNotification']['user_id'] = $userID;
                        $obj2['UserNotification']['badge_id'] = BADGE_PUPPY;
                        $obj2['UserNotification']['type_id'] = NOTIFICATION_AWARD_BADGE;

                        if($this->UserNotification->save($obj2)){
                            $response = REQUEST_OK;
                        }
                    }
                }
                
                $number_of_dogs = $this->Dog->countUserDogs($userID);
                if($number_of_dogs == 3 && !$this->UserBadge->userHasBadge($userID, BADGE_SUPERFAMILY)){
                    //Award badge and notification
                    if($this->UserBadge->awardBadge($userID, BADGE_SUPERFAMILY)){
                        $obj2['UserNotification']['user_from'] = $userID;
                        $obj2['UserNotification']['user_id'] = $userID;
                        $obj2['UserNotification']['badge_id'] = BADGE_SUPERFAMILY;
                        $obj2['UserNotification']['type_id'] = NOTIFICATION_AWARD_BADGE;

                        if($this->UserNotification->save($obj2)){
                            $response = REQUEST_OK;
                        }
                    }
                }
            }
            
            //Load additional data with this request
            if($response == REQUEST_OK){

                //Count unread notifications
                $count_notifications = $this->UserNotification->countUnreadNotifications($userID);
                $data['count_notifications'] = $count_notifications;

                //Count followers
                $this->loadModel('UserFollows');
                $count_followers = $this->UserFollows->countFollowers($userID);
                $data['count_followers'] = $count_followers;

                //Count inbox
                $this->loadModel('UserInbox');
                $count_inbox = $this->UserInbox->countUnreadMessages($userID);
                $data['count_inbox'] = $count_inbox;
            }

            $data['dog_id'] = $dogID;
            $data['error'] = $errorMessage;
        } else {
            $response = REQUEST_UNAUTHORISED;
        }
        
        $data['response'] = $response;
        
        $this->layout = 'blank';
        echo json_encode(compact('data', $data));
    }
    
    //Uploads an image for the specified place
    function addPlacePhoto(){
        if(isset($_REQUEST['user_id'])) $user_id = $_REQUEST['user_id'];
        if(isset($_REQUEST['token'])) $token = $_REQUEST['token'];
        if(isset($_REQUEST['place_id'])) $place_id = $_REQUEST['place_id'];
        
        $this->log("API->addPlacePhoto() called from user $user_id for place $place_id" , LOG_DEBUG);
        $response = REQUEST_FAILED;
        
        //Authorise user
        $this->loadModel('User');
        $authorised = $this->User->authorise($user_id,$token);
        if($authorised){
            
            //handle photo 
            if(isset($_FILES['photo'])){

                //Check for valid extension
                $dateString = Security::hash(time().rand(1, 10), 'md5');
                $fileExtension = ".jpeg";
                $fileName = $dateString.$fileExtension;
                $uploadfile = UPLOAD_PATH.PLACE_PATH."/". "$fileName";

                //Thumbnail
                $filenameThumb = "thumb_".$dateString.$fileExtension;
                $uploadfileThumb = UPLOAD_PATH . PLACE_PATH . "/". "$filenameThumb";

                $this->log("API->addPlacePhoto() uploadfile is $uploadfile AND thumb is $uploadfileThumb" , LOG_DEBUG);

                if(is_uploaded_file($_FILES['photo']['tmp_name']) && move_uploaded_file($_FILES['photo']['tmp_name'], $uploadfile)){
                    if(is_uploaded_file($_FILES['thumb']['tmp_name']) && move_uploaded_file($_FILES['thumb']['tmp_name'], $uploadfileThumb)){
                        $this->log("API->addPlacePhoto() uploading succeeded" , LOG_DEBUG);

                        //Save photo info to the db
                        $this->loadModel('Photo');
                        $obj = array();
                        $obj['Photo']['path'] = $fileName;
                        $obj['Photo']['thumb'] = $filenameThumb;
                        $obj['Photo']['user_id'] = $user_id;
                        $obj['Photo']['place_id'] = $place_id;
                        $obj['Photo']['type_id'] = PLACE_PHOTO_TYPE;

                        if($this->Photo->save($obj)){
                            $photoID = $this->Photo->getLastInsertID();

                            $response = REQUEST_OK;
                            $this->log("API->addPlacePhoto() saved photo $photoID to db" , LOG_DEBUG);
                        } else {
                            $this->log("API->addPlacePhoto() saving photo to db failed" , LOG_DEBUG);
                            $response = REQUEST_FAILED;
                            $errorMessage = ERROR_PHOTO_UPLOAD;
                        }
                    } else {
                        $this->log("API->addPlacePhoto() thumb uploading failed" , LOG_DEBUG);
                        $response = REQUEST_FAILED;
                        $errorMessage = ERROR_PHOTO_UPLOAD;
                    }

                } else {
                    $this->log("API->addPlacePhoto() photo uploading failed" , LOG_DEBUG);
                    $response = REQUEST_FAILED;
                    $errorMessage = ERROR_PHOTO_UPLOAD;
                }
            } else {
                $this->log("API->addPlacePhoto() no photo found" , LOG_DEBUG);
            }
        } else {
            $response = REQUEST_UNAUTHORISED;
        }
        
        $data['response'] = $response;
        
        $this->layout = 'blank';
        echo json_encode(compact('data', $data));
    }
    
    function addPhoto(){
        if(isset($_REQUEST['user_id'])) $userID = $_REQUEST['user_id'];
        if(isset($_REQUEST['token'])) $token = $_REQUEST['token'];
        
        $this->log("API->addPhoto() called for user $userID" , LOG_DEBUG);
        
        //Authorise user
        $this->loadModel('User');
        $authorised = $this->User->authorise($userID,$token);
        if($authorised){
        
            $response = null;
            $errorMessage = null;

            //handle photo 
            if(isset($_FILES['photo'])){

                //Check for valid extension
                $dateString = Security::hash(time().rand(1, 10), 'md5');
                $fileExtension = ".jpeg";
                $fileName = $dateString.$fileExtension;
                $uploadfile = UPLOAD_PATH.USER_PATH."/". "$fileName";

                //Thumbnail
                $filenameThumb = "thumb_".$dateString.$fileExtension;
                $uploadfileThumb = UPLOAD_PATH . USER_PATH . "/". "$filenameThumb";

                $this->log("API->addPhoto() uploadfile is $uploadfile AND thumb is $uploadfileThumb" , LOG_DEBUG);

                if(is_uploaded_file($_FILES['photo']['tmp_name']) && move_uploaded_file($_FILES['photo']['tmp_name'], $uploadfile)){
                    if(is_uploaded_file($_FILES['thumb']['tmp_name']) && move_uploaded_file($_FILES['thumb']['tmp_name'], $uploadfileThumb)){
                        $this->log("API->addPhoto() uploading succeeded" , LOG_DEBUG);

                        //Save photo info to the db
                        $this->loadModel('Photo');
                        $obj = array();
                        $obj['Photo']['path'] = $fileName;
                        $obj['Photo']['thumb'] = $filenameThumb;
                        $obj['Photo']['user_id'] = $userID;
                        $obj['Photo']['type_id'] = GALLERY_PHOTO_TYPE;

                        if($this->Photo->save($obj)){
                            $photoID = $this->Photo->getLastInsertID();

                            $response = REQUEST_OK;
                            $this->log("API->addPhoto() saved photo $photoID to db" , LOG_DEBUG);
                        } else {
                            $this->log("API->addPhoto() saving photo to db failed" , LOG_DEBUG);
                            $response = REQUEST_FAILED;
                            $errorMessage = ERROR_PHOTO_UPLOAD;
                        }
                    } else {
                        $this->log("API->addPhoto() thumb uploading failed" , LOG_DEBUG);
                        $response = REQUEST_FAILED;
                        $errorMessage = ERROR_PHOTO_UPLOAD;
                    }

                } else {
                    $this->log("API->addPhoto() photo uploading failed" , LOG_DEBUG);
                    $response = REQUEST_FAILED;
                    $errorMessage = ERROR_PHOTO_UPLOAD;
                }
            } else {
                $this->log("API->addPhoto() no photo found" , LOG_DEBUG);
            }

            $this->log("API->addPhoto() returns: response $response error $errorMessage" , LOG_DEBUG);
        
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
                $count_inbox = $this->UserInbox->countUnreadMessages($userID);
                $data['count_inbox'] = $count_inbox;
            }

            $data['photo_id'] = $photoID;
            $data['error'] = $errorMessage;
        } else {
            $response = REQUEST_UNAUTHORISED;
        }
        
        $data['response'] = $response;
        
        $this->layout = 'blank';
        echo json_encode(compact('data', $data));
    }
    
    //Creates a new place
    function addPlace(){
        if(isset($_REQUEST['user_id'])) $user_id = $_REQUEST['user_id'];
        if(isset($_REQUEST['name'])) $name = $_REQUEST['name'];
        if(isset($_REQUEST['category_id'])) $categoryId = $_REQUEST['category_id'];
        if(isset($_REQUEST['longitude'])) $longitude = $_REQUEST['longitude'];
        if(isset($_REQUEST['latitude'])) $latitude = $_REQUEST['latitude'];
        if(isset($_REQUEST['token'])) $token = $_REQUEST['token'];
        
        $this->log("API->addPlace() called for $name", LOG_DEBUG);
        
        //Authorise user
        $this->loadModel('User');
        $authorised = $this->User->authorise($user_id,$token);
        if($authorised){
        
            $placeCreated = false;
            $placeID = null;
            $response = null;
            $errorMessage = null;

            //Save place object
            $this->loadModel('Place');
            $place = array();
            $place['Place']['user_id'] = $user_id;
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

                //Thumbnail
                $filenameThumb = "thumb_".$dateString.$fileExtension;
                $uploadfileThumb = UPLOAD_PATH . PLACE_PATH . "/". "$filenameThumb";

                $this->log("API->addPlace() uploadfile is $uploadfile AND thumb is $uploadfileThumb" , LOG_DEBUG);

                if(is_uploaded_file($_FILES['photo']['tmp_name']) && move_uploaded_file($_FILES['photo']['tmp_name'], $uploadfile)){
                    if(is_uploaded_file($_FILES['thumb']['tmp_name']) && move_uploaded_file($_FILES['thumb']['tmp_name'], $uploadfileThumb)){
                        $this->log("API->addPlace() uploading succeeded" , LOG_DEBUG);

                        //Save photo info to the db
                        $this->loadModel('Photo');
                        $obj = array();
                        $obj['Photo']['path'] = $fileName;
                        $obj['Photo']['thumb'] = $filenameThumb;
                        $obj['Photo']['user_id'] = $user_id;
                        $obj['Photo']['type_id'] = PLACE_PHOTO_TYPE;
                        $obj['Photo']['place_id'] = $placeID;
                        
                        if($this->Photo->save($obj)){
                            $photoID = $this->Photo->getLastInsertID();

                            $this->log("API->addPlace() saved photo $photoID to db" , LOG_DEBUG);

                            //Update place with profile photo
                            $place['Place']['id'] = $placeID;
                            $place['Place']['photo_id'] = $photoID;
                            if(!$this->Place->save($place)){
                                $this->log("API->addPlace() failed to set profile image for place $placeID" , LOG_DEBUG);

                                $response = REQUEST_FAILED;
                                $errorMessage = ERROR_PLACE_PHOTO_UPLOAD;
                            } else {
                                $response = REQUEST_OK;
                            }

                        } else {
                            $this->log("API->addPlace() saving photo to db failed" , LOG_DEBUG);
                            $response = REQUEST_FAILED;
                            $errorMessage = ERROR_PLACE_PHOTO_UPLOAD;
                        }
                    } else {
                        $this->log("API->addPlace() thumb uploading failed" , LOG_DEBUG);
                        $response = REQUEST_FAILED;
                        $errorMessage = ERROR_PLACE_PHOTO_UPLOAD;
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
        
            //Badge handling
            if($response == REQUEST_OK){
                $this->loadModel('UserBadge');
                $this->loadModel('UserNotification');
                
                if($categoryId == PLACE_CATEGORY_CRUELTY && !$this->UserBadge->userHasBadge($user_id, BADGE_CRUELTY)){
                    //Award badge and notification
                    if($this->UserBadge->awardBadge($user_id, BADGE_CRUELTY)){
                        $this->UserNotification->create();
                        $obj2['UserNotification']['user_from'] = $user_id;
                        $obj2['UserNotification']['user_id'] = $user_id;
                        $obj2['UserNotification']['badge_id'] = BADGE_CRUELTY;
                        $obj2['UserNotification']['type_id'] = NOTIFICATION_AWARD_BADGE;

                        if($this->UserNotification->save($obj2)){
                            $response = REQUEST_OK;
                        }
                    }
                } else if($categoryId == PLACE_CATEGORY_PARK && !$this->UserBadge->userHasBadge($user_id, BADGE_GODFATHER)){
                    //Award badge and notification
                    if($this->UserBadge->awardBadge($user_id, BADGE_GODFATHER)){
                        $this->UserNotification->create();
                        $obj2['UserNotification']['user_from'] = $user_id;
                        $obj2['UserNotification']['user_id'] = $user_id;
                        $obj2['UserNotification']['badge_id'] = BADGE_GODFATHER;
                        $obj2['UserNotification']['type_id'] = NOTIFICATION_AWARD_BADGE;

                        if($this->UserNotification->save($obj2)){
                            $response = REQUEST_OK;
                        }
                    }
                } else if($categoryId == PLACE_CATEGORY_HOMELESS && !$this->UserBadge->userHasBadge($user_id, BADGE_SAVIOR)){
                    //Award badge and notification
                    $homelessPlaces = $this->Place->countPlacesByUser($user_id, PLACE_CATEGORY_HOMELESS);
                    
                    if($homelessPlaces == 4 && $this->UserBadge->awardBadge($user_id, BADGE_SAVIOR)){
                        $this->UserNotification->create();
                        $obj2['UserNotification']['user_from'] = $user_id;
                        $obj2['UserNotification']['user_id'] = $user_id;
                        $obj2['UserNotification']['badge_id'] = BADGE_SAVIOR;
                        $obj2['UserNotification']['type_id'] = NOTIFICATION_AWARD_BADGE;

                        if($this->UserNotification->save($obj2)){
                            $response = REQUEST_OK;
                        }
                    }
                } 
            }
            //End badge handling
            
            //Load additional data with this request
            if($response == REQUEST_OK){

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
            }
            
            $data['place_id'] = $placeID;
            $data['error'] = $errorMessage;
        } else {
            $response = REQUEST_UNAUTHORISED;
        }
        
        $data['response'] = $response;
        
        $this->layout = 'blank';
        echo json_encode(compact('data', $data));
    }
    
    function editDog(){
        if(isset($_REQUEST['user_id'])) $user_id = $_REQUEST['user_id'];
        if(isset($_REQUEST['token'])) $token = $_REQUEST['token'];
        if(isset($_REQUEST['edit'])) $editDog = $_REQUEST['edit'];
        if(isset($_REQUEST['dog_id'])) $dog_id = $_REQUEST['dog_id'];
        if(isset($_REQUEST['name'])) $name = $_REQUEST['name'];
        if(isset($_REQUEST['breed_id'])) $breed = $_REQUEST['breed_id'];
        if(isset($_REQUEST['age'])) $age = $_REQUEST['age'];
        if(isset($_REQUEST['weight'])) $weight = $_REQUEST['weight'];
        if(isset($_REQUEST['gender'])) $gender = $_REQUEST['gender'];
        if(isset($_REQUEST['mating'])) $mating = $_REQUEST['mating'];
        if(isset($_REQUEST['size'])) $size = $_REQUEST['size'];
        
        $this->log("API->editDog() called by user id $user_id for dog id $dog_id", LOG_DEBUG);
        
        //Authorise user
        $this->loadModel('User');
        $authorised = $this->User->authorise($user_id,$token);
        if($authorised){
            
            //If we're editing the entire dog profile
            if($editDog){
                //$dogCreated = false;
                $dogID = null;
                $response = null;
                $errorMessage = null;
                
                //Save dog object
                $this->loadModel('Dog');
                $dog = array();
                $dog['Dog']['breed_id'] = $breed;
                $dog['Dog']['owner_id'] = $user_id;
                $dog['Dog']['name'] = $name;
                $dog['Dog']['gender'] = $gender;
                $dog['Dog']['mating'] = $mating;
                $dog['Dog']['weight'] = $weight;
                $dog['Dog']['size'] = $size;
                $dog['Dog']['age'] = $age;
                $this->log("API->addDog() called ", LOG_DEBUG);
                $this->Dog->id = $dog_id;
                if($this->Dog->save($dog)){
                    
                    $response = REQUEST_OK;
                    $dogID = $this->Dog->getLastInsertID();
                } else {
                    $response = REQUEST_FAILED;
                    $errorMessage = ERROR_DOG_CREATION;
                }
            }
            
            if(isset($_FILES['photo'])){
                //Check for valid extension
                $dateString = Security::hash(time().rand(1, 10), 'md5');
                $fileExtension = ".jpeg";
                $fileName = $dateString.$fileExtension;
                $uploadfile = UPLOAD_PATH.DOG_PATH."/". "$fileName";

                //Thumbnail
                $filenameThumb = "thumb_".$dateString.$fileExtension;
                $uploadfileThumb = UPLOAD_PATH . DOG_PATH . "/". "$filenameThumb";

                $this->log("API->editDog() uploadfile is $uploadfile AND thumb is $uploadfileThumb" , LOG_DEBUG);

                if(is_uploaded_file($_FILES['photo']['tmp_name']) && move_uploaded_file($_FILES['photo']['tmp_name'], $uploadfile)){
                    if(is_uploaded_file($_FILES['thumb']['tmp_name']) && move_uploaded_file($_FILES['thumb']['tmp_name'], $uploadfileThumb)){
                        $this->log("API->editDog() uploading succeeded" , LOG_DEBUG);

                        //Save photo info to the db
                        $this->loadModel('Photo');
                        $obj = array();
                        $obj['Photo']['path'] = $fileName;
                        $obj['Photo']['thumb'] = $filenameThumb;
                        $obj['Photo']['user_id'] = $user_id;
                        $obj['Photo']['type_id'] = DOG_PHOTO_TYPE;

                        if($this->Photo->save($obj)){
                            $photoID = $this->Photo->getLastInsertID();
                            
                            $data['photo'] = $fileName;
                            $data['thumb'] = $filenameThumb;
                            $this->log("API->editDog() saved photo $photoID to db" , LOG_DEBUG);

                            //Update dog with profile photo
                            $this->loadModel('Dog');
                            $dog['Dog']['id'] = $dog_id;
                            $dog['Dog']['photo_id'] = $photoID;
                            if(!$this->Dog->save($dog)){
                                $this->log("API->editDog() failed to set profile image for dog $dog_id" , LOG_DEBUG);

                                $response = REQUEST_FAILED;
                                $errorMessage = ERROR_DOG_PHOTO_UPLOAD;
                            } else {
                                $response = REQUEST_OK;
                            }

                        } else {
                            $this->log("API->editDog() saving photo to db failed" , LOG_DEBUG);
                            $response = REQUEST_FAILED;
                            $errorMessage = ERROR_DOG_PHOTO_UPLOAD;
                        }
                    } else {
                        $this->log("API->editDog() thumb uploading failed" , LOG_DEBUG);
                        $response = REQUEST_FAILED;
                        $errorMessage = ERROR_DOG_PHOTO_UPLOAD;
                    }

                } else {
                    $this->log("API->editDog() photo uploading failed" , LOG_DEBUG);
                    $response = REQUEST_FAILED;
                    $errorMessage = ERROR_DOG_PHOTO_UPLOAD;
                }
            }
            
        } else {
            $response = REQUEST_UNAUTHORISED;
        }
        
        $data['response'] = $response;
        
        $this->layout = 'blank';
        echo json_encode(compact('data', $data));
    } 
    
    function editUser(){
        if(isset($_REQUEST['user_id'])) $user_id = $_REQUEST['user_id'];
        if(isset($_REQUEST['name'])) $name = $_REQUEST['name'];
        if(isset($_REQUEST['email'])) $email = $_REQUEST['email'];
        if(isset($_REQUEST['birth_date'])) $birthDate = $_REQUEST['birth_date'];
        if(isset($_REQUEST['country'])) $country = $_REQUEST['country'];
        if(isset($_REQUEST['address'])) $address = $_REQUEST['address'];
	if(isset($_REQUEST['gender'])) $gender = $_REQUEST['gender'];
        if(isset($_REQUEST['newsletter'])) $newsletter = $_REQUEST['newsletter'];
        if(isset($_REQUEST['token'])) $token = $_REQUEST['token'];
        if(isset($_REQUEST['edit'])) $editUser = $_REQUEST['edit'];
        
        $this->log("API->editUser() called for user id $user_id", LOG_DEBUG);
        
        //Authorise user
        $this->loadModel('User');
        $authorised = $this->User->authorise($user_id,$token);
        if($authorised){
            
            //If we're editing the entire user profile
            if($editUser){
                $response = null;
                $errorMessage = null;
                
                //Save dog object
                $this->loadModel('User');
                $user = array();
                $user['User']['name'] = $name;
                $user['User']['email'] = $email;
                
                if(isset($birthDate) && $birthDate != ''){
                    $day = substr($birthDate, 0,2);
                    $month = substr($birthDate, 5,2);
                    $year = substr($birthDate, 10,4);
                    $d = date("Y/m/d", mktime(0, 0, 0, $month, $day, $year));
                    $user['User']['birth_date'] = $d;
                }
                
                $user['User']['country_id'] = $country;
                $user['User']['address'] = $address;
                $user['User']['gender'] = $gender;
                $user['User']['newsletter'] = $newsletter;
                $this->log("API->editUser() called ", LOG_DEBUG);
                $this->User->id = $user_id;
                if($this->User->save($user)){

                    $response = REQUEST_OK;
                } else {
                    $response = REQUEST_FAILED;
                    $errorMessage = ERROR_USER_CREATION;
                }
            }
            
            //Change profile photo if requested
            if(isset($_FILES['photo'])){
                //Check for valid extension
                $dateString = Security::hash(time().rand(1, 10), 'md5');
                $fileExtension = ".jpeg";
                $fileName = $dateString.$fileExtension;
                $uploadfile = UPLOAD_PATH . USER_PATH . "/". "$fileName";

                //Thumbnail
                $filenameThumb = "thumb_".$dateString.$fileExtension;
                $uploadfileThumb = UPLOAD_PATH . USER_PATH . "/". "$filenameThumb";
                
                $this->log("API->editUser() uploadfile is $uploadfile AND thumb is $uploadfileThumb" , LOG_DEBUG);

                if(is_uploaded_file($_FILES['photo']['tmp_name']) && move_uploaded_file($_FILES['photo']['tmp_name'], $uploadfile)){
                    if(is_uploaded_file($_FILES['thumb']['tmp_name']) && move_uploaded_file($_FILES['thumb']['tmp_name'], $uploadfileThumb)){
                        $this->log("API->editUser() uploading succeeded" , LOG_DEBUG);

                        //Save photo info to the db
                        $this->loadModel('Photo');
                        $obj = array();
                        $obj['Photo']['path'] = $fileName;
                        $obj['Photo']['thumb'] = $filenameThumb;
                        $obj['Photo']['user_id'] = $user_id;
                        $obj['Photo']['type_id'] = USER_PHOTO_TYPE;
                        if($this->Photo->save($obj)){
                            $photoID = $this->Photo->getLastInsertID();

                            $this->log("API->editUser() saved photo $photoID to db" , LOG_DEBUG);
                            
                            $data['thumb'] = $filenameThumb;
                            $data['photo'] = $fileName;
                            
                            //Update user with profile photo
                            $user['User']['id'] = $user_id;
                            $user['User']['photo_id'] = $photoID;
                            if(!$this->User->save($user)){
                                $this->log("API->editUser() failed to set profile image for user $user_id" , LOG_DEBUG);

                                $response = REQUEST_FAILED;
                                $errorMessage = ERROR_USER_PHOTO_UPLOAD;
                            } else {
                                $response = REQUEST_OK;
                            }

                        } else {
                            $this->log("API->editUser() saving photo to db failed" , LOG_DEBUG);
                            $response = REQUEST_FAILED;
                            $errorMessage = ERROR_USER_PHOTO_UPLOAD;
                        }

                    } else {
                        $this->log("API->editUser() uploading failed" , LOG_DEBUG);
                        $response = REQUEST_FAILED;
                        $errorMessage = ERROR_USER_PHOTO_UPLOAD;
                    }
                } else {
                    $this->log("API->signup() uploading failed" , LOG_DEBUG);
                    $response = REQUEST_FAILED;
                    $errorMessage = ERROR_USER_PHOTO_UPLOAD;
                }
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
            $response = REQUEST_UNAUTHORISED;
        }
        
        $data['response'] = $response;
        
        $this->layout = 'blank';
        echo json_encode(compact('data', $data));
    }
    
    function editPassword(){
        if(isset($_REQUEST['user_id'])) $user_id = $_REQUEST['user_id'];
        if(isset($_REQUEST['old_pass'])) $oldPass = $_REQUEST['old_pass'];
        if(isset($_REQUEST['new_pass'])) $newPass = $_REQUEST['new_pass'];
        if(isset($_REQUEST['token'])) $token = $_REQUEST['token'];
        
        $this->log("API->editPassword() called for user with $user_id", LOG_DEBUG);
        
        //Authorise user
        $this->loadModel('User');
        $authorised = $this->User->authorise($user_id,$token);
        if($authorised){
            $response = null;

            if($this->User->validateClientCredentialsByUserId($user_id, $oldPass)){
                $pass = array();
                $pass['User']['password'] = $this->User->hashPassword($newPass);
                $this->User->id = $user_id;
                if($this->User->save($pass)){
                    
                    $securityToken = $this->User->generateToken($user_id, $newPass);
                    $data['token'] = $securityToken;
                    $response = REQUEST_OK;
                } else {
                    $response = REQUEST_FAILED;
                }
            }else{
                $response = ERROR_USER_PASSWORD;
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
        }else{
            $response = REQUEST_UNAUTHORISED;
        }
        
        $data['response'] = $response;
        
        $this->layout = 'blank';
        echo json_encode(compact('data', $data));
    }
    
    function lostDog(){
        if(isset($_REQUEST['user_id'])) $user_id = $_REQUEST['user_id'];
        if(isset($_REQUEST['dog_id'])) $dog_id = $_REQUEST['dog_id'];
        if(isset($_REQUEST['lat'])) $latitude = $_REQUEST['lat'];
        if(isset($_REQUEST['lon'])) $longitude = $_REQUEST['lon'];
        if(isset($_REQUEST['token'])) $token = $_REQUEST['token'];
        
        $this->log("API->lostDog() called for user with $user_id and dog $dog_id", LOG_DEBUG);
        
        //Authorise user
        $this->loadModel('User');
        $authorised = $this->User->authorise($user_id,$token);
        if($authorised){
            $this->log("API->lostDog() called ", LOG_DEBUG);
            
            $placeID = null;
            $response = null;
            $errorMessage = null;
            
            $this->loadModel('Dog');
            $currentDog = $this->Dog->findAllById($dog_id);
            $dogName = $currentDog[0]['Dog']['name'];
            $dogPhoto = $currentDog[0]['Dog']['photo_id'];

            //Save place object
            $this->loadModel('Place');
            $place = array();
            $place['Place']['user_id'] = $user_id;
            $place['Place']['name'] = $dogName;
            $place['Place']['category_id'] = PLACE_LOST_DOG;
            $place['Place']['dog_id'] = $dog_id;
            $place['Place']['lon'] = $longitude;
            $place['Place']['lat'] = $latitude;
            $place['Place']['photo_id'] = $dogPhoto;
            if($this->Place->save($place)){
                $response = REQUEST_OK;
                $placeID = $this->Place->getLastInsertID();
            } else {
                $response = REQUEST_FAILED;
                $errorMessage = ERROR_PLACE_CREATION;
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
                
                //Feed entry
                $this->loadModel('Feed');

                $userObject = $this->User->findById($user_id);
                $user_name = $userObject['User']['name'];
                
                $feed['Feed']['user_from'] = $user_id;
                $feed['Feed']['user_from_name'] = $user_name;
                $feed['Feed']['target_dog_id'] = $dog_id;
                $feed['Feed']['target_dog_name'] = $dogName;
                $feed['Feed']['type_id'] = FEED_DOG_LOST;

                $feedOK = $this->Feed->save($feed);

                if(!$feedOK){
                    $this->log("API->addActivityComment() error creating feed", LOG_DEBUG);
                    $response = ERROR_FEED_CREATION;
                } else {
                    $this->log("API->addActivityComment() saved feed ", LOG_DEBUG);
                }
                
                
            }
        }else{
            $response = REQUEST_UNAUTHORISED;
        }
        
        $data['response'] = $response;
        
        $this->layout = 'blank';
        echo json_encode(compact('data', $data));
    }
    
    function foundDog(){
        if(isset($_REQUEST['user_id'])) $user_id = $_REQUEST['user_id'];
        if(isset($_REQUEST['dog_id'])) $dog_id = $_REQUEST['dog_id'];
        if(isset($_REQUEST['token'])) $token = $_REQUEST['token'];
        
        $this->log("API->foundDog() called for user with $user_id and dog $dog_id", LOG_DEBUG);
        
        //Authorise user
        $this->loadModel('User');
        $authorised = $this->User->authorise($user_id,$token);
        if($authorised){
            $this->log("API->foundDog() called ", LOG_DEBUG);
            $response = null;
            $errorMessage = null;

            //delete lost dog place 
            $this->loadModel('Place');
            if($this->Place->deleteAll(array('Place.dog_id' => $dog_id ), false)){
                $this->log("API->foundDog() called ", LOG_DEBUG);
                $response = REQUEST_OK;
            } else {
                $response = REQUEST_FAILED;
                $errorMessage = ERROR_PLACE_DELETION;
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
                
                //Feed entry
                $this->loadModel('Feed');
                $this->loadModel('Dog');
                
                $userObject = $this->User->findById($user_id);
                $user_name = $userObject['User']['name'];
                $dogObject = $this->Dog->findById($dog_id);
                $dog_name = $dogObject['Dog']['name'];
                
                $feed['Feed']['user_from'] = $user_id;
                $feed['Feed']['user_from_name'] = $user_name;
                $feed['Feed']['target_dog_id'] = $dog_id;
                $feed['Feed']['target_dog_name'] = $dog_name;
                $feed['Feed']['type_id'] = FEED_DOG_FOUND;

                $feedOK = $this->Feed->save($feed);

                if(!$feedOK){
                    $this->log("API->addActivityComment() error creating feed", LOG_DEBUG);
                    $response = ERROR_FEED_CREATION;
                } else {
                    $this->log("API->addActivityComment() saved feed ", LOG_DEBUG);
                }
            }
        }else{
            $response = REQUEST_UNAUTHORISED;
        }
        
        $data['response'] = $response;
        
        $this->layout = 'blank';
        echo json_encode(compact('data', $data));
    }
    
    function resetPassword(){
        if(isset($_REQUEST['email'])) $email = $_REQUEST['email'];
        
        $this->log("API->resetPassword() called for user with $email", LOG_DEBUG);
        
        $response = null;
        
        $this->loadModel('User');
        //find user
	$currentUser = $this->User->findAllByEmail($email);
        if($currentUser != null){
            $user_id = $currentUser[0]['User']['id'];
            if($user_id != null) {
                $pass = array();

                $password = $this->User->generatePassword(6);
                $this->log("API->resetPassword() generated new password $password", LOG_DEBUG);
                $pass['User']['password'] = $this->User->hashPassword($password);
                $this->User->id = $user_id;
                if($this->User->save($pass)){

                    $response = REQUEST_OK;
                } else {
                    $response = REQUEST_FAILED;
                }
            }else{
                $response = REQUEST_FAILED;
            }
         }else{
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
    
    function signup(){
        if(isset($_REQUEST['name'])) $name = $_REQUEST['name'];
        if(isset($_REQUEST['email'])) $email = $_REQUEST['email'];
        if(isset($_REQUEST['password'])) $password = $_REQUEST['password'];
        if(isset($_REQUEST['birth_date'])) $birthDate = $_REQUEST['birth_date'];
        if(isset($_REQUEST['country'])) $country = $_REQUEST['country'];
        if(isset($_REQUEST['address'])) $address = $_REQUEST['address'];
        if(isset($_REQUEST['facebook_id'])) $facebook_id = $_REQUEST['facebook_id'];
	if(isset($_REQUEST['gender'])) $gender = $_REQUEST['gender'];
        if(isset($_REQUEST['newsletter'])) $newsletter = $_REQUEST['newsletter'];
        
        $this->log("API->signup() called for $name with date $birthDate", LOG_DEBUG);
        
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
            
            if(isset($address) && $address != ''){
                $user['User']['address'] = $address;
            }
            
            if(isset($gender) && $gender != ''){
                $user['User']['gender'] = $gender;
            }
            
            if(isset($country) && $country != ''){
                $user['User']['country_id'] = $country;
            }
            
            if(isset($birthDate) && $birthDate != ''){
                $day = substr($birthDate, 0,2);
                $month = substr($birthDate, 5,2);
                $year = substr($birthDate, 10,4);
                $d = date("Y/m/d", mktime(0, 0, 0, $month, $day, $year));
                $user['User']['birth_date'] = $d;
            }
            
            if(isset($newsletter) && $newsletter != ''){
                $user['User']['newsletter'] = $newsletter;
            }
            
            if($this->User->save($user)){
                $userCreated = true;
                $userID = $this->User->getLastInsertID();
                $securityToken = $this->User->generateToken($userID, $password);
                $response = REQUEST_OK;
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
                        $obj['Photo']['type_id'] = USER_PHOTO_TYPE;
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
            } else if($userCreated && !isset($_FILES['photo'])){
                //For FB registrations we need to create a dummy profile photo, pointing to the FB profile photo
                if($facebook_id != null){
                    
                    $fb_path = "http://graph.facebook.com/$facebook_id/picture?height=320&width=320";
                    $fb_thumb = "http://graph.facebook.com/$facebook_id/picture?height=60&width=60";
                    
                    //Save photo info to the db
                    $this->loadModel('Photo');
                    $obj = array();
                    $obj['Photo']['path'] = $fb_path;
                    $obj['Photo']['thumb'] = $fb_thumb;
                    $obj['Photo']['user_id'] = $userID;
                    
                    if($this->Photo->save($obj)){
                        $photoID = $this->Photo->getLastInsertID();
                        
                        $this->log("API->signup() saved dummy photo $photoID for facebook user $facebook_id to db" , LOG_DEBUG);
                        $data['thumb'] = $fb_thumb;
                        $data['photo'] = $fb_path;
                        
                        //Update user with profile photo
                        $user['User']['id'] = $userID;
                        $user['User']['photo_id'] = $photoID;
                        if(!$this->User->save($user)){
                            $this->log("API->signup() failed to set dummy FB profile image for user $userID" , LOG_DEBUG);

                            $response = REQUEST_FAILED;
                            $errorMessage = ERROR_USER_PHOTO_UPLOAD;
                        } else {
                            $response = REQUEST_OK;
                        }
                        
                    } else {
                         $this->log("API->signup() saving dummy photo for facebook user $facebook_id to db failed" , LOG_DEBUG);
                        $response = REQUEST_FAILED;
                        $errorMessage = ERROR_USER_PHOTO_UPLOAD;
                    }
                }
            } else {
                $this->log("API->signup() no photo found" , LOG_DEBUG);
            }
        }
        
        $this->log("API->signup() returns: response $response error $errorMessage token $securityToken" , LOG_DEBUG);
        
        if($response == REQUEST_OK){
            
            //Load dog breeds
            $this->loadModel('DogBreed');
            //$breeds = $this->DogBreed->find('all');
            $breeds = $this->DogBreed->find('all', array(
                'conditions' => array('DogBreed.active' => '1')
            ));
            $data['breeds'] = $breeds;
            
            //dogfuel rules
            $this->loadModel('DogfuelRule');
            $rules = $this->DogfuelRule->find('all', array(
                'conditions' => array('DogfuelRule.active' => '1')
            ));
            $data['rules'] = $rules;
            
            //Place Categories
            $this->loadModel('PlaceCategory');
            $categories = $this->PlaceCategory->find('all');
            $data['categories'] = $categories;
            
            //Badge handling
            $this->loadModel('UserBadge');
            if(!$this->UserBadge->userHasBadge($userID, BADGE_ROOKIE)){
                
                //Award badge and notification
                $this->loadModel('UserNotification');
                if($this->UserBadge->awardBadge($userID, BADGE_ROOKIE)){
                    $obj2['UserNotification']['user_from'] = $userID;
                    $obj2['UserNotification']['user_id'] = $userID;
                    $obj2['UserNotification']['badge_id'] = BADGE_ROOKIE;
                    $obj2['UserNotification']['type_id'] = NOTIFICATION_AWARD_BADGE;

                    if($this->UserNotification->save($obj2)){
                        $response = REQUEST_OK;
                    }
                }
            }
            
            //Signup email for non FB users
            try {
                //if(!isset($facebook_id)){
                if(stristr($email, '@facebook.com') === FALSE){    
                    $Email = new CakeEmail('smtp');   
                    $Email->emailFormat('html')
                            ->subject('Welcome to Dogsquare!')
                            ->template('welcome')
                            ->to($email)
                            ->send();
                }
            } catch(SocketException $e) {
                $this->log("API->signup() error when trying to email $email ".$e->getMessage(), LOG_DEBUG);
            }
        }
        
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
        if(isset($_REQUEST['token'])) $token = $_REQUEST['token'];
        
        $this->log("API->addPlaceComment() called for palce: $placeId from user: $userId", LOG_DEBUG);
        
        //Authorise user
        $this->loadModel('User');
        $authorised = $this->User->authorise($userId,$token);
        if($authorised){
        
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

                $date = $this->PlaceComment->find('first', array(
                                            'conditions'=>array('id'=>$commentId),
                                            'fields'=>array('created')
                                          ));
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
                $count_inbox = $this->UserInbox->countUnreadMessages($userId);
                $data['count_inbox'] = $count_inbox;
            }
            
            $data['comment_id'] = $commentId;
            $data['error'] = $errorMessage;
            $data['date'] = strtotime($date['PlaceComment']['created']);
            
        } else {
            $response = REQUEST_UNAUTHORISED;
        }
        
        $data['response'] = $response;
        
        $this->layout = 'blank';
        echo json_encode(compact('data', $data));
    }
    
    //add a comment for a given activity
    function addActivityComment(){
        if(isset($_REQUEST['user_id'])) $userId = $_REQUEST['user_id'];
        if(isset($_REQUEST['comment'])) $comment = $_REQUEST['comment'];
        if(isset($_REQUEST['activity_id'])) $activityId = $_REQUEST['activity_id'];
        if(isset($_REQUEST['token'])) $token = $_REQUEST['token'];
        
        $this->log("API->addActivityComment() called for activity: $activityId from user: $userId", LOG_DEBUG);
        
        //Authorise user
        $this->loadModel('User');
        $authorised = $this->User->authorise($userId,$token);
        if($authorised){
        
            $commentId = null;
            $response = null;
            $errorMessage = null;

            //Obtain activity info
            $this->loadModel('Activity');
            $activity_obj = $this->Activity->findById($activityId);

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

                        $date = $this->ActivityComment->find('first', array(
                                            'conditions'=>array('id'=>$commentId),
                                            'fields'=>array('created')
                                          ));
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
                    $count_inbox = $this->UserInbox->countUnreadMessages($userId);
                    $data['count_inbox'] = $count_inbox;
                }
                
                //Feed entry
                if($response == REQUEST_OK){
                    $this->loadModel('Feed');

                    $userObject = $this->User->findById($userId);
                    $user_name = $userObject['User']['name'];
                    $target_user_id = $activity_obj['Activity']['user_id'];
                    $targetUserObject = $this->User->findById($target_user_id);
                    $target_user_name = $targetUserObject['User']['name'];

                    $feed['Feed']['user_from'] = $userId;
                    $feed['Feed']['user_from_name'] = $user_name;
                    $feed['Feed']['target_user_id'] = $target_user_id;
                    $feed['Feed']['target_user_name'] = $target_user_name;
                    $feed['Feed']['type_id'] = FEED_FRIEND_COMMENT_ACTIVITY;

                    $feedOK = $this->Feed->save($feed);

                    if(!$feedOK){
                        $this->log("API->addActivityComment() error creating feed", LOG_DEBUG);
                        $response = ERROR_FEED_CREATION;
                    } else {
                        $this->log("API->addActivityComment() saved feed ", LOG_DEBUG);
                    }
                }
                
            } else {
                $response = REQUEST_INVALID;
            }
            
            $data['comment_id'] = $commentId;
            $data['error'] = $errorMessage;
            $data['date'] = strtotime($date['ActivityComment']['created']);
            
        } else {
            $response = REQUEST_UNAUTHORISED;
        }
        
        $data['response'] = $response;
        
        $this->layout = 'blank';
        echo json_encode(compact('data', $data));
    }
    
    //addNote adds a note and edits a note
    function addNote(){
        if(isset($_REQUEST['user_id'])) $user_id = $_REQUEST['user_id'];
        if(isset($_REQUEST['title'])) $title = $_REQUEST['title'];
        if(isset($_REQUEST['description'])) $description = $_REQUEST['description'];
        if(isset($_REQUEST['date'])) $date = $_REQUEST['date'];
        if(isset($_REQUEST['completed'])) $completed = $_REQUEST['completed'];
        if(isset($_REQUEST['remind'])) $remind = $_REQUEST['remind'];
        if(isset($_REQUEST['interaction_type'])) $interactionType = $_REQUEST['interaction_type'];
        if(isset($_REQUEST['note_id'])) $noteId = $_REQUEST['note_id'];
        if(isset($_REQUEST['token'])) $token = $_REQUEST['token'];
        
        
        //Authorise user
        $this->loadModel('UserNotification');
        $this->loadModel('User');
        $authorised = $this->User->authorise($user_id,$token);
        if($authorised){
            
            $response = null;
            $errorMessage = null;
            
            if($interactionType == ADD_NOTE) {
                //Save note object
                $this->loadModel('UserPassport');
                $pass = array();
                $pass['UserPassport']['user_id'] = $user_id;
                $pass['UserPassport']['title'] = $title;
                $pass['UserPassport']['description'] = $description;
                $pass['UserPassport']['due_date'] = $date;
                $pass['UserPassport']['completed'] = $completed;
                $pass['UserPassport']['remind'] = $remind;
                $this->log("API->addNote() called ", LOG_DEBUG);
                if($this->UserPassport->save($pass)){
                    $noteId = $this->UserPassport->getLastInsertID();
                    
                    $data['note_id'] = $noteId;
                    $response = REQUEST_OK;
                } else {
                    $response = REQUEST_FAILED;
                    $errorMessage = ERROR_NOTE_CREATION;
                }
            } else if($interactionType == EDIT_NOTE) {
                //Save note object
                $this->loadModel('UserPassport');
                $pass = array();
                $pass['UserPassport']['user_id'] = $user_id;
                $pass['UserPassport']['title'] = $title;
                $pass['UserPassport']['description'] = $description;
                $pass['UserPassport']['due_date'] = $date;
                $pass['UserPassport']['completed'] = $completed;
                $pass['UserPassport']['remind'] = $remind;
                $this->log("API->addNote() called ", LOG_DEBUG);
                $this->UserPassport->id = $noteId;
                if($this->UserPassport->save($pass)){
                    $response = REQUEST_OK;
                } else {
                    $response = REQUEST_FAILED;
                    $errorMessage = ERROR_NOTE_CREATION;
                }

                //Badge handling
                if($response == REQUEST_OK){
                    $this->loadModel('UserBadge');
                    if($completed == 1 && !$this->UserBadge->userHasBadge($user_id, BADGE_HEALTHY)){
                        //Award badge and notification
                        if($this->UserBadge->awardBadge($user_id, BADGE_HEALTHY)){
                            $obj2['UserNotification']['user_from'] = $user_id;
                            $obj2['UserNotification']['user_id'] = $user_id;
                            $obj2['UserNotification']['badge_id'] = BADGE_HEALTHY;
                            $obj2['UserNotification']['type_id'] = NOTIFICATION_AWARD_BADGE;

                            if($this->UserNotification->save($obj2)){
                                $response = REQUEST_OK;
                            }
                        }
                    }
                }
            }

            $this->log("API->addNote() returns: response $response error $errorMessage" , LOG_DEBUG);
            
            //Load additional data with this request
            if($response == REQUEST_OK){
                //return date to use locally
                
                /*
                $date = $this->UserPassport->find('first', array(
                                            'conditions'=>array('id'=>$noteId),
                                            'fields'=>array('due_date')
                                          ));
                $dateTimestamp = strtotime($date['UserPassport']['due_date']);
                $data['date'] = $dateTimestamp;
                $this->log("API->addNote() returns to user, date: $dateTimestamp and note id: $noteId", LOG_DEBUG);
                */
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
            }
        } else {
            $response = REQUEST_UNAUTHORISED;
        }
        
        $data['response'] = $response;
        
        $this->layout = 'blank';
        echo json_encode(compact('data', $data));
    }
    
    //delete note
    function deleteNote(){
        if(isset($_REQUEST['user_id'])) $user_id = $_REQUEST['user_id'];
        if(isset($_REQUEST['note_id'])) $note_id = $_REQUEST['note_id'];
        if(isset($_REQUEST['token'])) $token = $_REQUEST['token'];
        
        //Authorise user
        $this->loadModel('User');
        $authorised = $this->User->authorise($user_id,$token);
        if($authorised){
            $response = null;
            $errorMessage = null;
            
            $this->log("API->deleteNote() called ", LOG_DEBUG);
            
            //delete note 
            $this->loadModel('UserPassport');

            $this->UserPassport->id = $note_id;
            if($this->UserPassport->delete()){
                $response = REQUEST_OK;
            } else {
                $response = REQUEST_FAILED;
                $errorMessage = ERROR_NOTE_DELETION;
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
            $response = REQUEST_UNAUTHORISED;
        }
        
        $this->log("API->deleteNote() returns: response $response error $errorMessage" , LOG_DEBUG);
        $data['response'] = $response;
        
        $this->layout = 'blank';
        echo json_encode(compact('data', $data));
    }
    
    //delete dog
    function deleteDog(){
        if(isset($_REQUEST['user_id'])) $user_id = $_REQUEST['user_id'];
        if(isset($_REQUEST['dog_id'])) $dog_id = $_REQUEST['dog_id'];
        if(isset($_REQUEST['token'])) $token = $_REQUEST['token'];
        
        //Authorise user
        $this->loadModel('User');
        $authorised = $this->User->authorise($user_id,$token);
        if($authorised){
            $response = null;
            $errorMessage = null;
            
            $this->log("API->deleteDog() called ", LOG_DEBUG);
            
            //delete dog 
            $this->loadModel('Dog');

            $this->Dog->id = $dog_id;
            if($this->Dog->delete()){
                $response = REQUEST_OK;
                
                //Clear lost dog places
                $this->Dog->deleteLostDogs($dog_id);
                
            } else {
                $response = REQUEST_FAILED;
                $errorMessage = ERROR_DOG_DELETION;
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
            $response = REQUEST_UNAUTHORISED;
        }
        
        $this->log("API->deleteDog() returns: response $response error $errorMessage" , LOG_DEBUG);
        $data['response'] = $response;
        
        $this->layout = 'blank';
        echo json_encode(compact('data', $data));
    }
    
    //delete inbox messages
    function deleteInboxMessages(){
        if(isset($_REQUEST['user_id'])) $user_id = $_REQUEST['user_id'];
        if(isset($_REQUEST['target_id'])) $target_id = $_REQUEST['target_id'];
        if(isset($_REQUEST['token'])) $token = $_REQUEST['token'];
        
        //Authorise user
        $this->loadModel('User');
        $authorised = $this->User->authorise($user_id,$token);
        if($authorised){
            $response = null;
            $errorMessage = null;
            
            $this->log("API->deleteInboxMessages() called ", LOG_DEBUG);
            
            //delete note object
            $this->loadModel('UserInbox');
            if($this->UserInbox->deleteAll(array('UserInbox.user_from' => $target_id, 'UserInbox.user_to' => $user_id))){
                if($this->UserInbox->deleteAll(array('UserInbox.user_to' => $target_id, 'UserInbox.user_from' => $user_id))){
                
                    $response = REQUEST_OK;
                } else {
                    $response = REQUEST_FAILED;
                    $errorMessage = ERROR_NOTE_DELETION;
                }
            } else {
                $response = REQUEST_FAILED;
                $errorMessage = ERROR_NOTE_DELETION;
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
            $response = REQUEST_UNAUTHORISED;
        }
        
        $this->log("API->deleteInboxMessages() returns: response $response error $errorMessage" , LOG_DEBUG);
        $data['response'] = $response;
        
        $this->layout = 'blank';
        echo json_encode(compact('data', $data));
    }
    
    //Checks if the specified list of emails maps to dogsquare users
    function areUsers(){
        if(isset($_REQUEST['user_id'])) $user_id = $_REQUEST['user_id'];
        if(isset($_REQUEST['list'])) $list = $_REQUEST['list'];
        if(isset($_REQUEST['token'])) $token = $_REQUEST['token'];
        
        //Authorise user
        $this->loadModel('User');
        $authorised = $this->User->authorise($user_id,$token);
        if($authorised){
        
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

            $response = REQUEST_OK;
            $data['results'] = $userData;
        } else {
            $response = REQUEST_UNAUTHORISED;
        }
        
        $data['response'] = $response;
        
        $this->layout = 'blank';
        echo json_encode(compact('data', $data));
    }
    
    function searchUser(){
        if(isset($_REQUEST['user_id'])) $user_id = $_REQUEST['user_id'];
        if(isset($_REQUEST['name'])) $name = $_REQUEST['name'];
        if(isset($_REQUEST['token'])) $token = $_REQUEST['token'];
        
        //Authorise user
        $this->loadModel('User');
        $authorised = $this->User->authorise($user_id,$token);
        if($authorised){
        
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

            $response = REQUEST_OK;
            $data['users'] = $userData;
        } else {
            $response = REQUEST_UNAUTHORISED;
        }
        
        $data['response'] = $response;
        
        $this->layout = 'blank';
        echo json_encode(compact('data', $data));
    }
    
    //Follows the specified user
    function followUser(){
        if(isset($_REQUEST['user_id'])) $user_id = $_REQUEST['user_id'];
        if(isset($_REQUEST['follow_user'])) $follow_user = $_REQUEST['follow_user'];
        if(isset($_REQUEST['token'])) $token = $_REQUEST['token'];
        
        //Authorise user
        $this->loadModel('User');
        $authorised = $this->User->authorise($user_id,$token);
        if($authorised){
        
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
        
            //Check for mutual followers
            $mutual_follower = $this->UserFollows->isMutualFollower($user_id, $follow_user);
            $data['mutual_followers'] = $mutual_follower;
            
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
                
                //Send email notification
                //if($userTarget['User']['facebook_id'] == null){
                if(stristr($userTarget['User']['email'], '@facebook.com') === FALSE){    
                    $this->loadModel('Dog');
                    $this->loadModel('Photo');
                    $photoObject = $this->Photo->findById($user['User']['photo_id']);
                    $photoSourceObject = $this->Photo->findById($userTarget['User']['photo_id']);
                    $follow_stats = $this->UserFollows->getFollowStats($user_id);
                    $userDogsCount = $this->Dog->countUserDogs($user_id);
                    
                    $emailUser = $target_user_name;;
                    $followerUser = $user_name;
                    $followers = $follow_stats['followers'];
                    $following = $follow_stats['following'];
                    $dogs = $userDogsCount;
                    
                    try {
                        $Email = new CakeEmail('smtp');  
                        $Email->emailFormat('html')
                                ->subject('New follow Dogsquare')
                            ->template('follow')
                            ->viewVars(array('emailUser' => $emailUser, 'followerUser' => $followerUser, 'followers' => $followers, 'following' => $following, 'dogs' => $dogs, 'userPhoto' => $photoObject['Photo']['path'], 'sourcePhoto' => $photoSourceObject['Photo']['thumb']))    
                            ->to($userTarget['User']['email'])
                            ->send();
                        } catch(SocketException $e) {
                            $this->log("API->followUser() error when trying to email ".$userTarget['User']['email'] ." with error " .$e->getMessage(), LOG_DEBUG);
                        }
                }
                
            }
        } else {
            $response = REQUEST_UNAUTHORISED;
        }
        
        $data['response'] = $response;
        
        $this->layout = 'blank';
        echo json_encode(compact('data', $data));
    }
    
    //Unfollows the specified user
    function unfollowUser(){
        if(isset($_REQUEST['user_id'])) $user_id = $_REQUEST['user_id'];
        if(isset($_REQUEST['follow_user'])) $follow_user = $_REQUEST['follow_user'];
        if(isset($_REQUEST['token'])) $token = $_REQUEST['token'];
        
        //Authorise user
        $this->loadModel('User');
        $authorised = $this->User->authorise($user_id,$token);
        if($authorised){
        
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
            
            //Check for mutual followers
            $mutual_follower = $this->UserFollows->isMutualFollower($user_id, $follow_user);
            $data['mutual_followers'] = $mutual_follower;
            
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
        } else {
            $response = REQUEST_UNAUTHORISED;
        }
        
        $data['response'] = $response;
        
        $this->layout = 'blank';
        echo json_encode(compact('data', $data));
    }
    
    //Returns the followers of user $target_id
    function getFollowers(){
        if(isset($_REQUEST['user_id'])) $user_id = $_REQUEST['user_id'];
        if(isset($_REQUEST['target_id'])) $target_id = $_REQUEST['target_id'];
        if(isset($_REQUEST['token'])) $token = $_REQUEST['token'];
        
        //Authorise user
        $this->loadModel('User');
        $authorised = $this->User->authorise($user_id,$token);
        if($authorised){
        
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

            $response = REQUEST_OK;
            $data['users'] = $users;
            
        } else {
            $response = REQUEST_UNAUTHORISED;
        }
        
        $data['response'] = REQUEST_OK;
        
        $this->layout = 'blank';
        echo json_encode(compact('data', $data));
    }
    
    //Returns the users following user $target_id
    function getFollowing(){
        if(isset($_REQUEST['user_id'])) $user_id = $_REQUEST['user_id'];
        if(isset($_REQUEST['target_id'])) $target_id = $_REQUEST['target_id'];
        if(isset($_REQUEST['token'])) $token = $_REQUEST['token'];
        
        //Authorise user
        $this->loadModel('User');
        $authorised = $this->User->authorise($user_id,$token);
        if($authorised){
        
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
            
            $data['users'] = $users;
            $response = REQUEST_OK;
        } else {
            $response = REQUEST_UNAUTHORISED;
        }
        
        $data['response'] = $response;
        
        $this->layout = 'blank';
        echo json_encode(compact('data', $data));
    }
    
    //Returns the users that are mutually followed for user $user_id
    function getMutualFollowers(){
        if(isset($_REQUEST['user_id'])) $user_id = $_REQUEST['user_id'];
        if(isset($_REQUEST['target_id'])) $target_id = $_REQUEST['target_id'];
        if(isset($_REQUEST['token'])) $token = $_REQUEST['token'];
        
        //Authorise user
        $this->loadModel('User');
        $authorised = $this->User->authorise($user_id,$token);
        if($authorised){
        
            $this->loadModel('UserFollows');
            $mutual_followers = $this->UserFollows->getMutualFollowers($target_id);
            $data['mutual_followers'] = $mutual_followers;
            $response = REQUEST_OK;
            
        } else {
            $response = REQUEST_UNAUTHORISED;
        }
        
        $data['response'] = REQUEST_OK;
        
        $this->layout = 'blank';
        echo json_encode(compact('data', $data));
    }
    
    //Sends a walk request to the specified user
    function walkRequest(){
        if(isset($_REQUEST['user_id'])) $user_id = $_REQUEST['user_id'];
        if(isset($_REQUEST['target_id'])) $target_id = $_REQUEST['target_id'];
        if(isset($_REQUEST['token'])) $token = $_REQUEST['token'];
        
        //Authorise user
        $this->loadModel('User');
        $authorised = $this->User->authorise($user_id,$token);
        if($authorised){
        
            //send a message with the default walk request text
            $this->loadModel('UserInbox');
            $obj = array();
            $obj['UserInbox']['user_from'] = $user_id;
            $obj['UserInbox']['user_to'] = $target_id;
            $obj['UserInbox']['message'] = WALK_REQUEST_MSG;

            $message_id = null;
            if($this->UserInbox->save($obj)){
                $response = REQUEST_OK;
                $message_id = $this->UserInbox->getLastInsertID();

                $this->loadModel('UserNotification');

                $obj2['UserNotification']['user_from'] = $user_id;
                $obj2['UserNotification']['user_id'] = $target_id;
                $obj2['UserNotification']['type_id'] = NOTIFICATION_WALK_REQUEST;

                if($this->UserNotification->save($obj2)){
                    $response = REQUEST_OK;
                }else{
                    $response = REQUEST_FAILED;
                }

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
        } else {
            $response = REQUEST_UNAUTHORISED;
        }
        
        $data['response'] = $response;
        $data['message_id'] = $message_id;
        
        $this->layout = 'blank';
        echo json_encode(compact('data', $data));
    }
    
    //Returns the unread notifications of the specified user
    function getNotifications(){
        if(isset($_REQUEST['user_id'])) $user_id = $_REQUEST['user_id'];
        if(isset($_REQUEST['token'])) $token = $_REQUEST['token'];
        
        $this->log("API->getNotifications() called for user $user_id" , LOG_DEBUG);
        
        //Authorise user
        $this->loadModel('User');
        $authorised = $this->User->authorise($user_id,$token);
        if($authorised){
        
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

            $response = REQUEST_OK;
            $data['notifications'] = $notifications;
        } else {
            $response = REQUEST_UNAUTHORISED;
        }
        
        $data['response'] = $response;
        
        $this->layout = 'blank';
        echo json_encode(compact('data', $data));
    }
    
    //Returns all the activity related data for the specified activity id
    function getActivity(){
        if(isset($_REQUEST['user_id'])) $user_id = $_REQUEST['user_id'];
        if(isset($_REQUEST['activity_id'])) $activity_id = $_REQUEST['activity_id'];
        if(isset($_REQUEST['token'])) $token = $_REQUEST['token'];
        
        $this->log("API->getActivity() called for user $user_id and activity $activity_id" , LOG_DEBUG);
        
        //Authorise user
        $this->loadModel('User');
        $authorised = $this->User->authorise($user_id,$token);
        if($authorised){
        
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

            $response = REQUEST_OK;
            $data['activity'] = $activity;
            $data['dogs'] = $dogs;
            $data['coordinates'] = $coordinates;
            $data['comments'] = $comments;
        } else {
            $response = REQUEST_UNAUTHORISED;
        }
        
        $data['response'] = $response;
        
        $this->layout = 'blank';
        echo json_encode(compact('data', $data));
    }
    
    function getActivityLikedUsers(){
        if(isset($_REQUEST['user_id'])) $user_id = $_REQUEST['user_id'];
        if(isset($_REQUEST['activity_id'])) $activity_id = $_REQUEST['activity_id'];
        if(isset($_REQUEST['token'])) $token = $_REQUEST['token'];
        
        //Authorise user
        $this->loadModel('User');
        $authorised = $this->User->authorise($user_id,$token);
        if($authorised){
        
            $this->loadModel('Activity');
            $likedUsers = $this->Activity->getLikedUsers($activity_id);

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

            $response = REQUEST_OK;
            $data['users'] = $likedUsers;
        } else {
            $response = REQUEST_UNAUTHORISED;
        }
        
        $data['response'] = $response;
        
        $this->layout = 'blank';
        echo json_encode(compact('data', $data));
        
    }
    
    function getPlaceCheckinUsers(){
        if(isset($_REQUEST['user_id'])) $user_id = $_REQUEST['user_id'];
        if(isset($_REQUEST['place_id'])) $place_id = $_REQUEST['place_id'];
        if(isset($_REQUEST['token'])) $token = $_REQUEST['token'];
        
        //Authorise user
        $this->loadModel('User');
        $authorised = $this->User->authorise($user_id,$token);
        
        if($authorised){
            $this->loadModel('Place');
            $likedUsers = $this->Place->getCheckinUsers($place_id);

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

            $response = REQUEST_OK;
            $data['users'] = $likedUsers;
        } else {
            $response = REQUEST_UNAUTHORISED;
        }
        
        $data['response'] = $response;
        $this->layout = 'blank';
        echo json_encode(compact('data', $data));
        
    }
    
    function getPlaceLikedUsers(){
        if(isset($_REQUEST['user_id'])) $user_id = $_REQUEST['user_id'];
        if(isset($_REQUEST['place_id'])) $place_id = $_REQUEST['place_id'];
        if(isset($_REQUEST['token'])) $token = $_REQUEST['token'];
        
        //Authorise user
        $this->loadModel('User');
        $authorised = $this->User->authorise($user_id,$token);
        
        if($authorised){
            $this->loadModel('Place');
            $likedUsers = $this->Place->getLikedUsers($place_id);

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

            $response = REQUEST_OK;
            $data['users'] = $likedUsers;
        } else {
            $response = REQUEST_UNAUTHORISED;
        }
        
        $data['response'] = $response;
        $this->layout = 'blank';
        echo json_encode(compact('data', $data));
        
    }
    
    function getDogLikedUsers(){
        if(isset($_REQUEST['user_id'])) $user_id = $_REQUEST['user_id'];
        if(isset($_REQUEST['dog_id'])) $dog_id = $_REQUEST['dog_id'];
        if(isset($_REQUEST['token'])) $token = $_REQUEST['token'];
        
        //Authorise user
        $this->loadModel('User');
        $authorised = $this->User->authorise($user_id,$token);
        
        if($authorised){
            $this->loadModel('Dog');
            $likedUsers = $this->Dog->getLikedUsers($dog_id);

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

            $response = REQUEST_OK;
            $data['users'] = $likedUsers;
        } else {
            $response = REQUEST_UNAUTHORISED;
        }
        
        $data['response'] = $response;
        $this->layout = 'blank';
        echo json_encode(compact('data', $data));
        
    }
    
    function getPhotos(){
        if(isset($_REQUEST['user_id'])) $user_id = $_REQUEST['user_id'];
        if(isset($_REQUEST['token'])) $token = $_REQUEST['token'];
        if(isset($_REQUEST['target_id'])) $target_id = $_REQUEST['target_id'];
        if(isset($_REQUEST['type_id'])) $type_id = $_REQUEST['type_id'];
        
        //Authorise user
        $this->loadModel('User');
        $authorised = $this->User->authorise($user_id,$token);
        if($authorised){
        
            $this->loadModel('Photo');
            
            if($type_id == USER_PHOTO_TYPE){
                $photos = $this->Photo->getUserPhotos($target_id);
            } else if($type_id == PLACE_PHOTO_TYPE){
                $photos = $this->Photo->getPlacePhotos($target_id);
            }
            
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

            $response = REQUEST_OK;
            $data['photos'] = $photos;
        } else {
            $response = REQUEST_UNAUTHORISED;
        }
        
        $data['response'] = $response;
        
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
        
        //BEFORE we save this activity, we need to check how long ago was the last one
        $this->loadModel('Dog');
        $dog_ids = $this->Dog->getUserDogIDs($user_id);
        $this->loadModel('ActivityDog');
        $this->loadModel('UserBadge');
        $this->loadModel('UserNotification');
        
        $lastActivityDays = $this->ActivityDog->getDaysSinceLastActivity($dog_ids);
        $this->log("API->saveActivity() lastActivityDays = $lastActivityDays for user $user_id" , LOG_DEBUG);
        
        if($lastActivityDays >= 21 && !$this->UserBadge->userHasBadge($user_id, BADGE_LAZY)){
            //Award badge and notification
            if($this->UserBadge->awardBadge($user_id, BADGE_LAZY)){
                $this->UserNotification->create();
                $obj2['UserNotification']['user_from'] = $user_id;
                $obj2['UserNotification']['user_id'] = $user_id;
                $obj2['UserNotification']['badge_id'] = BADGE_LAZY;
                $obj2['UserNotification']['type_id'] = NOTIFICATION_AWARD_BADGE;

                if($this->UserNotification->save($obj2)){
                    $response = REQUEST_OK;
                }
            }
        }
        
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
        $obj['Activity']['start_lat'] = $coordinates[0]['lat'];
        $obj['Activity']['start_lon'] = $coordinates[0]['lon'];
        
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
                
                if(is_array($dogs)){
                    foreach($dogs as $key => $val){
                        $this->ActivityDog->create();
                        $obj3['ActivityDog']['activity_id'] = $activity_id;
                        $obj3['ActivityDog']['dog_id'] = $dogs[$key]['dog_id'];
                        $obj3['ActivityDog']['walk_distance'] = $dogs[$key]['walk_distance'];
                        $obj3['ActivityDog']['playtime'] = $dogs[$key]['playtime'];
                        $obj3['ActivityDog']['dogfuel'] = $dogs[$key]['dogfuel'];
                        
                        //Add the dog id to a temp array for badge checking later on
                        $dog_ids[] = $dogs[$key]['dog_id'];
                        
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
        
        //Badge handling
        if($response == REQUEST_OK){
           
            if(!$this->UserBadge->userHasBadge($user_id, BADGE_CROSSFIT)){
                //$activity_counts2Weeks = $this->ActivityDog->getMaxActivityCounts(14, $dog_ids, 100);
                $activity_counts2Weeks = $this->ActivityDog->getMaxDogfuelInPeriod(14, $dog_ids);
                $targetValue = 600;
                if($activity_counts2Weeks >= $targetValue){
                    //Award badge and notification
                    if($this->UserBadge->awardBadge($user_id, BADGE_CROSSFIT)){
                        $this->UserNotification->create();
                        $obj2['UserNotification']['user_from'] = $user_id;
                        $obj2['UserNotification']['user_id'] = $user_id;
                        $obj2['UserNotification']['badge_id'] = BADGE_CROSSFIT;
                        $obj2['UserNotification']['type_id'] = NOTIFICATION_AWARD_BADGE;

                        if($this->UserNotification->save($obj2)){
                            $response = REQUEST_OK;
                        }
                    }
                }
            } 
            
            $hasBadgeAthletic = $this->UserBadge->userHasBadge($user_id, BADGE_ATHLETIC);
            $hasBadgeOlympian = $this->UserBadge->userHasBadge($user_id, BADGE_OLYMPIAN);
            
            if(!$hasBadgeAthletic || !$hasBadgeOlympian){
                //$activity_counts1Month = $this->ActivityDog->getMaxActivityCounts(30, $dog_ids, 100);
                $activity_counts1Month = $this->ActivityDog->getMaxDogfuelInPeriod(30, $dog_ids);
                $targetValue = 1000;
                
                //Athletic badge
                if($activity_counts1Month >= $targetValue && !$hasBadgeAthletic){
                    //Award badge and notification
                    if($this->UserBadge->awardBadge($user_id, BADGE_ATHLETIC)){
                        $this->UserNotification->create();
                        $obj2['UserNotification']['user_from'] = $user_id;
                        $obj2['UserNotification']['user_id'] = $user_id;
                        $obj2['UserNotification']['badge_id'] = BADGE_ATHLETIC;
                        $obj2['UserNotification']['type_id'] = NOTIFICATION_AWARD_BADGE;

                        if($this->UserNotification->save($obj2)){
                            $response = REQUEST_OK;
                        }
                    }
                }
                
                //Olympian badge
                if($activity_counts1Month >= 1200 && !$hasBadgeOlympian){
                    //Award badge and notification
                    if($this->UserBadge->awardBadge($user_id, BADGE_OLYMPIAN)){
                        $this->UserNotification->create();
                        $obj2['UserNotification']['user_from'] = $user_id;
                        $obj2['UserNotification']['user_id'] = $user_id;
                        $obj2['UserNotification']['badge_id'] = BADGE_OLYMPIAN;
                        $obj2['UserNotification']['type_id'] = NOTIFICATION_AWARD_BADGE;

                        if($this->UserNotification->save($obj2)){
                            $response = REQUEST_OK;
                        }
                    }
                }
            }
        }

        $this->log("API->saveActivity() returns activity id $activity_id ", LOG_DEBUG);
        
        $data['activity_id'] = $activity_id;
        $data['response'] = $response;  
        $this->layout = 'blank';
        echo json_encode(compact('data', $data));
    }
    
    //Returns the data required for the current user's profile
    function getUser(){
        if(isset($_REQUEST['user_id'])) $user_id = $_REQUEST['user_id'];
        if(isset($_REQUEST['token'])) $token = $_REQUEST['token'];
        
        //Authorise user
        $this->loadModel('User');
        $authorised = $this->User->authorise($user_id,$token);
        if($authorised){
        
            //user data
            $otherUser = $this->User->getOtherUserById($user_id, $user_id);

            //activities list
            $this->loadModel('Activity');
            $activities = $this->Activity->getActivityList($user_id);

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

            $response = REQUEST_OK;
            $data['user'] = $otherUser;
            $data['activities'] = $activities;
        } else {
            $response = REQUEST_UNAUTHORISED;
        }
        
        $data['response'] = $response;
        $this->layout = 'blank';
        echo json_encode(compact('data', $data));
    }
    
    function getBadges(){
        if(isset($_REQUEST['user_id'])) $user_id = $_REQUEST['user_id'];
        if(isset($_REQUEST['target_id'])) $target_id = $_REQUEST['target_id'];
        if(isset($_REQUEST['token'])) $token = $_REQUEST['token'];
        
        //Authorise user
        $this->loadModel('User');
        $authorised = $this->User->authorise($user_id,$token);
        if($authorised){
        
            $this->loadModel('UserBadge');
            $badges = $this->UserBadge->getUserBadges($target_id);
            $data['badges'] = $badges;
            
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
            
            $response = REQUEST_OK;
        }else{
            $response = REQUEST_UNAUTHORISED;
        }
        
        $data['response'] = $response;
        $this->layout = 'blank';
        echo json_encode(compact('data', $data));
    }
    
    //Returns the newsfeed for the specified user
    function getFeed(){
        if(isset($_REQUEST['user_id'])) $user_id = $_REQUEST['user_id'];
        if(isset($_REQUEST['token'])) $token = $_REQUEST['token'];
        
        //Authorise user
        $this->loadModel('User');
        $authorised = $this->User->authorise($user_id,$token);
        if($authorised){
        
            $this->loadModel('Feed');
            $feed = $this->Feed->getFeed($user_id);
            $response = REQUEST_OK;

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
        } else {
            $response = REQUEST_UNAUTHORISED;
        }
        
        $data['response'] = $response;  
        
        $this->layout = 'blank';
        echo json_encode(compact('data', $data));
    }
    
    function getDog(){
        if(isset($_REQUEST['dog_id'])) $dog_id = $_REQUEST['dog_id'];
        if(isset($_REQUEST['user_id'])) $user_id = $_REQUEST['user_id'];
        if(isset($_REQUEST['token'])) $token = $_REQUEST['token'];
        if(isset($_REQUEST['timezone'])) $timezone = $_REQUEST['timezone'];
        if(isset($_REQUEST['month'])) $month = $_REQUEST['month'];
        if(isset($_REQUEST['day'])) $day = $_REQUEST['day'];
        if(isset($_REQUEST['year'])) $year = $_REQUEST['year'];
        
        //Authorise user
        $this->loadModel('User');
        $authorised = $this->User->authorise($user_id,$token);
        if($authorised){
        
            $this->loadModel('Dog');
            $this->loadModel('DogLike');
            $dog = $this->Dog->getDogById($dog_id);
            
            $fromDate = "$year-$month-$day 00:00:01";
            $toDate = "$year-$month-$day 23:59:59";
            $dog['dogfuel'] = $this->Dog->getLatestDogfuel($dog_id, $fromDate, $toDate, $timezone);
            $dog['liked'] = $this->DogLike->userLikesDog($user_id, $dog_id);
            
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

            $response = REQUEST_OK;
            $data['dog'] = $dog;
        } else {
            $response = REQUEST_UNAUTHORISED;
        }
        
        $data['response'] = $response;
        $this->layout = 'blank';
        echo json_encode(compact('data', $data));
    }
    
    function getPlace(){
        if(isset($_REQUEST['place_id'])) $place_id = $_REQUEST['place_id'];
        if(isset($_REQUEST['user_id'])) $user_id = $_REQUEST['user_id'];
        if(isset($_REQUEST['token'])) $token = $_REQUEST['token'];
        
        //Authorise user
        $this->loadModel('User');
        $authorised = $this->User->authorise($user_id,$token);
        if($authorised){
        
            $this->loadModel('Place');
            $place = $this->Place->getPlaceById($place_id, $user_id);
            $comments = $this->Place->getPlaceComments($place_id);
            $likes = $this->Place->getPlaceLikes($place_id);
            $checkins = $this->Place->getPlaceCheckins($place_id);
            
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

            $response = REQUEST_OK;
            $data['place'] = $place;
            $data['comments'] = $comments;
            $data['likes'] = $likes;
            $data['checkins'] = $checkins;
        } else {
            $response = REQUEST_UNAUTHORISED;
        }
        
        $data['response'] = $response;
        
        $this->layout = 'blank';
        echo json_encode(compact('data', $data));
    }
    
    //Returns all the required data for the specified user $target_id
    function getOtherUser(){
        if(isset($_REQUEST['user_id'])) $user_id = $_REQUEST['user_id'];
        if(isset($_REQUEST['target_id'])) $target_id = $_REQUEST['target_id'];
        if(isset($_REQUEST['token'])) $token = $_REQUEST['token'];
        if(isset($_REQUEST['timezone'])) $timezone = $_REQUEST['timezone'];
        if(isset($_REQUEST['month'])) $month = $_REQUEST['month'];
        if(isset($_REQUEST['day'])) $day = $_REQUEST['day'];
        if(isset($_REQUEST['year'])) $year = $_REQUEST['year'];
        
        //Authorise user
        $this->loadModel('User');
        $authorised = $this->User->authorise($user_id,$token);
        if($authorised){
        
            $otherUser = $this->User->getOtherUserById($user_id, $target_id);

            $this->loadModel('Dog');
            $fromDate = "$year-$month-$day 00:00:01";
            $toDate = "$year-$month-$day 23:59:59";
            $dogs = $this->Dog->getUserDogs($target_id, $fromDate, $toDate, $timezone);
            
            $this->loadModel('UserBadge');
            $badgeCount = $this->UserBadge->countUserBadges($target_id);

            //activities list
            $this->loadModel('Activity');
            $activities = $this->Activity->getActivityList($target_id);

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
            
            //Check for lost dogs from this user
            $mutual_follower = true;
            $hasLostDog = $this->User->hasLostDog($target_id);
            if($hasLostDog){
                $mutual_follower = true;
            } else {
                //Check if mutual followrs
                $mutual_follower = $this->UserFollows->isMutualFollower($user_id, $target_id);
            }
            
            $data['mutual_follower'] = $mutual_follower;

            $response = REQUEST_OK;
            $data['user'] = $otherUser;
            $data['badge_count'] = $badgeCount;
            $data['dogs'] = $dogs;
            $data['activities'] = $activities;
        } else {
            $response = REQUEST_UNAUTHORISED;
        }
        
        $data['response'] = $response;
        
        $this->layout = 'blank';
        echo json_encode(compact('data', $data));
    }
    
    //Sends a message from $user_id to $target_id
    function sendMessage(){
        if(isset($_REQUEST['user_id'])) $user_id = $_REQUEST['user_id'];
        if(isset($_REQUEST['target_id'])) $target_id = $_REQUEST['target_id'];
        if(isset($_REQUEST['message'])) $message = $_REQUEST['message'];
        if(isset($_REQUEST['token'])) $token = $_REQUEST['token'];
        
        $this->log("API->sendMessage() called with sender $user_id to user $target_id with message $message", LOG_DEBUG);
        
        //Authorise user
        $this->loadModel('User');
        $authorised = $this->User->authorise($user_id,$token);
        if($authorised){
        
            $this->loadModel('UserInbox');
            $obj = array();
            $obj['UserInbox']['user_from'] = $user_id;
            $obj['UserInbox']['user_to'] = $target_id;
            $obj['UserInbox']['message'] = $message;

            $message_id = null;
            if($this->UserInbox->save($obj)){
                $response = REQUEST_OK;
                $message_id = $this->UserInbox->getLastInsertID();
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

            $this->log("API->sendMessage() returns message_id $message_id", LOG_DEBUG);

            $response = REQUEST_OK;
            $data['message_id'] = $message_id;
            
        } else {
            $response = REQUEST_UNAUTHORISED;
        }
        
        $data['response'] = $response;
        
        $this->layout = 'blank';
        echo json_encode(compact('data', $data));
    }
    
    //Gets all unread messages
    function getMessages(){
        if(isset($_REQUEST['user_id'])) $user_id = $_REQUEST['user_id'];
        if(isset($_REQUEST['token'])) $token = $_REQUEST['token'];
        
        //Authorise user
        $this->loadModel('User');
        $authorised = $this->User->authorise($user_id,$token);
        if($authorised){
        
            $this->loadModel('UserInbox');
            $messages = $this->UserInbox->getUnreadMessages($user_id);

            //Count unread notifications
            $this->loadModel('UserNotification');
            $count_notifications = $this->UserNotification->countUnreadNotifications($user_id);
            $data['count_notifications'] = $count_notifications;

            //Count followers
            $this->loadModel('UserFollows');
            $count_followers = $this->UserFollows->countFollowers($user_id);
            $data['count_followers'] = $count_followers;

            //Count inbox
            $count_inbox = $this->UserInbox->countUnreadMessages($user_id);
            $data['count_inbox'] = $count_inbox;

            $response = REQUEST_OK;
            $data['messages'] = $messages;
        } else {
            $response = REQUEST_UNAUTHORISED;
        }
        
        $data['response'] = $response;
        
        $this->layout = 'blank';
        echo json_encode(compact('data', $data));
    }
    
    //sets messages to read
    function setMessagesRead(){
        if(isset($_REQUEST['user_id'])) $user_id = $_REQUEST['user_id'];
        if(isset($_REQUEST['list'])) $list = $_REQUEST['list'];
        if(isset($_REQUEST['token'])) $token = $_REQUEST['token'];
        
        //Authorise user
        $this->loadModel('User');
        $authorised = $this->User->authorise($user_id,$token);
        if($authorised){
        
            $list = json_decode(urldecode($list));

            if(!empty($list)){

                $listToString = implode(",", $list);

                $this->log("API->setMessagesRead() uses stringList: $listToString", LOG_DEBUG);

                $this->loadModel('UserInbox');
                $return = $this->UserInbox->setMessagesToRead($listToString);
                $this->log("API->setMessagesRead() affected rows are $return", LOG_DEBUG);
                if($return > 0){
                    $response = REQUEST_OK;
                }else{
                    $response = REQUEST_FAILED;
                }
            }else {
                $response = REQUEST_OK;
            }

            //Count unread notifications
            $this->loadModel('UserNotification');
            $count_notifications = $this->UserNotification->countUnreadNotifications($user_id);
            $data['count_notifications'] = $count_notifications;

            //Count followers
            $this->loadModel('UserFollows');
            $count_followers = $this->UserFollows->countFollowers($user_id);
            $data['count_followers'] = $count_followers;

            if($response == REQUEST_OK){
                //Count inbox
                $count_inbox = $this->UserInbox->countUnreadMessages($user_id);
                $data['count_inbox'] = $count_inbox;
            }
        } else {
            $response = REQUEST_UNAUTHORISED;
        }
        
        $data['response'] = $response;
        
        $this->layout = 'blank';
        echo json_encode(compact('data', $data));
    }
    
    //sets notifications to read
    function setNotificationsRead(){
        if(isset($_REQUEST['user_id'])) $user_id = $_REQUEST['user_id'];
        if(isset($_REQUEST['list'])) $list = $_REQUEST['list'];
        if(isset($_REQUEST['token'])) $token = $_REQUEST['token'];
        
        //Authorise user
        $this->loadModel('User');
        $authorised = $this->User->authorise($user_id,$token);
        if($authorised){
        
            $this->log("API->setNotificationRead() sets to read notifications with id: $list", LOG_DEBUG);

            $list = json_decode(urldecode($list));

            if(!empty($list)){

                $listToString = implode(",", $list);

                $this->log("API->setNotificationRead() uses stringList: $listToString", LOG_DEBUG);

                $this->loadModel('UserNotification');
                $return = $this->UserNotification->setNotificationsToRead($listToString);
                $this->log("API->setNotificationsRead() affected rows are $return", LOG_DEBUG);
                if($return > 0){
                    $response = REQUEST_OK;
                }else{
                    $response = REQUEST_FAILED;
                }
            } else {
                $response = REQUEST_OK;
            }

            if($response == REQUEST_OK){
                //Count unread notifications
                $count_notifications = $this->UserNotification->countUnreadNotifications($user_id);
                $data['count_notifications'] = $count_notifications;
            }

            //Count followers
            $this->loadModel('UserFollows');
            $count_followers = $this->UserFollows->countFollowers($user_id);
            $data['count_followers'] = $count_followers;

            //Count inbox
            $this->loadModel('UserInbox');
            $count_inbox = $this->UserInbox->countUnreadMessages($user_id);
            $data['count_inbox'] = $count_inbox;

        } else {
            $response = REQUEST_UNAUTHORISED;
        }
        
        $data['response'] = $response;
        
        $this->layout = 'blank';
        echo json_encode(compact('data', $data));
    }
    
    //Redirects to the requested user profile image (thumb or normal)
    function photo(){
        $thumb = 1;
        
        if(isset($_REQUEST['user_id'])) $user_id = $_REQUEST['user_id'];
        if(isset($_REQUEST['thumb'])) $thumb = $_REQUEST['thumb'];
        
        $this->loadModel('User');
        $photo = $this->User->getProfilePhoto($user_id);
        
        if($thumb){
            $url = "/uploaded_files/users/".$photo['thumb'];
        } else {
            $url = "/uploaded_files/users/".$photo['photo'];
        }
        
        $this->redirect($url);
    }
    
    //Redirects to the requested dog profile image (thumb or normal)
    function photo_dog(){
        $thumb = 1;
        
        if(isset($_REQUEST['dog_id'])) $dog_id = $_REQUEST['dog_id'];
        if(isset($_REQUEST['thumb'])) $thumb = $_REQUEST['thumb'];
        
        $this->loadModel('Dog');
        $photo = $this->Dog->getProfilePhoto($dog_id);
        
        if($thumb){
            $url = "/uploaded_files/dogs/".$photo['thumb'];
        } else {
            $url = "/uploaded_files/dogs/".$photo['photo'];
        }
        
        $this->redirect($url);
    }
    
    //Searches for users and places
    function search(){
        if(isset($_REQUEST['user_id'])) $user_id = $_REQUEST['user_id'];
        if(isset($_REQUEST['name'])) $name = $_REQUEST['name'];
        if(isset($_REQUEST['token'])) $token = $_REQUEST['token'];
        
        //Authorise user
        $this->loadModel('User');
        $authorised = $this->User->authorise($user_id,$token);
        if($authorised){
        
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
            
            $data['users'] = $userData;
            $data['places'] = $placeData;
            $response = REQUEST_OK;
        } else {
            $response = REQUEST_UNAUTHORISED;
        }
        
        $data['response'] = $response;
        
        $this->layout = 'blank';
        echo json_encode(compact('data', $data));
    }
    
    //Searches for nearby places
    function getPlaces(){
        if(isset($_REQUEST['user_id'])) $user_id = $_REQUEST['user_id'];
        if(isset($_REQUEST['lat'])) $lat = $_REQUEST['lat'];
        if(isset($_REQUEST['lon'])) $lon = $_REQUEST['lon'];
        if(isset($_REQUEST['breed_list'])) $breed_list = $_REQUEST['breed_list'];
        
        //Name is only used when searching from the searchfield
        if(isset($_REQUEST['name'])) {
            $name = $_REQUEST['name'];
        } else {
            $name = null;
        }
        $breed_list = json_decode(urldecode($breed_list));
        
        if(!empty($breed_list)){
            $breedsToString = implode(",", $breed_list);
        }
        
        $category_id = null;
        if(isset($_REQUEST['category_id']) && $_REQUEST['category_id'] != null){
            $category_id = $_REQUEST['category_id'];
        }
        
        $response = null;
        
        $this->loadModel('Place');
        
        //Determine what we are searching for
        if($category_id == MAP_FILTER_RECENTLY_OPEN){
            //Return places from ALL categories that were created in the past X days
            $places = $this->Place->getRecentlyOpenedPlacesNearby($lat, $lon);
            $data['places'] = $places;
            $response = is_array($places) ? REQUEST_OK : REQUEST_FAILED;
        } else if($category_id == MAP_FILTER_MATING){
            //Returns dogs that have the mating flag and have done an activity within 30km from your coordinates. (only compare the starting point of the activity)
            $dogs = $this->Place->getMatingDogsNearby($lat, $lon);
            $data['places'] = $dogs;
            $response = is_array($dogs) ? REQUEST_OK : REQUEST_FAILED;
        } else if($category_id == MAP_FILTER_SAME_BREED){
            //Same as above but instead of the mating flag, we check for the breed - should be same as the breeds of the current user's dogs 
            $dogs = $this->Place->getSameBreedDogsNearby($lat, $lon, $breedsToString);
            $data['places'] = $dogs;
            $response = is_array($dogs) ? REQUEST_OK : REQUEST_FAILED;
        } else {
            $places = $this->Place->getPlacesNearby($name, $lat, $lon, $category_id);
            $data['places'] = $places;
            
            $this->loadModel('UserFollows');
            //$mutual = $this->UserFollows->getMutualFollowersList($user_id);
            
            $mutual = null;
            //if($mutual != null){
                $this->loadModel('PlaceCheckin');
                $checkins = $this->PlaceCheckin->getNearbyCheckins($lat, $lon, $mutual);
                $data['checkins'] = $checkins;
                
                $this->loadModel('Activity');
                $activities = $this->Activity->getNearbyActivities($lat, $lon, $mutual);
                $data['activities'] = $activities;
            //}
            
            $response = is_array($places) ? REQUEST_OK : REQUEST_FAILED;
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
    
    //Performs a checkin
    function checkin(){
        if(isset($_REQUEST['user_id'])) $user_id = $_REQUEST['user_id'];
        if(isset($_REQUEST['place_id'])) $place_id = $_REQUEST['place_id'];
        if(isset($_REQUEST['token'])) $token = $_REQUEST['token'];
        
        $this->log("API->checkin() called for user: $user_id to check on place with id: $place_id", LOG_DEBUG);
        
        //Authorise user
        $this->loadModel('User');
        $authorised = $this->User->authorise($user_id,$token);
        if($authorised){
        
            $checkinID = null;
            $response = null;
            $errorMessage = null;
            $category_id = null;
            
            //Load the requested place
            $this->loadModel('Place');
            $placeObject = $this->Place->findById($place_id);
            if($placeObject != null){
                $category_id = $placeObject['Place']['category_id'];
            }
            
            //Save checkin object
            $this->loadModel('PlaceCheckin');
            $checkin = array();
            $checkin['PlaceCheckin']['user_id'] = $user_id;
            $checkin['PlaceCheckin']['place_id'] = $place_id;

            if($this->PlaceCheckin->save($checkin)){
                $response = REQUEST_OK;
                $checkinID = $this->PlaceCheckin->getLastInsertID();
            } else {
                $response = REQUEST_FAILED;
                $errorMessage = ERROR_CHECKIN_CREATION;
            }

            //Badge handling
            $this->loadModel('UserBadge');
            $this->loadModel('UserNotification');
            
            if(!$this->UserBadge->userHasBadge($user_id, BADGE_101_DALMATIANS)){
                $total_checkins = $this->User->countCheckins($user_id, null);
                $this->log("API->checkin() user $user_id has $total_checkins total checkins", LOG_DEBUG);
                
                //Award badge and notification
                if($total_checkins == 101 && $this->UserBadge->awardBadge($user_id, BADGE_101_DALMATIANS)){
                    $this->UserNotification->create();
                    $obj2['UserNotification']['user_from'] = $user_id;
                    $obj2['UserNotification']['user_id'] = $user_id;
                    $obj2['UserNotification']['badge_id'] = BADGE_101_DALMATIANS;
                    $obj2['UserNotification']['type_id'] = NOTIFICATION_AWARD_BADGE;

                    if($this->UserNotification->save($obj2)){
                        $response = REQUEST_OK;
                    }
                }
            }
            
            if($category_id == PLACE_CATEGORY_OTHER_PLACE && !$this->UserBadge->userHasBadge($user_id, BADGE_VIP)){
                //$vipCheckins = $this->User->countCheckins($user_id, PLACE_CATEGORY_OTHER_PLACE);
                //$this->log("API->checkin() user $user_id has $vipCheckins checkins of type other place", LOG_DEBUG);
                
                //Award badge and notification
                if($this->UserBadge->awardBadge($user_id, BADGE_VIP)){
                    $this->UserNotification->create();
                    $obj2['UserNotification']['user_from'] = $user_id;
                    $obj2['UserNotification']['user_id'] = $user_id;
                    $obj2['UserNotification']['badge_id'] = BADGE_VIP;
                    $obj2['UserNotification']['type_id'] = NOTIFICATION_AWARD_BADGE;

                    if($this->UserNotification->save($obj2)){
                        $response = REQUEST_OK;
                    }
                }
            } else if($category_id == PLACE_CATEGORY_BEACH && !$this->UserBadge->userHasBadge($user_id, BADGE_SWIMMIE)){
                //$vipCheckins = $this->User->countCheckins($user_id, PLACE_CATEGORY_OTHER_PLACE);
                //$this->log("API->checkin() user $user_id has $vipCheckins checkins of type other place", LOG_DEBUG);
                
                //Award badge and notification
                if($this->UserBadge->awardBadge($user_id, BADGE_SWIMMIE)){
                    $this->UserNotification->create();
                    $obj2['UserNotification']['user_from'] = $user_id;
                    $obj2['UserNotification']['user_id'] = $user_id;
                    $obj2['UserNotification']['badge_id'] = BADGE_SWIMMIE;
                    $obj2['UserNotification']['type_id'] = NOTIFICATION_AWARD_BADGE;

                    if($this->UserNotification->save($obj2)){
                        $response = REQUEST_OK;
                    }
                }
            } else if($category_id == PLACE_CATEGORY_WORKPLACE && !$this->UserBadge->userHasBadge($user_id, BADGE_WORKIE)){
                //$vipCheckins = $this->User->countCheckins($user_id, PLACE_CATEGORY_OTHER_PLACE);
                //$this->log("API->checkin() user $user_id has $vipCheckins checkins of type other place", LOG_DEBUG);
                
                //Award badge and notification
                if($this->UserBadge->awardBadge($user_id, BADGE_WORKIE)){
                    $this->UserNotification->create();
                    $obj2['UserNotification']['user_from'] = $user_id;
                    $obj2['UserNotification']['user_id'] = $user_id;
                    $obj2['UserNotification']['badge_id'] = BADGE_WORKIE;
                    $obj2['UserNotification']['type_id'] = NOTIFICATION_AWARD_BADGE;

                    if($this->UserNotification->save($obj2)){
                        $response = REQUEST_OK;
                    }
                }
            }
            
            //End badge handling
            
            //Feed entry
            if($response == REQUEST_OK){
                $this->loadModel('Feed');

                $userObject = $this->User->findById($user_id);
                $user_name = $userObject['User']['name'];
                $place_name = $placeObject['Place']['name'];
                $place_id = $placeObject['Place']['id'];
                
                $feed['Feed']['user_from'] = $user_id;
                $feed['Feed']['user_from_name'] = $user_name;
                $feed['Feed']['target_place_id'] = $place_id;
                $feed['Feed']['target_place_name'] = $place_name;
                $feed['Feed']['type_id'] = FEED_CHECKIN;

                $feedOK = $this->Feed->save($feed);

                if(!$feedOK){
                    $this->log("API->checkin() error creating feed", LOG_DEBUG);
                    $response = ERROR_FEED_CREATION;
                } else {
                    $this->log("API->checkin() saved feed ", LOG_DEBUG);
                }
            }
            
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

            $data['checkin_id'] = $checkinID;
            $data['error'] = $errorMessage;
            
        } else {
            $response = REQUEST_UNAUTHORISED;
        }
        
        $data['response'] = $response;
        
        $this->layout = 'blank';
        echo json_encode(compact('data', $data));
        
    }
    
    //Sets the user's current location
    function saveLocation(){
        
    }
    
    //Sets a like for an activity
    function likeActivity(){
        if(isset($_REQUEST['user_id'])) $user_id = $_REQUEST['user_id'];
        if(isset($_REQUEST['activity_id'])) $activity_id = $_REQUEST['activity_id'];
        if(isset($_REQUEST['token'])) $token = $_REQUEST['token'];
        
        //Authorise user
        $this->loadModel('User');
        $authorised = $this->User->authorise($user_id,$token);
        if($authorised){
        
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
                
                //Feed entry
                if($response == REQUEST_OK){
                    $this->loadModel('Feed');

                    $userObject = $this->User->findById($user_id);
                    $user_name = $userObject['User']['name'];
                    $target_user_id = $activity_obj['Activity']['user_id'];
                    $targetUserObject = $this->User->findById($target_user_id);
                    $target_user_name = $targetUserObject['User']['name'];

                    $feed['Feed']['user_from'] = $user_id;
                    $feed['Feed']['user_from_name'] = $user_name;
                    $feed['Feed']['target_user_id'] = $target_user_id;
                    $feed['Feed']['target_user_name'] = $target_user_name;
                    $feed['Feed']['type_id'] = FEED_FRIEND_LIKE_ACTIVITY;

                    $feedOK = $this->Feed->save($feed);

                    if(!$feedOK){
                        $this->log("API->likeActivity() error creating feed", LOG_DEBUG);
                        $response = ERROR_FEED_CREATION;
                    } else {
                        $this->log("API->likeActivity() saved feed ", LOG_DEBUG);
                    }
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
        } else {
            $response = REQUEST_UNAUTHORISED;
        }
        
        $data['response'] = $response;
        
        $this->layout = 'blank';
        echo json_encode(compact('data', $data));
    }
    
    //Deletes a like for an activity
    function unlikeActivity(){
        if(isset($_REQUEST['user_id'])) $user_id = $_REQUEST['user_id'];
        if(isset($_REQUEST['activity_id'])) $activity_id = $_REQUEST['activity_id'];
        if(isset($_REQUEST['token'])) $token = $_REQUEST['token'];
        
        //Authorise user
        $this->loadModel('User');
        $authorised = $this->User->authorise($user_id,$token);
        if($authorised){
        
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
        } else {
            $response = REQUEST_UNAUTHORISED;
        }
        
        $data['response'] = $response;
        
        $this->layout = 'blank';
        echo json_encode(compact('data', $data));
    }
    
    //Sets a like for a place
    function likePlace(){
        if(isset($_REQUEST['user_id'])) $user_id = $_REQUEST['user_id'];
        if(isset($_REQUEST['place_id'])) $place_id = $_REQUEST['place_id'];
        if(isset($_REQUEST['token'])) $token = $_REQUEST['token'];
        
        //Authorise user
        $this->loadModel('User');
        $authorised = $this->User->authorise($user_id,$token);
        if($authorised){
        
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
        } else {
            $response = REQUEST_UNAUTHORISED;
        }
        
        $data['response'] = $response;
        
        $this->layout = 'blank';
        echo json_encode(compact('data', $data));
    }
    
    //Deletes a like for a place
    function unlikePlace(){
        if(isset($_REQUEST['user_id'])) $user_id = $_REQUEST['user_id'];
        if(isset($_REQUEST['place_id'])) $place_id = $_REQUEST['place_id'];
        if(isset($_REQUEST['token'])) $token = $_REQUEST['token'];
        
        //Authorise user
        $this->loadModel('User');
        $authorised = $this->User->authorise($user_id,$token);
        
        if($authorised){
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
        } else {
            $response = REQUEST_UNAUTHORISED;
        }
        
        $data['response'] = $response;
        
        $this->layout = 'blank';
        echo json_encode(compact('data', $data));
    }
    
    //Sets a like for a dog
    function likeDog(){
        if(isset($_REQUEST['user_id'])) $user_id = $_REQUEST['user_id'];
        if(isset($_REQUEST['dog_id'])) $dog_id = $_REQUEST['dog_id'];
        if(isset($_REQUEST['token'])) $token = $_REQUEST['token'];
        
        //Authorise user
        $this->loadModel('User');
        $authorised = $this->User->authorise($user_id,$token);
        if($authorised){
        
        
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
   
            //Badge handling
            if($response == REQUEST_OK){
                $this->loadModel('UserBadge');
                $this->loadModel('UserNotification');
                $dogLikes = $this->DogLike->countOtherUserLikes($dog_id);
                
                if($dogLikes == 20 && !$this->UserBadge->userHasBadge($user_id, BADGE_SUPERSTAR)){
                    //Award badge and notification
                    if($this->UserBadge->awardBadge($userID, BADGE_SUPERSTAR)){
                        $obj2['UserNotification']['user_from'] = $user_id;
                        $obj2['UserNotification']['user_id'] = $user_id;
                        $obj2['UserNotification']['badge_id'] = BADGE_SUPERSTAR;
                        $obj2['UserNotification']['type_id'] = NOTIFICATION_AWARD_BADGE;

                        if($this->UserNotification->save($obj2)){
                            $response = REQUEST_OK;
                        }
                    }
                }
            }

            //Load additional data with this request
            if($response == REQUEST_OK){

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
        } else {
            $response = REQUEST_UNAUTHORISED;
        }
        
        $data['response'] = $response;
        
        $this->layout = 'blank';
        echo json_encode(compact('data', $data));
    }
    
    //Deletes a like for a dog
    function unlikeDog(){
        if(isset($_REQUEST['user_id'])) $user_id = $_REQUEST['user_id'];
        if(isset($_REQUEST['dog_id'])) $dog_id = $_REQUEST['dog_id'];
        if(isset($_REQUEST['token'])) $token = $_REQUEST['token'];
        
        //Authorise user
        $this->loadModel('User');
        $authorised = $this->User->authorise($user_id,$token);
        
        if($authorised){
        
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
        } else {
            $response = REQUEST_UNAUTHORISED;
        }
        
        $data['response'] = $response;
        
        $this->layout = 'blank';
        echo json_encode(compact('data', $data));
    }
}

?>
