<?php

class IndexController extends Zend_Controller_Action
{

    public function init()
    {
        /* Initialize action controller here */
    }

    public function indexAction()
    {
        // action body
        //$lineSelect = new Form_LineSelect();
        //$this->view->lineSelect = $lineSelect;
        $session = new Form_SelectLine();
        $this->view->lineSelect = $session;
    }

    public function aboutAction()
    {
        
    }

}

