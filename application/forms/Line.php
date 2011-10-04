<?php

/**
 * Line form
 */
class Form_Line extends Zend_Form {
    
    public function init() {
        $this->setMethod('post');

        $this->addElement('text', 'name', array(
            'label' => 'Line name',
            'required' => true
        ));
        $this->name->setDecorators(array(
            'ViewHelper',
            'Description',
            'Errors',
            array(array('data' => 'HtmlTag'), array('tag' => 'td')),
            array('Label', array('tag' => 'td')),
            array(array('row' => 'HtmlTag'), array('tag' => 'tr', 'openOnly' => true))
        ));
        
        $this->addElement('submit', 'submit', array(
            'ignore' => true,
            'label' => 'Submit'
        ));
        $this->submit->setDecorators(array(
               'ViewHelper',

               'Description',

               'Errors', array(array('data'=>'HtmlTag'), array('tag' => 'td',

               'colspan'=>'2','align'=>'center')),

               array(array('row'=>'HtmlTag'),array('tag'=>'tr', 'closeOnly'=>'true'))
        ));
    }
}
?>
