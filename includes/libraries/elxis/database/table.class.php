<?php 
/**
* @version		$Id: table.class.php 1284 2012-09-13 20:32:11Z datahell $
* @package		Elxis
* @subpackage	Database
* @copyright	Copyright (c) 2006-2012 Elxis CMS (http://www.elxis.org). All rights reserved.
* @license		Elxis Public License ( http://www.elxis.org/elxis-public-license.html )
* @author		Elxis Team ( http://www.elxis.org )
* @description 	Elxis CMS is free software. Read the license for copyright notices and details
*/

defined('_ELXIS_') or die ('Direct access to this location is not allowed.');


abstract class elxisDbTable {

	protected $table = '';
	protected $primary_key = '';
	protected $columns = array(); //overload columns names, types, and values
	protected $errorMsg = '';
	protected $db = null;
	protected $new_row = true;


	/**********************************************/
	/* MAGIC CONSTRUCTOR INITIATED BY CHILD CLASS */
	/**********************************************/
	protected function __construct($table, $primary_key) {
		$this->table = $table;
		$this->primary_key = $primary_key;
		$this->columns = array();
		$this->errorMsg = '';
		$this->db = eFactory::getDB();
		$this->new_row = true;
	}


	/********************/
	/* LOAD A TABLE ROW */
	/********************/
	public function load($id) {
		$this->new_row = true;
		if (!isset($this->columns[ $this->primary_key ])) { $this->errorMsg = 'No primary key is set for this item!'; return false; }
		if ($this->columns[ $this->primary_key ]['type'] == 'string') {
			$data_type = PDO::PARAM_STR;
		} else {
			$data_type = PDO::PARAM_INT;
			$id = (int)$id;
		}

		$sql = "SELECT * FROM ".$this->db->adapter->quoteIdentifier($this->table)." WHERE ".
		$this->db->adapter->quoteIdentifier($this->primary_key)." = :pkey";
		$stmt = $this->db->prepareLimit($sql, 0, 1);
		$stmt->bindParam(':pkey', $id, $data_type);
		$stmt->execute();
		$row = $stmt->fetch(PDO::FETCH_ASSOC);
		unset($stmt);
		if (!$row) {
			$this->errorMsg = 'Item not found!';
			return false;
		}
		foreach ($row as $col => $val) {
			if (isset($this->columns[$col])) {
				$this->columns[$col]['value'] = $val;
			}
		}
		$this->new_row = false;
		return true;
	}


	/********************************/
	/* BIND AN ARRAY TO THIS OBJECT */
	/********************************/
	public function bind($array) {
		if (count($this->columns) == 0) {
			$this->errorMsg = 'There is no active db table instance to bind values to!';
			return false;
		}

		if (!is_array($array) || (count($array) == 0)) {
			$this->errorMsg = 'Provided values is not an array or it is an empty array!';
			return false;
		}

		foreach($this->columns as $name => $arr) {
			if (isset($array[$name])) {
				switch($arr['type']) {
					case 'integer': case 'bit': $this->columns[$name]['value'] = (int)$array[$name]; break;
					case 'numeric':
						$value = filter_var($array[$name], FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
						if (is_numeric($value)) {
							$this->columns[$name]['value'] = number_format($value, 2, '.', '');
						} else {
							$this->columns[$name]['value'] = null;
						}
					break;
					case 'binary': case 'text': $this->columns[$name]['value'] = $array[$name]; break;
					case 'string': default:
						$this->columns[$name]['value'] = filter_var($array[$name], FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW);
					break;
				}
			}
		}

		return true;
	}


	/**********************************/
	/* MAGIC SETTER FOR TABLE COLUMNS */
	/**********************************/
	public function __set($name, $value) {
		$this->setValue($name, $value);
	}


	/**********************************/
	/* MAGIC GETTER FOR TABLE COLUMNS */
	/**********************************/
    public function __get($name) {
    	if (!isset($this->columns[$name])) { return null; }
    	return $this->columns[$name]['value'];
    }


	/******************/
	/* GET TABLE NAME */
	/******************/
	public function getTable() {
		return $this->table;
	}


	/******************/
	/* GET TABLE NAME */
	/******************/
	public function getColumns() {
		return $this->columns;
	}


	/*************************/
	/* GET TABLE PRIMARY KEY */
	/*************************/
	public function getPrimaryKey() {
		return $this->primary_key;
	}


	/***********************************/
	/* SET VALUE FOR A DB TABLE COLUMN */
	/***********************************/
	protected function setValue($column, $value=null) {
		if (get_class($this) == 'genericDbTable') {
			$this->columns[$column]['type'] = 'string';
			$this->columns[$column]['value'] = $value;
			return;
		}

		if (!isset($this->columns[$column])) { return; }
		if ($value === null) {
			$this->columns[$column]['value'] = null;
			return;
		}
		switch($this->columns[$column]['type']) {
			case 'integer': $this->columns[$column]['value'] = (int)$value; break;
			case 'bit': $this->columns[$column]['value'] = (intval($value) === 1) ? 1 : 0; break;
			case 'numeric':
				if (!is_numeric($value)) { return; }
				$this->columns[$column]['value'] = number_format($value, 2, '.', '');
			break;
			case 'binary': case 'text': $this->columns[$column]['value'] = $value; break;
			case 'string': default: $this->columns[$column]['value'] = (string)$value; break;
		}
	}


	/******************************/
	/* INSERT OR UPDATE TABLE ROW */
	/******************************/
	public function store() {
		if ($this->new_row) {
			$ok = $this->insert();
		} else {
			$ok = $this->update();
		}
		return $ok;
	}


	/********************************************************************************/
	/* FORCE NEW ROW (BE CAREFULL FOR DUBLICATE ENTRIES IF $reset_primary IS FALSE) */
	/********************************************************************************/
	public function forceNew($reset_primary=false) {
		$this->new_row = true;
		if ($reset_primary) {
			$this->columns[ $this->primary_key ]['value'] = null;
		}
	}


	/**************/
	/* INSERT ROW */
	/**************/
	public function insert() {
		$this->errorMsg = '';
		if (!$this->check()) { return false; }
		$data = $this->makeParams();
		if (!$data) { return false; }

		$cols = array();
		foreach ($data['cols'] as $col) {
			$cols[] = $this->db->adapter->quoteIdentifier($col);
		}

        $sql = "INSERT INTO ".$this->db->adapter->quoteIdentifier($this->table)
        ."\n (".implode(', ', $cols).")"
        ."\n VALUES (".implode(', ', $data['vals']).")";
        $stmt = $this->db->prepare($sql);
        foreach ($data['parameters'] as $par) {
        	$stmt->bindParam($par[0], $par[1], $par[2]);
        }

        try {
			$stmt->execute();
		} catch (PDOException $e) {
			$this->errorMsg = $e->getMessage();
			elxisError::logWarning('DATABASE WARNING ('.$e->getCode().")\n".$this->errorMsg);
			return false;
		}

		$this->new_row = false;
		if ($this->primary_key) {
			$this->setValue($this->primary_key, $this->db->lastInsertId($this->table, $this->primary_key));
		}
		return true;
    }


	/**************/
	/* UPDATE ROW */
	/**************/
	public function update() {
		$this->errorMsg = '';
		if (!$this->check()) { return false; }
		$data = $this->makeParams();
		if (!$data) { return false; }

		if (count($data['cols']) < 2) {
			$this->errorMsg = 'Not enough information were supplied to update the row!';
			elxisError::logWarning('DATABASE WARNING ('.get_class($this).")\n".$this->errorMsg);
        	return false;
		}

		if (!$this->primary_key || !in_array($this->primary_key, $data['cols'])) {
			$this->errorMsg = 'The primary key '.$this->primary_key.' is not inside the provided table columns!';
			elxisError::logWarning('DATABASE WARNING ('.get_class($this).")\n".$this->errorMsg);
        	return false;
		}

		$p = -1;
        $sql = "UPDATE ".$this->db->adapter->quoteIdentifier($this->table);
        $sql .= "\n SET";
        $c = count($data['cols']);
        for ($i=0; $i < $c; $i++) {
        	if ($data['cols'][$i] == $this->primary_key) {
				$p = $i;
        		continue;
        	}
        	$sql .= ' '.$this->db->adapter->quoteIdentifier($data['cols'][$i]).' = '.$data['vals'][$i].',';
        }
        $sql = preg_replace('/(\,)$/', '', $sql);

		if ($p < 0) { return false; } //this will never happen but just in case
        $sql .= "\n WHERE ".$this->db->adapter->quoteIdentifier($data['cols'][$p]).' = '.$data['vals'][$p];
        $stmt = $this->db->prepare($sql);
        foreach ($data['parameters'] as $par) {
        	$stmt->bindParam($par[0], $par[1], $par[2]);
        }

        try {
			$stmt->execute();
       		return true;
		} catch (PDOException $e) {
			$this->errorMsg = $e->getMessage();
			elxisError::logWarning('DATABASE WARNING ('.$e->getCode().")\n".$this->errorMsg);
			return false;
		}
    }


	/************/
	/* COPY ROW */
	/************/
	public function copy() {
		$this->columns[ $this->primary_key ]['value'] = null;
		$this->new_row = true;
		return $this->insert();
	}


	/**********************************************************************/
	/* DELETE CURRENT ROW OR ANY ROW (BY PROVIDING THE PRIMARY KEY VALUE) */
	/**********************************************************************/
	public function delete($id='') {
		$this->errorMsg = '';
		if ($this->table == '') { $this->errorMsg = 'No database table is loaded!'; return false; }
		if ($this->primary_key == '') { $this->errorMsg = 'No primary key is set for table '.$this->table.'!'; return false; }
		if (!isset($this->columns[ $this->primary_key ])) { $this->errorMsg = 'Primary key '.$this->primary_key.' was not found in table columns!'; return false; }

		$delete_current = false;
		if (empty($id) === true) {
			$delete_current = true;
			$id = $this->columns[ $this->primary_key ]['value'];
			if (empty($id) === true) { $this->errorMsg = 'No row is loaded, there is nothing to delete!'; return false; }
		}

		if ($this->columns[ $this->primary_key ]['type'] == 'string') {
			$data_type = PDO::PARAM_STR;
		} else {
			$data_type = PDO::PARAM_INT;
			$id = (int)$id;
		}

        $sql = "DELETE FROM ".$this->db->adapter->quoteIdentifier($this->table)
        ."\n WHERE ".$this->db->adapter->quoteIdentifier($this->primary_key).' = :xval';
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':xval', $id, $data_type);
        try {
			$stmt->execute();
			if ($delete_current) {
				$this->forceNew(true);
			}
       		return true;
		} catch (PDOException $e) {
			$this->errorMsg = $e->getMessage();
			elxisError::logWarning('DATABASE WARNING ('.$e->getCode().")\n".$this->errorMsg);
			return false;
		}
	}


	/***********************************/
	/* MOVE (RE-ORDER) ITEM UP OR DOWN */
	/***********************************/
	public function move($inc, $wheres=array()) {
		$sql = "SELECT ".$this->db->quoteId($this->primary_key).", ".$this->db->quoteId('ordering')." FROM ".$this->db->quoteId($this->table);
		if ($inc < 0) {
			$sql .= "\n WHERE ordering < :aorder";
			$orderby = "\n ORDER BY ordering DESC";
		} else if ($inc > 0) {
			$sql .= "\n WHERE ordering > :aorder";
			$orderby = "\n ORDER BY ordering ASC";
		} else {
			$sql .= "\n WHERE ordering = :aorder";
			$orderby = "\n ORDER BY ordering ASC";
		}

		$binds = array();
		if (is_array($wheres) && (count($wheres) > 0)) {
			$w = 1;
			foreach ($wheres as $where) {
				$column = $where[0];
				if (isset($this->columns[$column])) {
					switch ($this->columns[$column]['type']) {
						case 'integer': case 'bit': $pdotype = PDO::PARAM_INT; break;
						case 'binary': case 'text': $pdotype = PDO::PARAM_LOB; break;
						case 'string': case 'numeric': default: $pdotype = PDO::PARAM_STR; break;
					}
				} else if (is_int($where[2]) || is_numeric($where[2])) {
					$pdotype = PDO::PARAM_INT;
				} else if ($where[2] === null) {
					$pdotype = PDO::PARAM_NULL;
				} else {
					$pdotype = PDO::PARAM_STR;
				}

				$sql .= " AND ".$this->db->quoteId($column)." ".$where[1]." :xw".$w;
				$value = ($where[1] == 'LIKE') ? '%'.$where[2].'%' : $where[2];
				$binds[] = array(':xw'.$w, $value, $pdotype);
				$w++;
			}
		}

		$sql .= $orderby;

		$aorder = (int)$this->ordering;
		$avalue = $this->columns[ $this->primary_key ]['value'];

		$stmt = $this->db->prepareLimit($sql, 0, 1);
		$stmt->bindParam(':aorder', $aorder, PDO::PARAM_INT);
		if (count($binds) > 0) {
			foreach ($binds as $bind) {
				$stmt->bindParam($bind[0], $bind[1], $bind[2]);
			}
		}
		$stmt->execute();
		$otherrow = $stmt->fetch(PDO::FETCH_ASSOC);

		if ($otherrow) {
			$border = (int)$otherrow['ordering'];
			$bvalue = (int)$otherrow[ $this->primary_key ];

			$sql = "UPDATE ".$this->db->quoteId($this->table)." SET ordering = :border"
			."\n WHERE ".$this->db->quoteId($this->primary_key)." = :avalue";
			$stmt = $this->db->prepare($sql);
			$stmt->bindParam(':border', $border, PDO::PARAM_INT);
			$stmt->bindParam(':avalue', $avalue, PDO::PARAM_INT);
        	try {
				$stmt->execute();
			} catch (PDOException $e) {
				$this->errorMsg = $e->getMessage();
				elxisError::logWarning('DATABASE WARNING ('.$e->getCode().")\n".$this->errorMsg);
				return false;
			}

			$sql = "UPDATE ".$this->db->quoteId($this->table)." SET ".$this->db->quoteId('ordering')." = :aorder"
			."\n WHERE ".$this->db->quoteId($this->primary_key)." = :bvalue";
			$stmt = $this->db->prepare($sql);
			$stmt->bindParam(':aorder', $aorder, PDO::PARAM_INT);
			$stmt->bindParam(':bvalue', $bvalue, PDO::PARAM_INT);
        	try {
				$stmt->execute();
			} catch (PDOException $e) {
				$this->errorMsg = $e->getMessage();
				elxisError::logWarning('DATABASE WARNING ('.$e->getCode().")\n".$this->errorMsg);
				return false;
			}
			
			$this->ordering = $border;
		} else {
			$sql = "UPDATE ".$this->db->quoteId($this->table)." SET ordering = :aorder"
			."\n WHERE ".$this->db->quoteId($this->primary_key)." = :avalue";
			$stmt = $this->db->prepare($sql);
			$stmt->bindParam(':aorder', $aorder, PDO::PARAM_INT);
			$stmt->bindParam(':avalue', $avalue, PDO::PARAM_INT);
        	try {
				$stmt->execute();
			} catch (PDOException $e) {
				$this->errorMsg = $e->getMessage();
				elxisError::logWarning('DATABASE WARNING ('.$e->getCode().")\n".$this->errorMsg);
				return false;
			}
			$this->ordering = $aorder;
		}
		return true;
	}


	/*****************/
	/* RE-ORDER ROWS */
	/*****************/
	public function reorder($wheres=array(), $fixall=false) {
		if (!isset($this->columns['ordering'])) {
			$this->errorMsg = 'Table '.$this->table.' does not support ordering!';
			elxisError::logWarning("DATABASE WARNING\n".$this->errorMsg);
			return false;
		}

		$sql = "SELECT ".$this->db->quoteId($this->primary_key)." FROM ".$this->db->quoteId($this->table)
		."\n WHERE ".$this->db->quoteId('ordering')." >= 0";
		$binds = array();
		if (is_array($wheres) && (count($wheres) > 0)) {
			$w = 1;
			foreach ($wheres as $where) {
				$column = $where[0];
				if (isset($this->columns[$column])) {
					switch ($this->columns[$column]['type']) {
						case 'integer': case 'bit': $pdotype = PDO::PARAM_INT; break;
						case 'binary': case 'text': $pdotype = PDO::PARAM_LOB; break;
						case 'string': case 'numeric': default: $pdotype = PDO::PARAM_STR; break;
					}
				} else if (is_int($where[2]) || is_numeric($where[2])) {
					$pdotype = PDO::PARAM_INT;
				} else if ($where[2] === null) {
					$pdotype = PDO::PARAM_NULL;
				} else {
					$pdotype = PDO::PARAM_STR;
				}

				$sql .= " AND ".$this->db->quoteId($column)." ".$where[1]." :xw".$w;
				$value = ($where[1] == 'LIKE') ? '%'.$where[2].'%' : $where[2];
				$binds[] = array(':xw'.$w, $value, $pdotype);
				$w++;
			}
		}

		$sql .= "\n ORDER BY ".$this->db->quoteId('ordering')." ASC";
		$stmt = $this->db->prepare($sql);
			if (count($binds) > 0) {
			foreach ($binds as $bind) {
				$stmt->bindParam($bind[0], $bind[1], $bind[2]);
			}
		}
		$stmt->execute();
		$orders = $stmt->fetchCol();
		if (!$orders) {
			$this->errorMsg = 'There are no rows matching WHERE criteria!';
			return false;
		}

		$n = count($orders);
		$k = $this->primary_key;

		if ($n == 1) {
			if (($orders[0] == $this->$k) && ($this->ordering != 1) || ($orders[0] <> $this->$k)) {
				$neworder = 1;
				$id = $orders[0];
				$sql = "UPDATE ".$this->db->quoteId($this->table)." SET ".$this->db->quoteId('ordering')." = :aorder"
				."\n WHERE ".$this->db->quoteId($this->primary_key)." = :avalue";
				$stmt = $this->db->prepare($sql);
				$stmt->bindParam(':aorder', $neworder, PDO::PARAM_INT);
				$stmt->bindParam(':avalue', $id, PDO::PARAM_INT);
        		$stmt->execute();
			}
			return true;
		}

		$ordering = ($this->ordering < 1) ? 1 : $this->ordering;
		$ordering = min($ordering, $n) - 1;

		$idx = -1;
		foreach ($orders as $i => $id) {
			if ($id == $this->$k) { $idx = $i; break; }
		}
		if ($idx == -1) {
			$this->errorMsg = 'Current row was not found between the returned rows!';
			return false;
		}

		if ($idx == $ordering) {
			$final = $orders;
		} else {
			$final = array();
			$reverse = ($ordering < $idx) ? false : true;
			if ($reverse) {
				$orders = array_reverse($orders);
				$idx = ($n - 1) - $idx;
				$ordering = ($n - 1) - $ordering;
			}

			$ls = 0;
			foreach ($orders as $key => $value) {
				if ($key < $ordering) {
					$final[$key] = $value;
				} else if ($key == $ordering) {
					$final[$key] = $orders[$idx];
					$ls = $key + 1;
					$final[$ls] = $value;
				} else if ($key < $idx) {
					$ls++;
					$final[$ls] = $value;
				} else if ($key == $idx) {
					//do nothing
				} else if ($key > $idx) {
					$final[$key] = $value;
				}
			}

			if ($reverse) { $final = array_reverse($final); $orders = array_reverse($orders); }
		}

		$sql = "UPDATE ".$this->db->quoteId($this->table)." SET ".$this->db->quoteId('ordering')." = :aorder"
		."\n WHERE ".$this->db->quoteId($this->primary_key)." = :avalue";
		$stmt = $this->db->prepare($sql);
		foreach ($final as $ord => $id) {
			if ($fixall) {
				$neworder = $ord + 1;
				$stmt->bindParam(':aorder', $neworder, PDO::PARAM_INT);
				$stmt->bindParam(':avalue', $id, PDO::PARAM_INT);
        		$stmt->execute();
			} else if ($orders[$ord] != $id) {
				$neworder = $ord + 1;
				$stmt->bindParam(':aorder', $neworder, PDO::PARAM_INT);
				$stmt->bindParam(':avalue', $id, PDO::PARAM_INT);
        		$stmt->execute();
			}
		}
		return true;
	}


	/*************************************/
	/* MAKE INSERT/UPDATE ROW PARAMETERS */
	/*************************************/
	private function makeParams() {
        $parameters = array();
        $cols = array();
        $vals = array();
        $i = 0;

		$class = get_class($this);
        if (!$this->columns) {
			$this->errorMsg = 'No columns information for database table '.$this->table;
			elxisError::logWarning('DATABASE WARNING ('.$class.")\n".$this->errorMsg);
        	return false;
        }

        foreach ($this->columns as $name => $column) {
        	$cols[$i] = $name;
        	$vals[$i] = ':col'.$i;
        	$val = $column['value'];
        	if ($val === NULL) {
				$type = PDO::PARAM_NULL;
			} else {
				if ($class == 'genericDbTable') {
					if (is_int($val)) {
						$type = PDO::PARAM_INT;
					} else {
						$type = PDO::PARAM_STR;
					}
				} else {
					switch ($column['type']) {
						case 'interger':
						case 'bit';
							$type = PDO::PARAM_INT;
							$val = (int)$val;
						break;
						case 'binary':
						case 'text':
							$type = PDO::PARAM_LOB;
						break;
						case 'string':
						case 'numeric':
						default:
							$type = PDO::PARAM_STR;
							$val = (string)$val;
						break;
					}							
				}
			}
        	$parameters[$i] = array(':col'.$i, $val, $type);
			$i++;					
        }

		$data = array(
			'cols' => $cols,
			'vals' => $vals,
			'parameters' => $parameters
		);

		return $data;
    }


	/********************************************/
	/* SET VALUES FOR MULTIPLE DB TABLE COLUMNS */
	/********************************************/
	protected function setValues($arr=array()) {
		if (!$arr) { return; }
		foreach ($arr as $column => $value) {
			$this->setValue($column, $value);
		}
	}


	/*****************************************/
	/* CHECK ROW VALUES BEFORE INSERT/UPDATE */
	/*****************************************/
	protected function check() {
		if (!isset($this->columns[ $this->primary_key ])) {
			$this->errorMsg = 'The primary key '.$this->primary_key.' is not inside the provided table columns!';
			return false;
		}
		return true;
	}


	/*************************/
	/* GET TABLE OR DB ERROR */
	/*************************/
	public function getErrorMsg() {
		if ($this->errorMsg != '') {
			return $this->errorMsg;
		} else {
			return $this->db->getErrorMsg();
		}
	}

}



class genericDbTable extends elxisDbTable {

	public function __construct($table, $primary_key) {
		parent::__construct($table, $primary_key);
	}

}

?>