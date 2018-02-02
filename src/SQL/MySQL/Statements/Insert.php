<?php

namespace Materia\Data\SQL\MySQL\Statements;

/**
 * MySQL INSERT statement
 *
 * @package	Materia.Data
 * @author	Filippo Bovo
 * @link	https://lab.alchemica.io/projects/materia/
 **/

class Insert extends \Materia\Data\SQL\MySQL\Statement {

	protected $_ignore = FALSE;

	/**
	 * Constructor
	 *
	 * @param	\Materia\Data\SQL\MySQL\Connection	$dbh
	 **/
	public function __construct( \Materia\Data\SQL\MySQL\Connection $dbh ) {

		parent::__construct( $dbh );

	}

	/**
	 * @return	string
	 **/
	public function __toString() : string {

		if ( empty( $this->_table ) ) {

			throw new \RuntimeException( 'No table set for insertion' );

		}

		if ( empty( $this->_columns ) ) {

			throw new \RuntimeException( 'Missing columns for insertion' );

		}

		$sql  = 'INSERT' . ( $this->_ignore ? ' IGNORE' : '' ) . ' INTO ' . $this->_dbh->getPrefix() . $this->_table;
		$sql .= ' ( ' . implode( ' , ', $this->_columns ) . ' )';
		$sql .= ' VALUES ( ' . implode( ' , ', array_keys( $this->_bindings ) ) . ' )';

		return $sql;

	}

	/**
	 * Set table
	 *
	 * @param	string	$table
	 * @return	self
	 **/
	public function into( string $table ) : self {

		$this->setTable( $table );

		return $this;

	}

	/**
	 * IGNORE
	 *
	 * @return	self
	 **/
	public function ignore() : self {

		$this->_ignore = TRUE;

		return $this;

	}

	/**
	 * Set columns' values
	 *
	 * @param	array	$values
	 * @return	self
	 **/
	public function values( array $values ) : self {

		$this->_columns = array_keys( $values );

		$this->bind( array_values( $values ), ':value' );

		return $this;

	}

	/**
	 * Executes SQL query and returns the last insert ID
	 *
	 * @return  int
	 * @throws  \RuntimeException
	 **/
	public function execute() : int {

		$query = $this->__toString();

		try {

			// Prepare
			$stmh   = $this->_dbh->prepare( $query );
			// Execute the query
			$result = $stmh->execute( $this->_bindings );

			if ( $result ) {

				return $this->_dbh->lastInsertId();

			}

		}
		catch ( \PDOException $e ) {

			throw new \RuntimeException( $e->getMessage(), $e->getCode() );

		}

		return 0;

	}

}
