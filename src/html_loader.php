<?php

/**
 * Created by PhpStorm.
 * User: keyang
 * Date: 1/20/15
 * Time: 12:41 PM
 */

require "html_element.php";

class HtmlLoader
{

    private $url;
    private $html_content;
    private $json_result;

    private $exclue_list = array("div", "head", "body", "!doctype", "html");

    public function __construct($url)
    {
        $this->url = $url;
    }

    public function load_html_content()
    {

        if (empty($this->url)) {
            throw new Exception("request url can not be empty!");
        }
        $html = file_get_contents($this->url);
        $this->html_content = $html;
    }

    public function parse_html_to_json()
    {
        if (empty($this->html_content)) {
            throw new Exception("request html content can not be empty!");
        }

        $this->html_content = $this->fixHtml($this->html_content);


        $dom = new DOMDocument();
        $dom->recover = TRUE;
        $dom->loadHTML($this->html_content);
        $htmlNode = $dom->documentElement;

        $this->json_result = json_encode(array($htmlNode->tagName => $this->node_info_to_json($htmlNode)));


    }

    public function fixHtml($html)
    {
        $startIndex = 0;

        while ($startIndex >= 0 && $startIndex < strlen($html)) {
            $firstHtmlElement = $this->findNextHtmlElement($html, $startIndex);

            if ($firstHtmlElement == null) break;

            $secondHtmlElement = $this->findNextHtmlElement($html, $firstHtmlElement->get_end_position() + 1);

            if ($secondHtmlElement == null) break;

            if ($firstHtmlElement->is_self_closed_element() || $firstHtmlElement->is_end_tag()) {
                $startIndex = $firstHtmlElement->get_end_position();
            } else if (!in_array($firstHtmlElement->get_tag_name(), $this->exclue_list)) {

                if ($firstHtmlElement->get_tag_name() == $secondHtmlElement->get_tag_name() && $secondHtmlElement->is_end_tag()) {
                    $startIndex = $secondHtmlElement->get_end_position();
                } else if ($firstHtmlElement->get_tag_name() == $secondHtmlElement->get_tag_name() && !$secondHtmlElement->is_end_tag()) {
                    if ($secondHtmlElement->has_attribute()) {
                        $html = substr_replace($html, "/", $firstHtmlElement->get_end_position(), 0);
                        $startIndex = $firstHtmlElement->get_end_position();
                    } else if ($secondHtmlElement->get_start_position() - $firstHtmlElement->get_end_position() > 1) {

                        $html = substr_replace($html, "/", $secondHtmlElement->get_start_position() + 1, 0);
                        $startIndex = $firstHtmlElement->get_end_position();
                    }

                } else if ($firstHtmlElement->get_tag_name() != $secondHtmlElement->get_tag_name()) {

                    $html = substr_replace($html, "/", $firstHtmlElement->get_end_position(), 0);
                    $startIndex = $firstHtmlElement->get_end_position();
                } else {
                    $startIndex = $secondHtmlElement->get_end_position();
                }
            } else {
                $startIndex = $firstHtmlElement->get_end_position();
            }

            $startIndex += 1;

            if (strpos($html, "<", $startIndex) != TRUE)
                break;

        };


        return $html;
    }


    private function findNextHtmlElement($html, $startIndex)
    {

        $firstStartAppear = strpos($html, "<", $startIndex);
        if ($firstStartAppear < 0) return null;

        $firstEndAppear = strpos($html, ">", $firstStartAppear);
        if ($firstEndAppear < 0) return null;
        $firstFullElement = substr($html, $firstStartAppear, $firstEndAppear - $firstStartAppear + 1);


        return new HtmlElement($firstFullElement, $firstStartAppear, $firstEndAppear);

    }

    private function traverseNode($nodes)
    {
        $returnValue = array();
        foreach ($nodes as $node) {
            $nodeValue = null;


            if ($node->localName == "") continue;

            if ($node->localName == "div" && $node->getAttribute("id") !== "") {
                $returnValue[$node->localName . "#" . $node->getAttribute("id")] = $this->node_info_to_json($node);

            } else {
                $returnValue[$node->localName] = $this->node_info_to_json($node);

            }
        }

        return $returnValue;
    }

    private function node_info_to_json($node)
    {
        $returnValue = array("attributes" => array(), "value" => array());

        if ($node->attributes && $node->attributes->length > 0) {

            $length = $node->attributes->length;
            for ($i = 0; $i < $length; ++$i) {
                $item = $node->attributes->item($i);
                $returnValue["attributes"][$item->nodeName] = $item->nodeValue;
            }
        }

        $returnValue["value"] = $node->nodeValue;

        if ($node->hasChildNodes() && $node->childNodes->length > 1) {
            $returnValue["child_nodes"] = $this->traverseNode($node->childNodes);
        }

        if($node->childNodes->length > 1 && $returnValue["child_nodes"] !== null  && count($returnValue["child_nodes"])==0){
            unset($returnValue[2]);
        }



        return $returnValue;
    }


    public function get_json_result()
    {
        return $this->json_result;
    }

    public function get_html()
    {
        return $this->html_content;
    }
}