<?php

namespace Propel\Generator\Builder\Model;

use CG\Model\PhpClass;
use CG\Model\PhpTrait;
use CG\Model\PhpInterface;

class RecordBuilder extends AbstractBuilder {
	
	private $record;
	
	/* (non-PHPdoc)
	 * @see \Propel\Generator\Builder\Model\AbstractBuilder::getTwigLoader()
	*/
	protected function getTwigLoader() {
		return new \Twig_Loader_Filesystem(__DIR__ . '/templates/record');
	}

	public function build() {
		$traitName = sprintf('%s\\Base\\%sTrait', 
				$this->getDatabase()->getNamespace(), 
				$this->getTable()->getPhpName());
		
		$this->record = new PhpClass();
		$this->record
			->setNamespace($this->getDatabase()->getNamespace())
			->setName($this->getTable()->getPhpName())
			->addTrait(new PhpTrait($traitName))
			->addInterface(new PhpInterface('Propel\\Runtime\\ActiveRecord\\ActiveRecordInterface'))
		;
		
		return $this->generateCode($this->record);
	}
}