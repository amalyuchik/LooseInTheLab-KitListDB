<?php
/**
 * Created by PhpStorm.
 * User: sportypants
 * Date: 4/15/17
 * Time: 5:16 PM
 */
class select_input
{
    var $data_array_ID_data = array();
    var $label = '';
    var $select_name = '';
    var $selected_item_id = '';

    function __construct($data_array_ID_data = array('ID','data'), $label, $select_name, $selected_item_id=null)
    {
        $this->data_array = $data_array_ID_data;
        $this->label = $label;
        $this->select_name = $select_name;
        $this->selected_item_id = $selected_item_id;

        if($this->label !== '')
            echo "<label for='".$this->select_name."' class=\"control-label\">$this->label: &nbsp; </label>";
        echo "<div class=\"form-group\" style='margin-right: 20px;'><select onchange='alertMessage($this->select_name);' class=\"form-control\" id=\"$this->select_name\" name=\"$this->select_name\" style=\"max-width: 200px;\">";
        echo "<option value=\"\">Select ".$this->label."</option>\n\r"; //Need to make this as an initial selected item
        foreach($this->data_array as $arr)
        {
            echo "<option value=\"".$arr['ID']."\"";

            if($this->selected_item_id == $arr['ID'])
                echo " selected";

            echo " >".$arr['data']."</option>";
        }
        //echo "</input>";
        echo "</select></div>";
        if($this->label !== '')
           echo "&nbsp";
    }
}
?>