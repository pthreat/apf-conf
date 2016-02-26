<?php 

	
	//Interfaces & Traits
	///////////////////////////////////////////////////////////////////

	interface Renderizable{

		public function render();

	}

	interface Decoratable{

		public function setOutputDecorator($decorator);
		public function getOutputDecorator();

	}

	trait DecoratableTrait{

		public function setOutputDecorator($log){

			$this->outputDecorator	=	$log;
			return $this;

		}

		public function getOutputDecorator(){

			//If context is web, choose another output decorator instead of Log

			if($this->outputDecorator === NULL){

				$this->setOutputDecorator(new \apf\core\Log());

			}

			return $this->outputDecorator;

		}

	}

	/////////////////////////////////////////////////////////////

	abstract class Form implements Renderizable{

		public function __construct(){

			$this->configure();

		}

		//According to SAPI context get 

		public static function factory(){
		}

		abstract public function configure();

		public function getElements(){

			return $this->elements;

		}

		public function render(){

			$output	=	'';

			foreach($this->elements as $widget){

				$output	=	sprintf('%s%s',$output,$widget);

			}

			return $output;

		}

		public function __toString(){

			return $this->render();

		}

	}

	abstract class CliForm extends Form{

		public function addElement(CliElement $widget){

				$this->elements[]	=	$widget;
				return $this;

		}

		public function render(){

			$output	=	'';

			foreach($this->elements as $widget){

				echo sprintf('%s) %s (%s)',$output,$widget);

			}

			return $output;

		}

	}

	abstract class WebForm extends Form{

		public function addElement(WebElement $widget){

			$this->elements[]	=	$widget;
			return $this;

		}

	}

	////////////////////////////////////////////////////
	//ABSTRACT FORM ELEMENTS
	////////////////////////////////////////////////////

	class Attribute{

		private	$name			=	NULL;
		private	$value		=	NULL;
		private	$separator	=	'=';

		public function __construct($name,$value){

			$this->setName($name);
			$this->setValue($value);

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

		public function __toString(){

			return sprintf('%s%s%s',$this->name,$this->separator,$this->value);

		}

	}

	abstract class Element implements Renderizable{

		private	$name					=	NULL;
		private	$value				=	NULL;
		private	$description		=	NULL;
		private	$validator			=	NULL;
		private	$attributes			=	NULL;

		public function __construct(Array $config){

			if(array_key_exists('name',$config)){

				$this->setName($config['name']);

			}

			if(array_key_exists('value',$config)){

				$this->setValue($config['value']);

			}
			
			if(array_key_exists('validator',$config)){

				$this->setValidator($config['validator']);

			}

			if(array_key_exists('description',$config)){

				$this->setDescription($config['description']);

			}

			if(array_key_exists('attributes',$config){

				$this->setAttributes($config['attributes']);

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

		public function addAttribute(Attribute $attribute){

			$this->attributes[]	=	$attribute;
			return $this;

		}

		public function getAttributes(){

			return $this->attributes;

		}

		public function setAttributes(Array $attributes){

			foreach($attributes as $attribute){

				$this->addAttribute($attribute);

			}

			return $this;

		}

		public function setDescription($description){

			$this->description	=	$description;
			return $this;

		}

		public function getDescription(){

			return $this->description;

		}

		public function setValidator(Validator $validator){

			$this->validator	=	$validator;
			return $this;

		}
	
		public function getValidator(){

			return $this->validator;

		}

		public function __toString(){

			return $this->render();

		}

	}

	abstract class CliElement extends Element{

		public function parseAttributes(){

			foreach($this->attributes as $attr){

				switch($attr->getName()){

					case 'color':
					break;

					default:
					break;

				}

			}

		}

	}

	abstract class InputElement extends Element{

	}

	abstract class SelectElement extends Element{

		private $options	=	Array();

		public function __construct(Array $parameters=Array()){

			parent::__construct($parameters);

			if(isset($parameters['options'])){

				foreach($parameters['options'] as $option){

					$option	=	new OptionElement($option);
					$this->addOption($option);

				}

			}

		}

		public function addOption(OptionElement $option){

			$this->options[]	=	$option;

		}

		public function getOptions(){

			return $this->options;

		}

	}

	abstract class OptionElement extends Element{

		public function __construct(Array $parameters=Array()){

			parent::__construct($parameters);

		}

	}

	//CLI SPECIFIC WIDGETS
	//////////////////////////////////////

	class PromptElement extends CliElement{

		private	$prompt	= '>';
		private	$buffer	=	1024;

		public function __construct(Array $parameters=Array()){

			parent::__construct($parameters);

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

		public function render(){
		}

	}

	class TestForm extends Form{

		public function configure(){

			$this->addElement(
									new SelectElement(
															Array(
																	  'options'=>Array(
																							  Array(
																									  'name'				=>'a',
																									  'description'	=>'Set value of A'
																							  )
																	  )
															)
									)
			);

		}

	}


