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
        $this->data_array_ID_data = $data_array_ID_data;
        $this->label = $label;
        $this->select_name = $select_name;
        $this->selected_item_id = $selected_item_id;
        $on_change = null;


    }

    function create_select_field($on_change)
    {
        $return_string = '';

        if($this->label !== '')
            $return_string .= "<label for='".$this->select_name."' class=\"control-label\">$this->label: &nbsp; </label>";

        $return_string .= "<select";

        if ($on_change)
            $return_string .= " onchange='selected($this->selected_item_id);' "; //ASM need to add this flag to other pages so it works properly

        $return_string .= " class=\"form-control\" id=\"$this->select_name\" name=\"$this->select_name\" style=\"max-width: 200px;\">";
        $return_string .= "<option value=\"\">Select ".$this->label."</option>"; //Need to make this as an initial selected item

        foreach($this->data_array_ID_data as $arr)
        {
            $return_string .= "<option value=\"".$arr['ID']."\"";
            if($this->selected_item_id == $arr['ID'])
                $return_string .= " selected ";

            $return_string .= ">".$arr['data']."</option>"."\r\n";
        }

        $return_string .= "</select>";//</div>
        if($this->label !== '')
            $return_string .= "&nbsp";

        return $return_string;
    }
}
