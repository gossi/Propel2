<?php

namespace Propel\Generator\Builder\Model;

use CG\Model\PhpTrait;
use CG\Model\PhpMethod;
use CG\Core\CodeGenerator;
use CG\Model\PhpParameter;

class BaseRecordBuilder extends AbstractBuilder {
	
	private $record;
	
	
	/* (non-PHPdoc)
	 * @see \Propel\Generator\Builder\Model\AbstractBuilder::getTwigLoader()
	 */
	protected function getTwigLoader() {
		return new \Twig_Loader_Filesystem(__DIR__ . '/templates/record');
	}

	
	public function build() {
		$this->record = new PhpTrait();
		$this->record
			->setNamespace($this->getDatabase()->getNamespace())
			->setName($this->getTable()->getPhpName())

		// add use statements
			->addUseStatement($this->getTableMapBuilder()->getClassName())
		
		;

		
		// modify the record
		$this->addAccessors();
		$this->addMutators();
		$this->addImportExport();
		$this->applyBehaviors();
		
		// collect stuff from hooks preSave, postSave, etc. and add it

		return $this->generateCode($this->record);
	}
	
	protected function addImportExport() {
		$this->record->addTrait('Propel\\Runtime\\Parts\\ImportExportTrait');
		
		// getRawFields()
		$keyTypeParam = new PhpParameter('keyType');
		$keyTypeParam->setType('string', '(optional) One of the class type constants '
				. 'TableMap::TYPE_PHPNAME, TableMap::TYPE_STUDLYPHPNAME, TableMap::TYPE_COLNAME, '
				. 'TableMap::TYPE_FIELDNAME, TableMap::TYPE_NUM. Defaults to TableMap::TYPE_PHPNAME');
		
		$getRawFields = new PhpMethod();
		$getRawFields
			->addParameter($keyTypeParam)
			->setBody($this->twig->render('getRawFields.twig', ['map' => $this->getTableMapClassName()]))
		;
		
		$this->record->addMethod($getRawFields);
		
		// add portion to __call() method
		$this->addMagicCall($this->twig->render('__call_import_export.twig'));
	}
	
	protected function addAccessors() {
		
	}
	
	protected function addMutators() {
		
	}
	
	protected function applyBehaviors() {
		foreach ($this->getTable()->getBehaviors() as $behavior) {
			// builder should be instanceof RecordBuilderInterface
			$builder = $behavior->getObjectBuilderModifier();
			
			$builder->modifyObject($this->record); // not present yet
			
			// iterate over hooks preSave, postSave, etc.
		}
	}

}