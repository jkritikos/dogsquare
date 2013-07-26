<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of ApiController
 *
 * @author jace
 */
class ApiController extends AppController{
    
    var $components = array('Cookie', 'RequestHandler');
    var $helpers = array('Js','Time');
    //put your code here
    
    function hello(){
        
        $data['test'] = 'edd';
        
        $this->layout = 'blank';
        echo json_encode(compact('data', $data));
    }
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    function login(){
        
    }
    
    function signup(){
        
    }
}

?>
