<?php

// FORMULIZE PDO SUPPORT, BASED ON PDO SUPPORT IN IMPRESSCMS
// JULIAN EGELSTAFF - NOV 16 2010

if (!defined("ICMS_ROOT_PATH")) {
    die("ImpressCMS root path not defined");
}

include_once ICMS_ROOT_PATH."/class/database/database.php";

class XoopsPDODatabase extends XoopsDatabase {

	var $conn;

	/**
	 * Row count of the most recent statement
	 * @var int
	 */
	var $rowCount = 0;
	
	var $allowWebChanges = false;

	public function connect($selectdb = true) {
		$this->conn = new PDO("sqlsrv:Server=(local)\\sqlexpress;Database=".XOOPS_DB_NAME.";quotedid=0", XOOPS_DB_USER, XOOPS_DB_PASS); // each driver has a specific connection string structure of its own
		// these lines should be unnecessary thanks to quotedid=0 which sets this value on the connection itself
		//$this->conn->setAttribute(PDO::SQLSRV_ATTR_DIRECT_QUERY,1);
		//$this->conn->query("SET QUOTED_IDENTIFIER OFF;");
		return true;
	}
	public function close() {
		$this->conn = null;
		return true;
	}
	public function quoteString($string) {
		return $this->conn->quote($string);
	}
	public function quote($string) {
		return $this->conn->quote($string);
	}
	public function escape($string) {
		return $this->conn->escape($string);
	}
	public function error() {
		$error = $this->conn->errorInfo();
		return $error[2];
	}
	public function errno() {
		$error = $this->conn->errorInfo();
		return $error[1];
	}
	public function genId($sequence) {
		return 0; // will use auto_increment
	}
	public function query($sql, $limit = 0, $start = 0) {
		if (!$this->allowWebChanges && strtolower(substr(trim($sql), 0, 6)) != 'select')  {
			trigger_error(_CORE_DB_NOTALLOWEDINGET, E_USER_WARNING);
			return false;
		}
		return $this->queryF($sql, $limit, $start);
	}
	public function queryF($sql, $limit = 0, $start = 0) {
		if (!empty ($limit)) {
			$start = !empty($start) ? (int)$start . ',' : '';
			$sql .= ' LIMIT ' . $start . (int)$limit;
		}
		$result = false;
		try {
			$sql = $this->convertToSQLServerSyntax($sql);
			$pdoStatement = $this->conn->prepare("$sql", array(PDO::ATTR_CURSOR=>PDO::CURSOR_SCROLL)); // should now return a $result for which the rowCount will be accurate
			if(!$pdoStatement) {
			    $this->logger->addQuery($sql, $this->error(), $this->errno());
				//print "Prepare failed for this SQL:<br>$sql<br>SQL Server reports: ".$this->error()."<br>";
			}
			$result = $pdoStatement->execute();
			if(!$result) {
			   $this->logger->addQuery($sql, $this->error(), $this->errno());
			} else {
				$this->logger->addQuery($sql);
			}
			$this->rowCount = $pdoStatement->rowCount(); // the cursor needs to be set to always return the full result in order for the rowcount to be a valid number
		} catch (Exception $e) {
		}
		return $pdoStatement;
	}
	public function getInsertId() {
		// Need to check with MS staff to see that lastInsertId is bound to our connection
		return $this->conn->lastInsertId();
	}
	public function getAffectedRows() {
		return $this->rowCount;
	}
	public function getFieldName($result, $offset) {
		$column = $result->getColumnMeta($offset);
		return $column['name'];
	}
	public function getFieldType($result, $offset) {
		$column = $result->getColumnMeta($offset);
		return $column['mysql:decl_type'];
	}
	public function getFieldsNum($result) {
		return $result->columnCount();
	}
	public function fetchRow($result) {
		return $result->fetch( PDO::FETCH_NUM );
	}
	public function fetchArray($result) {
		return $result->fetch( PDO::FETCH_ASSOC );
	}
	public function fetchBoth($result) {
		return $result->fetch( PDO::FETCH_BOTH );
	}
	public function getRowsNum($result) {
		return $result->rowCount(); // the cursor needs to be set to always return the full result in order for the rowcount to be a valid number
	}
	public function freeRecordSet($result) {
		$result->closeCursor();
		return true;
	}

	private function convertToSQLServerSyntax($sql) {
	    // change SHOW COLUMNS to the SQL Server equivalent
	    if(substr($sql, 0, 12)=="SHOW COLUMNS") {
		// need to get the table name and then insert it as appropriate in the replacement SQL syntax
		// need to get the LIKE param and handle that too
		$fromPos = strpos($sql, "FROM");
		$tableName = substr($sql, $fromPos+5);
		$sql = "SELECT column_name FROM information_schema.columns WHERE table_name='" . trim($tableName) . "'";
		if($likePos = strpos($tableName, "LIKE")) {
		    $likeName = substr($tableName, $likePos+5);
		    $sql .= " AND column_name = '".trim($likeName)."'";
		} 
	    }
	    if(trim($sql) == "SHOW TABLES") {
		// convert to get table names from the information schema
		$sql = "SELECT table_name FROM information_schema.tables";
	    }
	    // replace backticks with [ ]
	    $replacement = "[";
	    $backtickPos = 0;
	    while($backtickPos = strpos($sql, "`", $backtickPos+1)) {
		$sql = substr_replace($sql, $replacement,$backtickPos,1);
		$replacement = $replacement == "[" ? "]" : "[";
	    }
	    
	    // check for a limit clause and if found, send to the necessary function
	    // assume all caps LIMIT space and a number near the end of the statement is the actual limit clause that we need to work with
	    if($limitPos = strrpos($sql, "LIMIT")) {
		// verify it's at the end
		// check for comma
		// if one value, then it's the limit, if two, second is limit, first is offset
		if($limitPos >= strlen($sql) - 10) {// if the limit is pretty much at the end of the sql....
        	    $limitClause = substr($sql, $limitPos);
		    $sql = substr($sql, 0, $limitPos);
		    if($commaPos = strpos($limitClause, ",")) {
			$limit = intval(substr($limitClause,$commaPos+1));
			$offset = intval(str_replace("LIMIT", "", substr($limitClause, 0, $commaPos)));
		    } else {
			$limit = intval(str_replace("LIMIT", "", $limitClause));
			$offset = 0;
		    }
		}
		$sql = $this->limit_to_top_n($sql, $offset, $limit);
	    }
	    return $sql;
	}

	// thanks to moodle for this code
	private function limit_to_top_n($sql, $offset, $limit) {
	    if ($limit < 1 && $offset < 1) {
		return $sql;
	    }
	    $limit = max(0, $limit);
	    $offset = max(0, $offset);
    
	    if ($limit > 0 && $offset == 0) {
		$sql1 = preg_replace('/^([\s(])*SELECT( DISTINCT | ALL)?(?!\s*TOP\s*\()/i',
		    "\\1SELECT\\2 TOP $limit", $sql);
	    } else {
		// Only apply TOP clause if we have any limitnum (limitfrom offset is hadled later)
		if ($limit < 1) {
		   $limit = "9223372036854775806"; // MAX BIGINT -1
		}
		if (preg_match('/\w*FROM[\s|{]*([\w|.]*)[\s|}]?/i', $sql, $match)) {
		    $from_table = $match[1];
		    if (preg_match('/SELECT[\w|\s]*(\*)[\w|\s]*FROM/i', $sql)) {
			/*
			// Nov 17 2010 - I don't get why this is necessary, why not just use the columns as declared in the original statement?!
			// Need all the columns as the emulation returns some temp cols
			$cols = array_keys($this->get_columns($from_table));
			$cols = implode(', ', $cols);
			*/
			$fromPos = strpos($sql, "FROM");
			$cols = str_replace("SELECT", "", substr($sql, 0, $fromPos-1));
		    } else {
			$cols = '*';
		    }
		    $sql1 = 'SELECT '.$cols.' FROM ( '
			.'SELECT sub2.*, ROW_NUMBER() OVER(ORDER BY sub2.line2) AS line3 FROM ( '
			.'SELECT 1 AS line2, sub1.* FROM '
			.''.$from_table.' AS sub1 '
			.') AS sub2 '
			.') AS sub3 '
			.'WHERE line3 BETWEEN '.($offset+1).' AND '
			.($offset + $limit);
		} else {
		    $sql1 = "SELECT 'Invalid table'";
		}
	    }
    
	    return $sql1;
	}

}

class XoopsPDODatabaseSafe extends XoopsPDODatabase
{

  /**
   * perform a query on the database
   *
   * @param string $sql a valid MySQL query
   * @param int $limit number of records to return
   * @param int $start offset of first record to return
   * @return resource query result or FALSE if successful
   * or TRUE if successful and no result
   */
	function query($sql, $limit=0, $start=0)
	{
		return $this->queryF($sql, $limit, $start);
	}
}

/**
 * Read-Only connection to a MySQL database.
 *
 * This class allows only SELECT queries to be performed through its
 * {@link query()} method for security reasons.
 *
 * @package     database
 * @subpackage  mysql
 * @since XOOPS
 *
 * @author      Kazumi Ono  <onokazu@xoops.org>
 * @copyright   copyright (c) 2000-2003 XOOPS.org
 * @license		http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU General Public License (GPL)
 */
class XoopsPDODatabaseProxy extends XoopsPDODatabase
{

  
}

