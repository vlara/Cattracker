<?php

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
                $line = new Model_Line($form->getValues());
                $lineMapper->save($line);
                return $this->_helper->redirector('managelines');
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
                return $this->_helper->redirector('managelocations');
            }
        }
        $this->view->form = $form;
    }


}
