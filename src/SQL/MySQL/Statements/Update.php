<?php

namespace Materia\Data\SQL\MySQL\Statements;

/**
 * MySQL UPDATE statement
 *
 * @package	Materia.Data
 * @author	Filippo Bovo
 * @link	https://lab.alchemica.io/projects/materia/
 **/

class Update extends \Materia\Data\SQL\MySQL\Statement {

	/**
	 * Constructor
	 *
	 * @param	\Materia\Data\SQL\MySQL\Connection	$dbh
	 * @param	string								$table
	 **/
	public function __construct( \Materia\Data\SQL\MySQL\Connection $dbh, string $table ) {

		parent::__construct( $dbh );

		// TODO: move away from constructor clauses initialization
		$this->_where = new \Materia\Data\SQL\MySQL\Clauses\Where();
		$this->_order = new \Materia\Data\SQL\MySQL\Clauses\Order();
		$this->_limit = new \Materia\Data\SQL\MySQL\Clauses\Limit();

		$this->setTable( $table );

	}

	/**
	 * @return	string
	 **/
	public function __toString() : string {

		if ( empty( $this->_table ) ) {

			throw new \RuntimeException( 'No table set for update' );

		}

		if ( empty( $this->_bindings ) ) {

			throw new \RuntimeException( 'Missing values for update' );

		}

		$bound = array_filter( $this->_bindings, function( $v, $k ) {

			return strpos( $k, ':value' ) === 0;

		}, ARRAY_FILTER_USE_BOTH );
		$map   = array_map(
			function( $n, $m ) {

				return $n ? $n . ' = ' . $m : '';

			},
			$this->_columns,
			array_keys( $bound )
		);

		$sql  = 'UPDATE ' . $this->_dbh->getPrefix() . $this->_table;
		$sql .= ' SET ' . implode( ' , ', $map );
		$sql .= $this->_where;
		$sql .= $this->_order;
		$sql .= $this->_limit;

		return $sql;

	}

	/**
	 */
	public function values( array $values ) : self {

		$this->_columns = array_keys( $values );

		$this->bind( array_values( $values ), ':value' );

		return $this;

	}

	/**
	 * WHERE clause
	 *
	 * @param	string	$column
	 * @param	string	$operator
	 * @param	mixed	$value
	 * @param	boolean	$is_column
	 * @return	self
	 **/
	public function where( string $column, string $operator, $value, $is_column = FALSE ) : self {

		return $this->whereAnd( $column, $operator, $value, $is_column );

	}

	/**
	 * WHERE clause, AND chaining
	 *
	 * @param	string	$column
	 * @param	string	$operator
	 * @param	mixed	$value
	 * @param	boolean	$is_column
	 * @return	self
	 **/
	public function whereAnd( string $column, string $operator, $value, $is_column = FALSE ) : self {

		if ( $is_column ) {

			$this->_where->set( $column, $operator, $value, 'AND' );

		}
		else {

			$this->_where->set( $column, $operator, $this->bind( $value, ':where' ), 'AND' );

		}

		return $this;

	}

	/**
	 * WHERE clause, OR chaining
	 *
	 * @param	string	$column
	 * @param	string	$operator
	 * @param	mixed	$value
	 * @param	boolean	$is_column
	 * @return	self
	 **/
	public function whereOr( string $column, string $operator, $value, $is_column = FALSE ) : self {

		if ( $is_column ) {

			$this->_where->set( $column, $operator, $value, 'OR' );

		}
		else {

			$this->_where->set( $column, $operator, $this->bind( $value, ':where' ), 'OR' );

		}

		return $this;

	}

	/**
	 * ORDER clause
	 *
	 * @param	string	$column
	 * @param	boolean	$reverse
	 * @return	self
	 **/
	public function order( string $column, bool $reverse = FALSE ) {

		$this->_order->set( $column, $reverse );

		return $this;

	}

	/**
	 * LIMIT clause
	 *
	 * @param	integer	$count
	 * @param	integer	$offset
	 * @return	self
	 **/
	public function limit( int $count, int $offset = 0 ) {

		$this->_limit->set( $count, $offset );

		return $this;

	}

	/**
	 * Executes SQL query and returns number of updated rows
	 *
	 * @return	int
	 **/
	public function execute() : int {

		$query = $this->__toString();

		try {
			// Prepare
			$stmh   = $this->_dbh->prepare( $query );
			// Execute the query
			$result = $stmh->execute( $this->_bindings );

			if ( $result ) {

				return $stmh->rowCount();

			}

		}
		catch ( \PDOException $e ) {

			throw new \RuntimeException( $e->getMessage(), $e->getCode() );

		}

		return 0;

	}

}
