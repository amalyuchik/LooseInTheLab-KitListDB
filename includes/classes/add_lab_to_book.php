<?php
/**
 * Created by PhpStorm.
 * User: amalyuchik
 * Date: 7/20/2017
 * Time: 11:40 AM
 */

class add_lab_to_book
{
    var $book_id = null;
    var $global_lab_names_list = array();

    /**
     * add_lab_to_book constructor.
     * @param null $book_id
     * @param array $global_lab_names_list
     */
    public function __construct(array $global_lab_names_list,$book_id)
    {
        $this->book_id = $book_id;
        $this->global_lab_names_list = $global_lab_names_list;
        $base_url = "http://www.seriouslyfunnyscience.com/";
        $thispage = $_SERVER['PHP_SELF'];
        //$return_row = $this->return_value_string($global_lab_names_list,'', $_SERVER['PHP_SELF']);

    }
    function return_value_string($global_lab_names_list,$return_row, $thispage)
    {
        $return_row .= "<tr style=\"background-color: lemonchiffon;\"><td></td><td style='padding-top: 10px;min-width: 300px;'>";
        $return_row .= "<p style=\"text-align: right;margin-top:22px;float: right;\">";
        //echo "<a href=\"". $thispage . "?save_add=1&lab_id=$this->lab_id\" ><i style=\"color: red;\" class=\"fa fa-floppy-o\" aria-hidden=\"true\"></i></a>";//
        $return_row .= "&nbsp;<a href=\"". $thispage . "?book_id=$this->book_id\" ><i style=\"color: red;\" class=\"fa fa-times\" aria-hidden=\"true\"></i></a>"; //
        $return_row .= "&nbsp;&nbsp;<input type=\"submit\"  value=\"Add\" >";
        $return_row .= "<input type=\"hidden\" name=\"book_id\" value=".$this->book_id."></p>";
        $select_field_lab = new select_input($global_lab_names_list,'Lab to Add','lab_id');
        $return_row .= $select_field_lab->create_select_field(false);
        $return_row .= "</td><td></td></tr>";


        return $return_row;
    }
}