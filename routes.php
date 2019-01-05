<?php
    /* This file creates the routes */

    /* Including Route Class */
    include 'Route.php';

    /* Creating route instance */
    $route = new Route();

    $route->add("/id/{id}/mateus/{mateus}" , "GET", "Home@index");
    
    /* $route->add("/userid/{user_id}", "POST", "Home@contact"); */
    
    $route->add("/username/{user_name}", "POST", function($user_name){
        echo $user_name;
    });
    
    $route->add("/redirect", "POST", function(){
        include_once("html.html");
    });
    
    /* $route->showRoutes(); */