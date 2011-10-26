<?php

/**
 * Arrival form
 */
class Form_SelectLine extends Zend_Form {
    
    public function init() {
        $this->setMethod('post');
        
        $lineMapper = new Model_LineMapper();
        $lines = $lineMapper->fetchAll();
        
        $lineSelect = new Zend_Form_Element_Select("lineSelector");
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
        
    }
}
?>
