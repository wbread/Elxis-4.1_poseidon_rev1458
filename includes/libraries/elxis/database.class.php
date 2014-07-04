<?php 
/**
* @version		$Id: database.class.php 1311 2012-09-30 08:01:03Z datahell $
* @package		Elxis
* @subpackage	Database
* @copyright	Copyright (c) 2006-2012 Elxis CMS (http://www.elxis.org). All rights reserved.
* @license		Elxis Public License ( http://www.elxis.org/elxis-public-license.html )
* @author		Elxis Team ( http://www.elxis.org )
* @description 	Elxis CMS is free software. Read the license for copyright notices and details
*/

defined('_ELXIS_') or die ('Direct access to this location is not allowed.');


elxisLoader::loadFile('includes/libraries/elxis/database/statement.class.php');
elxisLoader::loadFile('includes/libraries/elxis/database/table.class.php');
elxisLoader::loadFile('includes/libraries/elxis/database/importer.class.php');


class elxisDatabase {

    private $pdo = null; //pdo instance
	private $dbtype= 'mysql';
	private $host = 'localhost';
	private $port = 0;
	private $dbname = '';
	private $persistent = 0;
	private $dsn = '';
	private $scheme = '';
	private $table_prefix = 'elx_';
	private $debug = 0;
	public $adapter = null; //db adapter object
	private $inTransaction = false; //is there an active transaction? true/false
	//private $sql = ''; //last sql
	//private $table_handlers = array(); //array of included file handlers for db tables
	private $errorCode = 0;
	private $errorMsg = '';
	private $backTrace = '';


	/************************/
	/* DATABASE CONSTRUCTOR */
	/************************/
	public function __construct($params=array(), $options=array(), $connect=true) {
		if (!is_array($params) || (count($params) == 0)) {
			$elxis = eFactory::getElxis();
			$this->dbtype = $elxis->getConfig('DB_TYPE');
			$this->host = $elxis->getConfig('DB_HOST');
			$this->port = (int)$elxis->getConfig('DB_PORT');
			$this->dbname = $elxis->getConfig('DB_NAME');
			$user = $elxis->getConfig('DB_USER');
			$pass = $elxis->getConfig('DB_PASS');
			$this->persistent = (int)$elxis->getConfig('DB_PERSISTENT');
			$this->dsn = trim($elxis->getConfig('DB_DSN'));
			$this->scheme = $elxis->getConfig('DB_SCHEME');
			$this->table_prefix = $elxis->getConfig('DB_PREFIX');
			$this->debug = $elxis->getConfig('DEBUG');
		} else {
			$this->dbtype = isset($params['dbtype']) ? $params['dbtype'] : 'mysql';
			$this->host = isset($params['host']) ? $params['host'] : 'localhost';
			$this->port = isset($params['port']) ? (int)$params['port'] : 0;
			$this->dbname = $params['dbname'];
			$user = isset($params['username']) ? $params['username'] : 'root';
			$pass = isset($params['password']) ? $params['password'] : '';
			$this->persistent = isset($params['persistent']) ? (int)$params['persistent'] : 0;
			$this->dsn = isset($params['dsn']) ? trim($params['dsn']) : '';
			$this->scheme = isset($params['scheme']) ? $params['scheme'] : '';
			$this->table_prefix = isset($params['table_prefix']) ? $params['table_prefix'] : '';
			$this->debug = isset($params['debug']) ? (int)$params['debug'] : 0;
		}

		if ($connect) {
			$this->connect($this->dsn, $user, $pass, $options);
		}
	}


	/*****************************************/
	/* GENERATE DSN STRING AND CONNECT TO DB */
	/*****************************************/
	public function connect($dsn, $user, $pass, $options=array(), $silent=false) {
		if ($this->pdo) { return true; }
		switch($this->dbtype) {
			case 'mysql':
				if ($dsn != '') {
					$this->dsn = $dsn;
				} else {
					$this->dsn = 'mysql:host='.$this->host;
					if ($this->port > 0) { $this->dsn .= ';port='.$this->port; }
					$this->dsn .= ';dbname='.$this->dbname.';charset=utf8';
				}
				$options[PDO::MYSQL_ATTR_INIT_COMMAND] = 'SET NAMES utf8';
				$options[PDO::ATTR_PERSISTENT] = ($this->persistent == 1) ? true : false;
			break;
			case 'pgsql':
				if ($dsn != '') {
					$this->dsn = $dsn;
				} else {
					$this->dsn = 'pgsql:host='.$this->host;
					if ($this->port > 0) { $this->dsn .= ';port='.$this->port; }
					$this->dsn .= ';dbname='.$this->dbname.';user='.$user.';password='.$pass;
				}
			break;
			case 'sqlite': //sqlite 3
			case 'sqlite2': //sqlite 2
				$user = '';
				$pass = '';
				if ($dsn != '') {
					$this->dsn = $dsn;
				} else {
					if ($this->scheme == '') { $this->scheme = ':memory:'; }
					if ($this->scheme == 'memory') { $this->scheme = ':memory:'; }
					if ($this->scheme != ':memory:') {
						if (!file_exists($this->scheme)) {
							$ok = @touch($this->scheme);
							if (!$ok) {
								die('ERROR: Database '.$this->scheme.' not found and could not be created!');
							}
							chmod($this->scheme, 0666);
						}
					}
					$this->dsn = $this->dbtype.':'.$this->scheme;
				}
			break;
			case 'firebird':
				if ($dsn != '') {
					$this->dsn = $dsn;
				} else {
					if ($this->scheme == '') {
						die('ERROR: You didnt specified a valid Firebird database!');
					}
					if ($this->host == 'localhost') {
						if (($this->scheme == '') || (!file_exists($this->scheme)) || (!is_file($this->scheme))) {
							die('ERROR: Database '.$this->scheme.' not found!');
						}
					}
					$this->dsn = 'firebird:dbname='.$this->scheme.';host='.$this->host.';charset=UTF8';
					if (version_compare(phpversion(), '5.3.0', '>=')) {
						$options[PDO::FB_ATTR_DATE_FORMAT] = 'CCYY-MM-DD HH:NN:SS';
					}
				}
			break;
			case 'oci':
				if ($dsn != '') {
					$this->dsn = $dsn;
				} else {
					/*
					$this->dsn = 'oci:dbname=';
					if ($this->host == 'localhost') {
						$this->dsn .= '//localhost';
					} elseif ($this->host != '') {
						$this->dsn .= $this->host;
					}
					if ($this->port > 0) { $this->dsn .= ':'.$this->port; }
					$this->dsn .= '/'.$this->dbname.';charset=AL32UTF8';
					*/
					$this->dsn = 'oci:';
					if ($this->host != '') {
						$this->dsn .= 'dbname=(DESCRIPTION=(ADDRESS_LIST=(ADDRESS=(PROTOCOL=TCP)(HOST='.$this->host.')';
						if ($this->port > 0) {
							$this->dsn .= '(PORT='.$this->port.')';
						} else {
							$this->dsn .= '(PORT=1521)';
						}
						$this->dsn .= '))(CONNECT_DATA=(SID='.$this->dbname.')))';
					} else {
						$this->dsn .= 'dbname='.$this->dbname;
					}
					$this->dsn .= ';charset=AL32UTF8';
				}
				$options[PDO::ATTR_CASE] = PDO::CASE_LOWER;
				//NLS_DATE_FORMAT = 'RRRR-MM-DD HH24:MI:SS';
			break;
			case 'odbc':
				$this->dsn = $dsn;
				if ($this->dsn == '') { die('ERROR: Please provide a valid DSN connection string!'); }
			break;
			case 'odbc_access':
				if ($dsn != '') {
					$this->dsn = $dsn;
				} else {
					if (($this->scheme == '') || (!file_exists($this->scheme)) || (!is_file($this->scheme))) {
						die('Microsoft Access (.mdb) database not found or not provided!');
					}
					$this->dsn = 'odbc:Driver={Microsoft Access Driver (*.mdb)};Dbq='.$this->scheme.';Uid='.$user;
				}
			break;
			case 'odbc_mssql':
				if ($dsn != '') {
					$this->dsn = $dsn;
				} else {
					$this->dsn = 'odbc:Driver={SQL Native Client};Server='.$this->host.';Database='.$this->dbname.';Uid='.$user.';Pwd='.$pass; 
				}
			break;
			case 'odbc_db2':
				if ($dsn != '') {
					$this->dsn = $dsn;
				} else {
					if ($this->port == 0) { $this->port = 50000; }
					$this->dsn = 'odbc:DRIVER={IBM DB2 ODBC DRIVER};HOSTNAME='.$this->host.';PORT='.$this->port.';DATABASE='.$this->dbname.';PROTOCOL=TCPIP;UID='.$user.';PWD='.$pass;
				}
			break;
			case 'ibm':
				if ($dsn != '') {
					$this->dsn = $dsn;
				} else {
					if ($this->port == 0) { $this->port = 56789; }
					$this->dsn = 'ibm:DRIVER={IBM DB2 ODBC DRIVER};HOSTNAME='.$this->host.';PORT='.$this->port.';DATABASE='.$this->dbname.';PROTOCOL=TCPIP;UID='.$user.';PWD='.$pass;
				}
			break;
			case 'informix':
				if ($dsn != '') {
					$this->dsn = $dsn;
				} else {
					if ($this->port == 0) { $this->port = 9800; }
					$this->dsn = 'informix:host='.$this->host.'; service='.$this->port.'; database='.$this->dbname.'; server=ids_server; protocol=onsoctcp; EnableScrollableCursors=1';
				}
			break;
			case '4D':
				if ($dsn != '') {
					$this->dsn = $dsn;
				} else {
					$this->dsn = '4D:host='.$this->host;
					if ($this->port > 0) { $this->dsn .= ';port='.$this->port; }
					$this->dsn .= ';user='.$user.';password='.$pass;
					if ($this->dbname != '') { $this->dsn .= ';dbname='.$this->dbname; }
					$this->dsn .= ';charset=UTF-8';
				}
			break;
			case 'cubrid':
				if ($dsn != '') {
					$this->dsn = $dsn;
				} else {
					if ($this->port == 0) { $this->port = 33000; }
					$this->dsn = 'cubrid:host='.$this->host.';port='.$this->port.';dbname='.$this->dbname;
				}
			break;
			case 'mssql':
				if ($dsn != '') {
					$this->dsn = $dsn;
				} else {
					$seperator = (ELXIS_OS == 'WIN') ? ',' : ':';
					$extra = ($this->port > 0) ? $seperator.$this->port : '';
					$this->dsn = 'mssql:host='.$this->host.''.$extra.';dbname='.$this->dbname.';charset=UTF-8';
				}
			break;
			case 'sybase':
			case 'freetds':
			case 'dblib':
				if ($dsn != '') {
					$this->dsn = $dsn;
				} else {
					$seperator = (ELXIS_OS == 'WIN') ? ',' : ':';
					$extra = ($this->port > 0) ? $seperator.$this->port : '';
					$this->dsn = $this->dbtype.':host='.$this->host.''.$extra.';dbname='.$this->dbname.';charset=UTF-8';
				}
			break;
			default:
				die('Unsupported database type!');
			break;
		}

		try {
			$this->pdo = new PDO($this->dsn, $user, $pass, $options);
			$this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION); //PDO::ERRMODE_WARNING //ERRMODE_EXCEPTION
			$this->pdo->setAttribute(PDO::ATTR_ORACLE_NULLS, PDO::NULL_NATURAL);
		} catch (PDOException $e) {
			if ($silent == true) {
				$this->errorMsg = 'Connection to DB failed! '.$e->getMessage();
				return false;
			} else {
				$code = $e->getCode();
				if ($code == '') {
					$code = 'DBC';
				} else {
					$code = 'DBC'.$code;
				}
				exitPage::make('fatal', $code, 'Database connection failed! '.$e->getMessage());
				exit();
				//$msg = 'ERROR ('.$e->getCode().'): '.$e->getMessage()."\n";
				//$msg .= $e->getTraceAsString();
				//trigger_error($msg, E_USER_ERROR);
			}
		}

		$this->loadAdapter();
      	$this->pdo->setAttribute(PDO::ATTR_STATEMENT_CLASS, array('elxisPDOStatement', array($this->pdo, $this->debug)));
		return true;
	}


	/**********************/
	/* DISCONNECT FROM DB */
	/**********************/
	public function disconnect() {
		$this->pdo = null;
	}


	/*************************/
	/* LOAD DATABASE ADAPTER */
	/*************************/
	private function loadAdapter() {
		$dbtype = strtolower($this->dbtype);
		if ($dbtype == 'sqlite2') { $dbtype = 'sqlite'; }
		elxisLoader::loadFile('includes/libraries/elxis/database/adapter.class.php');
		if (file_exists(ELXIS_PATH.'/includes/libraries/elxis/database/adapters/'.$dbtype.'.adapter.php')) {
			$class = 'elxis'.ucfirst($dbtype).'Adapter';
			elxisLoader::loadFile('includes/libraries/elxis/database/adapters/'.$dbtype.'.adapter.php');
		} else {
			$class = 'elxisGenericAdapter';
			elxisLoader::loadFile('includes/libraries/elxis/database/adapters/generic.adapter.php');
		}
		$this->adapter = new $class($this->pdo);
	}


	/**********************************/
	/* GET ARRAY OF AVAILABLE DRIVERS */
	/**********************************/
	public function getAvailableDrivers() {
		return $this->pdo->getAvailableDrivers();
	}


	/***********************/
	/* GET A PDO ATTRIBUTE */
	/***********************/
	public function getAttribute($attribute) {
        try {
            return $this->pdo->getAttribute($attribute);
        } catch (PDOException $e) {
            return null;
        }
	}


	/***********************/
	/* SET A PDO ATTRIBUTE */
	/***********************/
	public function setAttribute($attribute, $value) {
        try {
            return $this->pdo->setAttribute($attribute, $value);
        } catch (PDOException $e) {
            return false;
        }
	}


	/************************/
	/* GET DATABASE VERSION */
	/************************/
	public function version() {
		return $this->pdo->getAttribute(PDO::ATTR_SERVER_VERSION);
	}


	/*****************************************************************/
	/* GET THE LAST ID GENERATED BY AN IDENTITY/AUTOINCREMENT COLUMN */
	/*****************************************************************/
    public function lastInsertId($tableName=null, $primaryKey=null) {
    	if ($tableName !== null) {
    		$tableName = preg_replace('/(#__)/', $this->table_prefix, $tableName);
    	}
        return $this->adapter->lastInsertId($tableName, $primaryKey);
    }


	/******************/
	/* QUOTE A STRING */
	/******************/
    public function quote($value, $parameter_type = PDO::PARAM_STR) {
        if (is_int($value) || is_float($value)) { return $value; }
        return $this->pdo->quote($value, $parameter_type);
    }


	/***********************/
	/* QUOTE AN IDENTIFIER */
	/***********************/
    public function quoteId($string) {
    	return $this->adapter->quoteIdentifier($string);
    }


	/*************************/
	/* PREPARE SQL STATEMENT */
	/*************************/
	public function prepare($sql, $options=array(), $prefix='#__') {
		$sql = $this->replacer($sql, $prefix);
		return $this->pdo->prepare($sql, $options);
	}


	/*******************************************/
	/* PREPARE SQL STATEMENT WITH LIMIT/OFFSET */
	/*******************************************/
	public function prepareLimit($sql, $offset=-1, $limit=-1, $options=array(), $prefix='#__') {
		$sql = $this->limiter($this->replacer($sql, $prefix), $offset, $limit);
		return $this->pdo->prepare($sql, $options);
	}


	/****************/
	/* SQL REPLACER */
	/****************/
	public function replacer($sql, $prefix='#__') {
		if ($prefix != '') {
			$sql = preg_replace('/('.$prefix.')/', $this->table_prefix, $sql);
		} else {
			$sql = $sql;
		}
		return $sql;
	}


	/*********************************/
	/* SET LIMIT/OFFSET ON SQL QUERY */
	/*********************************/
	public function limiter($sql, $offset=-1, $limit=-1) {
		$limit = (int)$limit;
		$offset = (int)$offset;
		if (($limit > 0) || ($offset > 0)) {
			$sql = $this->adapter->addLimit($sql, $offset, $limit);
		}
		return $sql;
	}


	/*********************/
	/* MONITOR SQL QUERY */
	/*********************/
	public function monitor($sql='') {
		if ($this->debug > 1) {
			if (($this->debug == 2) || ($this->debug == 3)) {
				eRegistry::get('ePerformance')->addQuery();
			} else if (($this->debug == 4) || ($this->debug == 5)) {
				eRegistry::get('ePerformance')->addQuery($sql);
			}
		}
	}


	/****************************/
	/* LIST ALL DATABASE TABLES */
	/****************************/
	public function listTables() {
		return $this->adapter->listTables();
	}


	/*****************************************/
	/* BACKUP DATABASE AND RETURN RESULT SQL */
	/*****************************************/
	public function backup($userparams = array()) {
		$params = array(
			'tables' => array(),
			'create_db' => false,
			'add_drop' => true,
			'add_insert' => true,
			'no_insert_tables' => array()
		);

		if (is_array($userparams) && (count($userparams) > 0)) {
			foreach ($userparams as $key => $val) {
				if (isset($params[$key])) {
					$params[$key] = $userparams[$key];
				}
			}
		}

		if (count($params['tables']) == 0) {
			$params['tables'] = $this->listTables();
		}

		/*
		returns:
		sql data: on success
		0: backup failed
		-1: not supported database type
		-2: Invalid/Insuficient backup parameters
		-3: Other (database specific) error
		*/
		return $this->adapter->backup($params);
	}


	/*****************************************************************/
	/* IMPORT SQL FILE INTO DATABASE                                 */
	/* THE FILE SHOULD BE PROPERLY FORMATTED FOR THE CURRENT DB TYPE */
	/* USE #__ IN SQL FILE AS TABLES PREFIX REPLACEMENT              */
	/*****************************************************************/
	public function import($sqlfile) {
		$sqlfile = trim($sqlfile);
		if (($sqlfile == '') || !is_file($sqlfile)) {
			$this->errorMsg = 'SQL file to import does not exist!';
			return false;
		}
		if (!preg_match('#(\.sql)$#i', $sqlfile)) {
			$this->errorMsg = 'You can only import SQL files!';
			return false;
		}

		$queries = 0;
		$templine = '';
		$lines = file($sqlfile);
		if ($lines) {
			foreach ($lines as $line) {
				$trimmed_line = trim($line);
				if (($trimmed_line == '') || (substr($trimmed_line, 0, 2) == '--')) { continue; }
				$templine .= $line;
				if (substr($trimmed_line, -1, 1) == ';') {
					$templine = $this->replacer($templine);
					if ($this->pdo->exec($templine) !== false) { $queries++; }
					/*
					$stmt = $this->prepare($templine);
					if ($stmt->execute()) { $queries++; }
					*/
					$templine = '';
				}
			}
		}

		return $queries;
	}


	/***********************/
	/* BEGIN A TRANSACTION */
	/***********************/
	public function beginTransaction() {
		if ($this->inTransaction) {
			return false;
		} else {
			$this->inTransaction = $this->pdo->beginTransaction();
			return $this->inTransaction;
		}
	}


	/************************/
	/* COMMIT A TRANSACTION */
	/************************/
	public function commit() {
		$this->inTransaction = false;
		return $this->pdo->commit();
	}


	/***************************/
	/* ROLL BACK A TRANSACTION */
	/***************************/
	public function rollBack() {
		$this->inTransaction = false;
		return $this->pdo->rollBack();
	}


	/**********************************/
	/* GET IF A TRANSACTION IS ACTIVE */
	/**********************************/
	public function inTransaction() {
		return $this->inTransaction;
	}


	/******************************************************/
	/* EXECUTE A QUERY AND RETURN RESULT AS PDO STATEMENT */
	/******************************************************/
	public function query($statement, $params = array(), $options = array()) {
		$this->clearErrors();
		$options[] = array('fetch' => PDO::FETCH_OBJ);
		try {
			if ($statement instanceof elxisPDOStatement) {
				$stmt = $statement;
				$stmt->execute(null, $options);
			} else {
				$stmt = $this->prepare($statement);
				$stmt->execute($params, $options);
			}
			return $stmt;
		} catch (PDOException $e) {
			$this->errorCode = $e->getCode();
			$this->errorMsg = $e->getMessage();
			$this->backTrace = $e->getTraceAsString();
			elxisError::logWarning('DATABASE WARNING ('.$this->errorCode.")\n".$this->errorMsg);
        	return NULL;
		}
	}


    /**************************************************/
    /* EXECUTE SQL AND RETURN NUMBER OF AFFECTED ROWS */
    /**************************************************/
	public function exec($sql, $prefix='#__') {
		$sql = $this->replacer($sql, $prefix);
    	$this->monitor();
    	return $this->pdo->exec($sql);
    }


	/*********************************/
	/* NOBODY CAN CLONE THE INSTANCE */
	/*********************************/
	private function __clone() {
	}

	public function getType() {
		return $this->dbtype;
	}

	public function getHost() {
		return $this->host;
	}

	public function getPort() {
		return $this->port;
	}

	public function getName() {
		return $this->dbname;
	}

	public function getDSN() {
		return $this->dsn;
	}

	public function getScheme() {
		return $this->scheme;
	}

	public function getPersistent() {
		return $this->persistent;
	}

    public function errorCode() {
    	return $this->pdo->errorCode();
    }

    public function errorInfo() {
    	return $this->pdo->errorInfo();
    }


	/*********************/
	/* CLEAR LAST ERRORS */
	/*********************/
	public function clearErrors() {
		$this->errorCode = 0;
		$this->errorMsg = '';
		$this->backTrace = '';
	}


	/****************************/
	/* GET STATEMENT ERROR CODE */
	/****************************/
	public function getErrorCode() {
		return $this->errorCode;
	}


	/*******************************/
	/* GET STATEMENT ERROR MESSAGE */
	/*******************************/
	public function getErrorMsg() {
		return $this->errorMsg;
	}


	/**********************************/
	/* GET STATEMENT ERROR BACK TRACE */
	/**********************************/
	public function getBackTrace() {
		return $this->backTrace;
	}

}
  
?>