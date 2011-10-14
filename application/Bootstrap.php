<?php

/**
 * This is the bootstrap
 */
class Bootstrap extends Zend_Application_Bootstrap_Bootstrap
{
    
    //set top nav and left nav
    protected function _initNavigation() {
        $this->bootstrap('layout');
        $layout = $this->getResource('layout');
        $view = $layout->getView();

        //set top navigation
        $config = new Zend_Config_Xml(APPLICATION_PATH . "/configs/navigation.xml", "TopNavigation");
        $navigation = new Zend_Navigation($config);

        //pass to view
        $view->topNav = $navigation;

        //add to zend registry
        Zend_Registry::set('Zend_Navigation', $leftNavigation);
    }
    
    protected function _initAppAutoload()
    {
        $moduleLoad = new Zend_Application_Module_Autoloader(array(
            'namespace' => '',
            'basePath' => APPLICATION_PATH
        ));
    }


}

