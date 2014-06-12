<?php

class Report extends AppModel {
    var $name = "Report";
    var $useTable = false;
    
    function getNumberOfUsers($from, $to){
        $start = time();
        
        $sql = "select count(*) cnt from users p where 1=1 ";
        
        if(!empty($from)){
            $day = substr($from, 0,2);
            $month = substr($from, 3,2);
            $year = substr($from, 6,4);
            $from = date("Y/m/d", mktime(0, 0, 0, $month, $day, $year));
            $sql .= " and CONVERT_TZ(p.created, 'SYSTEM', '+2:00') >= '$from 00:00:00' ";
        }

        if(!empty($to)){
            $day = substr($to, 0,2);
            $month = substr($to, 3,2);
            $year = substr($to, 6,4);
            $to = date("Y/m/d", mktime(23, 59, 0, $month, $day, $year));
            $sql .= " and CONVERT_TZ(p.created, 'SYSTEM', '+2:00') <= '$to 23:59:00' ";
        }
        
        $count = 0;
        $rs = $this->query($sql);
	if(is_array($rs)){
            foreach($rs as $i => $values){
                $count = $rs[$i][0]['cnt'];
            }
	}
        
        $end = time();
        $timeTook = ($end - $start);
        $this->log("Report->getNumberOfUsers() took $timeTook seconds", LOG_DEBUG);
        
        return $count;
    }
    
    function getNumberOfActivities($from, $to){
        $start = time();
        
        $sql = "select count(*) cnt from activities p where 1=1 ";
        
        if(!empty($from)){
            $day = substr($from, 0,2);
            $month = substr($from, 3,2);
            $year = substr($from, 6,4);
            $from = date("Y/m/d", mktime(0, 0, 0, $month, $day, $year));
            $sql .= " and CONVERT_TZ(p.created, 'SYSTEM', '+2:00') >= '$from 00:00:00' ";
        }

        if(!empty($to)){
            $day = substr($to, 0,2);
            $month = substr($to, 3,2);
            $year = substr($to, 6,4);
            $to = date("Y/m/d", mktime(23, 59, 0, $month, $day, $year));
            $sql .= " and CONVERT_TZ(p.created, 'SYSTEM', '+2:00') <= '$to 23:59:00' ";
        }
        
        $count = 0;
        $rs = $this->query($sql);
	if(is_array($rs)){
            foreach($rs as $i => $values){
                $count = $rs[$i][0]['cnt'];
            }
	}
        
        $end = time();
        $timeTook = ($end - $start);
        $this->log("Report->getNumberOfActivities() took $timeTook seconds", LOG_DEBUG);
        
        return $count;
    }
    
    function getNumberOfDogs($from, $to){
        $start = time();
        
        $sql = "select count(*) cnt from dogs p where 1=1 ";
        
        if(!empty($from)){
            $day = substr($from, 0,2);
            $month = substr($from, 3,2);
            $year = substr($from, 6,4);
            $from = date("Y/m/d", mktime(0, 0, 0, $month, $day, $year));
            $sql .= " and CONVERT_TZ(p.created, 'SYSTEM', '+2:00') >= '$from 00:00:00' ";
        }

        if(!empty($to)){
            $day = substr($to, 0,2);
            $month = substr($to, 3,2);
            $year = substr($to, 6,4);
            $to = date("Y/m/d", mktime(23, 59, 0, $month, $day, $year));
            $sql .= " and CONVERT_TZ(p.created, 'SYSTEM', '+2:00') <= '$to 23:59:00' ";
        }
        
        $count = 0;
        $rs = $this->query($sql);
	if(is_array($rs)){
            foreach($rs as $i => $values){
                $count = $rs[$i][0]['cnt'];
            }
	}
        
        $end = time();
        $timeTook = ($end - $start);
        $this->log("Report->getNumberOfDogs() took $timeTook seconds", LOG_DEBUG);
        
        return $count;
    }
    
    function getNumberOfPlaces($from, $to){
        $start = time();
        
        $sql = "select count(*) cnt from places p where 1=1 ";
        
        if(!empty($from)){
            $day = substr($from, 0,2);
            $month = substr($from, 3,2);
            $year = substr($from, 6,4);
            $from = date("Y/m/d", mktime(0, 0, 0, $month, $day, $year));
            $sql .= " and CONVERT_TZ(p.created, 'SYSTEM', '+2:00') >= '$from 00:00:00' ";
        }

        if(!empty($to)){
            $day = substr($to, 0,2);
            $month = substr($to, 3,2);
            $year = substr($to, 6,4);
            $to = date("Y/m/d", mktime(23, 59, 0, $month, $day, $year));
            $sql .= " and CONVERT_TZ(p.created, 'SYSTEM', '+2:00') <= '$to 23:59:00' ";
        }
        
        $count = 0;
        $rs = $this->query($sql);
	if(is_array($rs)){
            foreach($rs as $i => $values){
                $count = $rs[$i][0]['cnt'];
            }
	}
        
        $end = time();
        $timeTook = ($end - $start);
        $this->log("Report->getNumberOfPlaces() took $timeTook seconds", LOG_DEBUG);
        
        return $count;
    }
    
    function getNumberOfCheckins($from, $to){
        $start = time();
        
        $sql = "select count(*) cnt from place_checkins p where 1=1 ";
        
        if(!empty($from)){
            $day = substr($from, 0,2);
            $month = substr($from, 3,2);
            $year = substr($from, 6,4);
            $from = date("Y/m/d", mktime(0, 0, 0, $month, $day, $year));
            $sql .= " and CONVERT_TZ(p.created, 'SYSTEM', '+2:00') >= '$from 00:00:00' ";
        }

        if(!empty($to)){
            $day = substr($to, 0,2);
            $month = substr($to, 3,2);
            $year = substr($to, 6,4);
            $to = date("Y/m/d", mktime(23, 59, 0, $month, $day, $year));
            $sql .= " and CONVERT_TZ(p.created, 'SYSTEM', '+2:00') <= '$to 23:59:00' ";
        }
        
        $count = 0;
        $rs = $this->query($sql);
	if(is_array($rs)){
            foreach($rs as $i => $values){
                $count = $rs[$i][0]['cnt'];
            }
	}
        
        $end = time();
        $timeTook = ($end - $start);
        $this->log("Report->getNumberOfCheckins() took $timeTook seconds", LOG_DEBUG);
        
        return $count;
    }
    
    function getNumberOfPhotos($from, $to){
        $start = time();
        
        $sql = "select count(*) cnt from photos p where 1=1 ";
        
        if(!empty($from)){
            $day = substr($from, 0,2);
            $month = substr($from, 3,2);
            $year = substr($from, 6,4);
            $from = date("Y/m/d", mktime(0, 0, 0, $month, $day, $year));
            $sql .= " and CONVERT_TZ(p.created, 'SYSTEM', '+2:00') >= '$from 00:00:00' ";
        }

        if(!empty($to)){
            $day = substr($to, 0,2);
            $month = substr($to, 3,2);
            $year = substr($to, 6,4);
            $to = date("Y/m/d", mktime(23, 59, 0, $month, $day, $year));
            $sql .= " and CONVERT_TZ(p.created, 'SYSTEM', '+2:00') <= '$to 23:59:00' ";
        }
        
        $count = 0;
        $rs = $this->query($sql);
	if(is_array($rs)){
            foreach($rs as $i => $values){
                $count = $rs[$i][0]['cnt'];
            }
	}
        
        $end = time();
        $timeTook = ($end - $start);
        $this->log("Report->getNumberOfPhotos() took $timeTook seconds", LOG_DEBUG);
        
        return $count;
    }
    
    function getDailyUsersTimelineData(){
        $sql = "select count(*) cnt, date(s.created) d from users s group by d order by d";
        
        $rs = $this->query($sql);
	
        $data = array();
        if(is_array($rs)){
            foreach($rs as $i => $values){
                $cnt = $rs[$i]['0']['cnt'];
                $date = $rs[$i]['0']['d'];
                
                $obj['date'] = $date;
                $obj['cnt'] = $cnt;
                
                $data[] = $obj;
            }
        }
        
        //$dataList = implode(",", $data);
        return $data;
    }
    
    function getUsersByCountry(){
        $sql = "select count(*) cnt, c.name from users u left join countries c on (u.country_id = c.id) group by c.name order by cnt desc";
        $rs = $this->query($sql);
	
        $data = array();
        if(is_array($rs)){
            foreach($rs as $i => $values){
                $cnt = $rs[$i]['0']['cnt'];
                $name = $rs[$i]['c']['name'];
                
                $obj['name'] = $name;
                $obj['cnt'] = $cnt;
                
                $data[] = $obj;
            }
        }
        
        //$dataList = implode(",", $data);
        return $data;
    }
    
    
    
}

?>