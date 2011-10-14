<?php

/**
 * Line form
 */
class Form_UrlForm extends Zend_Form {
    
    public function init() {
        $this->setMethod('post');
        
        $lineMapper = new Model_LineMapper();
        $lines = $lineMapper->fetchAll();
        
        $this->setMethod('post');
        
        $lineSelect = new Zend_Form_Element_Select("line");
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
        $lineSelect->setRequired(true);
        $this->addElement($lineSelect);
        
        $sessionMapper = new Model_SessionMapper();
        $sessions = $sessionMapper->fetchAll();
        
        $sessionSelect = new Zend_Form_Element_Select("sessionID");
        $sessionSelect->addMultiOption("0", "- Select Session -");
        foreach($sessions as $session){
            $sessionSelect->addMultiOption($session->getId(), $session->getDesc());
        }
        $sessionSelect->setDecorators(array(
            'ViewHelper',
            'Description',
            'Label',
            'Errors'
        ));
        $sessionSelect->setRequired(true);
        $this->addElement($sessionSelect);

        $this->addElement('text', 'url', array(
            'label' => 'URL',
            'required' => true
        ));
        $this->url->setDecorators(array(
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
