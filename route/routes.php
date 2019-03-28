<?php
    /* This file creates the routes */

    /* Including Route Class */
    include 'Route.php';

    /* Creating route instance */
    $route = new Route();

    $route->get("/", function(){
		include_once("view/home.php");
	});
    
    $route->get("/name/{name}/lastname/{lastname}", function($name, $lastname){
        echo $name." ".$lastname;
        echo "<br>Age: ".$_GET["age"];
    });
    
    $route->post("user/name/{name}/lastname/{lastname}", "User@show");
	
    $route->get("/show-routes", function() use ($route){
        $route->showRoutes();
    });