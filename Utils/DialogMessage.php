<?php


namespace XVEngine\Bundle\DialogBundle\Utils;


use XVEngine\Bundle\ButtonBundle\Component\Button\ButtonComponent;
use XVEngine\Bundle\DialogBundle\Component\Dialog\DialogComponent;
use XVEngine\Bundle\HTMLBundle\Component\Utils\HtmlComponent;
use XVEngine\Bundle\HTMLBundle\Component\Utils\HtmlComponentItem;
use XVEngine\Core\Component\AbstractComponent;
use XVEngine\Core\Handler\ActionHandler;
use XVEngine\Core\Handler\ServiceHandler;

class DialogMessage {

    /**
     * @var string
     */
    protected $id;

    /**
     *
     * @var AbstractComponent 
     */
    private $message;
    
    /**
     *
     * @var AbstractComponent 
     */
    private $header;
    
    /**
     *
     * @var string
     */
    private $width;


    /**
     * @var DialogMessageButton[]
     */
    private $buttons = [];


    /**
     * DialogMessage constructor.
     * @author Krzysztof Bednarczyk
     * @param string $id
     * @param string $header
     * @param string $message
     */
    public function __construct($id, $header = null, $message = null) {
        $this->setID($id);
        $header && $this->setHeader($header);
        $message && $this->setMessage($message);
    }


    /**
     * @author Krzysztof Bednarczyk
     * @param $id
     * @return $this
     */
    public function setID($id){
        $this->id = $id;
        return $this;
    }


    /**
     * @author Krzysztof Bednarczyk
     * @param $message
     * @return $this
     */
    public function setMessage($message){
        if($message instanceof AbstractComponent){
            $this->message = $message;
            return $this;
        }
        
        $view = new HtmlComponent();
        $view->setHTML("
            <div class='mt20px mb20px'> {$message} </div>
        ");
                
        $this->message = $view;
        return $this;
    }

    /**
     * @return HtmlComponent
     */
    public function getMessage(){
        return $this->message;
    }
    
    /**
     * 
     * @param string $width
     * @return DialogMessage
     */
    public function setWidth($width){
        $this->width = $width;
        
        return $this;
    }
    
    /**
     * 
     * @param AbstractComponent|string $header
     * @return DialogMessage
     */
    public function setHeader($header){
        if($header instanceof AbstractComponent){
            $this->header = $header;
            return $this;
        }
        $view = new HtmlComponent();
        $view->setHTML("
            <h1> {$header} </h1>
        ");
                
        $this->header = $view;
        return $this;
    }
    
    /**
     * 
     * @return DialogComponent
     */
    public function getView(){
        $view = new DialogComponent($this->id);
        $view->addClass("dialog-message");
        $view->setHeaderComponent($this->header);
        $view->setContentComponent($this->message);
        $this->buttons && $view->setFooterComponent($this->getFooterComponent());
        $this->width && $view->setWidth($this->width);
        return $view;
    }

    /**
     * @author Krzysztof Bednarczyk
     * @return bool|HtmlComponent
     */
    public function getFooterComponent(){

        $view = new HtmlComponent();
        $view->addClass("actions-btns");
        $html = "";
        $id = 1;
        foreach($this->buttons as  $button){
            $html .= "<button-{$id}></button-{$id}>";
            $view->addItem(new HtmlComponentItem("> button-{$id}", $button->getView()));
            $id++;
        }

        $view->setHTML($html);
        return $view;
    }



    /**
     * @author Krzysztof Bednarczyk
     * @return ServiceHandler
     */
    public function getHandler(){
        $service = new ServiceHandler("ui.sharedPlace");
        $service->addComponent($this->getView());
        return $service;
    }


    /**
     * @author Krzysztof Bednarczyk
     * @param DialogMessageButton $button
     * @return $this
     */
    public function addButton(DialogMessageButton $button){
        $this->buttons[] = $button;
        return $this;
    }
}
