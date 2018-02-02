<?php

namespace Materia\Data\SQL\MySQL\Clauses;

/**
 * MySQL WHERE clause
 *
 * @package Materia.Data
 * @author  Filippo Bovo
 * @link    https://lab.alchemica.io/projects/materia/
 **/

class Where implements \Materia\Data\SQL\MySQL\Clause {

	private $_data = [];

	/**
	 * @see \Materia\Data\SQL\MySQL\Clause::__toString()
	 **/
	public function __toString() : string {

		if ( empty( $this->_data ) ) {

			return '';

		}

		return ' WHERE ' . implode( ' ', $this->_data );

	}

	/**
	 * Set condition
	 *
	 * @param	string	$column
	 * @param	string	$operator
	 * @param	mixed	$value
	 * @param	string	$chain
	 **/
	public function set( string $column, string $operator, $value, string $chain = 'AND' ) {

		// Handles NULL value
		if ( $value === NULL ) {

			$operator =	in_array( $operator, [ '=', 'IS' ] ) ? 'IS' : ( in_array( $operator, [ ' != ', '<>', 'NOT IS' ] ) ? 'NOT IS' : $operator );
			$value    = 'NULL';

		}

		// Not empty, prepend the chain type
		if ( $this->_data ) {

			$this->_data[] = $chain . ' ' . $column . ' ' . $operator . ' ' . $value;

		}
		// Empty
		else {

			$this->_data[] = $column . ' ' . $operator . ' ' . $value;

		}

	}

	/**
	 * Set IN condition
	 *
	 * @param	string	$column
	 * @param	string	$operator
	 * @param	mixed	$value
	 * @param	string	$chain
	 **/
	public function in( string $column, bool $operator, array $values, string $chain = 'AND' ) {

		// Not empty, prepend the chain type
		if ( $this->_data ) {

			$this->_data[] = $chain . ' ' . $column . ' ' . ( $operator ? 'IN' : 'NOT IN' ) . ' (' . implode( ', ', $values ) . ')';

		}
		// Empty
		else {

			$this->_data[] = $column . ' ' . ( $operator ? 'IN' : 'NOT IN' ) . ' (' . implode( ', ', $values ) . ')';

		}

	}

}
