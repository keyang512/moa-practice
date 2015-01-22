<?php
/**
 * Created by PhpStorm.
 * User: keyang
 * Date: 1/20/15
 * Time: 8:50 PM
 */

require "./html_element.php";


class HtmlElementCollectionAlgorithm {

    private $firstElement;
    private $secondElement;

    public function __construct($firstElement,$secondElement){
        $this->firstElement = $firstElement;
        $this->secondElement = $secondElement;
    }

    public function fix_algorithm(){

           $firstElement = $this->firstElement;
           $secondElement = $this->secondElement;

            if($firstElement->is_self_closed_element()){
                return;
            }


        if($firstElement->getTagName() == $secondElement->getTagName()){
            if(!($firstElement->is_self_closed_element())){
                $secondElement>append_close_mark_head();
            }
        }else{
            $firstElement->append_close_mark_to_tail();
        }


    }

}