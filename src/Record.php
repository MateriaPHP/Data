<?php

namespace Materia\Data;

/**
 * Abstract Record class
 *
 * @package	Materia.Data
 * @author	Filippo Bovo
 * @link	https://lab.alchemica.io/projects/materia/
 **/

abstract class Record implements \ArrayAccess {

	const NAME        = NULL;
	const TABLE       = NULL;
	const PREFIX      = NULL;
	const PRIMARY_KEY = NULL;

	protected $_data;
	protected $_connection;

	protected static $_properties = [];

	/**
	 * Constructor
	 *
	 * @param	\Materia\Data\SQL\Connection	$connection		RDBMS connection
	 * @throws	\UnexpectedValueException
	 **/
	public function __construct( \Materia\Data\SQL\Connection $connection ) {

		if ( ! static::NAME || ! static::TABLE || ! static::PRIMARY_KEY ) {

			throw new \UnexpectedValueException( "Invalid Record definition" );

		}

		$this->_data       = [];
		$this->_connection = $connection;

	}

	/**
	 * Object cloning
	 **/
	public function __clone() {

		$this->offsetUnset( static::PRIMARY_KEY );

	}

	/**
	 * String conversion
	 *
	 * @return	string
	 **/
	public function __toString() : string {

		return ( string ) $this->offsetGet( static::PRIMARY_KEY );

	}

	/**
	 * Set the value of a property
	 *
	 * @param	string	$key	property name
	 * @param	mixed	$value	property value
	 **/
	public function __set( string $key, $value ) {

		$this->offsetSet( $key, $value );

	}

	/**
	 * Get the value of a property
	 *
	 * @param	string	$key	property name
	 * @return	mixed			property value
	 **/
	public function __get( string $key ) {

		return $this->offsetGet( $key );

	}

	/**
	 * Unset a property
	 *
	 * @param	string	$key	property name
	 **/
	public function __unset( string $key ) {

		$this->offsetUnset( $key );

	}

	/**
	 * Checks if a property exists
	 *
	 * @param	string	$key	property name
	 * @return	boolean
	 **/
	public function __isset( string $key ) {

		return $this->offsetExists( $key );

	}

	/**
	 * @see	\ArrayAccess::offsetGet()
	 **/
	public function offsetGet( $offset ) {

		// Remove the prefix
		if ( static::PREFIX && ( strpos( $offset, static::PREFIX ) === 0 ) ) {

			$offset = substr( $offset, strlen( static::PREFIX ) );

		}

		// Get the value
		if ( isset( static::$_properties[$offset] ) ) {

			$method = 'get' . static::$_properties[$offset];

			return $this->{$method}();

		}
		else if ( isset( $this->_data[$offset] ) ) {

			return $this->_data[$offset];

		}

	}

	/**
	 * @see		\ArrayAccess::offsetSet()
	 * @throws	\InvalidArgumentException
	 **/
	public function offsetSet( $offset, $value ) {

		// Only strings please
		if ( !is_string( $offset ) || is_numeric( $offset ) ) {

			throw new \InvalidArgumentException( sprintf( 'Argument 1 passed to %s must be a string, %s given', [ __METHOD__, gettype( $offset ) ] ) );

		}

		// Remove the prefix
		if ( static::PREFIX && ( strpos( $offset, static::PREFIX ) === 0 ) ) {

			$offset = substr( $offset, strlen( static::PREFIX ) );

		}

		// Set the value
		if ( isset( static::$_properties[$offset] ) ) {

			$method = 'set' . static::$_properties[$offset];

			$this->{$method}( $value );

		}
		else {

			$this->_data[$offset] = $value;

		}
	}

	/**
	 * @see	\ArrayAccess::offsetUnset()
	 **/
	public function offsetUnset( $offset ) {

		// Remove the prefix
		if ( static::PREFIX && ( strpos( $offset, static::PREFIX ) === 0 ) ) {

			$offset = substr( $offset, strlen( static::PREFIX ) );

		}

		// Avoid deleting of protected props
		if ( isset( $this->_data[$offset] ) ) {

			unset( $this->_data[$offset] );

		}

	}

	/**
	 * @see	\ArrayAccess::offsetExists()
	 **/
	public function offsetExists( $offset ) : bool {

		// Remove the prefix
		if( static::PREFIX && ( strpos( $offset, static::PREFIX ) === 0 ) ) {

			$offset = substr( $offset, strlen( static::PREFIX ) );

		}

		return isset( $this->_data[$offset] );

	}

	/**
	 * Save the Record
	 *
	 * @param	bool	$force
	 * @return	bool
	 **/
	public function save( bool $force = FALSE ) : bool {

		$data = [];

		// Prepend prefix (foreach seems to be the fastest)
		foreach ( $this->_data as $key => $value ) {

			if ( $key != static::PREFIX ) {

				$key        = static::PREFIX . $key;
				$data[$key] = $value;

			}

		}

		// Insert
		if ( $force || ! $this->offsetExists( static::PRIMARY_KEY ) ) {

			if ( $this->offsetExists( static::PRIMARY_KEY ) ) {

				$id = $this->_connection
				           ->insert()
				           ->ignore()
				           ->into( static::TABLE )
				           ->values( $data )
				           ->execute();

			}
			else {

				$id = $this->_connection
				           ->insert()
				           ->into( static::TABLE )
				           ->values( $data )
				           ->execute();

			}

			if ( $id && ! $this->offsetExists( static::PRIMARY_KEY ) ) {

				$this->offsetSet( static::PRIMARY_KEY, $id );

				return TRUE;

			}

		}
		// Update
		else {

			$rows = $this->_connection
			             ->update( static::TABLE )
			             ->values( $data )
			             ->where( static::PREFIX . static::PRIMARY_KEY, '=', $this->offsetGet( static::PRIMARY_KEY ) )
			             ->execute();

			if ( $rows !== FALSE ) {

				return TRUE;

			}

		}

		return FALSE;

	}

	/**
	 * Load a Record
	 *
	 * @param	mixed	$id			PK value
	 * @return	bool
	 **/
	public function load( $id ) : bool {

		$record = $this->_connection
		               ->select()
		               ->from( static::TABLE )
		               ->where( static::PREFIX . static::PRIMARY_KEY, '=', $id )
		               ->first( static::class, [ $this->_connection ] );

		if ( $record ) {

			$this->_data = $record->_data;

			return TRUE;

		}

		return FALSE;

	}

	/**
	 * Remove the Record
	 *
	 * @return	bool
	 **/
	public function remove() : bool {

		if ( $this->offsetExists( static::PRIMARY_KEY ) ) {

			return $this->_connection
			            ->delete()
			            ->from( static::TABLE )
			            ->where( static::PREFIX . static::PRIMARY_KEY, '=', $this->offsetGet( static::PRIMARY_KEY ) )
			            ->execute() ? TRUE : FALSE;

		}

		return FALSE;

	}

}
