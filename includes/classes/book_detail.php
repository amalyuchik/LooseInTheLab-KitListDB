<?php
/**
 * Created by PhpStorm.
 * User: amalyuchik
 * Date: 7/19/2017
 * Time: 10:38 AM
 */
require_once($_SERVER['DOCUMENT_ROOT']."/kit_db/includes/globals.php");
require_once($_SERVER['DOCUMENT_ROOT']."/kit_db/includes/sql_functions.php");
require_once($_SERVER['DOCUMENT_ROOT']."/kit_db/includes/table_rows.php");

class book_detail
{
    var $connection_array = array();
    var $book_name = '';
    var $book_id;
    var $conn;
    var $select_sql;

    /**
     * book_detail constructor.
     * @param array $connection_array
     * @param string $book_name
     * @param $book_id
     * @param $conn
     * @param $select_sql
     */
    public function __construct(array $connection_array, $book_name, $book_id, $conn, $select_sql)
    {
        $this->connection_array = $connection_array;
        $this->book_name = $book_name;
        $this->book_id = $book_id;
        $this->conn = $conn;
        $this->select_sql = $select_sql;
    }


    function retrieve_book()
    {
        $query_select = $this->select_sql->query("book_contents", null, null, $this->book_id);
        $sql_execute = $this->conn->runconn_sql_execute($this->connection_array, $query_select);
        var $do_once=0;
        if($sql_execute !== null)
        {
            echo "<table style='border: solid 1px black;'>";
            echo "<tr> ";
            foreach ($sql_execute[0] as $k => $v) {
                if ($do_once < count($sql_execute[0]))
                    echo "<th class=\"table_header\">" . $k . "</th>";
                else
                    continue;

                $do_once++;
            }
            echo "</tr>";
            $do_once = $do_once-$do_once;
            foreach($sql_execute as $k=>$v)
            {
                echo "<tr><td style=\"padding-left:5px;padding-right:5px;border:1px solid black;\">";
                echo "<a href=\"".$base_url."kit_db/lab_edit.php?lab_id=";
                echo $sql_execute[$do_once]['Lab ID']."\">".$sql_execute[$do_once]['Lab ID']."</a>";
                echo "</td><td style=\"padding-left:5px;padding-right:5px;border:1px solid black;\">";
                echo $sql_execute[$do_once]['Lab Name'];
                echo "</td></tr>";
                $book_name = $sql_execute[$do_once]['Book Name'];
                $do_once++;
            }
//            foreach (new TableRows(new RecursiveArrayIterator($sql_execute)) as $k => $v)
//            {
//                echo $v;
//            }
            echo "</table>";
        }
        echo "<p>". count($sql_execute) ."Labs in this book.</p>";
    }

}