<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
class Form_LocationsSelect extends Zend_form
{
    public function init()
    {
        $locationMapper = new Model_LocationMapper();
        $locations = $locationMapper->fetchAll();
        
        $this->setMethod('post');
        
        $locSelect = new Zend_Form_Element_Select("id");
        $locSelect->addMultiOption("0", "- Select Location -");
        foreach($locations as $location){
            $locSelect->addMultiOption($location->getId(), $location->getName());
        }
        $locSelect->setDecorators(array(
            'ViewHelper',
            'Description',
            'Label',
            'Errors'
        ));
        
        $this->addElement($locSelect);
    }
}
?>
