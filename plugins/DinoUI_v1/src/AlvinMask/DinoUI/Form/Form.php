<?php

namespace AlvinMask\DinoUI\Form;

use pocketmine\form\Form as IForm;
use pocketmine\Player;

abstract class Form implements IForm{

    protected $data = [];
    private $callable;

    public function __construct(?callable $callable){
     $this->callable = $callable;
    }

    public function getCallable():?callable{
     return $this->callable;
    }

    public function setCallable(?callable $callable){
     $this->callable = $callable;
    }

    public function handleResponse(Player $p, $data):void{
     $this->processData($data);
     $callable = $this->getCallable();
     if($callable !== null){
      $callable($p, $data);
     }
    }

    public function processData(&$data):void{
    }

    public function jsonSerialize(){
     return $this->data;
    }

}