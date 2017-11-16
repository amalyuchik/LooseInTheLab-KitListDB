<?php
/**
 * Created by IntelliJ IDEA.
 * User: amalyuchik
 * Date: 11/15/2017
 * Time: 3:52 PM
 */

class presenter_kit_list_item
{
    var $source_product = array();
    var $product_id;
    var $product_name;
    var $product_sku;
    var $product_category_id;
    var $product_category;
    var $product_reusable_price = 0;
    var $product_consumable_price = 0;
    var $reusable_qty = 0;
    var $consumable_qty = 0;

    /**
     * classroom_kit_list_item constructor.
     * @param array $source_product
     * @param $product_id
     * @param $product_name
     * @param $product_sku
     * @param $product_category_id
     * @param $product_category
     * @param product_reusable_price
     * @param $product_price
     * @param $reusable_qty
     * @param $consumable_qty
     */
    public function __construct(array $source_product)
    {
        $this->product_id = $source_product['product_id'];
        $this->product_name = $source_product['product_name'];
        $this->product_sku = $source_product['product_sku'];
        $this->product_category_id = $source_product['product_category_id_fk'];
        $this->product_category = $source_product['product_category'];
        if ($source_product['reusable']) {
            $this->reusable_qty = $source_product['lab_product_line_presenter_qty'];
            $this->product_reusable_price = $source_product['product_price']*$source_product['lab_product_line_presenter_qty'];
        }
        else {
            $this->consumable_qty = $source_product['lab_product_line_presenter_qty'];
            $this->product_consumable_price = $source_product['product_price']*$source_product['lab_product_line_presenter_qty'];
        }
        //$this->source_product = [];
        return $this;
    }
}