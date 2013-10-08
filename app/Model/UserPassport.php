<?php
App::uses('AppModel', 'Model');
class UserPassport extends AppModel {
     public $name = 'UserPassport';
     
     function getNotes($user_id){
        $sql = "select up.id, up.title, up.description, unix_timestamp(up.due_date) due_date, up.completed, up.remind ";
        $sql .= "from user_passports up ";
        $sql .= "where up.user_id = $user_id ";
        
        $rs = $this->query($sql);
        $data = array();
        $obj = array();
        
        if(is_array($rs)){
            foreach($rs as $i => $values){
                $obj['Note']['id'] = $rs[$i]['up']['id'];
                $obj['Note']['title'] = $rs[$i]['up']['title'];
		$obj['Note']['description'] = $name = $rs[$i]['up']['description'];
		$obj['Note']['date'] = $rs[$i][0]['due_date'] * 1000;
                $obj['Note']['completed'] = $rs[$i]['up']['completed'];
                $obj['Note']['remind'] = $rs[$i]['up']['remind'];

		$data[] = $obj;
            }
        }
        
        return $data;
    }
}

?>