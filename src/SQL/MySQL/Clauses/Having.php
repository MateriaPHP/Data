<?php

namespace Materia\Data\SQL\MySQL\Clauses;

/**
 * MySQL HAVING clause
 *
 * @package	Materia.Data
 * @author	Filippo Bovo
 * @link	https://lab.alchemica.io/projects/materia/
 **/

class Having implements \Materia\Data\SQL\MySQL\Clause {

	private $_data = [];

	/**
	 * @see \Materia\Data\SQL\MySQL\Clause::__toString()
	 **/
	public function __toString() : string {

		if ( empty( $this->_data ) ) {

			return '';

		}

		return ' HAVING ' . trim( implode( ' ', $this->_data ) );

	}

	/**
	 * Set HAVING condition
	 *
	 * @param	string	$column
	 * @param	string	$operator
	 * @param	mixed	$value
	 * @param	string	$chain
	 **/
	public function set( string $column, string $operator, string $value, string $chain = 'AND' ) {

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
	 * Set HAVING COUNT condition
	 *
	 * @param	string	$column
	 * @param	string	$operator
	 * @param	mixed	$value
	 **/
	public function count( string $column, string $operator, string $value ) {

		$this->set( 'COUNT( ' . $column . ' )', $operator, $value );

	}

	/**
	 * Set HAVING MAX condition
	 *
	 * @param	string	$column
	 * @param	string	$operator
	 * @param	mixed	$value
	 **/
	public function max( string $column, string $operator, string $value ) {

		$this->set( 'MAX( ' . $column . ' )', $operator, $value );

	}

	/**
	 * Set HAVING MIN condition
	 *
	 * @param	string	$column
	 * @param	string	$operator
	 * @param	mixed	$value
	 **/
	public function min( string $column, string $operator, string $value ) {

		$this->set( 'MIN( ' . $column . ' )', $operator, $value );

	}

	/**
	 * Set HAVING AVG condition
	 *
	 * @param	string	$column
	 * @param	string	$operator
	 * @param	mixed	$value
	 **/
	public function avg( string $column, string $operator, string $value ) {

		$this->set( 'AVG( ' . $column . ' )', $operator, $value );

	}

	/**
	 * Set HAVING SUM condition
	 *
	 * @param	string	$column
	 * @param	string	$operator
	 * @param	mixed	$value
	 **/
	public function sum( string $column, string $operator, string $value ) {

		$this->set( 'SUM( ' . $column . ' )', $operator, $value );

	}

}
