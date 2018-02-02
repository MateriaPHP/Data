<?php

namespace Materia\Data\SQL;

/**
 * Abstract storage class
 *
 * @package Materia.Data
 * @author  Filippo Bovo
 * @link    https://lab.alchemica.io/projects/materia/
 **/

interface Connection {

	/**
	 * Select statement
	 *
	 * @param	array	$columns	columns
	 * @return	Select
	 **/
	public function select( array $columns = [] );

	/**
	 * Insert statement
	 *
	 * @return  Insert
	 **/
	public function insert();

	/**
	 * Update statement
	 *
	 * @param	string	$table	target table name
	 * @return	Update
	 **/
	public function update( string $table );

	/**
	 * Deletes rows
	 *
	 * @param	string	$table	target table name
	 * @return	Delete
	 **/
	public function delete( string $table );

}
