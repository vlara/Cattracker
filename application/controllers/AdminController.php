<?php
require_once 'Simpledom/simple_html_dom.php';
class AdminController extends Zend_Controller_Action
{

    public function init()
    {
        $contextSwitch = $this->_helper->getHelper('contextSwitch');
        $contextSwitch->addActionContext("crr", array('json'))
                      ->initContext();
    }

    public function indexAction()
    {
        // action body
    }
    
    public function unifiedadminAction(){
        //Line
        $lineMapper = new Model_LineMapper();
        $this->view->currentLines = $lineMapper->fetchAll();
        $formLine = new Form_Line();
        $this->view->formLine = $formLine;
        //Locations
        $locationMapper = new Model_LocationMapper();
        $this->view->Locations = $locationMapper->fetchAll();
        $formLocation= new Form_Location();
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
    }
    
    /**
     * Function that gets the times from a website
     * and imports the info into our database
     */
    public function urlparserAction(){
        $url = $this->_getParam("url");
        $html = file_get_html($url);
        $locationMapper = new Model_LocationMapper();
        $arrivalMapper = new Model_ArrivalMapper();
        $x = 0;
        foreach($html->find('tr') as $tr){
            if($x > 0){
                //LOCATION
                $location = new Model_Location();
                $desc = $tr->find('td span',0)->plaintext;
                //name needs to be the substring of name - desc
                $name = $tr->find('td.text-left',0)->plaintext;
                $name = substr($name, 0, strlen($name)-strlen($desc));
                $name = trim($name);
                // check if the name exists
                $locationMapper->findByName($name, $location);
                $locationID = $location->getId();
                if(isset($locationID)){
                    // get id
                } elseif(strlen($name) > 0) {
                    // save, then get id
                    $location->setName($name);
                    $location->setDescription($desc);
                    $locationMapper->save($location);
                    $locationID = $locationMapper->getLastInsertedID();
                }
                foreach($tr->find('.day') as $time){
                    $t = $time->plaintext;
                    if(strlen($t)==3){
                        $t = '0'.$t;
                    }
                    $arrival = new Model_Arrival();
                    $arrival->setLine($this->_getParam("line"));
                    $arrival->setLocation($locationID);
                    $arrival->setTime($t);
                    $arrival->setSessionID($this->_getParam("sessionID"));
                    $arrivalMapper->save($arrival);
                }
                foreach($tr->find('.night') as $time){
                    $t = $time->plaintext;
                    $finalTime = 0;
                    if(strrpos($t,':')){
                        $h = explode(':',$t);
                        if($h[0] < 12){
                            $h[0] = $h[0] + 12;
                            $finalTime = $h[0] . ':' . $h[1];
                        }
                    } else {
                        if(strlen($t)==4 && (substr($t,0,2) == '12')){
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
    
    public function managearrivalAction(){
        $arrivalMapper = new Model_ArrivalMapper();
        $request = $this->getRequest();
        print_r($request);
        //$form = new Form_Arrival();
        
        if($this->getRequest()->isPost()){
            //if($form->isValid($request->getPost())){
                $arrival = new Model_Arrival();
                $arrival->setLine($request->getParam("line"));
                $arrival->setLocation($request->getParam("location"));
                $arrival->setTime($request->getParam("time"));
                $arrival->setSessionID($request->getParam('sessionID'));
                print_r($arrival);
                $arrivalMapper->save($arrival);
                return $this->_helper->redirector('unifiedadmin');
            //}
        }
    }
    public function managesessionAction(){
        $sessionMapper = new Model_SessionMapper();
        $request = $this->getRequest();
        if ($this->getRequest()->isPost()){
            $session = new Model_Session();
            $session->setDesc($request->getParam("Description"));
            $session->setActive($request->getParam("Active"));
            $sessionMapper->save($session);
            return $this->_helper->redirector('unifiedadmin');
        }
    }
    public function managelinesAction()
    {
        // Query all 
        //$this->view->headScript()->appendFile("/js/deleteLine.js");
        //$this->view->headScript()->appendFile("/js/managelines.js");
        $lineMapper = new Model_LineMapper();
        $this->view->currentLines = $lineMapper->fetchAll();
        
        $request = $this->getRequest();
        $form = new Form_Line();
        
        if($this->getRequest()->isPost()) {
            if($form->isValid($request->getPost())) {
                $line = new Model_Line();
                $line->setName($this->_getParam('name'));
                $lineMapper->save($line);
                // get lastinsertedid
                $lineID = $lineMapper->getLastInsertedID();
                $doo = new Model_DaysOperation();
                $dooMapper = new Model_DaysOperationMapper();
                $doo->setLineID($lineID);
                // followed by a for loop for the days
                if($this->_getParam('M')){
                    $doo->setDay(1);
                    $dooMapper->save($doo);
                }
                if($this->_getParam('T')){
                    $doo->setDay(2);
                    $dooMapper->save($doo);
                }
                if($this->_getParam('W')){
                    $doo->setDay(3);
                    $dooMapper->save($doo);
                }
                if($this->_getParam('TH')){
                    $doo->setDay(4);
                    $dooMapper->save($doo);
                }
                if($this->_getParam('F')){
                    $doo->setDay(5);
                    $dooMapper->save($doo);
                }
                if($this->_getParam('S')){
                    $doo->setDay(6);
                    $dooMapper->save($doo);
                }
                if($this->_getParam('SU')){
                    $doo->setDay(7);
                    $dooMapper->save($doo);
                }
                
                //finally redirect
                return $this->_helper->redirector('unifiedadmin');
            }
        }
        
        $this->view->form = $form;
        
    }
    
    /* CRR Action to create, remove, rename a line */
    public function crrAction(){
        $this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender();

        // crrm = create, remove, rename, move operations
        $operation = $this->_getParam("operation");
        $lineMapper = new Model_LineMapper();
        $locationMapper = new Model_LocationMapper();
        $arrivalMapper = new Model_ArrivalMapper();
        $sessionMapper = new Model_SessionMapper();
        switch ($operation) {
            case "create":
                break;
            case "remove":
                $id = $this->_getParam("lineID");
                $lineMapper->deleteLine($id);
                break;
            case "rename":
                $data = array(
                    'id' => $this->_getParam('id'),
                    'name' => $this->_getParam('value')
                );
                $line = new Model_Line($data);
                $lineMapper->save($line);
                echo $this->_getParam('value');
                break;
            case "renameName":
                $location = new Model_Location();
                $locationMapper->find($this->_getParam('id'), $location);
                $location->setName($this->_getParam('value'));
                $locationMapper->save($location);
                echo $this->_getParam('value');
                break;
            case "renameLongitude":
                $location = new Model_Location();
                $locationMapper->find($this->_getParam('id'), $location);
                $location->setLng($this->_getParam('value'));
                $locationMapper->save($location);
                echo $this->_getParam('value');
                break;
            case "renameLatitude":
                $location = new Model_Location();
                $locationMapper->find($this->_getParam('id'), $location);
                $location->setLat($this->_getParam('value'));
                $locationMapper->save($location);
                echo $this->_getParam('value');
                break;
            case "renameDescription":
                $location = new Model_Location();
                $locationMapper->find($this->_getParam('id'), $location);
                $location->setDescription($this->_getParam('value'));
                $locationMapper->save($location);
                echo $this->_getParam('value');
                break;
            case "removeLocation":
                $locationMapper->delete($this->_getParam('locationID'));
                break;
            case "removeArrival":
                $arrivalMapper->delete($this->_getParam('arrivalID'));
                break;
            case "arrivalRenameLine":
                $arrival = new Model_Arrival();
                $arrivalMapper->find($this->_getParam('sessionID'), $arrival);
                print_r($arrival);
                $arrival->setLine($this->_getParam('value'));
                $arrivalMapper->save($arrival);
                break;
            case "removeSession":
                $sessionMapper->delete($this->_getParam('sessionID'));
                break;
            case "editDescriptionSession":
                $session = new Model_Session();
                $sessionMapper->find($this->_getParam('id'), $session);
                $session->setDesc($this->_getParam('value'));
                $sessionMapper->save($session);
                echo $this->_getParam('value');
                break;
            case "editActiveSession":
                $session = new Model_Session();
                $sessionMapper->find($this->_getParam('id'), $session);
                $session->setActive($this->_getParam('value'));
                $sessionMapper->save($session);
                echo $this->_getParam('value');
                break;
        }
        
    }
    
    public function managelocationsAction(){
        $locationMapper = new Model_LocationMapper();
        $this->view->Locations = $locationMapper->fetchAll();
        $request = $this->getRequest();
        $form = new Form_Location();
        
        if($this->getRequest()->isPost()) {
            if($form->isValid($request->getPost())) {
                    $location = new Model_Location($form->getValues());
                    $locationMapper->save($location);
//                $line = new Model_Line($form->getValues());
//                $lineMapper->save($line);
                return $this->_helper->redirector('unifiedadmin');
            }
        }
        $this->view->form = $form;
    }


}

