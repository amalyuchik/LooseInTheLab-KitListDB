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

    var $book_detail_html_string = '';

    /**
     * book_detail constructor.
     * @param array $connection_array
     * @param string $book_name
     * @param $book_id
     * @param $conn
     * @param $select_sql
     */
    public function __construct(array $connection_array, $book_name, $book_id, $conn, $select_sql, $book_detail_html_string = '', $base_url = "http://www.seriouslyfunnyscience.com/")
    {
        $this->connection_array = $connection_array;
        $this->book_name = $book_name; //As of right now, this is empty. May change in the future
        $this->book_id = $book_id;
        $this->conn = $conn;
        $this->select_sql = $select_sql;
        $this->book_detail_html_string = $book_detail_html_string;
        $this->base_url = $base_url;
        echo $this->retrieve_book($this->book_id);
    }


    function retrieve_book($bk_id)
    {
        $query_select = $this->select_sql->query("book_contents", null, null, $bk_id);
        $sql_execute = $this->conn->runconn_sql_execute($this->connection_array, $query_select);
  //      var $do_once=0;
        if($sql_execute !== null)
        {
            $this->book_detail_html_string .= "<header><h1>Testing</h1></header>";
            $this->book_detail_html_string .=  "<table style='border: solid 1px black;'>";
            $this->book_detail_html_string .=  "<tr> ";
            foreach ($sql_execute[0] as $k => $v) {
                if ($do_once < count($sql_execute[0]))
                    $this->book_detail_html_string .=  "<th class=\"table_header\">" . $k . "</th>";
                else
                    continue;

                $do_once++;
            }
            $this->book_detail_html_string .=  "</tr>";
            $do_once = $do_once-$do_once;
            foreach($sql_execute as $k=>$v)
            {
                $this->book_detail_html_string .=  "<tr><td style=\"padding-left:5px;padding-right:5px;border:1px solid black;\">";
                $this->book_detail_html_string .=  "<a href=\"".$this->base_url."kit_db/lab_edit.php?lab_id=";
                $this->book_detail_html_string .=  $sql_execute[$do_once]['Lab ID']."\">".$sql_execute[$do_once]['Lab ID']."</a>";
                $this->book_detail_html_string .=  "</td><td style=\"padding-left:5px;padding-right:5px;border:1px solid black;\">";
                $this->book_detail_html_string .=  $sql_execute[$do_once]['Lab Name'];
                $this->book_detail_html_string .=  "</td></tr>";
                $this->book_name = $sql_execute[$do_once]['Book Name'];
                $do_once++;
            }
//            foreach (new TableRows(new RecursiveArrayIterator($sql_execute)) as $k => $v)
//            {
//                echo $v;
//            }
            $this->book_detail_html_string .=  "</table>";
        }
        $this->book_detail_html_string .=  "<p>". count($sql_execute) ."Labs in this book.</p>";
        return $this->book_detail_html_string;
    }

}