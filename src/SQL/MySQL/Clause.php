<?php

namespace Materia\Data\SQL\MySQL;

/**
 * MySQL abstract clause class
 *
 * @package	Materia.Data
 * @author	Filippo Bovo
 * @link	https://lab.alchemica.io/projects/materia/
 **/

interface Clause {

	/**
	 * Converts statement object into SQL query
	 **/
	public function __toString() : string;

}
