<?php 
/**
* @version		$Id$
* @package		Elxis
* @subpackage	Error handling
* @copyright	Copyright (c) 2006-2012 Elxis CMS (http://www.elxis.org). All rights reserved.
* @license		Elxis Public License ( http://www.elxis.org/elxis-public-license.html )
* @author		Elxis Team ( http://www.elxis.org )
* @description 	Elxis CMS is free software. Read the license for copyright notices and details
*/

defined('_ELXIS_') or die ('Direct access to this location is not allowed');


class elxisError {

	static private $ERROR_LOG = 0;
	static private $ERROR_REPORT = 1;
	static private $ERROR_ALERT = 0;
	static private $LOG_ROTATE = 0;
	static private $MAIL_MANAGER_NAME = '';
	static private $MAIL_MANAGER_EMAIL = '';
	static private $URL = 'http://localhost';
	static private $REPO_PATH = '';
	static private $DEBUG = false;


	/****************************/
	/* SET ENVIROMENT VARIABLES */
	/****************************/
	public static function init() {
		$elxis = eFactory::getElxis();
		self::$ERROR_LOG = (int)$elxis->getConfig('ERROR_LOG');
		self::$ERROR_REPORT = (int)$elxis->getConfig('ERROR_REPORT');
		self::$ERROR_ALERT = (int)$elxis->getConfig('ERROR_ALERT');
		self::$LOG_ROTATE = (int)$elxis->getConfig('LOG_ROTATE');
		self::$MAIL_MANAGER_NAME = $elxis->getConfig('MAIL_MANAGER_NAME');
		self::$MAIL_MANAGER_EMAIL = $elxis->getConfig('MAIL_MANAGER_EMAIL');
		if (self::$MAIL_MANAGER_EMAIL == '') { self::$ERROR_ALERT = 0; }
		self::$URL = $elxis->getConfig('URL');
		$repo_path = $elxis->getConfig('REPO_PATH');
		if ($repo_path == '') { $repo_path = ELXIS_PATH.'/repository'; }
		if (!is_dir($repo_path.'/') || !file_exists($repo_path.'/')) {
			exitPage::make('fatal', 'ERRO-0001', 'Elxis repository folder not found at '.$repo_path);
		}
		self::$REPO_PATH = $repo_path;
		self::$DEBUG = (intval($elxis->getConfig('DEBUG')) > 1) ? true : false;

		switch (self::$ERROR_REPORT) {
			case 1: error_reporting(E_ERROR); break;
			case 2: error_reporting(E_ALL ^ E_NOTICE); break;
			case 3: error_reporting(E_ALL); break;
			case 0: default: error_reporting(0); break;
		}
	}


	/********************/
	/* SEND EMAIL ALERT */
	/********************/
	static private function mailError($errfile, $errline, $errstr) {
		$file = self::$REPO_PATH.'/logs/lastnotify.txt';
		if (!file_exists($file)) {
			@touch($file);
			$proceed = true;
		} else {
			$last = filemtime($file);
			if ((time() - filemtime($file)) > 600) {
				@touch($file);
				$proceed = true;
			} else {
				$proceed = false;
			}
		}

		if (!$proceed) { return; }

    	$parsed = parse_url(self::$URL); 
 		$host = preg_replace('#^(www\.)#i', '', $parsed['host']);
    	$subject = 'Fatal error at '.$host;
    	$to = self::$MAIL_MANAGER_EMAIL.','.self::$MAIL_MANAGER_NAME;

		$body = 'A fatal error occured at '.self::$URL."\r\n";
		$body .= 'Please inspect the site to find-out was caused this error and fix it.'."\r\n\r\n";
		$body .= 'The detailed error message was:'."\r\n";
		$body .= 'ERROR in file '.$errfile.' line '.$errline."\r\n";
		$body .= $errstr."\r\n\r\n";
		if (self::$ERROR_LOG > 0) {
			$body .= 'This error was logged'."\r\n\r\n";
		}
		$body .= 'Date: '.date('Y-m-d H:i:s')." (UTC)\r\n";
		$body .= 'Site URL: '.self::$URL."\r\n\r\n\r\n";
		$body .= "---------------------------------------\r\n";
		$body .= 'Please do not reply to this message as it was generated automatically by the Elxis Error Handler ';
		$body .= 'and it was sent for informational purposes. If you dont want to receive error notifications you can ';
		$body .= 'set so at Elxis global configuration. Elxis Error Handler will not send you an other notification for ';
		$body .= 'the next 10 minutes even if more errors occurs.'."\r\n";

		$elxis = eFactory::getElxis();
		$elxis->sendmail($subject, $body, '', null, 'plain', $to, null, null, null, 1);
	}


	/**********************/
	/* SHOW ERROR MESSAGE */
	/**********************/
	static private function showMessage($severity, $errfile, $errline, $errstr) {
		if (!headers_sent()) { header('content-type: text/html; charset=utf-8'); }
		switch ($severity) {
			case 'ERROR':
				$bg = 'fdeeee';
				$col = 'FF0000';
			break;
			case 'WARNING':
				$bg = 'fff1db';
				$col = 'FF9900';
			break;
			default:
				$bg = 'fdfdee';
				$col = 'abac72';
			break;
		}
		echo '<div style="margin: 5px 0; padding: 3px; font-size: 12px; font-family: arial; border: 1px solid #'.$col.'; background-color: #'.$bg.';">'."\n";
		echo '<span style="font-weight: bold; color: #'.$col.';">'.$severity.'</span> in file <em>'.$errfile.'</em> line <em>'.$errline."</em><br />\n";
		echo nl2br($errstr)."\n";
		echo "</div>\n";
	}


	/**************************/
	/* SHOW/LOG ERROR MESSAGE */
	/**************************/
	static public function error($errno, $errstr, $errfile, $errline) {
   		switch ($errno) {
        	case E_ERROR: case E_USER_ERROR: case E_CORE_ERROR: case E_PARSE: case E_COMPILE_ERROR:
        		if (self::$DEBUG) {
        			$ePerformance = eRegistry::get('ePerformance');
        			$ePerformance->addError();
        		}
        		if (self::$ERROR_LOG > 0) {
					$msg = 'ERROR in file '.$errfile.' line '.$errline._LEND.$errstr;
					self::writetoLog($msg, 'error');
				}
				if (self::$ERROR_ALERT > 0) {
					self::mailError($errfile, $errline, $errstr);
				}
				if (self::$ERROR_REPORT > 0) {
					self::showMessage('ERROR', $errfile, $errline, $errstr);
				}
				die();
			break;
        	case E_WARNING: case E_USER_WARNING: case E_CORE_WARNING: case E_COMPILE_WARNING:
        		if (self::$DEBUG) {
        			$ePerformance = eRegistry::get('ePerformance');
        			$ePerformance->addError();
        		}
        		if (self::$ERROR_LOG > 1) {
					$msg = 'WARNING in file '.$errfile.' line '.$errline._LEND.$errstr;
					self::writetoLog($msg, 'warning');
				}
				if (self::$ERROR_REPORT > 1) {
					self::showMessage('WARNING', $errfile, $errline, $errstr);
        		}
			break;
        	case E_NOTICE: case E_USER_NOTICE: case E_STRICT:
        		if (self::$DEBUG) {
        			$ePerformance = eRegistry::get('ePerformance');
        			$ePerformance->addError();
        		}
        		if (self::$ERROR_LOG > 2) {
					$msg = 'NOTICE in file '.$errfile.' line '.$errline._LEND.$errstr;
					self::writetoLog($msg, 'notice');
				}
				if (self::$ERROR_REPORT > 2) {
					self::showMessage('NOTICE', $errfile, $errline, $errstr);
        		}
			break;
        	default: return; break;
        }
	}


	/***************************/
	/* SYSTEM SHUTDOWN HANDLER */
	/***************************/
	static public function shutdown() {
        $error = error_get_last();
        if ($error) {
			if (in_array($error['type'], array(E_ERROR, E_USER_ERROR, E_COMPILE_ERROR, E_CORE_ERROR, E_PARSE))) {
        		self::error($error['type'], $error['message'], $error['file'], $error['line']);
        		exit();
        	}
        }
	}


	/****************************************************************/
	/* LOG WARNING (USED TO SILENT LOG DB WARNINGS ON INSERTS, ETC) */
	/****************************************************************/
	static public function logWarning($message) {
        if (self::$ERROR_LOG > 1) {
			self::writetoLog($message, 'warning');
		}
	}


	/******************************************************************/
	/* LOG ERROR (USED TO SILENT LOG ERRORS LIKE PAGE NOT FOUND, ETC) */
	/******************************************************************/
	static public function logError($message) {
        if (self::$ERROR_LOG > 0) {
			self::writetoLog($message, 'error');
		}
	}


	/**************************************************/
	/* WRITE MESSSAGE INTO LOGS (IMPORTANT: PRIVATE!) */
	/**************************************************/
	static private function writetoLog($message, $type='error') {
		if ($type == '') { $type = 'error'; }
		$file = self::$REPO_PATH.'/logs/'.$type.'.log';
		if (self::$LOG_ROTATE == 1) {
			if (file_exists($file)) {
				$modym = date('Ym', filemtime($file));
				$creym = date('Ym');
				if ($creym > $modym) {
					$new_file = self::$REPO_PATH.'/logs/'.$type.'_'.$modym.'.log';
					@copy($file, $new_file);
					@unlink($file);
				}
			}
		}

		$txt = '['.date('Y-m-d H:i:s').']'." \t".$message._LEND._LEND;

		if (!$fp = @fopen($file, 'a')) { return false; }
		flock($fp, LOCK_EX);
		fwrite($fp, $txt);
		flock($fp, LOCK_UN);
		fclose($fp);
		return true;
	}

}


/*****************/
/* ERROR HANDLER */
/*****************/
function elxisErrorHandler($errno, $errstr, $errfile, $errline) {
	elxisError::error($errno, $errstr, $errfile, $errline);
}


/*********************/
/* SHUTDOWN FUNCTION */
/*********************/
function elxisErrorShutdown() {
	elxisError::shutdown();
}

?>