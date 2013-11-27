<?php
/**
 * Application level Controller
 *
 * This file is application-wide controller file. You can put all
 * application-wide controller-related methods here.
 *
 * PHP 5
 *
 * CakePHP(tm) : Rapid Development Framework (http://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link          http://cakephp.org CakePHP(tm) Project
 * @package       app.Controller
 * @since         CakePHP(tm) v 0.2.9
 * @license       http://www.opensource.org/licenses/mit-license.php MIT License
 */
App::uses('Controller', 'Controller');

/**
 * Application Controller
 *
 * Add your application-wide methods in the class below, your controllers
 * will inherit them.
 *
 * @package		app.Controller
 * @link		http://book.cakephp.org/2.0/en/controllers.html#the-app-controller
 */
class AppController extends Controller {
    
    /*Renders the login screen (with cookie data)*/
    function requireLogin($redirectUrl){
	$this->set('redirectUrl', $redirectUrl);
	$this->set('email', $this->Cookie->read('email'));
	$this->set('password', $this->Cookie->read('password'));
	$this->layout = 'blank';
	$this->render('/Users/login');
    }

    /*Renders the no-access view*/
    function noAccess(){
        $this->layout = 'blank';
	$this->render('/Users/rights');
    }
    
    /*Performs a var_dump, wrapped in <pre> tags*/
    function dump($d){
        echo "<pre>";var_dump($d); echo "</pre>";
    }
}
