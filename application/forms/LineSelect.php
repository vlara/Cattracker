<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
class Form_LineSelect extends Zend_Form {
    
    public function init() {
        $lineMapper = new Model_LineMapper();
        $lines = $lineMapper->fetchAll();
        
        $this->setMethod('post');
        
        $lineSelect = new Zend_Form_Element_Select("id");
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
a