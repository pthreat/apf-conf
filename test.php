<?php 

	interface Renderizable{

		public function render();

	}

	abstract class Form implements Renderizable{

		public function render(){

			foreach($this->widgets as $widget){

				$widget->render();

			}

		}

	}

	abstract class Validator{

		abstract public function __construct(Array $parameters=Array());

		abstract public function validate(Widget $widget);

	}

	class InputValidator extends Validator{

		private	$options	=	Array();
		private	$empty	=	FALSE;
		private	$trim		=	TRUE;

		public function __construct(Array $parameters=Array()){

			$this->setOptions(array_key_exists('options',$parameters)	?	$parameters["options"]	:	$this->options);
			$this->setEmpty(array_key_exists('empty',$parameters)			?	$parameters["empty"]		:	$this->empty);
			$this->setTrim(array_key_exists('trim',$parameters)			?	$parameters["trim"]		:	$this->trim);

		}

		public function setTrim($boolean){

			$this->trim	=	(boolean)$trim;
			return $this;

		}

		public function getTrim(){

			return $this->trim;

		}

		public function setEmpty($boolean){

			$this->empty	=	(boolean)$boolean;
			return $this;

		}

		public function getEmpty(){

			return $this->empty;

		}

		public function setOptions(Array $options){

			$this->options	=	$options;
			return $this;

		}

		public function getOptions(){

			return $this->options;

		}

		public function validate(Widget $widget){

			$value	=	$this->trim	?	trim($widget->getValue())	:	$widget->getValue();
			
			
			if(sizeof($this->options) && !in_array($value,$this->options)){

				throw new \InvalidArgumentException("Invalid value \"{$widget->getValue()}\"");

			}

			if(!$this->empty && empty($widget->getValue())){

				throw new \InvalidArgumentException("Value can not be empty");

			}

			return $value;

		}	

	}

	abstract class Widget implements Renderizable{

		private	$name					=	NULL;
		private	$value				=	NULL;
		private	$validator			=	NULL;
		private	$outputDecorator	=	NULL;

		public function __construct(Array $config){

			if(array_key_exists('name',$config)){

				$this->setName($config['name']);

			}

			if(array_key_exists('decorator',$config)){

				$this->setOutputDecorator($config['decorator']);

			}

			if(array_key_exists('value',$config)){

				$this->setValue($config['value']);

			}
			
			if(array_key_exists('validator',$config)){

				$this->setValidator($config['validator']);

			}

		}

		public function setName($name){

			$this->name	=	$name;
			return $this;

		}

		public function getName(){

			return $this->name;

		}

		public function setValue($value){

			$this->value	=	$value;
			return $this;

		}

		public function getValue(){

			return $this->value;

		}

		public function setValidator(Validator $validator){

			$this->validator	=	$validator;
			return $this;

		}
	
		public function getValidator(){

			return $this->validator;

		}

		public function setOutputDecorator($log){

			$this->outputDecorator	=	$log;
			return $this;

		}

		public function getOutputDecorator(){

			if($this->outputDecorator === NULL){

				$this->setOutputDecorator(new \apf\core\Log());

			}

			return $this->outputDecorator;

		}

		abstract public function render();

	}

	abstract class CliWidget extends Widget{


	}

	abstract class PromptableWidget extends CliWidget implements Renderizable{

		private	$prompt	= '>';
		private	$buffer	=	1024;

		public function __construct(Array $parameters=Array()){

			$this->setBuffer(array_key_exists('buffer',$parameters)	?	$parameters['buffer']	:	$this->buffer);
			$this->setPrompt(array_key_exists('prompt',$parameters)	?	$parameters['prompt']	:	$this->prompt);

		}

		public function setPrompt($prompt){

			$this->prompt	=	$prompt;
			return $this;

		}

		public function getPrompt(){

			return $this->prompt;

		}

		public function setBuffer($buffer){

			$this->buffer	=	(int)$buffer;
			return $this;

		}

		public function getBuffer(){

			return $this->buffer;

		}

	}

	class InputWidget extends PromptableWidget{

		public function render(){

			$this->getOutputDecorator()->log(parent::getPrompt());

			$fp		=	fopen("php://stdin",'r');
			$value	=	fgets($fp,parent::getBuffer());
			fclose($fp);

			return $value;
			
		}

	}

	class OptionWidget extends CliWidget{

		private	$description	=	NULL;	
		private	$separator		=	') ';

		public function __construct(Array $parameters=Array()){

			parent::__construct($parameters);

			$this->setDescription(array_key_exists('description',$parameters) ? $parameters['description']	:	$this->description);	
			$this->setSeparator(array_key_exists('separator',$parameters) ? $parameters['separator']	:	$this->separator);	

		}

		public function setDescription($description){

			$this->description	=	$description;
			return $this;

		}

		public function getDescription(){

			return $this->description;

		}

		public function setSeparator($separator){

			$this->separator	=	$separator;
			return $this;

		}

		public function getSeparator(){

			return $this->separator;

		}

		public function render(){

			return $this->getOutputDecorator()->log(sprintf('%s%s%s',parent::getName(),$this->getSeparator(),$this->getDescription()));

		}

		public function __toString(){

			return $this->render();

		}

	}

	class SelectWidget extends InputWidget{

		private $options	=	Array();

		public function __construct(Array $parameters=Array()){

			parent::__construct($parameters);

			if(isset($parameters['options'])){

				foreach($parameters['options'] as $option){

					$option	=	new OptionWidget($option);
					$option->setOutputDecorator(parent::getOutputDecorator());

					$this->addOption($option);

				}

			}

		}

		public function addOption(OptionWidget $option){

			$this->options[]	=	$option;

		}

		public function getOptions(){

			return $this->options;

		}

		public function render(){

			foreach($this->options as $option){

				$option->render();

			}

			return parent::render();

		}

	}

	class TestForm extends Form{

		public function configure(){

		}

	}


