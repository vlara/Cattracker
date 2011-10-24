<?php

class ApiController extends Zend_Controller_Action {

    public function init() {
        /* Initialize action controller here */
//      $contextSwitch = $this->_helper->getHelper('contextSwitch');
//        $contextSwitch->addActionContext("getalllines", array('xml'))->initContext();
//        $contextSwitch->addActionContext("getalllines", array('xml'))
//                      ->initContext();
    }

    public function indexAction() {
        // action body
    }

    public function getallsessionsAction() {
        $this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender();
        //$this->_response->clearHeaders();
        $this->_response->setHeader('Content-Type', 'text/xml; charset=utf-8');
        $dom = new DOMDocument("1.0");
        $node = $dom->createElement("Sessions");
        $parnode = $dom->appendChild($node);

        $sessionMapper = new Model_SessionMapper();
        $sessions = $sessionMapper->fetchAll();
        foreach ($sessions as $session) {
            $node = $dom->createElement("Session");
            $newnode = $parnode->appendChild($node);
            $newnode->setAttribute("id", $session->getId());
            $newnode->setAttribute("desc", $session->getDesc());
            $newnode->setAttribute("active", $session->getActive());
        }
        echo $dom->saveXML();
    }

    //Location and Session Work

    public function getalllinesAction() {
        $this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender();
        $this->_response->setHeader('Content-Type', 'text/xml; charset=utf-8');
        $dom = new DOMDocument("1.0");
        $node = $dom->createElement("Lines");
        $parnode = $dom->appendChild($node);
        $lineMapper = new Model_LineMapper();
        $lines = $lineMapper->fetchAll();
        foreach ($lines as $line) {
            $node = $dom->createElement("Line");
            $newnode = $parnode->appendChild($node);
            $newnode->setAttribute("id", $line->getId());
            $newnode->setAttribute("name", $line->getName());
        }
        $this->_response->setBody($dom->saveXML());
    }

}

