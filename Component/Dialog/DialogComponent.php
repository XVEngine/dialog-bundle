<?php

namespace XVEngine\Bundle\DialogBundle\Component\Dialog;

use XVEngine\Core\Component\AbstractComponent;

class DialogComponent extends AbstractComponent {

    
    public function initialize() {
        $this->setComponentName('dialog.dialogComponent');
    }

    /**
     * 
     * @param int|string $width
     * @return DialogComponent
     */
    public function setWidth($width) {
        $this->setParam("width", $width);
        return $this;
    }
    
    /**
     * 
     * @param int|string $height
     * @return DialogComponent
     */
    public function setHeight($height) {
        $this->setParam("height", $height);
        return $this;
    }

    
    /**
     * 
     * @param AbstractComponent $component
     * @return DialogComponent
     */
    public function setHeaderComponent(AbstractComponent $component){
        $this->setParam('headerComponent', $component);
        return $this;
    }
    
    /**
     * 
     * @param AbstractComponent $component
     * @return DialogComponent
     */
    public function setContentComponent(AbstractComponent $component){
        $this->setParam('contentComponent', $component);
        return $this;
    }

    /**
     * @author Krzysztof Bednarczyk
     * @return AbstractComponent
     */
    public function getContentComponent(){
        return $this->getParam("contentComponent");
    }
    
    /**
     * 
     * @param AbstractComponent $component
     * @return DialogComponent
     */
    public function setFooterComponent(AbstractComponent $component){
        $this->setParam('footerComponent', $component);
        return $this;
    }
    
    /**
     * 
     * @param boolean $value
     * @return DialogComponent
     */
    public function setManualClose($value){
        $this->setParam("manualClose", !!$value);
        return $this;
    }
}
