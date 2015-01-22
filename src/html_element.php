<?php

/**
 * Created by PhpStorm.
 * User: keyang
 * Date: 1/20/15
 * Time: 8:46 PM
 */
class HtmlElement
{

    private $element_declarative;
    private $start_position_in_html_string;
    private $end_position_in_html_string;
    private $is_self_closed_element = false;
    private $is_end_tag = false;

    private $is_need_append_close_mark_to_head = false;
    private $is_need_append_close_mark_to_Tail = false;

    private $tag_name;

    public function __construct($element_declarative, $start_position_in_html_string, $end_position_in_html_string)
    {
        $this->element_declarative = $element_declarative;
        $this->start_position_in_html_string = $start_position_in_html_string;
        $this->end_position_in_html_string = $end_position_in_html_string;


        if (substr($element_declarative, strlen($element_declarative) - strlen("/>"), strlen("/>")) === "/>") {
            $this->is_self_closed_element = true;
        } else if(substr($element_declarative, 0, strlen("</")) === "</"){
            $this->is_end_tag = true;
        }

        if ($this->is_end_tag()) {
            $this->tag_name = substr($element_declarative, 2, strlen($element_declarative) - 3);
        } else if ($this->is_self_closed_element && !strpos($element_declarative, " ")) {
            $this->tag_name = substr($element_declarative, 1, strlen($element_declarative) - 3);

        } else {
            if (strpos($element_declarative, " ") > 0) {

                $this->tag_name = substr(explode(" ", $element_declarative)[0], 1);
            } else {

                $this->tag_name = substr($element_declarative, 1, strlen($element_declarative) - 2);

            }
        }

    }

    public function is_self_closed_element()
    {
        return $this->is_self_closed_element;
    }

    public function append_close_mark_to_head()
    {
        $this->is_need_append_close_mark_to_head = true;
    }

    public function is_need_append_close_mark_to_head()
    {
        return $this->is_need_append_close_mark_to_head;
    }

    public function append_close_mark_to_Tail()
    {
        $this->is_need_append_close_mark_to_Tail = true;
    }

    public function is_need_append_close_mark_to_Tail()
    {
        return $this->is_need_append_close_mark_to_Tail;
    }

    public function get_tag_name()
    {
        return $this->tag_name;
    }

    public function get_end_position()
    {
        return $this->end_position_in_html_string;
    }

    public function get_start_position()
    {
        return $this->start_position_in_html_string;
    }

    public function is_end_tag(){
        return $this->is_end_tag;
    }

    public function has_attribute(){
        return strpos($this->element_declarative, " ") > 0;
    }

    public function get_full(){
        return $this->element_declarative;
    }
}