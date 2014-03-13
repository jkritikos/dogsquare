<?php

class User extends AppModel {
    var $name = 'User';
    
    public $hasMany = array(
        'UserRole' => array(
            'className' => 'UserRole',
        )
    );
    
    function getProfilePhoto($user_id){
        $sql = "select p.path, p.thumb from photos p inner join users u on (u.photo_id = p.id) where u.id=$user_id";
        
        $rs = $this->query($sql);
        $data = array();
        if(is_array($rs)){
            foreach($rs as $i => $values){
                $data['photo'] = $rs[$i]['p']['path'];
                $data['thumb'] = $rs[$i]['p']['thumb'];
            }
        }
        
        return $data;
    }
    
    function areUsers($emailList, $userId){
        $this->log("User->areUsers() called with list $emailList", LOG_DEBUG);
        
        $sql = "select u.name, u.email, u.id, ";
        $sql .= " (select uf.id from user_follows uf where uf.user_id = $userId and uf.follows_user=u.id) as followed";
        $sql .= " from users u where u.email in ($emailList)";
        $rs = $this->query($sql);
        $data = array();
        $emails = array();
	if(is_array($rs)){
            foreach($rs as $i => $values){
                $name = $rs[$i]['u']['name'];
		$id = $rs[$i]['u']['id'];
		$email = $rs[$i]['u']['email'];
                $followed = $rs[$i][0]['followed'];
		
		$obj['User']['name'] = $name;
		$obj['User']['email'] = $email;
		$obj['User']['id'] = $id;
                $obj['User']['followed'] = $followed;

                $emails[] = $email;
		$data[] = $obj;
            }
	}
        
        $object['users'] = $data;
        $object['matching_emails'] = $emails;
        
	$this->log("User->areUsers() returns ".count($data), LOG_DEBUG);
	return $object;
    }
    
    function websearch($name,$email,$status){
        $sql = "select u.name, p.thumb, u.email, u.id, date_format(u.created, '%d/%m/%Y %H:%i' ) as created ";
        $sql .= " from users u inner join photos p on (u.photo_id=p.id) where 1=1";

	if($name != ''){
            $sql .= " and u.name like '%$name%' order by u.name";
	}

	if($email != ''){
            $sql .= " and u.email = $email ";
	}
        
        if($status != ''){
            $sql .= " and u.active=$status ";
        }

	$rs = $this->query($sql);

	$data = array();
	if(is_array($rs)){
            foreach($rs as $i => $values){
                $name = $rs[$i]['u']['name'];
		$id = $rs[$i]['u']['id'];
		$email = $rs[$i]['u']['email'];
		$created = $rs[$i]['0']['created'];
                $thumb = $rs[$i]['p']['thumb'];
                
		$obj['User']['name'] = $name;
		$obj['User']['email'] = $email;
		$obj['User']['created'] = $created;
		$obj['User']['id'] = $id;
                $obj['User']['thumb'] = $thumb;

		$data[] = $obj;
            }
	}

	$this->log("User->search() returns ".count($data), LOG_DEBUG);
	return $data;
    }
    
    function search($name,$email,$status, $userId){
        $sql = "select u.name, p.thumb, u.email, u.id, date_format(u.created, '%d/%m/%Y %H:%i' ) as created,";
        $sql .= " (select uf.id from user_follows uf where uf.user_id = $userId and uf.follows_user=u.id) as followed";
        $sql .= " from users u inner join photos p on (u.photo_id=p.id) where u.active=1 ";

	if($name != ''){
            $sql .= " and u.name like '%$name%' order by u.name";
	}

	if($email != ''){
            $sql .= " and u.email = $email ";
	}
        
        if($status != ''){
            $sql .= " and u.active=$status ";
        }

	$rs = $this->query($sql);

	$data = array();
	if(is_array($rs)){
            foreach($rs as $i => $values){
                $name = $rs[$i]['u']['name'];
		$id = $rs[$i]['u']['id'];
		$email = $rs[$i]['u']['email'];
		$created = $rs[$i]['0']['created'];
                $followed = $rs[$i]['0']['followed'];
                $thumb = $rs[$i]['p']['thumb'];
                
		$obj['User']['name'] = $name;
		$obj['User']['email'] = $email;
		$obj['User']['created'] = $created;
		$obj['User']['id'] = $id;
                $obj['User']['followed'] = $followed;
                $obj['User']['thumb'] = $thumb;

		$data[] = $obj;
            }
	}

	$this->log("User->search() returns ".count($data), LOG_DEBUG);
	return $data;
    }
    
    /*Converts the full object structure to a simpler one, containing just IDs*/
    function simplifyRoles($roles){
        $obj = array();
        
        if($roles != null){
            foreach($roles as $r => $data){
                $obj[] = $data['role_id'];
            }
        }
        
        return $obj;
    }
    
    /*Returns an MD5 hash of the supplied string*/
    function hashPassword($s){
        $hash = null;
        if($s != null){
            $hash = Security::hash($s, 'md5');
        }
        
        return $hash;
    }
    
    //Validates the specified credentials and checks for an active user
    function authorise($userID, $token){
        $response = false;
        
        $user = $this->findById($userID);
        if($user != null){
            if($user['User']['active'] == 1){
                $passwordHash = $user['User']['password'];
                $tokenInput = "t0k3n!$userID@$passwordHash";
                $newToken = Security::hash($tokenInput, 'md5');
                
                $this->log("User->authorise() for user $userID comparing user token $token with server token $newToken", LOG_DEBUG);
                if($newToken == $token){
                    $response = true;
                }
            }
        }
        
        $this->log("User->authorise() returns $response for user $userID", LOG_DEBUG);
        return $response;
    }
    
    /*Creates a security token for the specified user id*/
    function generateToken($userID,$clearPassword){
        $password = $this->hashPassword($clearPassword);
        $input = "t0k3n!$userID@$password";
        return Security::hash($input, 'md5');
    }
    
    //Checks whether the specified facebook id / password combi is valid. 
    //Returns the user id on success, null otherwise
    function validateDummyFacebookCredentials($facebook_id, $pwd){
        $this->log("User->validateDummyFacebookCredentials() called with facebook_id $facebook_id and password $pwd", LOG_DEBUG);
        $id = null;
        
        //try to login
        $currentUser = $this->findAllByFacebookId($facebook_id);
	if($currentUser != null){
            //if the account is active
            if($currentUser[0]['User']['active'] == '1'){
                $userHash = Security::hash($pwd, 'md5');

		//and the password is a match
		if($currentUser[0]['User']['password'] == $userHash){
                    $id = $currentUser[0]['User']['id'];
		} else {
                    $id = null;
		}
            }
        } else {
            $id = null;
	}
        
        $this->log("User->validateDummyFacebookCredentials() returns $id", LOG_DEBUG);
        return $id;
    }
    
    function generatePassword ($length = 8){
        // initialize variables
        $password = "";
        $i = 0;
        $possible = "0123456789bcdfghjkmnpqrstvwxyz"; 
 
        // add random characters to $password until $length is reached
        while ($i < $length) {
            // pick a random character from the possible ones
            $char = substr($possible, mt_rand(0, strlen($possible)-1), 1);
 
            // we don't want this character if it's already in the password
            if (!strstr($password, $char)) { 
                $password .= $char;
                $i++;
            }
        }
        return $password;
    }
    
    /*Checks whether the specified email/password combination is valid.
    Returns the user id on success, null otherwise*/
    function validateClientCredentials($email, $password){
        $this->log("User->validateClientCredentials() called with email $email and password $password", LOG_DEBUG);
        $id = null;
        
        //try to login
	$currentUser = $this->findAllByEmail($email);
	if($currentUser != null){
            //if the account is active
            if($currentUser[0]['User']['active'] == '1'){
                $userHash = Security::hash($password, 'md5');

		//and the password is a match
		if($currentUser[0]['User']['password'] == $userHash){
                    $id = $currentUser[0]['User']['id'];
		} else {
                    $id = null;
		}
            }
	} else {
            $id = null;
	}
        
        $this->log("User->validateClientCredentials() returns $id", LOG_DEBUG);
        return $id;
    }
    
    function validateClientCredentialsByUserId($userId, $password){
        $this->log("User->validateClientCredentialsByUserId() called for user with id $userId and password $password", LOG_DEBUG);
        
        //find user
	$currentUser = $this->findAllById($userId);
	if($currentUser != null){
            //if the account is active
            if($currentUser[0]['User']['active'] == '1'){
                $userHash = Security::hash($password, 'md5');

		//and the password is a match
		if($currentUser[0]['User']['password'] == $userHash){
                   return true;
		} else {
                   return false;
		}
            }else {
                return false;
            }
	} else {
            return false;
	}
    }
    
    /*Checks whether the specified email/password combination is valid.
    Returns the user id on success, null otherwise*/
    function validateAdminCredentials($email, $password){
	$this->log("User->validateAdminCredentials() called with email $email and password $password", LOG_DEBUG);
	$result = null;

	//try to login
	$currentUser = $this->findAllByEmail($email);
	if($currentUser != null){
            //if the account is active
            if($currentUser[0]['User']['active'] == '1'){
                $userHash = Security::hash($password, 'md5');

		//and the password is a match
		if($currentUser[0]['User']['password'] == $userHash){
                    $result = $currentUser[0];
		} else {
                    $result = null;
		}
            }
	} else {
            $result = null;
	}

	$this->log("User->validateAdminCredentials() returns", LOG_DEBUG);
	return $result;
    }
    
    
    function getOtherUserById($userId, $targetId){
        $sql = "select u.id, u.name, u.email, u.facebook_id, u.gender, u.address, u.newsletter, u.birth_date, u.country_id, p.path, p.thumb, count(uf.id) as following, ";
        $sql .= " (select count(id) from user_follows where follows_user = $targetId) as followers,";
        $sql .= " (select id from user_follows where follows_user = $targetId and user_id = $userId) as followed";
        $sql .= " from users u";
        $sql .= " left outer join photos p on (u.photo_id=p.id)";
        $sql .= " left outer join user_follows uf on (u.id=uf.user_id)";
        $sql .= " where u.id=$targetId";
        $rs = $this->query($sql);
        
        $obj['id'] = $rs[0]['u']['id'];
        $obj['name'] = $rs[0]['u']['name'];
        $obj['email'] = $rs[0]['u']['email'];
        $obj['address'] = $rs[0]['u']['address'];
        $obj['newsletter'] = $rs[0]['u']['newsletter'];
        $obj['birth_date'] = strtotime($rs[0]['u']['birth_date']) * 1000;
        $obj['country'] = $rs[0]['u']['country_id'];
        $obj['facebook_id'] = $rs[0]['u']['facebook_id'];
        $obj['gender'] = $rs[0]['u']['gender'];
        $obj['photo'] = $rs[0]['p']['path'];
        $obj['thumb'] = $rs[0]['p']['thumb'];
        $obj['following'] = $rs[0][0]['following'];
        $obj['followers'] = $rs[0][0]['followers'];
        $obj['followed'] = $rs[0][0]['followed'];
                
        return $obj;
    }
    
    //Returns a count of the checkins for the specified user - uses $category_id if specified
    function countCheckins($user_id, $category_id){
        if($category_id == null){
            $sql = "select count(*) cnt from place_checkins pc where pc.user_id=$user_id";
        } else {
            $sql = "select count(*) cnt from place_checkins pc inner join places p on (pc.place_id = p.id) where pc.user_id=$user_id and p.category_ud=$category_id";
        }
        
        $rs = $this->query($sql);
        $count = $rs[0][0]['cnt'];
        
        $this->log("User->countCheckins() returns $count checkins for user $user_id category $category_id", LOG_DEBUG);
        return $count;
    }
    
    //Checks if the specified user id has any lost dogs at the moment
    function hasLostDog($user_id){
        $sql = "select count(*) cnt from places p where p.dog_id in (select d.id from dogs d where d.owner_id=$user_id)";
        $rs = $this->query($sql);
        $count = $rs[0][0]['cnt'];
        
        return $count > 0;
    }
}

?>