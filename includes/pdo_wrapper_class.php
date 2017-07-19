<?php
//require_once($_SERVER['DOCUMENT_ROOT']."/kit_db/includes/globals.php");

/**
 * Created by PhpStorm.
 * User: sportypants
 * Date: 4/5/17
 * Time: 12:40 AM
 */
#################################################
/*
 * class pdo_wrapper_class.php - wrapper for the PHP PDO functions
 *
 * methods:
 * pdoConnect      - connects to database
 * pdoExecute      - executes an SQL query, can have SQL parameters
 * pdoRowCount     - returns number of rows affected
 * pdoGetAll       - returns all rows in a 2-dimensional hash array
 * pdoGetAssoc     - returns all rows in a 2-dimensional hash array, 1st retrieved column is index of array
 * pdoGetRow       - returns the first row of results in a 1-dimensional array
 * pdoInsUpd       - creates an insert or update statement from tablename and array with data
 * pdoLastInsertId - returns last created ID from an autoincrement column
 * pdoException    - displays error message after exception
 *
 * Licensed under the terms of the BSD License:
 *      http://www.opensource.org/licenses/bsd-license.php
 *
 * ****************************************************
 * You must copy this copyright notice into any modification of this class
 * ****************************************************
 * Copyright by:
 * Author    Emile Schenk
 *           http://www.donauweb.at
 *           https://www.xing.com/profile/Emile_Schenk
 * License   BSD
 * ****************************************************
 * End copyright notice
 * ****************************************************
 * Created   February 2010
 * Revision  1.1
 *
 * Changes
 *     Revision 1.0
 *         - Initial release
 *         - well tested, is in use in commercial projects
 *     Revision 1.1
 *         - More comments added
 *     Revision 1.2
 *         - pdoInsUpd: Older PHP version need separate variables when the same value is used several times in one SQL statement
 */

##################################################################################################
##################################################################################################
class PdoWrapper {
    private $dbh;
#################################################
# When useTablecols==1 a file must be included which defines all fields of tables.
# Only fields that are defined in this file will be INSERTED/UPDATED in the table.
# Example:
# global $tablecols;
# $tablecols['customers'] = array('id','firstname','lastname','address','postcode','city');
#################################################
    public $useTablecols = 0;
#################################################
# use nicely formatted output of datadumper: https://sourceforge.net/projects/datadumper
# for output of Exceptions
#################################################
    public $useDatadumper = 0;

    var $server;	// mySQL host to connect to
    var $port;		// mySQL port
    var $username;	// mySQL user name
    var $password;	// mySQL password
    var $database;	// mySQL database to select

    var $sql;

#################################################
    function pdoConnect($server='', $port='', $username='', $password='', $database='') {

        ini_set('mssql.charset', 'UTF-8');

//'titan.yesco.com', 1433, 'appuser', '@pp$AreFun' //ASM this was for old YESCO DB

        !empty($server) ? $this->server = $server : $this->server = 'kitliastdb.db.3766381.hostedresource.com';
        !empty($port) ? $this->port = $port : $this->port = 3306;
        !empty($username) ? $this->username = $username : $this->username = 'kitliastdb';
        !empty($password) ? $this->password = $password : $this->password = 'CalStars9462!';
        !empty($database) ? $this->database = $database : $this->database = 'kitliastdb';

//	dblib:host=Titan;dbname=YescoApps','appuser','@pp$AreFun' //ASM this was for old YESCO DB
        $connString = "dblib:host=$this->server:$this->port;dbname=$this->database";

        try {
            $this->dbh = new PDO($connString, $this->username, $this->password);
        }
        catch(Exception $e) {
            $this->pdoException( $e->getMessage() );
            exit;
        }
        $this->dbh->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );
//	$this->dbh->exec("set names utf8");  /This doesn't work yet
        return true;
    }


#################################################
    function pdoExecute($query, $array=array()) {
        $this->sql = array('query'=>$query, 'input'=>$array);
        try{
            $stmt = $this->dbh->prepare($query);
            $ret = $stmt->execute($array);
            $stmt->setFetchMode(PDO::FETCH_ASSOC);
            return $stmt;
        }
        catch (Exception $e) {
            $this->pdoException( $e->getMessage() );
            exit;
        }
    }

#################################################
    function pdoRowCount($stmt) {
        try{
            $count = $stmt->rowCount();
            return $count;
        }
        catch (Exception $e) {
            $this->pdoException( $e->getMessage() );
            exit;
        }
    }

#################################################
    function pdoGetAll($query, $array=array()) {
        $arr = array();
        $rows = array();

        $stmt = $this->pdoExecute($query, $array);

//EITHER DO THIS


        /*
            while ($arr = $stmt->fetch())
            {
                $rows[] = $arr;
            }
        */


//OR DO THIS, BUT NOT BOTH
        $rows = $stmt->fetchAll();


        /*
        if (strpos($query, "[lead_activity]")){
            error_log(round(memory_get_usage() / (1024*1024),3));
        }
        */


        return $rows;
    }

#################################################
    function pdoGetAssoc($query, $array=array()) {
        $rows = $this->pdoGetAll($query, $array);
        foreach($rows as $row) {
            $idx = array_shift($row);
            $rows_assoc[$idx] = $row;
        }
        return $rows_assoc;
    }

#################################################
    function pdoGetRow($query, $array=array()) {
        $stmt = $this->pdoExecute($query, $array);
        $row = $stmt->fetch();
        return $row;
    }

#################################################
    function pdoBeginTransaction() {
        $this->dbh->beginTransaction();
    }
#################################################
    function pdoRollback() {
        $this->dbh->rollBack();
    }
#################################################
    function pdoCommit() {
        $this->dbh->commit();
    }

#################################################
    /* function pdoInsUpd
     * Parameters:
     * $table: name of table
     * $data: hash-array with values of data for INSERT/UPDATE
     * $action: 'insert' | 'insert duplicate' | 'insert delayed' | 'update'
     *          duplicate adds to SQL: ON DUPLICATE KEY UPDATE
     *          delayed adds to SQL: DELAYED
     * $parameters: WHERE parameters for UPDATE
     *              hash-array
     *              index can be: fieldname | 'fieldname <operator>' where operator can be '=', '<', '>', '<>', '>=', '<=', 'like'
     *              when no operator is mentioned '=' is taken as operator
     *              e.g. array('myfield', 'myvalue') results in "WHERE myfield='myvalue'"
     *              e.g. array('myfield >', 'myvalue') results in "WHERE myfield>'myvalue'"
     *              e.g. array('myfield like', 'myvalue') results in "WHERE myfield like '%myvalue%'"
     *
     * When $this->useTablecols==1 a file must be included which defines all fields of the used table.
     * Only fields that are defined in this file will be INSERTED/UPDATED in the table.
     * Example of file:
     * global $tablecols;
     * $tablecols['customers'] = array('id','firstname','lastname','address','postcode','city');
    */
#################################################
    function pdoInsUpd($table, $data, $action = 'insert', $parameters = '') {
        global $tablecols;

        reset($data);
        $this->sql = '';

        foreach($data as $idx=>$d) {
            if($this->useTablecols==1) {
                if(!is_array($tablecols[$table])) break;
                if(!in_array($idx, $tablecols[$table])) continue;
            }

            switch (strtolower((string)$d)) {
                case 'now()':
                case 'null':
                    $insertcolumns .= "$idx, ";
                    $insertvalues .= "$d, ";
                    $updatevalues .= "$idx=$d, ";
                    break;
                default:
                    $insertcolumns .= "`$idx`, ";
                    $insertvalues .= ":$idx, ";
                    $duplicatevalues .= "`$idx`=:dup_$idx, ";
                    $updatevalues .= "`$idx`=:$idx, ";
                    if(is_null($d)) $d = '';
                    $insertarray[$idx] = $d;
                    $duplicatearray['dup_'.$idx] = $d;
                    break;
            }
        }
        if(strlen($insertcolumns) <3) {
            $txt = '';
            if($this->useTablecols==1) {
                $txt = "table $table not found in tablecols.php or ";
            }
            $this->pdoException($txt . "no columns to insert");
            exit;
        }
        $insertcolumns    = rtrim($insertcolumns, ', ');
        $insertvalues     = rtrim($insertvalues, ', ');
        $duplicatevalues  = rtrim($duplicatevalues, ', ');
        $updatevalues     = rtrim($updatevalues, ', ');

        $action = strtolower($action);
        $delayed = '';
        if(substr_count($action, 'delayed') >0) {
            $delayed = 'DELAYED';
        }
        if(substr_count($action, 'insert') >0) {
            $query = "INSERT $delayed INTO $table ($insertcolumns) VALUES ($insertvalues)";
            if(substr_count($action, 'duplicate') >0) {
                $query .= " ON DUPLICATE KEY UPDATE $duplicatevalues";
                $insertarray = array_merge($insertarray, $duplicatearray);
            }
        }
        elseif(substr_count($action, 'update') >0) {
            $operators = array('=', '<', '>', '<>', '>=', '<=', 'like');
            foreach($parameters as $idx=>$parameter) {
                list($where_var, $where_operator) = explode(' ', $idx);
                if($where_operator == '') {
                    $where_operator = '=';
                }
                if(!in_array($where_operator, $operators)) {
                    continue;
                }
                if($where_operator == 'like') {
                    $parameter = "%$parameter%";
                }
                $where_array[] = "$where_var $where_operator :where_$idx";
                $insertarray['where_'.$idx] = $parameter;
            }
            $where = implode(' AND ', $where_array);
            $query = "UPDATE $table SET $updatevalues WHERE $where";
        }
        return $this->pdoExecute($query, $insertarray);
    }

#################################################
    function pdoLastInsertId() {
        //if it's mysql, do this:
//	return $this->dbh->lastInsertId();

//MS SQL do this:
        $sql = "SELECT CAST(COALESCE(SCOPE_IDENTITY(), @@IDENTITY) AS int)";
        $sth = $this->dbh->query($sql);
        $sth->execute();
        $result = $sth->fetch();
        return $result[0];
    }

#################################################
    function pdoException($message) {
        $pdo_error['error'] = 'PDO-SQL-ERROR';
        $pdo_error['PDO_error'] = $message;
        $pdo_error['SQL'] = $this->sql;
        $pdo_error['debug_backtrace']= debug_backtrace();
        if($this->useDatadumper) {
            de($pdo_error);
        }
        else {
            var_dump($pdo_error);
            exit;
        }
    }

#################################################
} # end class






