<?php
/**
 * Created by PhpStorm.
 * User: sportypants
 * Date: 5/2/17
 * Time: 8:26 AM
 */
class add_lab_product
{
    var $lab_id = null;
    var $global_product_names_list = array();

    function __construct($global_product_names_list, $lab_id)
    {
        //require_once($_SERVER['DOCUMENT_ROOT']."/kit_db/includes/form_element_classes/select_input.php");
        $this->global_product_names_list = $global_product_names_list;
        $this->lab_id = $lab_id;
        $base_url = "http://www.seriouslyfunnyscience.com/";
        $thispage = $_SERVER['PHP_SELF'];

        echo "<tr style=\"background-color: lemonchiffon;\">";
        echo "<td><p style=\"width:200px;\">";
        $select_field_product = new select_input($global_product_names_list,'','product_id');
        echo $select_field_product->create_select_field(false);
        echo "</p></td>";

        echo "<td><p>";
        echo '';
        echo "</p></td>";

        echo "<td><p>";
        echo "<input type=\"hidden\" name=\"lab_id\" value=".$this->lab_id."><input type=\"text\" size=\"5\" name=\"classroom_qty_product_add\" maxlength=\"255\" value=\"\">";
        echo "</p></td>";

        echo "<td><p>";
        echo "<input type=\"text\" size=\"5\" name=\"refill_qty_product_add\" maxlength=\"255\" value=\"\">";
        echo "</p></td>";

        echo "<td><p>";
        echo "<input type=\"text\" size=\"12\" name=\"refill_qty_detail_product_add\" maxlength=\"255\" value=\"\">";
        echo "</p></td>";

        echo "<td><p>";
        echo "<input type=\"text\" size=\"5\" name=\"participant_qty_product_add\" maxlength=\"255\" value=\"\">";
        echo "</p></td>";

        echo "<td><p>";
        echo "<input type=\"text\" size=\"5\" name=\"presenter_qty_product_add\" maxlength=\"255\" value=\"\">";
        echo "</p></td>";

        echo "<td><p>";
        echo "<input type=\"text\" size=\"5\" name=\"retail_qty_product_add\" maxlength=\"255\" value=\"\">";
        echo "</p></td>";

        echo "<td><p>";
        echo "<input type=\"text\" size=\"12\" name=\"retail_qty_detail_product_add\" maxlength=\"255\" value=\"\">";
        echo "</p></td>";

        echo "<td align=\"center\"><p style=\"margin-left: -10px;\">";
        echo "<input type=\"checkbox\" name=\"product_reusable_product_add\" value=\"1\">";
        echo "</p></td>";

        echo "<td><p>";
        echo "";
        echo "</p></td>";

        echo "<td><p style=\"padding-right:10px;\">";
        //echo "<a href=\"". $thispage . "?save_add=1&lab_id=$this->lab_id\" ><i style=\"color: red;\" class=\"fa fa-floppy-o\" aria-hidden=\"true\"></i></a>";//
        echo "&nbsp;<a href=\"". $thispage . "?lab_id=$this->lab_id\" ><i style=\"color: red;\" class=\"fa fa-times\" aria-hidden=\"true\"></i></a>"; //
        echo "&nbsp;&nbsp;<input type=\"submit\"  value=\"Add\" >";
        echo "</p></td>";

        echo "</tr>";

    }
}
?>
