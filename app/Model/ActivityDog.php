<?php

class ActivityDog extends AppModel {
    var $name = 'ActivityDog';
     
    public $belongsTo = array(
        'Dog' => array(
            'className' => 'Dog',
        )
    );
    
    //Returns the max number of times ANY of the specified $dog_ids earned dogfuel bars of value $dogfuelValue within the last $daysAgo days
    function getMaxActivityCounts($daysAgo, $dog_ids, $dogfuelValue){
        $dogList = implode(",", $dog_ids);
        $sql = "select count(*) cnt, ad.dog_id from activity_dogs ad where ad.dogfuel=$dogfuelValue and ad.dog_id in ($dogList) and ad.created >= now() - interval $daysAgo day group by ad.dog_id order by cnt desc limit 1";
        $rs = $this->query($sql);
        
        $data = array();
        $count = 0;
        if(is_array($rs)){
            foreach($rs as $i => $values){
                //If we ever need to switch it back to detailed data, here it is
                //$obj['dog_id'] = $rs[$i]['ad']['dog_id'];
                //$obj['cnt'] = $rs[$i][0]['cnt'];

                //$data[] = $obj;
                $count = $rs[$i][0]['cnt'];
            }
        }
        
        return $count;
    }
    
    //Returns the max total amount of dogfuel earned by the specified dogs in the past amount of days
    function getMaxDogfuelInPeriod($daysAgo, $dog_ids){
        $dogList = implode(",", $dog_ids);
        $sql = "select sum(ad.dogfuel) as fuel, ad.dog_id from activity_dogs ad where ad.dog_id in ($dogList) and ad.created >= now() - interval $daysAgo day group by ad.dog_id order by fuel desc limit 1";
        $rs = $this->query($sql);
        
        $data = array();
        $count = 0;
        if(is_array($rs)){
            foreach($rs as $i => $values){
                $count = $rs[$i][0]['fuel'];
            }
        }
        
        return $count;
    }
    
    function hasActivities($dog_ids){
        
    }
    
    //Returns the max number of days since the last activity for the specified dogs
    function getDaysSinceLastActivity($dog_ids){
        $dogList = implode(",", $dog_ids);
        $sql = "select datediff(now(), max(ad.created)) days from activity_dogs ad where ad.dog_id in ($dogList) order by days desc limit 1";
        
        $days = 0;
        $rs = $this->query($sql);
        if(is_array($rs)){
            foreach($rs as $i => $values){
                $days = $rs[0][0]['days'];
            }
           
        } 
        
        return $days;
    }
}

?>