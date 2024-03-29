<?php

//App::uses('Core', 'l10n', 'Sanitize');

class UsersController extends AppController {
    var $components = array('Cookie', 'RequestHandler');
    var $helpers = array('Js','Time');
    
    /*Executed before all functions*/
    function beforeFilter() {

        parent::beforeFilter();
	$this->set('headerTitle', "User Management");
	$this->set('activeTab', "users");
    }

    function index(){
        $currentUser = $this->Session->read('userID');
	if($currentUser != null){

	} else {
            $this->requireLogin('/Users/index');
	}
    }

    function search(){
        $currentUser = $this->Session->read('userID');
	if($currentUser != null){
            $this->set('headerTitle', "Search users");
            
            $this->loadModel('Country');
            $countries = $this->Country->find('list');
            $this->set('countries', $countries);
            
            if (!empty($this->request->data)){
                $name = $this->request->data['User']['name'];
		$email = $this->request->data['User']['email'];
                $status = $this->request->data['User']['active'];
                $country_id = $this->request->data['User']['country_id'];
                $registrationFrom = $this->request->data['User']['created_from'];
                $registrationTo = $this->request->data['User']['created_to'];

		$data = $this->User->websearch($name,$email,$status,$country_id,$registrationFrom,$registrationTo);
		$this->set('results', $data);
            }

	} else {
            $this->requireLogin('/Users/search');
	}
    }
    
    function viewPhotos($id){
        $currentUser = $this->Session->read('userID');
	if($currentUser != null){
            $this->loadModel('Photo');
            $photoList = $this->Photo->getGalleryUserPhotos($id);
            $this->set('photoList', $photoList);
            
            $userObj = $this->User->findById($id);        
            $this->set('user', $userObj);
            $this->set('user_id', $id);
            $this->set('headerTitle', $userObj['User']['name']);
            
            //count stuff for the menu
            $this->loadModel('UserFollows');
            $this->loadModel('Dog');
            $this->loadModel('Activity');
            $this->loadModel('ActivityComment');
            $this->loadModel('PlaceComment');
            $this->loadModel('PlaceLike');
            $this->loadModel('ActivityLike');
            $this->loadModel('Photo');
            $this->loadModel('PlaceCheckin');
            $follow_stats = $this->UserFollows->getFollowStats($id);
            $placeComments = $this->PlaceComment->countCommentsForUser($id);
            $placeLikes = $this->PlaceLike->countLikesForUser($id);
            $activityComments = $this->ActivityComment->countCommentsForUser($id);
            $activityLikes = $this->ActivityLike->countLikesForUser($id);
            $userPhotos = $this->Photo->countGalleryPhotosByUser($id);
            $userCheckins = $this->PlaceCheckin->countUserCheckins($id);
            
            $followers = $follow_stats['followers'];
            $following = $follow_stats['following'];
            $dogs = $this->Dog->countUserDogs($id);
            $activities = $this->Activity->countActivitiesForUser($id);
            $comments = $placeComments + $activityComments;
            $likes = $placeLikes + $activityLikes;
            $photos = $userPhotos;
            
            $this->set('followers', $followers);
            $this->set('following', $following);
            $this->set('dogs', $dogs);
            $this->set('activities', $activities);
            $this->set('comments', $comments);
            $this->set('likes', $likes);
            $this->set('photos', $photos);
            $this->set('checkins', $userCheckins);
            
	} else {
            $this->requireLogin("/Users/viewPhotos/$id");
	}
    }

    /*Creates a new user*/
    function create(){
	$currentUser = $this->Session->read('userID');
	if($currentUser != null){
            $this->set('headerTitle', "Create user");
            
            $this->loadModel('Roles');  
            $roles = $this->Roles->find("list");
            $this->set("roles", $roles);
            
            if (!empty($this->request->data)){
                
                $error = false;
                $this->loadModel('UserRole');
                $this->request->data['User']['password'] = Security::hash($this->request->data['User']['password'], 'md5');
                
		if($this->User->save($this->request->data)){
                    
                    $newUserId = $this->User->getLastInsertId();
                    foreach($this->data['UserRole']['role_id'] as $z){
                        $this->UserRole->create();
                        $obj = array();
                        $obj['UserRole']['user_id'] = $newUserId;
                        $obj['UserRole']['role_id'] = $z;
                        
                        if($this->UserRole->save($obj)){
                            
                        } else {
                            $error = true;
                            break;
                        }
                    }
                    
                    if(!$error){
                        $this->set('notification', 'New user successfully created.');
                        
                    } else {
                        $this->set('error', 'Unable to create the new user - please try again.');
                    }
                    
		} else {
                    $this->set('notification', 'Unable to create the new user - please try again.');
		}
            }

	} else {
            $this->requireLogin('/Users/create');
	}
    }
    
    /*AJAX validator for user email validations*/
    function validateEmail(){
        if(isset($_REQUEST['email'])) $email = $_REQUEST['email'];
        $this->log("Users->validateEmail() called for $email", LOG_DEBUG);
        
        if(!empty($email)){
            $dd = $this->User->findByEmail($email);
            if($dd != null && isset($dd['User']['email'])){
                $data['data[User][email]'] = "Email address already in use";

            } else {
                $data = true;
            }
        }
        
        $this->layout = 'blank';
        echo json_encode(compact('data', $data));
    }

    function edit($id){
        $currentUser = $this->Session->read('userID');
	if($currentUser != null){
            $this->set('headerTitle', "Edit user");
            $this->set('targetUserId', $id);
            
            $this->loadModel('Roles');  
            $roles = $this->Roles->find("list");
            $this->set("roles", $roles);

            if (!empty($this->request->data)){
                if($this->User->save($this->request->data)){
                    $this->set('notification', 'User details updated successfully.');
		} else {
                    $this->set('errorMsg', 'Unable to update the new details - please try again.');
		}
            }

            $userObj = $this->User->findById($id);        
            $this->set('user', $userObj);
            $this->set('user_id', $id);
            
            //count stuff for the menu
            $this->loadModel('UserFollows');
            $this->loadModel('Dog');
            $this->loadModel('Activity');
            $this->loadModel('ActivityComment');
            $this->loadModel('PlaceComment');
            $this->loadModel('PlaceLike');
            $this->loadModel('ActivityLike');
            $this->loadModel('Photo');
            $this->loadModel('PlaceCheckin');
            $follow_stats = $this->UserFollows->getFollowStats($id);
            $placeComments = $this->PlaceComment->countCommentsForUser($id);
            $placeLikes = $this->PlaceLike->countLikesForUser($id);
            $activityComments = $this->ActivityComment->countCommentsForUser($id);
            $activityLikes = $this->ActivityLike->countLikesForUser($id);
            $userPhotos = $this->Photo->countGalleryPhotosByUser($id);
            $userCheckins = $this->PlaceCheckin->countUserCheckins($id);
            
            $followers = $follow_stats['followers'];
            $following = $follow_stats['following'];
            $dogs = $this->Dog->countUserDogs($id);
            $activities = $this->Activity->countActivitiesForUser($id);
            $comments = $placeComments + $activityComments;
            $likes = $placeLikes + $activityLikes;
            $photos = $userPhotos;
            
            $this->set('followers', $followers);
            $this->set('following', $following);
            $this->set('dogs', $dogs);
            $this->set('activities', $activities);
            $this->set('comments', $comments);
            $this->set('likes', $likes);
            $this->set('photos', $photos);
            $this->set('checkins', $userCheckins);

	} else {
            $this->requireLogin("/Users/edit/$id");
	}
    }
    
    //Returns the list of followers of the specified user
    function viewFollowers($id){
        $currentUser = $this->Session->read('userID');
	if($currentUser != null){
            $user = $this->User->findById($id);
            $this->set('user', $user);
            $this->set('user_id', $id);
            $this->set('headerTitle', $user['User']['name']);
            
            //count stuff for the menu
            $this->loadModel('UserFollows');
            $this->loadModel('Dog');
            $this->loadModel('Activity');
            $this->loadModel('ActivityComment');
            $this->loadModel('PlaceComment');
            $this->loadModel('PlaceLike');
            $this->loadModel('ActivityLike');
            $this->loadModel('Photo');
            $this->loadModel('PlaceCheckin');
            $follow_stats = $this->UserFollows->getFollowStats($id);
            $placeComments = $this->PlaceComment->countCommentsForUser($id);
            $placeLikes = $this->PlaceLike->countLikesForUser($id);
            $activityComments = $this->ActivityComment->countCommentsForUser($id);
            $activityLikes = $this->ActivityLike->countLikesForUser($id);
            $userPhotos = $this->Photo->countGalleryPhotosByUser($id);
            $userCheckins = $this->PlaceCheckin->countUserCheckins($id);
            
            $followers = $follow_stats['followers'];
            $following = $follow_stats['following'];
            $dogs = $this->Dog->countUserDogs($id);
            $activities = $this->Activity->countActivitiesForUser($id);
            $comments = $placeComments + $activityComments;
            $likes = $placeLikes + $activityLikes;
            $photos = $userPhotos;
            
            $this->set('followers', $followers);
            $this->set('following', $following);
            $this->set('dogs', $dogs);
            $this->set('activities', $activities);
            $this->set('comments', $comments);
            $this->set('likes', $likes);
            $this->set('photos', $photos);
            $this->set('checkins', $userCheckins);
            
            //load the required data
            $followersList = $this->UserFollows->getFollowers($id);
            $this->set('followersList', $followersList);
            
	} else {
            $this->requireLogin("/Users/viewFollowers/$id");
	}
    }
    
    //Returns the list of users followed by the specified user
    function viewFollowing($id){
        $currentUser = $this->Session->read('userID');
	if($currentUser != null){
            $user = $this->User->findById($id);
            $this->set('user', $user);
            $this->set('user_id', $id);
            $this->set('headerTitle', $user['User']['name']);
            
            //count stuff for the menu
            $this->loadModel('UserFollows');
            $this->loadModel('Dog');
            $this->loadModel('Activity');
            $this->loadModel('ActivityComment');
            $this->loadModel('PlaceComment');
            $this->loadModel('PlaceLike');
            $this->loadModel('ActivityLike');
            $this->loadModel('Photo');
            $this->loadModel('PlaceCheckin');
            $follow_stats = $this->UserFollows->getFollowStats($id);
            $placeComments = $this->PlaceComment->countCommentsForUser($id);
            $placeLikes = $this->PlaceLike->countLikesForUser($id);
            $activityComments = $this->ActivityComment->countCommentsForUser($id);
            $activityLikes = $this->ActivityLike->countLikesForUser($id);
            $userPhotos = $this->Photo->countGalleryPhotosByUser($id);
            $userCheckins = $this->PlaceCheckin->countUserCheckins($id);
            
            $followers = $follow_stats['followers'];
            $following = $follow_stats['following'];
            $dogs = $this->Dog->countUserDogs($id);
            $activities = $this->Activity->countActivitiesForUser($id);
            $comments = $placeComments + $activityComments;
            $likes = $placeLikes + $activityLikes;
            $photos = $userPhotos;
            
            $this->set('followers', $followers);
            $this->set('following', $following);
            $this->set('dogs', $dogs);
            $this->set('activities', $activities);
            $this->set('comments', $comments);
            $this->set('likes', $likes);
            $this->set('photos', $photos);
            $this->set('checkins', $userCheckins);
            
            //load the required data
            $followersList = $this->UserFollows->getFollowing($id);
            $this->set('followersList', $followersList);
	} else {
            $this->requireLogin("/Users/viewFollowing/$id");
	}
    }
    
    //Returns the dogs owned by the specified user
    function viewDogs($id){
        $currentUser = $this->Session->read('userID');
	if($currentUser != null){
            $user = $this->User->findById($id);
            $this->set('user', $user);
            $this->set('user_id', $id);
            $this->set('headerTitle', $user['User']['name']);
            
            //count stuff for the menu
            $this->loadModel('UserFollows');
            $this->loadModel('Dog');
            $this->loadModel('Activity');
            $this->loadModel('ActivityComment');
            $this->loadModel('PlaceComment');
            $this->loadModel('PlaceLike');
            $this->loadModel('ActivityLike');
            $this->loadModel('Photo');
            $this->loadModel('PlaceCheckin');
            $follow_stats = $this->UserFollows->getFollowStats($id);
            $placeComments = $this->PlaceComment->countCommentsForUser($id);
            $placeLikes = $this->PlaceLike->countLikesForUser($id);
            $activityComments = $this->ActivityComment->countCommentsForUser($id);
            $activityLikes = $this->ActivityLike->countLikesForUser($id);
            $userPhotos = $this->Photo->countGalleryPhotosByUser($id);
            $userCheckins = $this->PlaceCheckin->countUserCheckins($id);
            
            $followers = $follow_stats['followers'];
            $following = $follow_stats['following'];
            $dogs = $this->Dog->countUserDogs($id);
            $activities = $this->Activity->countActivitiesForUser($id);
            $comments = $placeComments + $activityComments;
            $likes = $placeLikes + $activityLikes;
            $photos = $userPhotos;
            
            $this->set('followers', $followers);
            $this->set('following', $following);
            $this->set('dogs', $dogs);
            $this->set('activities', $activities);
            $this->set('comments', $comments);
            $this->set('likes', $likes);
            $this->set('photos', $photos);
            $this->set('checkins', $userCheckins);
            
            //load the required data
            $timezone = "+2:00";
            $fromDate = date("Y-m-d") . " 00:00:01";
            $toDate = date("Y-m-d") . " 23:59:59";
            $dogList = $this->Dog->getUserDogs($id, $fromDate, $toDate, $timezone);
            $this->set('dogList', $dogList);
            
            //echo "<pre>"; var_dump($dogList); echo "</pre>";
            
	} else {
            $this->requireLogin("/Users/viewDogs/$id");
	}
    }
    
    //Returns the list of user activities
    function viewActivities($id){
        $currentUser = $this->Session->read('userID');
	if($currentUser != null){
            $user = $this->User->findById($id);
            $this->set('user', $user);
            $this->set('user_id', $id);
            $this->set('headerTitle', $user['User']['name']);
            
            //count stuff for the menu
            $this->loadModel('UserFollows');
            $this->loadModel('Dog');
            $this->loadModel('Activity');
            $this->loadModel('ActivityComment');
            $this->loadModel('PlaceComment');
            $this->loadModel('PlaceLike');
            $this->loadModel('ActivityLike');
            $this->loadModel('Photo');
            $this->loadModel('PlaceCheckin');
            $follow_stats = $this->UserFollows->getFollowStats($id);
            $placeComments = $this->PlaceComment->countCommentsForUser($id);
            $placeLikes = $this->PlaceLike->countLikesForUser($id);
            $activityComments = $this->ActivityComment->countCommentsForUser($id);
            $activityLikes = $this->ActivityLike->countLikesForUser($id);
            $userPhotos = $this->Photo->countGalleryPhotosByUser($id);
            $userCheckins = $this->PlaceCheckin->countUserCheckins($id);
            
            $followers = $follow_stats['followers'];
            $following = $follow_stats['following'];
            $dogs = $this->Dog->countUserDogs($id);
            $activities = $this->Activity->countActivitiesForUser($id);
            $comments = $placeComments + $activityComments;
            $likes = $placeLikes + $activityLikes;
            $photos = $userPhotos;
            
            $this->set('followers', $followers);
            $this->set('following', $following);
            $this->set('dogs', $dogs);
            $this->set('activities', $activities);
            $this->set('comments', $comments);
            $this->set('likes', $likes);
            $this->set('photos', $photos);
            $this->set('checkins', $userCheckins);
            
            //load the required data
            $activities = $this->Activity->getActivityList($id);
            $this->set('activitiesList', $activities);
            
            //echo "<pre>"; var_dump($dogList); echo "</pre>";
            
	} else {
            $this->requireLogin("/Users/viewActivities/$id");
	}
    }
    
    function viewComments($userId){
        $currentUser = $this->Session->read('userID');
	if($currentUser != null){
            
            $user = $this->User->findById($userId);
            $this->set('user', $user);
            $this->set('user_id', $userId);
            $this->set('headerTitle', $user['User']['name']);
            
            //load activity comments
            $this->loadModel('Activity');
            $activityComments = $this->Activity->getActivityCommentsByUser($userId, false);
            $this->set('activityComments', $activityComments);
            
            //load place comments
            $this->loadModel('PlaceComment');
            $placeComments = $this->PlaceComment->getPlaceCommentsByUser($userId, false);
            $this->set('placeComments', $placeComments);
            
            //load menu stuff
            $this->loadModel('UserFollows');
            $this->loadModel('Dog');
            $this->loadModel('Activity');
            $this->loadModel('ActivityComment');
            $this->loadModel('PlaceLike');
            $this->loadModel('ActivityLike');
            $this->loadModel('Photo');
            $this->loadModel('PlaceCheckin');
            $follow_stats = $this->UserFollows->getFollowStats($userId);
            $placeComments = $this->PlaceComment->countCommentsForUser($userId);
            $placeLikes = $this->PlaceLike->countLikesForUser($userId);
            $activityComments = $this->ActivityComment->countCommentsForUser($userId);
            $activityLikes = $this->ActivityLike->countLikesForUser($userId);
            $userPhotos = $this->Photo->countGalleryPhotosByUser($userId);
            $userCheckins = $this->PlaceCheckin->countUserCheckins($userId);
            
            $followers = $follow_stats['followers'];
            $following = $follow_stats['following'];
            $dogs = $this->Dog->countUserDogs($userId);
            $activities = $this->Activity->countActivitiesForUser($userId);
            $comments = $placeComments + $activityComments;
            $likes = $placeLikes + $activityLikes;
            $photos = $userPhotos;
            
            $this->set('followers', $followers);
            $this->set('following', $following);
            $this->set('dogs', $dogs);
            $this->set('activities', $activities);
            $this->set('comments', $comments);
            $this->set('likes', $likes);
            $this->set('photos', $photos);
            $this->set('checkins', $userCheckins);
	} else {
            $this->requireLogin('/Users/viewComments');
	}
    }
    
    function viewCheckins($userId){
        $currentUser = $this->Session->read('userID');
	if($currentUser != null){
            
            $user = $this->User->findById($userId);
            $this->set('user', $user);
            $this->set('user_id', $userId);
            $this->set('headerTitle', $user['User']['name']);
            
            //load menu stuff
            $this->loadModel('UserFollows');
            $this->loadModel('Dog');
            $this->loadModel('Activity');
            $this->loadModel('ActivityComment');
            $this->loadModel('PlaceLike');
            $this->loadModel('PlaceComment');
            $this->loadModel('ActivityLike');
            $this->loadModel('Photo');
            $this->loadModel('PlaceCheckin');
            $follow_stats = $this->UserFollows->getFollowStats($userId);
            $placeComments = $this->PlaceComment->countCommentsForUser($userId);
            $placeLikes = $this->PlaceLike->countLikesForUser($userId);
            $activityComments = $this->ActivityComment->countCommentsForUser($userId);
            $activityLikes = $this->ActivityLike->countLikesForUser($userId);
            $userPhotos = $this->Photo->countGalleryPhotosByUser($userId);
            $userCheckins = $this->PlaceCheckin->countUserCheckins($userId);
            
            $followers = $follow_stats['followers'];
            $following = $follow_stats['following'];
            $dogs = $this->Dog->countUserDogs($userId);
            $activities = $this->Activity->countActivitiesForUser($userId);
            $comments = $placeComments + $activityComments;
            $likes = $placeLikes + $activityLikes;
            $photos = $userPhotos;
            
            //load required data
            $checkinList = $this->PlaceCheckin->getUserCheckins($userId);
            $this->set('checkinList', $checkinList);
            
            $this->set('followers', $followers);
            $this->set('following', $following);
            $this->set('dogs', $dogs);
            $this->set('activities', $activities);
            $this->set('comments', $comments);
            $this->set('likes', $likes);
            $this->set('photos', $photos);
            $this->set('checkins', $userCheckins);
	} else {
            $this->requireLogin("/Users/viewCheckins/$userId");
	}
    }
    
    function viewActivity($activityId){
        $currentUser = $this->Session->read('userID');
	if($currentUser != null){
            
            $this->loadModel('Activity');
            $activity_dogs = $this->Activity->getActivityDogs($activityId);
            $activity_obj = $this->Activity->findById($activityId);
            $coordinates = $this->Activity->getActivityCoordinates($activityId);
            $this->set('activity', $activity_obj);
            $this->set('activity_dogs', $activity_dogs);
            $this->set('activity_coordinates', $coordinates);
            $this->set('activity_id', $activityId);
            
            $id = $activity_obj['Activity']['user_id'];
            $user = $this->User->findById($id);
            $this->set('user', $user);
            $this->set('user_id', $id);
            $this->set('headerTitle', $user['User']['name'] . " activity overview");
            
            $activity_comments = $this->Activity->countActivityComments($activityId);
            $activity_likes = $this->Activity->countActivityLikes($activityId);
            
            $this->set('comments', $activity_comments);
            $this->set('likes', $activity_likes);
            //echo "<pre>"; var_dump($coordinates); echo "</pre>";
            
	} else {
            $this->requireLogin("/Users/viewActivity/$id");
	}
    }
    
    //Returns the likes for the specified activity
    function viewActivityLikes($activityId){
        $currentUser = $this->Session->read('userID');
	if($currentUser != null){
            
            $this->loadModel('Activity');
            
            $activity_obj = $this->Activity->findById($activityId);
            $likesList = $this->Activity->getLikedUsers($activityId);
            $this->set('likesLists', $likesList);
            $this->set('activity_id', $activityId);
            
            $id = $activity_obj['Activity']['user_id'];
            $user = $this->User->findById($id);
            $this->set('user', $user);
            $this->set('user_id', $id);
            $this->set('headerTitle', $user['User']['name'] . " activity overview");
            
            $activity_comments = $this->Activity->countActivityComments($activityId);
            $activity_likes = $this->Activity->countActivityLikes($activityId);
            
            $this->set('comments', $activity_comments);
            $this->set('likes', $activity_likes);
            //echo "<pre>"; var_dump($coordinates); echo "</pre>";
            
	} else {
            $this->requireLogin("/Users/viewActivityLikes/$activityId");
	}
    }
    
    //Returns the comments for the specified activity
    function viewActivityComments($activityId){
        $currentUser = $this->Session->read('userID');
	if($currentUser != null){
            
            $this->loadModel('Activity');
            
            $activity_obj = $this->Activity->findById($activityId);
            $commentList = $this->Activity->getActivityComments($activityId, false);
            $this->set('commentList', $commentList);
            $this->set('activity_id', $activityId);
            
            $id = $activity_obj['Activity']['user_id'];
            $user = $this->User->findById($id);
            $this->set('user', $user);
            $this->set('user_id', $id);
            $this->set('headerTitle', $user['User']['name'] . " activity overview");
            
            $activity_comments = $this->Activity->countActivityComments($activityId);
            $activity_likes = $this->Activity->countActivityLikes($activityId);
            
            $this->set('comments', $activity_comments);
            $this->set('likes', $activity_likes);
            
            //echo "<pre>"; var_dump($commentList); echo "</pre>";
            
	} else {
            $this->requireLogin("/Users/viewActivityComments/$activityId");
	}
    }
    
    function viewLikes($userId){
        $currentUser = $this->Session->read('userID');
	if($currentUser != null){
            
            $user = $this->User->findById($userId);
            $this->set('user', $user);
            $this->set('user_id', $userId);
            $this->set('headerTitle', $user['User']['name']);
            
            //get activity likes
            $this->loadModel('ActivityLike');
            $activityLikes = $this->ActivityLike->getLikedActivities($userId);
            $this->set('activityLikes', $activityLikes);
            
            //get place likes
            $this->loadModel('PlaceLike');
            $placeLikes = $this->PlaceLike->getLikedPlaces($userId);
            $this->set('placeLikes', $placeLikes);
            
            //get dog likes
            $this->loadModel('DogLike');
            $dogLikes = $this->DogLike->getLikedDogs($userId);
            $this->set('dogLikes', $dogLikes);
            
            //menu stuff
            $this->loadModel('PlaceComment');
            $this->loadModel('UserFollows');
            $this->loadModel('Dog');
            $this->loadModel('Activity');
            $this->loadModel('ActivityComment');
            $this->loadModel('PlaceLike');
            $this->loadModel('ActivityLike');
            $this->loadModel('Photo');
            $this->loadModel('PlaceCheckin');
            $follow_stats = $this->UserFollows->getFollowStats($userId);
            $placeComments = $this->PlaceComment->countCommentsForUser($userId);
            $placeLikes = $this->PlaceLike->countLikesForUser($userId);
            $activityComments = $this->ActivityComment->countCommentsForUser($userId);
            $activityLikes = $this->ActivityLike->countLikesForUser($userId);
            $userPhotos = $this->Photo->countGalleryPhotosByUser($userId);
            $userCheckins = $this->PlaceCheckin->countUserCheckins($userId);
            
            $followers = $follow_stats['followers'];
            $following = $follow_stats['following'];
            $dogs = $this->Dog->countUserDogs($userId);
            $activities = $this->Activity->countActivitiesForUser($userId);
            $comments = $placeComments + $activityComments;
            $likes = $placeLikes + $activityLikes;
            $photos = $userPhotos;
            
            $this->set('followers', $followers);
            $this->set('following', $following);
            $this->set('dogs', $dogs);
            $this->set('activities', $activities);
            $this->set('comments', $comments);
            $this->set('likes', $likes);
            $this->set('photos', $photos);
            $this->set('checkins', $userCheckins);
	} else {
            $this->requireLogin("/Users/viewLikes/$userId");
	}
    }
    
    //AJAX call for deleting/restoring user comments
    function processComment(){
        if(isset($_REQUEST['comment_id'])) $comment_id = $_REQUEST['comment_id'];
        if(isset($_REQUEST['type_id'])) $type_id = $_REQUEST['type_id'];
        if(isset($_REQUEST['flag'])) $flag = $_REQUEST['flag'];
        
        //Activity or place comment
        $result = false;
        if($type_id == 1){
            $this->loadModel('ActivityComment');
            $obj['ActivityComment']['id'] = $comment_id;
            $obj['ActivityComment']['active'] = $flag;
            
            if($this->ActivityComment->save($obj)){
                $result = true;
            }
            
        } else {
            $this->loadModel('PlaceComment');
            $obj['PlaceComment']['id'] = $comment_id;
            $obj['PlaceComment']['active'] = $flag;
            
            if($this->PlaceComment->save($obj)){
                $result = true;
            }
        }
        
        $data['result'] = $result;
        
        $this->layout = 'blank';
        echo json_encode(compact('data', $data));
    }
    
    //AJAX call for deleting/restoring user comments
    function processPhoto(){
        if(isset($_REQUEST['photo_id'])) $photo_id = $_REQUEST['photo_id'];
        if(isset($_REQUEST['flag'])) $flag = $_REQUEST['flag'];
        
        //Activity or place comment
        $result = false;
        
        $this->loadModel('Photo');
        $obj['Photo']['id'] = $photo_id;
        $obj['Photo']['active'] = $flag;

        if($this->Photo->save($obj)){
            $result = true;
        }
        
        $data['result'] = $result;
        
        $this->layout = 'blank';
        echo json_encode(compact('data', $data));
    }
    
    function login(){
	$this->layout = 'blank';

	if (!empty($this->request->data)){

            $email = $this->request->data['User']['email'];
            $password = $this->request->data['User']['password'];
            $userObj = $this->User->validateAdminCredentials($email, $password);
            $roles = $this->User->simplifyRoles($userObj['UserRole']);
            
            $userId = $userObj['User']['id'];
                        
            if($userId != null){
                $this->Session->write('userID', $userId);
		$this->Session->write('name', $userObj['User']['name']);
                $this->Session->write('role', $roles);

		//Redirect to home or wherever was initially requested
		if(!empty($this->request->data['User']['redirectUrl'])){
                    $redirectUrl = $this->request->data['User']['redirectUrl'];
		} else {
                    $redirectUrl = "/Users/index";
		}

		//Set cookie if required
		if(isset($this->request->data['User']['remember_me']) && $this->request->data['User']['remember_me'] == '1'){
                    $this->Cookie->write('email', $userObj['User']['email'], false, '+1 week');
		} else {
                    $this->Cookie->delete('email');
		}

		//Redirect
                $this->log("Users->login() redirecting to $redirectUrl", LOG_DEBUG);
                $this->redirect($redirectUrl);
            } else {
                $this->set('errorMsg', 'Invalid username/password');
            }
	} else {
            //Provide login data from cookie to the view
            $this->set('email', $this->Cookie->read('email'));
	}
    }

    function resetPassword(){
	$this->layout = 'blank';

	if (!empty($this->request->data)){
            $email = $this->request->data['User']['email'];
            $currentUser = $this->User->findAllByEmail($email);

            //if the user is found
            if($currentUser != null){
		$this->set('notificationMsg', "Your new password has been sent to $email");
            } else {
		$this->set('errorMsg', 'No user found with this email address');
            }
	}

    }

   
    function logout(){
        $this->Session->destroy();
	$this->redirect('/');
    }

    function profile(){
	$currentUser = $this->Session->read('userID');
	if($currentUser != null){
            $this->set('headerTitle', "My Profile");

            if (!empty($this->request->data)){
                $this->request->data['User']['password'] = Security::hash($this->request->data['User']['password'], 'md5');
		if($this->User->save($this->request->data)){
                    $this->set('notification', 'Your personal details have been successfully updated.');
                } else {
                    $this->set('notification', 'Unable to update your personal details - please try again.');
		}
            }

            $userObj = $this->User->findById($currentUser);
            $this->set('user', $userObj);

            //update session data
            $this->Session->write('name', $userObj['User']['name']);
	} else {
            $this->requireLogin('/Users/profile');
	}
    }
}

?>