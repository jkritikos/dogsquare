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
    
    function search($name,$email,$status, $userId){
        $sql = "select u.name, u.email, u.id, date_format(u.created, '%d/%m/%Y %H:%i' ) as created,";
        $sql .= " (select uf.id from user_follows uf where uf.user_id = $userId and uf.follows_user=u.id) as followed";
        $sql .= " from users u where 1=1";

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
                
		$obj['User']['name'] = $name;
		$obj['User']['email'] = $email;
		$obj['User']['created'] = $created;
		$obj['User']['id'] = $id;
                $obj['User']['followed'] = $followed;

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
    
    /*Creates a security token for the specified user id*/
    function generateToken($userID){
        $input = "t0k3n!$userID";
        return Security::hash($input, 'md5');
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

	$this->log("User->validateAdminCredentials() returns $result", LOG_DEBUG);
	return $result;
    }
    
    
    function getOtherUserById($userId, $targetId){
        $sql = "select u.id, u.name, u.email, u.facebook_id, u.gender, p.path, p.thumb, count(uf.id) as following, ";
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
        $obj['facebook_id'] = $rs[0]['u']['facebook_id'];
        $obj['gender'] = $rs[0]['u']['gender'];
        $obj['photo'] = $rs[0]['p']['path'];
        $obj['thumb'] = $rs[0]['p']['thumb'];
        $obj['following'] = $rs[0][0]['following'];
        $obj['followers'] = $rs[0][0]['followers'];
        $obj['followed'] = $rs[0][0]['followed'];
                
        return $obj;
    }
}

?>