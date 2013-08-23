<?php

class User extends AppModel {
    var $name = 'User';
    
    public $hasMany = array(
        'UserRole' => array(
            'className' => 'UserRole',
        )
    );

    function search($name,$email,$status){
        $sql = "select u.name, u.email, u.id, date_format(u.created, '%d/%m/%Y %H:%i' ) as created from users u where 1=1 ";

	if($name != ''){
            $sql .= " and u.name like '%$name%' ";
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
                
		$obj['User']['name'] = $name;
		$obj['User']['email'] = $email;
		$obj['User']['created'] = $created;
		$obj['User']['id'] = $id;

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
    Returns the user id on success, null otherwise. Used by the API*/
    function validateAdminCredentials($email, $password){
	$this->log("User->validateCredentials() called with email $email and password $password", LOG_DEBUG);
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

	$this->log("User->validateCredentials() returns $result", LOG_DEBUG);
	return $result;
    }
}

?>