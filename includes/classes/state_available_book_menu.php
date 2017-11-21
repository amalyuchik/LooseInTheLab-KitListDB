<?php
/**
 * Created by IntelliJ IDEA.
 * User: amalyuchik
 * Date: 11/20/2017
 * Time: 3:07 PM
 */
require_once($_SERVER['DOCUMENT_ROOT']."/kit_db/includes/globals.php");
require_once($_SERVER['DOCUMENT_ROOT']."/kit_db/includes/table_rows.php");
require_once($_SERVER['DOCUMENT_ROOT']."/kit_db/includes/classes/add_lab_to_book.php");
class state_available_book_menu
{
    var $connection_array = array();
    var $state_name = '';
    var $state_id;
    var $conn;
    var $select_sql;
    var $iteration = 0;
    //var $global_lab_names_list = array();
    var $book_menu_string = '';
    var $do_once = 0;


    public function __construct(array $connection_array, $state_name, $state_id, $conn, $select_sql, $base_url = "http://www.seriouslyfunnyscience.com/")
    {
        $this->connection_array = $connection_array;
        $this->state_name = $state_name; //As of right now, this is empty. May change in the future
        $this->state_id = $state_id;
        $this->conn = $conn;
        $this->select_sql = $select_sql;
        $this->book_menu_string = '';
        $this->iteration = 0;
        //$this->global_lab_names_list = $global_lab_names_list;
        $this->base_url = $base_url = "http://www.seriouslyfunnyscience.com/";

        echo $this->generate_book_name_menu($this->state_id);
    }

    function generate_book_name_menu($state_id)
    {
        $query_select = $this->select_sql->query("books", $state_id, null, null);
//        echo $query_select;
//        echo "this";
        $this->sql_execute = $this->conn->runconn_sql_execute($this->connection_array, $query_select);

        $this->book_menu_string = '';
        $this->iteration = $this->iteration - $this->iteration;
        $this->book_menu_string .= "<header>";
        $this->book_menu_string .= "<strong>Available Books for <em style='font-size: 18px; color: darkred;'>".$this->sql_execute[0]['book_state']."</em></strong>";

        $this->book_menu_string .= "</header>";
        $this->book_menu_string .= "<div class=\"list-group table-of-contents\">";
        foreach ($this->sql_execute as $k => $v)
        {
            $this->book_menu_string .= "<a class=\"list-group-item\" href=\"#\" onclick=\"postBook(";//" . $_SERVER['PHP_SELF'] . "?book_id=
            $this->book_menu_string .= $this->sql_execute[$this->iteration]['book_id'] .")\">" . $this->sql_execute[$this->iteration]['Book Name'];
            $this->book_menu_string .= "</a>";
            $this->book_menu_string .= "\r\n";
            $this->iteration++;
        }
        $this->book_menu_string .= "</div>";
        return $this->book_menu_string;
    }
}