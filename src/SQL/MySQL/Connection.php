<?php

namespace Materia\Data\SQL\MySQL;

/**
 * MySQL connection handler
 *
 * @package	Materia.Data
 * @author	Filippo Bovo
 * @link	https://lab.alchemica.io/projects/materia/
 **/

class Connection extends \PDO implements \Materia\Data\SQL\Connection {

	protected $_prefix;

	/**
	 * Constructor
	 *
	 * @param	string	$dsn			the Data Source Name
	 * @param	string	$username		user name to connect MySQL server
	 * @param	string	$password		password to connect MySQL server
	 * @param	array	$options		connection options
	 * @param	string	$prefix			tables prefix
	 **/
	public function __construct( string $dsn, string $username, string $password = NULL, array $options = [], string $prefix = '' ) {

		$defaults = [
			\PDO::ATTR_ERRMODE            => \PDO::ERRMODE_EXCEPTION,
			\PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_LAZY,
			\PDO::ATTR_EMULATE_PREPARES   => FALSE,
			\PDO::ATTR_AUTOCOMMIT         => FALSE,
		];

		// Initialize
		parent::__construct( $dsn, $username, $password, array_merge( $defaults, $options ) );

		$this->_prefix = $prefix;

	}

	/**
	 * @see \Materia\Data\SQL\Connection::select()
	 **/
	public function select( array $columns = [] ) {

		return new Statements\Select( $this, $columns );

	}

	/**
	 * @see \Materia\Data\SQL\Connection::insert()
	 **/
	public function insert() {

		return new Statements\Insert( $this );

	}

	/**
	 * @see \Materia\Data\SQL\Connection::update()
	 **/
	public function update( string $table ) {

		return new Statements\Update( $this, $table );

	}

	/**
	 * @see \Materia\Data\SQL\Connection::delete()
	 **/
	public function delete( string $table ) {

		return new Statements\Delete( $this, $table );

	}

	public function getPrefix() : string {

		return $this->_prefix;

	}

}
