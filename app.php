<?php
require 'vendor/autoload.php';
require 'src/html_loader.php';
$app = new \Slim\Slim(array('debug'=>true,'templates.path'=>__DIR__."/templates"));

$app->get("/", function() use ($app){
	$app->render("main.html");
});


$app->post("/",function() use ($app){
     try{
	 $url = $app->request->post("urlInput");
	 $html_load = new HtmlLoader($url);
	 $html_load->load_html_content();
	 $html_load->parse_html_to_json();
	 $app->render("post.html",array("url"=>$url,"html"=>$html_load->get_html(),"json"=>$html_load->get_json_result()));
	//	 $app->render("main.html",array("url"=>$url,"html"=>$html_load->get_html()));
     } catch(Exception $e){
	     echo "Something goes wrong";
		 echo $e;
	 }
});

$app->run();
	
?>