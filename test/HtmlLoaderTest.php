<?php
/**
 * Created by PhpStorm.
 * User: keyang
 * Date: 1/20/15
 * Time: 12:46 PM
 */

require "../src/html_loader.php";
class HtmlLoaderTest extends PHPUnit_Framework_TestCase {


    public function testFixFunction(){
        $url = "http://testing.moacreative.com/job_interview/php/index.html";
        $html_loader = new HtmlLoader($url);

        $html = "<a class=\"title\" href=\"http://www.imdb.com/title/tt1049413/\">up<a>";
        print $html_loader->fixHtml($html);

    }

    public function testHtmlLoaderCorrect(){
        $url = "http://testing.moacreative.com/job_interview/php/index.html";
        $html_loader = new HtmlLoader($url);
        $html_loader->load_html_content();
        $html_loader->parse_html_to_json();

        print $html_loader->get_json_result();

    }
}
