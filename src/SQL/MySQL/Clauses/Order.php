<?php

namespace Materia\Data\SQL\MySQL\Clauses;

/**
 * MySQL ORDER BY clause
 *
 * @package	Materia.Data
 * @author	Filippo Bovo
 * @link	https://lab.alchemica.io/projects/materia/
 **/

class Order implements \Materia\Data\SQL\MySQL\Clause {

	private $_data = [];

	/**
	 * @see \Materia\Data\SQL\MySQL\Clause::__toString()
	 **/
	public function __toString() : string {

		if ( empty( $this->_data ) ) {

			return '';

		}

		return ' ORDER BY ' . implode( ', ', $this->_data );

	}

	/**
	 * Set sorting
	 *
	 * @param	string	$column
	 * @param	boolean	$reverse
	 **/
	public function set( string $column, bool $reverse = FALSE ) {

		$this->_data[$column] = $column . ( $reverse ? ' DESC' : ' ASC' );

	}

	/**
	 * Returns sorting
	 *
	 * @return	array
	 **/
	public function get() : array {

		return $this->_data;

	}

}
