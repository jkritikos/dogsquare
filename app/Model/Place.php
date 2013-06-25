<?php

App::uses('AppModel', 'Model');
class Place extends AppModel {
     public $name = 'Place';
	 
	 
	 function search($name, $category, $status){
        $sql = "select p.id,  p.name, pc.name, date_format(p.created, '%d/%m/%Y %H:%i') as created ";
		$sql .= " from places p, place_categories pc  where pc.id = p.category_id ";
		
		if($name != ''){
	        $sql .= " and p.name like '%$name%' ";
		}
	
		if($category != ''){
	        $sql .= " and pc.name like '%$category%' ";
		}
	        
	    if($status != ''){
	        $sql .= " and p.active=$status ";
	    }
	

		$rs = $this->query($sql);
	
		$data = array();
		if(is_array($rs)){
            foreach($rs as $i => $values){
            	$id = $rs[$i]['p']['id'];
                $name = $rs[$i]['p']['name'];
				$category = $rs[$i]['pc']['name'];
				$created = $rs[$i]['0']['created'];
		                
				$obj['Place']['name'] = $name;
				$obj['Place']['category'] = $category;
				$obj['Place']['created'] = $created;
				$obj['Place']['id'] = $id;
		
				$data[] = $obj;
            }
		}
	
		$this->log("Place->search() returns ".count($data), LOG_DEBUG);
		return $data;
	}
}

?>