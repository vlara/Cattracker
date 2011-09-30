<?php

/**
 * Line form
 */
class Form_Line extends Zend_Form {
    
    public function init() {
        $this->setMethod('post');

        $this->addElement('text', 'name', array(
            'label' => 'Location name',
            'required' => true
        ));
        
        $this->addElement('submit', 'submit', array(
            'ignore' => true,
            'label' => 'Submit'
        ));
    }
}
?>
