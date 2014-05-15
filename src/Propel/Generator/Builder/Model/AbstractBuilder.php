<?php

namespace Propel\Generator\Builder\Model;

use Propel\Generator\Builder\DataModelBuilder;
use Propel\Generator\Model\Table;
use CG\Core\CodeGenerator;
use CG\Model\AbstractPhpStruct;
use CG\Model\PhpMethod;

class AbstractBuilder extends DataModelBuilder {
	
	private $_call;
	
	/**
	 * 
	 * @var \Twig_Environment
	 */
	protected $twig;
	
	/**
	 * 
	 * @var CodeGenerator
	 */
	protected $generator;
	
	
	/* (non-PHPdoc)
	 * @see \Propel\Generator\Builder\DataModelBuilder::__construct()
	 */
	public function __construct(Table $table) {
		parent::__construct($table);

		$this->twig = new \Twig_Environment($this->getTwigLoader());
		
		// generator shouldn't be generated with each RecordBuilder
		// another place 'outside' would be better, but also would be better to not instantiate
		// one builder per model
		$this->generator = new CodeGenerator();
	}
	
	/**
	 * 
	 * @return \Twig_LoaderInterface
	 */
	abstract protected function getTwigLoader();

	
	protected function addMagicCall($content) {
		$this->_call .= "\n" . $content;
	}
	
	protected function reset() {
		$this->_call = '';
	}

	public abstract function build();
	
	protected function generateCode(AbstractPhpStruct $struct) {
		if (!empty($this->_call)) {
			$call = new PhpMethod('__call');
			$call->setBody($this->_call);
			$struct->addMethod($call);
		}
		
		return $this->generator->generateCode($struct);
	}

	
	/**
	 * Returns the tableMap classname for current table.
	 * This is the classname that is used whenever object or tablemap classes want
	 * to invoke methods of the object classes.
	 * @param  boolean $fqcn
	 * @return string  (e.g. 'My')
	 */
	public function getTableMapClassName($fqcn = false) {
		$builder = $this->getTableMapBuilder();
		
		if ($fqcn) {
			return $builder->getClassName();
		}
		
		return $builder->getUnqualifiedClassName();
	}
}