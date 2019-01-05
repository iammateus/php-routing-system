<?php

    include 'routes.php';

    //Gets requested URI
    $requested_uri = isset($_GET["uri"]) ? $_GET["uri"] : "/";

    //Gets request type
    $request_type = $_SERVER['REQUEST_METHOD'];

    //Submits Route
    $route->submit($requested_uri, $request_type);