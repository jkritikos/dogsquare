<?php

//App::uses('Core', 'l10n', 'Sanitize');

class ReportsController extends AppController {
    var $components = array('Cookie', 'RequestHandler');
    var $helpers = array('Js','Time');
    
    function beforeFilter() {	
        parent::beforeFilter();
        $this->set('headerTitle', "Dashboard");
        $this->set('activeTab', "reports");
    }
	
    function index(){
        $currentUser = $this->Session->read('userID');
	if($currentUser != null){
            
            date_default_timezone_set('Europe/Athens');
            $today = date("d/m/Y");
            
            $todayUsers = $this->Report->getNumberOfUsers($today,$today);
            $totalUsers = $this->Report->getNumberOfUsers("","");
            $todayActivities = $this->Report->getNumberOfActivities($today,$today);
            $totalActivities = $this->Report->getNumberOfActivities("","");
            $todayDogs = $this->Report->getNumberOfDogs($today,$today);
            $totalDogs = $this->Report->getNumberOfDogs("","");
            $todayPlaces = $this->Report->getNumberOfPlaces($today,$today);
            $totalPlaces = $this->Report->getNumberOfPlaces("","");
            $todayCheckins = $this->Report->getNumberOfCheckins($today,$today);
            $totalCheckins = $this->Report->getNumberOfCheckins("","");
            $todayPhotos = $this->Report->getNumberOfPhotos($today,$today);
            $totalPhotos = $this->Report->getNumberOfPhotos("","");
            
            //graph
            $dailyUsers = $this->Report->getDailyUsersTimelineData();
            $this->set('dailyUsers', $dailyUsers);
            
            //echo "<pre>"; var_dump($dailyUsers); echo "</pre>";
            
            $this->set('todayUsers', $todayUsers);
            $this->set('totalUsers', $totalUsers);
            $this->set('todayActivities', $todayActivities);
            $this->set('totalActivities', $totalActivities);
            $this->set('todayDogs', $todayDogs);
            $this->set('totalDogs', $totalDogs);
            $this->set('todayPlaces', $todayPlaces);
            $this->set('totalPlaces', $totalPlaces);
            $this->set('todayCheckins', $todayCheckins);
            $this->set('totalCheckins', $totalCheckins);
            $this->set('todayPhotos', $todayPhotos);
            $this->set('totalPhotos', $totalPhotos);
            
	} else {
            $this->requireLogin('/Reports/index');
	}
    }
    
    function userCountry(){
        $currentUser = $this->Session->read('userID');
	if($currentUser != null){
            $this->set('headerTitle', "Users by country");
            
            $totalUsers = $this->Report->getNumberOfUsers("","");
            $users = $this->Report->getUsersByCountry();
            $this->set('users', $users);
            $this->set('totalUsers', $totalUsers);
            
	} else {
            $this->requireLogin('/Reports/userCountry');
	}
    }
    
}

?>