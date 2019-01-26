<?php
    /* This file creates the routes */

    /* Including Route Class */
    include 'Route.php';

    /* Creating route instance */
    $route = new Route();

    $route->add("/", "GET", function(){
        include_once("views/home.php");
    });
    
    $route->add("/name/{name}/lastname/{lastname}", "POST", function($name, $lastname){
        echo $name." ".$lastname;
    });
    
    $route->add("/name/{name}/lastname/{lastname}", "GET", function($name, $lastname){
        echo $name." ".$lastname;
    });
   