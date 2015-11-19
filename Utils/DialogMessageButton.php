<?php


namespace XVEngine\Bundle\DialogBundle\Utils;


use XVEngine\Bundle\ButtonBundle\Component\Button\ButtonComponent;
use XVEngine\Bundle\DialogBundle\Component\Dialog\DialogComponent;
use XVEngine\Bundle\HTMLBundle\Component\Utils\HtmlComponent;
use XVEngine\Core\Component\AbstractComponent;
use XVEngine\Core\Handler\AbstractHandler;
use XVEngine\Core\Handler\ActionHandler;
use XVEngine\Core\Handler\ServiceHandler;

class DialogMessageButton {
    /**
     * @var string}null
     */
    protected $id = null;

    /**
     * @var string
     */
    protected $label;

    /**
     * @var AbstractHandler
     */
    protected $handler;

    /**
     * @var string
     */
    protected $dialogId;

    /**
     * DialogMessageButton constructor.
     * @author Krzysztof Bednarczyk
     * @param string $label
     * @param AbstractHandler $handler
     */
    public function __construct($label, $handler = null) {
        $this->setLabel($label);
        $this->setHandler($handler);
    }

    /**
     * Get id value
     * @author Krzysztof Bednarczyk
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set id value
     * @author Krzysztof Bednarczyk
     * @param string $id
     * @return  $this
     */
    public function setId($id)
    {
        $this->id = $id;
        return $this;
    }

    /**
     * Get dialogId value
     * @author Krzysztof Bednarczyk
     * @return string
     */
    public function getDialogId()
    {
        return $this->dialogId;
    }

    /**
     * Set dialogId value
     * @author Krzysztof Bednarczyk
     * @param string $dialogId
     * @return  $this
     */
    public function setDialogId($dialogId)
    {
        $this->dialogId = $dialogId;
        return $this;
    }





    /**
     * Get label value
     * @author Krzysztof Bednarczyk
     * @return string
     */
    public function getLabel()
    {
        return $this->label;
    }

    /**
     * Set label value
     * @author Krzysztof Bednarczyk
     * @param string $label
     * @return  $this
     */
    public function setLabel($label)
    {
        $this->label = $label;
        return $this;
    }

    /**
     * Get handler value
     * @author Krzysztof Bednarczyk
     * @return AbstractHandler
     */
    public function getHandler()
    {
        return $this->handler;
    }

    /**
     * Set handler value
     * @author Krzysztof Bednarczyk
     * @param AbstractHandler|array $handler
     * @return  $this
     */
    public function setHandler($handler)
    {
        if(is_null($handler)){
            $this->handler = null;
            return $this;
        }
        if(is_array($handler)){
            $handler[0]->bindTo($handler[1]);
            $this->handler = $handler[0]();
        }

        if(is_callable($handler)){
            $this->handler = $handler();
        }

        if($handler instanceof AbstractHandler){
            $this->handler = $handler;
        }

        if(!($this->handler instanceof AbstractHandler)){
            throw new \InvalidArgumentException();
        }

        return $this;
    }


    /**
     * @author Krzysztof Bednarczyk
     * @return ButtonComponent
     */
    public function getView(){
        $view = new ButtonComponent($this->getId());
        $view->setText($this->getLabel());

        if($handler = $this->getHandler()){
            $view->on("onClick", $handler);
        }


        $view->on("onClick", function(){
            $action = new ActionHandler($this->getDialogId());
            $action->close();
            return $action;
        }, $this);

        return $view;
    }
}
