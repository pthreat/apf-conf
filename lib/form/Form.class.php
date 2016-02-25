<?php
	///////////////////////////////////////////////////
	//The form
	///////////////////////////////////////////////////

	abstract class Form{

		private $widgets	=	Array();

		public function addWidget(Widget $widget){

			$this->widgets[]	=	$widget;

		}

		public function getWidgets(){

			return $this->widgets;

		}

		public function render(){

			foreach($this->widgets as $widget){

				$widget->render();

			}

		}

		abstract public function configure();

	}

	class DirectoriesForm extends Form(){

		public function configure(){
		}

	}

	///////////////////////////////////////////////////
	//The widgets
	///////////////////////////////////////////////////

	abstract class Widget{

		abstract public function render();

	}

	abstract class CliWidget extends Widget{

		private	$outputDecorator	=	NULL;

		public function setOutputDecorator(LogInterface $log){

			$this->log	=	$log;

		}

		public function getOutputDecorator(){

			if(!$this->log){

				$this->log	=	new Log();

			}

			return $this->log;

		}

		public function setColor($color){

			$this->color	=	$color;
			return $this;

		}

		public function getColor(){

			return $this->color;

		}

		abstract protected function __render();

		public function render(){

			if($this->color){

				echo Ansi::colorize('',$this->color);

			}

			$this->render();

		}

	}

	abstract class PromptableWidget extends CliWidget{

		private	$prompt	=	NULL;

		public function setPrompt($prompt){

			$this->prompt	=	$prompt;

		}

		public function getPrompt(){

			return $this->prompt	?	$this->prompt	:	'>';

		}

	}

	class MenuOption{

		private	$name		=	NULL;
		private	$widget	=	NULL;
		private	$title	=	NULL;

		public function setName($name){

			$this->name	=	$name;
			return $this;

		}

		public function getName(){

			return $this->name;

		}

		public function setTitle($title){

			$this->title	=	$title;
			return $this;

		}

		public function getTitle(){

			return $this->title;

		}

		public function render(){

			return $this->getLog()->log(sprintf('%s) %s',$this->getName(),$this->getTitle()),0);

		}

		public function __toString(){

			return $this->render();

		}

	}

	class MenuWidget extends PromptableWidget{

		private	$options	=	Array();

		public function addOption(MenuOption $option){

			$this->option	=	$option;
			return $this;

		}

		public function getOptions(){

			return $this->options;

		}

		public function render(){

			if(!$this->prompt){

				$this->setPrompt((new Input())->setPrompt('>'));

			}

			foreach($this->options as $option){

				echo $option;

			}

			$this->prompt->render();

		}

	}

	class Input extends PromptableWidget{

		public function render(){

			return Cmd::input($this->prompt,$this->getLog());

		}

	}

	class Select extends PromptableWidget{

		private	$options	=	Array();

		public function addValue($name,$value){

			$this->options[$name]	=	$value;

		}

		public function getValues(){

			return $this->values;

		}

	}

