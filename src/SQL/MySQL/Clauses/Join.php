<?php

namespace Materia\Data\SQL\MySQL\Clauses;

/**
 * MySQL JOIN clause
 *
 * @package	Materia.Data
 * @author	Filippo Bovo
 * @link	https://lab.alchemica.io/projects/materia/
 **/

class Join implements \Materia\Data\SQL\MySQL\Clause {

	private $_data = [];

	/**
	 * @see \Materia\Data\SQL\MySQL\Clause::__toString()
	 **/
	public function __toString() : string {

		if ( empty( $this->_data ) ) {

			return '';

		}

		return ' ' . implode( ' ', $this->_data );

	}

	/**
	 * JOIN
	 *
	 * @param	string	$table
	 * @param	string	$first
	 * @param	string	$operator
	 * @param	string	$second
	 * @param	string	$type
	 **/
	public function join( string $table, string $first, string $operator, string $second, string $type = 'INNER' ) {

		$this->_data[] = $type . ' JOIN ' . $table . ' ON ' . $first . ' ' . $operator . ' ' . $second;

	}

	/**
	 * LEFT OUTER JOIN
	 *
	 * @param	string	$table
	 * @param	string	$first
	 * @param	string	$operator
	 * @param	string	$second
	 **/
	public function left( string $table, string $first, string $operator, string $second ) {

		$this->join( $table, $first, $operator, $second, 'LEFT OUTER');

	}

	/**
	 * RIGHT OUTER JOIN
	 *
	 * @param	string	$table
	 * @param	string	$first
	 * @param	string	$operator
	 * @param	string	$second
	 **/
	public function right( string $table, string $first, string $operator, string $second ) {

		$this->join( $table, $first, $operator, $second, 'RIGHT OUTER' );

	}

	/**
	 * FULL OUTER JOIN
	 *
	 * @param	string	$table
	 * @param	string	$first
	 * @param	string	$operator
	 * @param	string	$second
	 **/
	public function full( string $table, string $first, string $operator, string $second ) {

		$this->join( $table, $first, $operator, $second, 'FULL OUTER' );

	}

}
