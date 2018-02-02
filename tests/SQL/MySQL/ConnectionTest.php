<?php

namespace Materia\Data\SQL\MySQL;

/**
 * Test MySQL connection class
 *
 * @package Materia.Data
 * @author  Filippo Bovo
 * @link    https://lab.alchemica.io/projects/materia/
 **/

class ConnectionTest extends \PHPUnit\Framework\TestCase {

	public function setUp() {

		// Setting up the connection
		// $this->_connection = new Connection( $GLOBALS['DB_DSN'], $GLOBALS['DB_USER'], $GLOBALS['DB_PASSWORD'] );

		// $this->_connection
		// 	->exec('CREATE TEMPORARY TABLE test_products (product_id INT(11) NOT NULL AUTO_INCREMENT, product_name VARCHAR(32) NOT NULL, product_price DECIMAL(12,2) NOT NULL DEFAULT 0.00, PRIMARY KEY (product_id)) ENGINE=MEMORY;');

	}

	public function tearDown() {}

	public function testConnection() {

		$connection = new Connection( $GLOBALS['DB_DSN'], $GLOBALS['DB_USER'], $GLOBALS['DB_PASSWORD'] );

		$this->assertEquals( TRUE, ( $connection instanceof Connection ) );

	}

}
