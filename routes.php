<?php
    /* This file creates the routes */

    /* Including Route Class */
    include 'Route.php';

    /* Creating route instance */
    $route = new Route();

    $route->add("/", "GET", function(){
        include_once("home.php");
    });

    $route->add("/id/{id}/name/{name}" , "GET", "Home@info");
    
    $route->add("/name/{name}", "POST", function($name){
        echo $name;
    });