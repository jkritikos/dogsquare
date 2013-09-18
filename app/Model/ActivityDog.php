<?php

class ActivityDog extends AppModel {
    var $name = 'ActivityDog';
     
    public $belongsTo = array(
        'Dog' => array(
            'className' => 'Dog',
        )
    );
}

?>