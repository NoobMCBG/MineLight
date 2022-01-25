<?php

namespace AlvinMask\DinoUI\Form;

class CustomForm extends Form{

    private $labelMap = [];

    public function __construct(?callable $callable){
     parent::__construct($callable);
     $this->data["type"] = "custom_form";
     $this->data["title"] = "";
     $this->data["content"] = [];
    }

    public function processData(&$data):void{
     if(is_array($data)){
      $new = [];
      foreach ($data as $i => $v){
       $new[$this->labelMap[$i]] = $v;
      }
      $data = $new;
     }
    }

    public function setTitle(string $title):void{
     $this->data["title"] = $title;
    }

    public function addInput(string $text):void{
     $this->addContent(["type" => "input", "text" => $text, "placeholder" => "", "default" => null]);
     $this->labelMap[] = count($this->labelMap);
    }

    public function addDropdown(string $text, array $options):void{
     $this->addContent(["type" => "dropdown", "text" => $text, "options" => $options, "default" => null]);
     $this->labelMap[] = count($this->labelMap);
    }

    private function addContent(array $content):void{
     $this->data["content"][] = $content;
    }

}