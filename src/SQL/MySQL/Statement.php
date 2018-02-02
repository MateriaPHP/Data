<?php

namespace Materia\Data\SQL\MySQL;

/**
 * MySQL abstract statement
 *
 * @package	Materia.Data
 * @author	Filippo Bovo
 * @link	https://lab.alchemica.io/projects/materia/
 **/

abstract class Statement {

	protected $_dbh;

	protected $_table    = NULL;
	protected $_bindings = [];

	/**
	 * Constructor
	 *
	 * @param	Connection	$dbh
	 **/
	public function __construct( Connection $dbh ) {

		$this->_dbh = $dbh;

	}

	/**
	 * Converts statement object into SQL query
	 **/
	abstract public function __toString() : string;

	/**
	 * Set affected table
	 *
	 * @param	string	$table	table name
	 * @return	self
	 **/
	protected function setTable( string $table ) : self {

		$this->_table = $table;

		return $this;

	}

	/**
	 * Binds a value
	 *
	 * @param	mixed	$value
	 * @param	string	$prefix
	 * @return	mixed
	 **/
	protected function bind( $value, string $prefix ) {

		// Iterable value
		if ( is_array( $value ) || ( $value instanceof \Traversable ) ) {

			$placeholders = [];

			// Loop
			foreach ( $value as $k => $v ) {

				$placeholders[] = $this->bind( $v, $prefix );

			}

			// Returns an array of placeholders
			return $placeholders;

		}
		// Scalar
		else {

			$placeholder = $prefix . count( $this->_bindings );

			$this->_bindings[$placeholder] = $value;

			return $placeholder;

		}

	}

}
