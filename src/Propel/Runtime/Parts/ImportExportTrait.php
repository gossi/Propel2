<?php

namespace Propel\Runtime\Parts;

use Propel\Runtime\Parser\AbstractParser;
use Propel\Runtime\Map\TableMap;

trait ImportExportTrait {
	
	abstract protected function getRawFields($keyType);
	
	// meant to be override by the model
	protected function getFilteredFields($fields) {
		return $fields;
	}
	
	/**
	 * Export the current object properties to a string, using a given parser format
	 * <code>
	 * $book = BookQuery::create()->findPk(9012);
	 * echo $book->exportTo('JSON');
	 *  => {"Id":9012,"Title":"Don Juan","ISBN":"0140422161","Price":12.99,"PublisherId":1234,"AuthorId":5678}');
	 * </code>
	 *
	 * @param  mixed   $parser                 A AbstractParser instance, or a format name ('XML', 'YAML', 'JSON', 'CSV')
	 * @param  boolean $includeLazyLoadColumns (optional) Whether to include lazy load(ed) columns. Defaults to TRUE.
	 * @return string  The exported data
	 */
	public function exportTo($parser, $includeLazyLoadColumns) {
		if (!$parser instanceof AbstractParser) {
			$parser = AbstractParser::getParser($parser);
		}
		
		return $parser->fromArray($this->toArray(TableMap::TYPE_PHPNAME, $includeLazyLoadColumns, array(), true));
	}
	
	/**
	 * Exports the object as an array.
	 *
	 * You can specify the key type of the array by passing one of the class
	 * type constants.
	 *
	 * @param     string  $keyType (optional) One of the class type constants TableMap::TYPE_PHPNAME, TableMap::TYPE_STUDLYPHPNAME,
	 *                    TableMap::TYPE_COLNAME, TableMap::TYPE_FIELDNAME, TableMap::TYPE_NUM.
	 *                    Defaults to TableMap::TYPE_PHPNAME.
	 * @param     boolean $includeLazyLoadColumns (optional) Whether to include lazy loaded columns. Defaults to TRUE.
	 * @param     array $alreadyDumpedObjects List of objects to skip to avoid recursion
	 * @param     boolean $includeForeignObjects (optional) Whether to include hydrated related objects. Default to FALSE.
	 *
	 * @return array an associative array containing the field names (as keys) and field values
	 */
	public function toArray($keyType = TableMap::TYPE_PHPNAME, $includeLazyLoadColumns = true, $alreadyDumpedObjects = array(), $includeForeignObjects = false)
	{
		// change 'User' to the appropriate model name
		
		if (isset($alreadyDumpedObjects['User'][$this->getPrimaryKey()])) {
			return '*RECURSION*';
		}
		$alreadyDumpedObjects['User'][$this->getPrimaryKey()] = true;
		$fields = $this->getFilteredFields($this->getRawFields($keyType));
		// some logic to do this:
		
// 		$result = array(
// 				$keys[0] => $this->getId(),
// 				$keys[1] => $this->getName(),
// 				$keys[2] => $this->getEmail(),
// 				$keys[3] => $this->getPassword(),
// 		);
		$result = [];
		$virtualColumns = $this->virtualColumns;
		foreach ($virtualColumns as $key => $virtualColumn) {
			$result[$key] = $virtualColumn;
		}
	
		if ($includeForeignObjects) {
			// some more logic to include foreign objects, maybe another abstract method
		}

		return $result;
	}
	

	/**
	 * Populate the current object from a string, using a given parser format
	 * <code>
	 * $book = new Book();
	 * $book->importFrom('JSON', '{"Id":9012,"Title":"Don Juan","ISBN":"0140422161","Price":12.99,"PublisherId":1234,"AuthorId":5678}');
	 * </code>
	 *
	 * @param mixed $parser A AbstractParser instance,
	 *                       or a format name ('XML', 'YAML', 'JSON', 'CSV')
	 * @param string $data The source data to import from
	 *
	 * @return $this|\PB\Planschbecken\Model\User The current object, for fluid interface
	 */
	public function importFrom($parser, $data)
	{
		if (!$parser instanceof AbstractParser) {
			$parser = AbstractParser::getParser($parser);
		}
	
		$this->fromArray($parser->toArray($data), TableMap::TYPE_PHPNAME);
	
		return $this;
	}
	
	/**
	 * Populates the object using an array.
	 *
	 * This is particularly useful when populating an object from one of the
	 * request arrays (e.g. $_POST).  This method goes through the column
	 * names, checking to see whether a matching key exists in populated
	 * array. If so the setByName() method is called for that column.
	 *
	 * You can specify the key type of the array by additionally passing one
	 * of the class type constants TableMap::TYPE_PHPNAME, TableMap::TYPE_STUDLYPHPNAME,
	 * TableMap::TYPE_COLNAME, TableMap::TYPE_FIELDNAME, TableMap::TYPE_NUM.
	 * The default key type is the column's TableMap::TYPE_PHPNAME.
	 *
	 * @param      array  $arr     An array to populate the object from.
	 * @param      string $keyType The type of keys the array uses.
	 * @return void
	 */
	public function fromArray($arr, $keyType = TableMap::TYPE_PHPNAME)
	{
		$fields = $this->getRawFields($keyType);
	
		// populate, based on the $fields
	}
}