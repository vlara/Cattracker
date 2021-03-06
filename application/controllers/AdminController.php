<?php

require_once 'Simpledom/simple_html_dom.php';

class AdminController extends Zend_Controller_Action {

    public function init() {
        $contextSwitch = $this->_helper->getHelper('contextSwitch');
        $contextSwitch->addActionContext("crr", array('json'))
                ->initContext();
    }

    public function indexAction() {
        // action body
    }

    public function unifiedadminAction() {
        //Line
        $lineMapper = new Model_LineMapper();
        $this->view->currentLines = $lineMapper->fetchAll();
        $formLine = new Form_Line();
        $this->view->formLine = $formLine;
        //Locations
        $locationMapper = new Model_LocationMapper();
        $this->view->Locations = $locationMapper->fetchAll();
        $formLocation = new Form_Location();
        $this->view->formLocation = $formLocation;
        //Arrival
        $arrivalMapper = new Model_ArrivalMapper();
        $this->view->arrivals = $arrivalMapper->fetchAll();
        $formArrival = new Form_Arrival();
        $this->view->formArrival = $formArrival;
        //Session
        $sessionMapper = new Model_SessionMapper();
        $this->view->sessions = $sessionMapper->fetchAll();
        $formSession = new Form_Session();
        $this->view->formSession = $formSession;
        //URL
        $urlForm = new Form_UrlForm();
        $this->view->urlForm = $urlForm;
        //DaysOfOperation
        $doMapper = new Model_DaysOperationMapper();
        $this->view->dos = $doMapper->fetchAll();
    }

    /**
     * Function that gets the times from a website
     * and imports the info into our database
     */
    public function urlparserAction() {
        $url = $this->_getParam("url");
        $html = file_get_html($url);
        $locationMapper = new Model_LocationMapper();
        $arrivalMapper = new Model_ArrivalMapper();
        $x = 0;
        foreach ($html->find('tr') as $tr) {
            if ($x > 0) {
                //LOCATION
                $location = new Model_Location();
                $desc = $tr->find('td span', 0)->plaintext;
                //name needs to be the substring of name - desc
                $name = $tr->find('td.text-left', 0)->plaintext;
                $name = substr($name, 0, strlen($name) - strlen($desc));
                $name = trim($name);
                // check if the name exists
                $locationMapper->findByName($name, $location);
                $locationID = $location->getId();
                if (isset($locationID)) {
                    // get id
                } elseif (strlen($name) > 0) {
                    // save, then get id
                    $location->setName($name);
                    $location->setDescription($desc);
                    $locationMapper->save($location);
                    $locationID = $locationMapper->getLastInsertedID();
                }
                foreach ($tr->find('.day') as $time) {
                    $t = $time->plaintext;
                    if (strlen($t) == 3) {
                        $t = '0' . $t;
                    }
                    $arrival = new Model_Arrival();
                    $arrival->setLine($this->_getParam("line"));
                    $arrival->setLocation($locationID);
                    $arrival->setTime($t);
                    $arrival->setSessionID($this->_getParam("sessionID"));
                    $arrivalMapper->save($arrival);
                }
                foreach ($tr->find('.night') as $time) {
                    $t = $time->plaintext;
                    $finalTime = 0;
                    if (strrpos($t, ':')) {
                        $h = explode(':', $t);
                        if ($h[0] < 12) {
                            $h[0] = $h[0] + 12;
                            $finalTime = $h[0] . ':' . $h[1];
                        }
                    } else {
                        if (strlen($t) == 4 && (substr($t, 0, 2) == '12')) {
                            $finalTime = $t;
                        } else {
                            $finalTime = $t + 1200;
                        }
                    }
                    $arrival = new Model_Arrival();
                    $arrival->setLine($this->_getParam("line"));
                    $arrival->setLocation($locationID);
                    $arrival->setTime($finalTime);
                    $arrival->setSessionID($this->_getParam("sessionID"));
                    $arrivalMapper->save($arrival);
                }
            }
            $x++;
        }
        return $this->_helper->redirector('unifiedadmin');
    }

    public function managearrivalAction() {
        $arrivalMapper = new Model_ArrivalMapper();
        $request = $this->getRequest();

        if ($this->getRequest()->isPost()) {
            $arrival = new Model_Arrival();
            if (strlen($this->_getParam('ArrivalID')) > 0) {
                $id = $this->_getParam('ArrivalID');
                $arrival->setID($id);
            }
            $arrival->setLine($request->getParam("line"));
            $arrival->setLocation($request->getParam("location"));
            $arrival->setTime($request->getParam("time"));
            $arrival->setSessionID($request->getParam('sessionID'));
            $arrivalMapper->save($arrival);
            createLocationsXml();
            return $this->_helper->redirector('unifiedadmin');
        }
    }

    public function managesessionAction() {
        $sessionMapper = new Model_SessionMapper();
        $request = $this->getRequest();
        if ($this->getRequest()->isPost()) {
            $session = new Model_Session();
            if (strlen($this->_getParam('SessionID')) > 0) {
                $id = $this->_getParam('SessionID');
                $session->setId($id);
            }
            $session->setDesc($request->getParam("DescriptionForm"));
            $session->setActive($request->getParam("Active"));
            $sessionMapper->save($session);
            return $this->_helper->redirector('unifiedadmin');
        }
    }

    public function managelinesAction() {
        $lineMapper = new Model_LineMapper();
        $dooMapper = new Model_DaysOperationMapper();
        $this->view->currentLines = $lineMapper->fetchAll();
        $request = $this->getRequest();
        $form = new Form_Line();

        if ($this->getRequest()->isPost()) {
            if ($form->isValid($request->getPost())) {
                $line = new Model_Line();
                if (strlen($this->_getParam('LineID')) > 0) {
                    $id = $this->_getParam('LineID');
                    $line->setId($id);
                    //delete all days for table being edited
                    $dooMapper->deleteByLineID($id);
                }
                $line->setName($this->_getParam('LineName'));
                $lineMapper->save($line);
                
                // get lastinsertedid
                if (strlen($this->_getParam('LineID')) <= 0)
                    $lineID = $lineMapper->getLastInsertedID();
                else {
                    $lineID = $this->_getParam('LineID');
                }
                $this->createLineXml($lineID);
                $this->createLocationsXml();
                $doo = new Model_DaysOperation();

                $doo->setLineID($lineID);
                if ($this->_getParam('M')) {
                    $doo->setDay(1);
                    $dooMapper->save($doo);
                }
                if ($this->_getParam('T')) {
                    $doo->setDay(2);
                    $dooMapper->save($doo);
                }
                if ($this->_getParam('W')) {
                    $doo->setDay(3);
                    $dooMapper->save($doo);
                }
                if ($this->_getParam('TH')) {
                    $doo->setDay(4);
                    $dooMapper->save($doo);
                }
                if ($this->_getParam('F')) {
                    $doo->setDay(5);
                    $dooMapper->save($doo);
                }
                if ($this->_getParam('S')) {
                    $doo->setDay(6);
                    $dooMapper->save($doo);
                }
                if ($this->_getParam('SU')) {
                    $doo->setDay(7);
                    $dooMapper->save($doo);
                }
                return $this->_helper->redirector('unifiedadmin');
            }
        }
        $this->view->form = $form;
    }

    public function crrAction() {
        $this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender();
        $operation = $this->_getParam("operation");
        $lineMapper = new Model_LineMapper();
        $locationMapper = new Model_LocationMapper();
        $arrivalMapper = new Model_ArrivalMapper();
        $sessionMapper = new Model_SessionMapper();
        switch ($operation) {
            case "remove":
                $id = $this->_getParam("lineID");
                $lineMapper->deleteLine($id);
                $myFile = getcwd() . "/xml/Line-" . $id . ".xml";
                unlink($myFile);
                break;
            case "removeLocation":
                $locationMapper->delete($this->_getParam('locationID'));
                break;
            case "removeArrival":
                $arrivalMapper->delete($this->_getParam('arrivalID'));
                break;
            case "removeSession":
                $sessionMapper->delete($this->_getParam('sessionID'));
                break;
        }
    }

    public function managelocationsAction() {
        $locationMapper = new Model_LocationMapper();
        $this->view->Locations = $locationMapper->fetchAll();
        $request = $this->getRequest();
        $form = new Form_Location();

        if ($this->getRequest()->isPost()) {
            if ($form->isValid($request->getPost())) {
                $location = new Model_Location();
                $location->setName($this->_getParam("LocationName"));
                $location->setDescription($this->_getParam("LocationDescription"));
                $location->setLat($this->_getParam("lat"));
                $location->setLng($this->_getParam("lng"));
                if (strlen($this->_getParam("LocationID")) > 0) {
                    $location->setId($this->_getParam("LocationID"));
                }
                $locationMapper->save($location);
                $this->createLocationsXml();
                return $this->_helper->redirector('unifiedadmin');
            }
        }
        $this->view->form = $form;
    }

    public function createLocationsXml() {
        $dom = new DOMDocument("1.0");
        $node = $dom->createElement("Locations");
        $parnode = $dom->appendChild($node);
        $arrivalMapper = new Model_ArrivalMapper();
        $locationMapper = new Model_LocationMapper();
        $lineMapper = new Model_LineMapper();
        $arrivals = $arrivalMapper->fetchAll($id);
        
        foreach ($arrivals as $arrival) {
            $location = new Model_Location();
            $locationMapper->find($arrival->getLocation(), $location);
            $locations[] = $location;
            $node = $dom->createElement("Location");
            if (isset($locationParent[$location->getId()])) {
                $node2 = $dom->createElement("Arrival");
                $timenode = $locationParent[$location->getId()]->appendChild($node2);
                $timenode->setAttribute("time", $arrival->getTime());
                $line = new Model_Line();
                $lineName = $lineMapper->find($arrival->getLine(), $line);
                $timenode->setAttribute("line", $line->getName());
            } else {
                $newnode = $parnode->appendChild($node);
                $newnode->setAttribute("id", $location->getId());
                $newnode->setAttribute("name", $location->getName());
                $newnode->setAttribute("lat", $location->getLat());
                $newnode->setAttribute("lng", $location->getLng());
                $newnode->setAttribute("desc", $location->getDescription());
                $locationParent[$location->getId()] = $newnode;
                $node2 = $dom->createElement("Arrival");
                $timenode = $newnode->appendChild($node2);
                $timenode->setAttribute("time", $arrival->getTime());
                $line = new Model_Line();
                $lineName = $lineMapper->find($arrival->getLine(), $line);
                $timenode->setAttribute("line", $line->getName());
            }
        }
        $myFile = getcwd() . "/xml/Locations.xml";
        $fh = fopen($myFile, 'w') or die("can't open file");
        fwrite($fh,  $dom->saveXML());
        fclose($fh);
    }
    
        public function createLineXml($line){
        $id = $line;
        $dom = new DOMDocument("1.0");
        $node = $dom->createElement("Locations");
        $parnode = $dom->appendChild($node);
        $arrivalMapper = new Model_ArrivalMapper();
        $locationMapper = new Model_LocationMapper();
        $lineMapper = new Model_LineMapper();
        $arrivals = $arrivalMapper->fetchAllByLineID($id);

        foreach($arrivals as $arrival){
            $location = new Model_Location();
            $locationMapper->find($arrival->getLocation(), $location);
            $locations[] = $location;
            $node = $dom->createElement("Location");
            
            if (isset($locationParent[$location->getId()])){
                $node2 = $dom->createElement("Arrival");
                $timenode = $locationParent[$location->getId()]->appendChild($node2);
                $timenode->setAttribute("time", $arrival->getTime());
                $line = new Model_Line();
                $lineName = $lineMapper->find($arrival->getLine(), $line);
                $timenode->setAttribute("line", $line->getName());
            }
            else {
                $newnode = $parnode->appendChild($node);
                $newnode->setAttribute("id", $location->getId());
                $newnode->setAttribute("name", $location->getName());
                $newnode->setAttribute("lat", $location->getLat());
                $newnode->setAttribute("lng", $location->getLng());
                $newnode->setAttribute("desc", $location->getDescription());
                $locationParent[$location->getId()] = $newnode;
                $node2 = $dom->createElement("Time");
                $timenode = $newnode->appendChild($node2);
                $timenode->setAttribute("time", $arrival->getTime());
                $line = new Model_Line();
                $lineName = $lineMapper->find($arrival->getLine(), $line);
                $timenode->setAttribute("line", $line->getName());
                }
        }
        $myFile = getcwd() . "/xml/Line-" . $id . ".xml";
        $fh = fopen($myFile, 'w') or die("can't open file");
        fwrite($fh,  $dom->saveXML());
        fclose($fh);
    }

}

