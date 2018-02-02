<?php

namespace Materia\Data\SQL\MySQL\Clauses;

/**
 * MySQL LIMIT clause
 *
 * @package	Materia.Data
 * @author	Filippo Bovo
 * @link	https://lab.alchemica.io/projects/materia/
 **/

class Offset implements \Materia\Data\SQL\MySQL\Clause {

	private $_data = NULL;

	/**
	 * @see \Materia\Data\SQL\MySQL\Clause::__toString()
	 **/
	public function __toString() : string {

		if ( is_null( $this->_data ) ) {

			return '';

		}

		return ' OFFSET ' . $this->_data;

	}

	/**
	 * Set offset
	 *
	 * @param	integer	$number
	 **/
	public function set( int $number ) {

		$this->_data = $number;

	}

	/**
	 * Returns offset
	 *
	 * @return	array
	 **/
	public function get() {

		return $this->_data;

	}

}
