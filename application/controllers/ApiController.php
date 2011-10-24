<?php

class ApiController extends Zend_Controller_Action
{

    public function init()
    {
        /* Initialize action controller here */
        $contextSwitch = $this->_helper->getHelper('contextSwitch');
        $contextSwitch->addActionContext("getallsessions", array('xml'))
                      ->initContext();
    }

    public function indexAction()
    {
        // action body
    }
    
    public function getallsessionsAction(){
        $this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender();
        $sessionMapper = new Model_SessionMapper();
        $sessions = $sessionMapper->fetchAll();
        $dom = new DOMDocument("1.0");
        $node = $dom->createElement("Sessions");
        $parnode = $dom->appendChild($node);
        foreach($sessions as $session){
            $node = $dom->createElement("Session");
            $newnode = $parnode->appendChild($node);
            $newnode->setAttribute("id", $session->getId());
            $newnode->setAttribute("desc", $session->getDesc());
            $newnode->setAttribute("active", $session->getActive());
        }
        $this->_response->clearHeaders();
        $this->_response->setHeader('Content-Type', 'text/xml; charset=utf-8');
        echo $dom->saveXML();
    }


}

