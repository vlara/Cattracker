<?php

/**
 * Arrival form
 */
class Form_Arrival extends Zend_Form {
    
    public function init() {
        $this->setMethod('post');
        
        $this->addElement('text', 'time', array(
            'label' => 'time:',
            'required' => true
        ));
        $this->time->setDecorators(array(
            'ViewHelper',
            'Description',
            'Errors',
            array(array('data' => 'HtmlTag'), array('tag' => 'td')),
            array('Label', array('tag' => 'td')),
            array(array('row' => 'HtmlTag'), array('tag' => 'tr', 'openOnly' => true))
        ));
        
        $lineMapper = new Model_LineMapper();
        $lines = $lineMapper->fetchAll();
        
        $this->setMethod('post');
        
        $lineSelect = new Zend_Form_Element_Select("lines");
        $lineSelect->addMultiOption("0", "- Select Line -");
        foreach($lines as $line){
            $lineSelect->addMultiOption($line->getId(),$line->getName());
        }
        
        $lineSelect->setDecorators(array(
            'ViewHelper',
            'Description',
            'Label',
            'Errors'
        ));
        
        $this->addElement($lineSelect);
        
        $locationMapper = new Model_LocationMapper();
        $locations = $locationMapper->fetchAll();
        
        $this->setMethod('post');
        
        $locSelect = new Zend_Form_Element_Select("locations");
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
        
        $this->addElement('submit', 'submit', array(
            'ignore' => true,
            'label' => 'Submit'
        ));
        $this->submit->setDecorators(array(
            'ViewHelper',
            'Description',
            'Errors', array(array('data' => 'HtmlTag'), array('tag' => 'td',
                    'colspan' => '2', 'align' => 'center')),
            array(array('row' => 'HtmlTag'), array('tag' => 'tr', 'closeOnly' => 'true'))
        ));
    }
}
?>
