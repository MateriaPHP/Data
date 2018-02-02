<?php

namespace Materia\Data\SQL\MySQL\Statements;

/**
 * MySQL SELECT statement
 *
 * @package	Materia.Data
 * @author	Filippo Bovo
 * @link	https://lab.alchemica.io/projects/materia/
 **/

class Select extends \Materia\Data\SQL\MySQL\Statement implements \IteratorAggregate {

	protected $_dbh;
	protected $_join;
	protected $_where;
	protected $_group;
	protected $_having;
	protected $_order;
	protected $_limit;
	protected $_offset;
	protected $_result;

	protected $_columns   = [];
	protected $_distinct  = FALSE;
	protected $_aggregate = FALSE;

	/**
	 * Constructor
	 **/
	public function __construct( \Materia\Data\SQL\MySQL\Connection $dbh, array $columns = [] ) {

		parent::__construct( $dbh );

		$this->setColumns( $columns );

		// TODO: move away from constructor clauses initialization
		$this->_join   = new \Materia\Data\SQL\MySQL\Clauses\Join();
		$this->_where  = new \Materia\Data\SQL\MySQL\Clauses\Where();
		$this->_group  = new \Materia\Data\SQL\MySQL\Clauses\Group();
		$this->_having = new \Materia\Data\SQL\MySQL\Clauses\Having();
		$this->_order  = new \Materia\Data\SQL\MySQL\Clauses\Order();
		$this->_limit  = new \Materia\Data\SQL\MySQL\Clauses\Limit();
		$this->_offset = new \Materia\Data\SQL\MySQL\Clauses\Offset();

	}

	/**
	 * @return	string
	 **/
	public function __toString() : string {

		if ( empty( $this->_table ) ) {

			throw new \RuntimeException( 'No table set for selection' );

		}

		$sql  = $this->_distinct ? 'SELECT DISTINCT ' : 'SELECT ';
		$sql .= $this->_columns ? implode( ', ', $this->_columns ) : '*';
		$sql .= ' FROM ' . $this->_dbh->getPrefix() . $this->_table;
		$sql .= $this->_join;
		$sql .= $this->_where;
		$sql .= $this->_group;
		$sql .= $this->_having;
		$sql .= $this->_order;
		$sql .= $this->_limit;
		$sql .= $this->_offset;

		return $sql;

	}

	/**
	 * @see \IteratorAggregate::getIterator()
	 **/
	public function getIterator() {

		if ( !isset( $this->_result ) ) {

			$this->execute();

		}

		return new \IteratorIterator( $this->_result );

	}

	/**
	 **/
	protected function setColumns( array $columns ) : self {

		$this->_columns = array_merge( $this->_columns, $columns );
		$this->_columns = array_unique( $this->_columns );

		return $this;

	}

	/**
	 **/
	public function from( string $table ) : self {

		$this->setTable( $table );

		return $this;

	}

	/**
	 * @return	self
	 */
	public function distinct() : self {

		$this->_distinct = TRUE;

		return $this;

	}

	/**
	 **/
	public function expression( string $expression, string $column, string $alias = NULL ) : self {

		$this->_aggregate = TRUE;
		// Normalize
		$expression       = strtoupper( $expression );

		if ( in_array( $expression, [ 'COUNT', 'MIN', 'MAX', 'AVG', 'SUM' ] ) ) {

			$this->setColumns( [ $expression . '( ' . $column . ' )' . ( $alias ? ' AS ' . $alias : '' ) ] );

		}

		return $this;

	}

	/**
	 * JOIN
	 *
	 * @param	string	$table
	 * @param	string	$first
	 * @param	string	$operator
	 * @param	string	$second
	 * @param	string	$type
	 * @return	self
	 **/
	public function join( string $table, string $first, string $operator, string $second, string $type = 'INNER' ) : self {

		$this->_join->join( $this->_dbh->getPrefix() . $table, $first, $operator, $second );

		return $this;

	}

	/**
	 * LEFT OUTER JOIN
	 *
	 * @param	string	$table
	 * @param	string	$first
	 * @param	string	$operator
	 * @param	string	$second
	 * @return	self
	 **/
	public function leftJoin( string $table, string $first, string $operator, string $second ) : self {

		$this->_join->left( $this->_dbh->getPrefix() . $table, $first, $operator, $second );

		return $this;

	}

	/**
	 * RIGHT OUTER JOIN
	 *
	 * @param	string	$table
	 * @param	string	$first
	 * @param	string	$operator
	 * @param	string	$second
	 * @return	self
	 **/
	public function rightJoin( string $table, string $first, string $operator, string $second ) : self {

		$this->_join->right( $this->_dbh->getPrefix() . $table, $first, $operator, $second );

		return $this;

	}

	/**
	 * FULL OUTER JOIN
	 *
	 * @param	string	$table
	 * @param	string	$first
	 * @param	string	$operator
	 * @param	string	$second
	 * @return	self
	 **/
	public function fullJoin( string $table, string $first, string $operator, string $second ) : self {

		$this->_join->full( $this->_dbh->getPrefix() . $table, $first, $operator, $second );

		return $this;

	}

	/**
	 * WHERE condition
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
	 * WHERE condition, AND chained
	 *
	 * @param	string	$column
	 * @param	string	$operator
	 * @param	mixed	$value
	 * @param	boolean	$is_column
	 * @return	self
	 **/
	public function whereAnd( string $column, string $operator, $value, $is_column = FALSE ) : self {

		if ( is_array( $value ) ) {

			if ( !$is_column && ( $value !== NULL ) ) {

				foreach ( $value as &$v ) {

					$v = $this->bind( $v, ':where' );

				}

			}

			$this->_where->in( $column, !in_array( $operator, [ '!=', '<>', 'NOT IN' ] ), $value, 'AND' );

		}
		else {

			if ( $is_column || ( $value === NULL ) ) {

				$this->_where->set( $column, $operator, $value, 'AND' );

			}
			else {

				$this->_where->set( $column, $operator, $this->bind( $value, ':where' ), 'AND' );

			}

		}

		return $this;

	}

	/**
	 * WHERE condition, OR chained
	 *
	 * @param	string	$column
	 * @param	string	$operator
	 * @param	mixed	$value
	 * @param	boolean	$is_column
	 * @return	self
	 **/
	public function whereOr( string $column, string $operator, $value, $is_column = FALSE ) : self {

		if ( is_array( $value ) ) {

			if ( !$is_column ) {

				foreach ( $value as &$v ) {

					$v = $this->bind( $v, ':where' );

				}

			}

			$this->_where->in( $column, !in_array( $operator, [ '!=', '<>', 'NOT IN' ] ), $value, 'OR' );

		}
		else {

			if ( $is_column ) {

				$this->_where->set( $column, $operator, $value, 'OR' );

			}
			else {

				$this->_where->set( $column, $operator, $this->bind( $value, ':where' ), 'OR' );

			}

		}

		return $this;

	}

	/**
	 * GROUP BY
	 *
	 * @param	array	$columns
	 * @return	self
	 **/
	public function group( array $columns ) : self {

		$this->_group->set( $columns );

		return $this;

	}

	/**
	 * HAVING
	 *
	 * @param	string	$column
	 * @param	string	$operator
	 * @param	mixed	$value
	 * @param	boolean	$is_column
	 * @return	self
	 **/
	public function having( string $column, string $operator, $value, $is_column = FALSE ) : self {

		$this->_having->set( $column, $operator, $this->bind( $value, ':having' ) );

		return $this;

	}

	/**
	 * HAVING COUNT
	 *
	 * @param	string	$column
	 * @param	string	$operator
	 * @param	integer	$value
	 * @return	self
	 **/
	public function havingCount( string $column, string $operator, int $value ) : self {

		$this->_having->count( $column, $operator, $value );

		return $this;

	}

	/**
	 * ORDER BY
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
	 * LIMIT
	 *
	 * @param	integer	$count
	 * @return	self
	 **/
	public function limit( int $count, int $offset = 0 ) {

		$this->_limit->set( $count, $offset );

		return $this;

	}

	/**
	 * Executes SQL query and returns the result
	 *
	 * @param   string  $class
	 * @param	array	$params
	 * @return	bool
	 **/
	public function execute( string $class = NULL, array $params = [] ) : bool {

		$query = $this->__toString();

		try {

			// Prepare
			$stmh = $this->_dbh->prepare( $query );

			// Set the fetch mode
			if ( $class ) {

				$stmh->setFetchMode( \PDO::FETCH_CLASS | \PDO::FETCH_PROPS_LATE, $class, $params );

			}
			else {

				$stmh->setFetchMode( \PDO::FETCH_LAZY );

			}

			// Execute the query
			$result = $stmh->execute( $this->_bindings );

			if ( $result && ( $stmh instanceof \Traversable ) ) {

				$this->_result = $stmh;

			}
			else {

				$this->_result = FALSE;

			}

			return $result;

		}
		catch ( \PDOException $e ) {

			throw new \RuntimeException( $e->getMessage(), $e->getCode() );

		}

		return FALSE;

	}

	/**
	 * Get the first result
	 *
	 * @param   string  $class      name of the class to bind
	 * @param	array	$params
	 * @return	mixed
	 **/
	public function first( string $class = NULL, array $params = [] ) {

		$limit = $this->_limit->get();

		$this->limit( 1 );

		if ( $this->execute( $class, $params ) ) {

			$result = $this->_result->fetch();

			$this->reset();

			// Restore previous limit
			call_user_func_array( [ $this, 'limit' ], $limit );

			return $result;

		}

	}

	/**
	 * Executes SQL count
	 *
	 * @param   string  $column     name of the column
	 * @return	int
	 **/
	public function count( string $column = '*' ) : int {

		$count          = 0;
		$columns        = $this->_columns;
		$this->_columns = [];

		// Set and execute the query
		$this->expression( 'COUNT', $column );

		if ( $this->execute() ) {

			$count = $this->_result->fetchColumn();

			$this->reset();

		}

		// Restore columns
		$this->_columns = $columns;

		return $count;

	}

	/**
	 * Reset results
	 *
	 * @return	self
	 **/
	public function reset() : self {

		$this->_result = NULL;

		return $this;

	}

}
