<?PHP

// Release note: Wraps up database connections and DB access functions - connect/query/result etc.
// Version: 1.0.1
//editor : Nazibul


/*
 *  "myDatabaseConnector" singleton database connector
 *  constructor is private
 *  use "getinstance()" to get the class instance
 *  example:
 *      $cs = myDatabaseConnector::getinstance();
 *      $cs->Sql_exec($query);
 *      $cs->ClosedDBConnection();  //try not to call it
 */

include_once("config.php");

class myDatabaseConnector {

    private static $instance;
    private $connection;

    private function __construct() {
        $this->connection = $this->connectDB();
    }

    public static function getinstance() {
        if (!isset(self::$instance)) {
            $c = __CLASS__;
            self::$instance = new $c;
        }
        return self::$instance;
    }

    public function getConnection() {
        return $this->connection;
    }

    public function connectDB() {
        global $dbtype;
        global $Server;
        global $Database;
        global $UserID;
        global $Password;

        if ($dbtype == "odbc") {
            $cn = odbc_connect("Driver={SQL Server};Server=$Server;Database=$Database", "$UserID", "$Password");
            if (!$cn)
                die("err+db connection error");
            else
                return $cn;

            return $cn;
        } else if ($dbtype == "mssql") {
            $cn = mssql_connect("$Server", "$UserID", "$Password");
            $ret = mssql_select_db($Database);

            if (!$cn)
                die("err+db connection error");
            else
                return $cn;

            return $cn;
        } else if ($dbtype == "mysqli") {
            $cn = mysqli_connect($Server, $UserID, $Password, $Database);
            if (!$cn) {
                die("err+db connection error: " . mysqli_connect_error());
            } else
                return $cn;

            return $cn;
        } else {
            $cn = mysql_connect($Server, $UserID, $Password);
            mysql_select_db($Database);

            if (!$cn)
                die("err+db connection error");
            else
                return $cn;

            return $cn;
        }
    }

    public function ClosedDBConnection() {
        $cn = $this->connection;

        global $dbtype;

        if ($dbtype == 'odbc')
            odbc_close($cn);
        else if ($dbtype == 'mssql')
            mssql_close($cn);
        else if ($dbtype == 'mysqli')
            mysqli_close($cn);
        else
            mysql_close();
    }

    public function Sql_exec($qry) {
        $cn = $this->connection;

        global $dbtype;

        if ($dbtype == 'odbc') {
            $rs = odbc_exec($cn, $qry);
            if (!$rs)
                die("err+" . $qry);
            else
                return $rs;
        } else if ($dbtype == 'mssql') {
            $rs = mssql_query($qry, $cn);

            if (!$rs) {
                echo(mssql_get_last_message());
                die("err+" . $qry);
            } else
                return $rs;
        } else if ($dbtype == 'mysqli') {
            $rs = mysqli_query($cn, $qry);
            if (!$rs)
                die("err + $qry:" . mysqli_error($cn));
            else
                return $rs;
        } else {
            $rs = mysql_query($qry, $cn);
            if (!$rs)
                die("err+" . $qry);
            else
                return $rs;
        }
    }

    function Sql_fetch_array($rs) {
        global $dbtype;

        if ($dbtype == 'odbc')
            return odbc_fetch_array($rs);
        else if ($dbtype == 'mssql')
            return mssql_fetch_array($rs);
        else if ($dbtype == 'mysqli')
            return mysqli_fetch_array($rs);
        else
            return mysql_fetch_array($rs);
    }

    function Sql_Result($rs, $ColumnName) {
        global $dbtype;

        return $rs[$ColumnName];
    }

    function Sql_Num_Rows($result_count) {
        global $dbtype;

        if ($dbtype == 'odbc')
            return odbc_num_rows($result_count);
        else if ($dbtype == 'mssql')
            return mssql_num_rows($result_count);
        else if ($dbtype == 'mysqli')
            return mysqli_num_rows($result_count);
        else
            return mysql_num_rows($result_count);
    }

    function Sql_GetField($rs, $ColumnName) {
        global $dbtype;

        if ($dbtype == 'odbc')
            return odbc_result($rs, $ColumnName);
        else if ($dbtype == 'mssql')
            return mssql_result($rs, 0, $ColumnName);
        else if ($dbtype == 'mysqli') {
            $row = mysqli_fetch_assoc($rs);
            return $row[$ColumnName];
        } else
            return mysql_result($rs, 0, $ColumnName);
    }

    function Sql_Free_Result($rs) {
        global $dbtype;

        if ($dbtype == 'odbc')
            return odbc_free_result($rs);
        else if ($dbtype == 'mssql')
            return mssql_free_result($rs);
        else if ($dbtype == 'mysqli')
            return mysqli_free_result($rs);
        else
            return mysql_free_result($rs);
    }

    function runStoredProcedure($StoreProcName) {
        $cn = $this->connection;

        global $dbtype;

        $call_pass = "CALL " + $StoreProcName;

//        if ($dbtype == 'mysqli') {
//
//            $rs = mysqli_query($connection, $call_pass);
//            if (!$rs)
//                die("err + $StoreProcName:" . mysqli_error($cn));
//            else
//                return $rs;
//        }
    }

    function errorHandling($e, $userName, $userid) {
        $content = "Message: $userName has faced this error and his User ID is $userid  on " . date("l jS \of F Y h:i:s A") . "  $e \n";
        mkdir("log");
        mkdir("log/error");
        $myFile = "log/error/errorLog.txt";
        $fh = fopen($myFile, 'a+') or die("can't open file");

        fwrite($fh, $content);
        fclose($fh);
    }

    function transectionHandling($e, $userName, $userid) {
        $content = "Message: $userName has done this transection and his User ID is $userid  on " . date("l jS \of F Y h:i:s A") . "  $e \n";
        $myFile = $_SERVER['DOCUMENT_ROOT'] . "/log/error/errorLog.txt";
        $fh = fopen($myFile, 'w') or die("can't open file");

        fwrite($fh, $content);
        fclose($fh);
    }

}

?>