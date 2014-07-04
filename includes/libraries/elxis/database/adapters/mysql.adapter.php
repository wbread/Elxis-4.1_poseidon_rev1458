<?php 
/**
* @version		$Id: mysql.adapter.php 1138 2012-05-18 17:13:22Z datahell $
* @package		Elxis
* @subpackage	Database
* @copyright	Copyright (c) 2006-2012 Elxis CMS (http://www.elxis.org). All rights reserved.
* @license		Elxis Public License ( http://www.elxis.org/elxis-public-license.html )
* @author		Elxis Team ( http://www.elxis.org )
* @description 	Elxis CMS is free software. Read the license for copyright notices and details
*/

defined('_ELXIS_') or die ('Direct access to this location is not allowed.');


class elxisMysqlAdapter extends elxisDbAdapter {


	/*************************************/
	/* CALL THE PARENT CLASS CONSTRUCTOR */
	/*************************************/
	public function __construct($pdo=null) {
		parent::__construct($pdo);
		$this->quote_indentifier = '`';
	}


	/*************************************/
	/* ADD LIMIT/OFFSET TO SQL STATEMENT */
	/*************************************/
	public function addLimit($sql, $offset=-1, $limit=-1) {
		if ($offset >= 0) {
			if ($limit > 0) {
				return $sql.' LIMIT '.$offset.','.$limit;
			} else {
				return $sql.' LIMIT '.$offset.',18446744073709551615';
			}
		} else if ($limit > 0) {
			return $sql.' LIMIT '.$limit;
		} else {
			return $sql;
		}
	}


    /****************************/
	/* LIST ALL DATABASE TABLES */
	/****************************/
    public function listTables() {
    	$stmt = $this->pdo->prepare('SHOW TABLES');
    	$stmt->execute();
    	return $stmt->fetchCol();
    }


	/*******************/
	/* BACKUP DATABASE */
	/*******************/
	public function backup($params) {
		if (count($params) == 0) { return -2; }

		$elxis = eFactory::getElxis();
		$dsn = $elxis->getConfig('DB_DSN');
		if (trim($dsn) != '') {
			$dbname = $this->getFromDSN($dsn, 'dbname');
		} else {
			$dbname = $elxis->getConfig('DB_NAME');
		}
		if ($dbname == '') { return -2; }

		$com = '--';

		$out = $com."\n";
		$out .= $com.' MySQL backup taken by Elxis CMS v'.$elxis->getVersion()."\n";
		$out .= $com.' Copyright (c) 2006-'.date('Y')." elxis.org. All rights reserved.\n";
		$out .= $com.' Database: '.$dbname."\n";
		$out .= $com.' URL: '.$elxis->getConfig('URL')."\n";
		$out .= $com.' Date (UTC): '.gmdate('Y-m-d H:i:s')."\n";
		$out .= $com."\n\n";

		if ($params['create_db'] == true) {
			$out .= 'CREATE DATABASE IF NOT EXISTS '.$dbname.';'."\n";
			$out .= 'USE '.$dbname.';'."\n\n";
		}

		foreach ($params['tables'] as $table) {
			$query = $this->pdo->query('SHOW CREATE TABLE `'.$dbname.'`.'.$table);
			if ($query === false) { continue; }
			$out .= $com."\n";
			$out .= $com.' Definition of table '.$table."\n";
			$out .= $com."\n\n";

 			if ($params['add_drop'] == true) {
				$out .= 'DROP TABLE IF EXISTS `'.$table.'`;'."\n";
			}

    		$result = $query->fetchCol(1);
    		if (!is_array($result) || !isset($result[0])) { return 0; }
			$out .= $result[0].";\n\n";
			if ($params['add_insert'] == false) { continue; }
			if (in_array($table, $params['no_insert_tables'])) { continue; }

			$query = $this->pdo->query("SHOW COLUMNS FROM ".$table);
			if ($query === false) { continue; }
			$columns = $query->fetchAll(PDO::FETCH_ASSOC);
			if (!$columns) { continue; }
			$fields = array();
            foreach($columns as $key => $col) {
				$parts = explode(' ', strtolower($col['Type']));
				if ($paren = strpos($parts[0], '(')) {
					$type = substr($parts[0], 0, $paren);
				} else {
					$type = $parts[0];
				}
				$name = $col['Field'];
				if (in_array($type, array('tinyint', 'smallint', 'mediumint', 'int', 'integer', 'bigint'))) {
					$fields[$name] = 'int';
				} else {
					$fields[$name] = 'string';
				}
            }

			$stmt = $this->pdo->prepare('SELECT * FROM '.$table);
			if (!$stmt->execute()) { $out .= "\n\n"; continue; }
			$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
			if (!$rows) { continue; }

			$fieldstr = '`'.implode('`, `', array_keys($fields)).'`';
			$n = count($fields);
			foreach ($rows as $row) {
				$i = 1;
				$vstr = '';
				foreach($row as $k => $v) {
					if ($fields[$k] == 'int') {
						$vstr .= (int)$v;
					} else if ($v === NULL) {
						$vstr .= 'NULL';
					} else {
						$vstr .= '\''.addslashes($v).'\'';
					}
					if ($i < $n) { $vstr .= ', '; }
					$i++;
				}
				$out .= 'INSERT INTO `'.$table.'` ('.$fieldstr.') VALUES ('.$vstr.');'."\n";
			}
			$out .= "\n";
		}

		return $out;
	}

}

?>