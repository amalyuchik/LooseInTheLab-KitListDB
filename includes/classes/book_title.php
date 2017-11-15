<?php
/**
 * Created by IntelliJ IDEA.
 * User: amalyuchik
 * Date: 11/14/2017
 * Time: 1:15 PM
 */
require_once($_SERVER['DOCUMENT_ROOT']."/kit_db/includes/globals.php");
class book_title
{
    var $connection_array = array();
    var $book_title = '';
    var $book_id;
    var $conn;
    var $select_sql;


    /**
     * book_title constructor.
     * @param string $book_title
     * @param $book_id
     */
    public function __construct(array $connection_array,$book_title='', $book_id, $conn, $select_sql)
    {
        $this->connection_array = $connection_array;
        $this->book_title = $book_title;
        $this->book_id = $book_id;
        $this->conn = $conn;
        $this->select_sql = $select_sql;

        echo $this->retrieve_book_title($this->book_id);
    }

        function retrieve_book_title($bk_id)
    {
        $book_name_select = $this->select_sql->query("book_name", null, null, $bk_id);
        $book_name_sql_execute = $this->conn->runconn_sql_execute($this->connection_array, $book_name_select);
        $this->book_title = $book_name_sql_execute[0]['Book Name'];
    }
        public function __toString()
    {
        return $this->book_title;
    }
}