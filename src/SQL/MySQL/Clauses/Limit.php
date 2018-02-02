<?php

namespace Materia\Data\SQL\MySQL\Clauses;

/**
 * MySQL LIMIT clause
 *
 * @package Materia.Data
 * @author  Filippo Bovo
 * @link    https://lab.alchemica.io/projects/materia/
 **/

class Limit implements \Materia\Data\SQL\MySQL\Clause {

	private $_data = [ 1000 ];

	/**
	 * @see \Materia\Data\SQL\MySQL\Clause::__toString()
	 **/
	public function __toString() : string {

		if ( is_null( $this->_data ) ) {

			return '';

		}

		return ' LIMIT ' . implode( ', ', $this->_data );

	}

	/**
	 * Set limit
	 *
	 * @param	integer	$number
	 * @param	integer	$offset
	 **/
	public function set( int $number, int $offset = 0 ) {

		if ( $offset ) {

			$this->_data = [ $offset, $number ];

		}
		else {

			$this->_data = [ $number ];

		}

	}

	/**
	 * Returns limit
	 *
	 * @return	mixed
	 **/
	public function get() {

		return $this->_data;

	}

}
