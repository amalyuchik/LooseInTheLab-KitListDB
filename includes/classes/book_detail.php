<?php
/**
 * Created by PhpStorm.
 * User: amalyuchik
 * Date: 7/19/2017
 * Time: 10:38 AM
 */
require_once($_SERVER['DOCUMENT_ROOT']."/kit_db/includes/globals.php");
require_once($_SERVER['DOCUMENT_ROOT']."/kit_db/includes/table_rows.php");
require_once($_SERVER['DOCUMENT_ROOT']."/kit_db/includes/classes/add_lab_to_book.php");

class book_detail
{
    var $connection_array = array();
    var $book_name = '';
    var $book_id;
    var $conn;
    var $select_sql;
    var $global_lab_names_list = array();
    var $book_detail_html_string = '';
    var $do_once = 0;

    /**
     * book_detail constructor.
     * @param array $connection_array
     * @param string $book_name
     * @param $book_id
     * @param $conn
     * @param $select_sql
     */
    public function __construct(array $connection_array, $book_name, $book_id, $conn, $select_sql, $book_detail_html_string = '', $base_url = "http://www.seriouslyfunnyscience.com/", array $global_lab_names_list = null)
    {
        $this->connection_array = $connection_array;
        $this->book_name = $book_name; //As of right now, this is empty. May change in the future
        $this->book_id = $book_id;
        $this->conn = $conn;
        $this->select_sql = $select_sql;
        $this->book_detail_html_string = $book_detail_html_string;
        $this->global_lab_names_list = $global_lab_names_list;
        $this->base_url = $base_url = "http://www.seriouslyfunnyscience.com/";
//        $base_url = "http://www.seriouslyfunnyscience.com/";
        echo $this->retrieve_book($this->book_id,$this->global_lab_names_list);
    }


    function retrieve_book($bk_id, $global_lab_names_list)
    {
        $query_select = $this->select_sql->query("book_contents", null, null, $bk_id);
        $sql_execute = $this->conn->runconn_sql_execute($this->connection_array, $query_select);

        $book_name_select = $this->select_sql->query("book_name", null, null, $bk_id);
        $book_name_sql_execute = $this->conn->runconn_sql_execute($this->connection_array,$book_name_select);
  //      var $do_once=0;
        $this->book_detail_html_string .= "<header><h1>".$book_name_sql_execute[0]['Book Name']."</h1></header>";
        $this->book_detail_html_string .=  "<p>". count($sql_execute) ." Labs in this book.</p>";

        $this->book_detail_html_string .=  "<a href=\"".$this->base_url."kit_db/grade_level_kit.php?book_id=".$bk_id."\" target=_blank >Grade Level Kit</a> | ";
        $this->book_detail_html_string .=  "<a href=\"".$this->base_url."kit_db/classroom_level_kit.php?book_id=".$bk_id."\" target=_blank >Classroom Level Kit</a> | ";
        $this->book_detail_html_string .=  "<a href=\"".$this->base_url."kit_db/refill_kit.php?book_id=".$bk_id."\" target=_blank >Refill Kits</a> | ";
        $this->book_detail_html_string .=  "<a href=\"".$this->base_url."kit_db/workshop_kit.php?book_id=".$bk_id."\" target=_blank >Workshop Kits</a>";

        $this->book_detail_html_string .=  "<table id='book_detail_table' style='border: solid 1px grey;'>";
        $this->book_detail_html_string .=  "<tr> ";
        if ($sql_execute[0] != null) {
            foreach ($sql_execute[0] as $k => $v) {
                if ($do_once < count($sql_execute[0])) {
                    $this->book_detail_html_string .= "<th style='padding-top: 5px;padding-bottom: 5px;' class=\"table_header\">" . $k . "</th>";
                } else
                    continue;

                $do_once++;
            }
        }

        $this->book_detail_html_string .=  "</tr>";
        $do_once = $do_once-$do_once;

        $this->book_detail_html_string .= "<tr id=\"top_row\" class=\"blank_white_row\" style='border-bottom:1px solid grey;'>";


            $this->book_detail_html_string .= "<td><a id='add_link' onclick=\"addLabToBook(".$this->book_id.")\" href=\"#\">Add a Lab</a></td><td></td><td></td>";//$thispage ? book_id = $this->book_id & add_lab = 1

        $this->book_detail_html_string .= "</tr><tr id=\"insert_lab_row\" style='background-color: lemonchiffon;border-bottom:1px solid grey;'></tr>";

        if($sql_execute !== null)
        {
            foreach($sql_execute as $k=>$v)
            {
                $this->book_detail_html_string .=  "<tr><td style=\"padding-left:5px;padding-right:5px;border-bottom:1px solid grey;text-align: center;\">";
                $this->book_detail_html_string .=  "<a href=\"".$this->base_url."kit_db/lab_edit.php?lab_id=";
                $this->book_detail_html_string .=  $sql_execute[$do_once]['Lab ID']."\">".$sql_execute[$do_once]['Lab ID']."</a>";
                $this->book_detail_html_string .=  "</td><td style=\"padding-left:5px;padding-right:5px;border-bottom:1px solid grey;min-width: 300px;\">";
                $this->book_detail_html_string .=  "<a href=\"".$this->base_url."kit_db/lab_edit.php?lab_id=".$sql_execute[$do_once]['Lab ID']."\">".$sql_execute[$do_once]['Lab Name']."</a>";
                $this->book_detail_html_string .=  "</td>";
                $this->book_detail_html_string .=  "<td style=\"padding-left:5px;padding-right:5px;border-bottom:1px solid grey;\"><p style=\"padding - right:10px;min-width:30px;text-align:center;\">&nbsp;<a href=\"#\" onclick='deleteLabFromBook(".$sql_execute[$do_once]['Delete'].",". $this->book_id .");' ><i style = \"color: red;\" class=\"fa fa-trash-o\" aria - hidden = \"true\" ></i ></a ></p></td></tr>"."\r\n";

                $do_once++;
            }

            $this->book_detail_html_string .=  "</table>";
        }

        return $this->book_detail_html_string;
    }

}