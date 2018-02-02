<?php

namespace Materia\Data\SQL\MySQL\Clauses;

/**
 * MySQL GROUP BY clause
 *
 * @package	Materia.Data
 * @author	Filippo Bovo
 * @link	https://lab.alchemica.io/projects/materia/
 **/

class Group implements \Materia\Data\SQL\MySQL\Clause {

	private $_data = [];

	/**
	 * @see \Materia\Data\SQL\MySQL\Clause::__toString()
	 **/
	public function __toString() : string {

		if ( empty( $this->_data ) ) {

			return '';

		}

		return ' GROUP BY ' . implode( ', ', $this->_data );

	}

	/**
	 * Set grouping columns
	 *
	 * @param	array	$columns
	 **/
	public function set( array $columns ) {

		$this->_data = array_merge( $this->_data, array_values( $columns ) );

	}

	/**
	 * Returns grouped columns
	 *
	 * @return	array
	 **/
	public function get() : array {

		return $this->_data;

	}

}
