<?php

    /* 
        This class is created by Mateus Soares <https://github.com/mateussoares1997> 
        version: 0.0.1
    */

    class Route{

        //Routes list
        private $_routes = [];

        //Adds new route
        public function add($uri, $requestType, $method)
        {
            //@TODO Create script to check if there's a route with the same parameters 
            
            //Creates an array with all parameters
            $parametersArray = explode("/", $uri);

            //Cleans all empty indexes from URI's parameters (the script does not count "/" as a parameter)
            while(false !== ($index = array_search("", $parametersArray)))
            {
                unset($parametersArray[$index]);
            }

            /* Creating route parameters array and checking whether the route is valid */
            $parameters = [];

            foreach($parametersArray as $key => $value)
            {
                /* 
                    Checks whether the informed route structure is valid or not
                */

                if( 
                    (strpos($value, '{') != 0) || 
                    (strpos($value, '{') === 0 && strpos($value, '}') !== (strlen($value) - 1)) || 
                    (strpos($value, '{') === 0 && strlen($value) < 3) ||
                    (strpos($value, '{') === false && strpos($value, '}') !== false)
                )
                {
                    echo "Error: The route $uri is not valid, check the its syntax";
                    exit;
                }
                else
                {
                    /*
                        The parameter will be named with just its name OR "Variable: " + its name; 
                        When the name of the parameter has "Variable: " in it, the parameter is a variable to be sent when 
                        requested the route.
                    */
                    if(strpos($value, '{') === 0)
                    {   
                        //if the parameter has "{" it's a variable parameter and we have to check somethings 
                        
                        /*
                            First we have t check if the previous parameter exists and it's not a variable parameter as well
                            the routing system requires a route to have a parameter previous a variable parameter:
                            /{username} -> this is an invalid route
                            /user_name/{username} -> this is an valid route 
                        */ 

                        if(count($parameters) - 1 === -1)
                        //if the array of parameters has not a previous parameter the informed route is invalid
                        {
                            echo "Error: The route $uri is not valid";
                            exit;
                        }
                        else
                        {
                            if(false === strrpos($parameters[count($parameters) - 1], "Variable: "))
                            //if the array of parameters has a previous parameter and it's not a variable parameter then the route is valid
                            {
                                $parameters[] = "Variable: ". substr($value, 1, (strlen($value) - 2));
                            }
                            else
                            {
                                echo "Error: The route $uri is not valid";
                                exit;
                            }
                        }

                    }
                    else
                    {
                        $parameters[] = $value;
                    }
                    
                }

            }

            /* Mounts route and adds it in array  */
            $this->_routes[] = [
                "uri" => $uri,
                "parameters" => $parameters,
                "requestType" => $requestType,
                "action" => $method
            ];

        }

        //Submits route
        public function submit($requestedUri, $request_Type){
            
            $parametersSent = explode("/", $requestedUri);

            while(false !== ($index = array_search("", $parametersSent)))
            {
                unset($parametersSent[$index]);
            }

            foreach($this->_routes as $key => $route)
            {

                /* TODO make checking of route params and create "do action" */

                //Checks whether the current loop's route has the same quantity of arguments of requested url 
                if(count($route["parameters"]) === count($parametersSent))
                {

                    $errors = 0;
                    $parameters = [];
                    foreach($route["parameters"] as $key => $route_parameter)
                    {
                        if(false !== strrpos($route_parameter, "Variable: "))
                        {
                            $parameters[str_replace("Variable: ","",$route_parameter)] = $parametersSent[$key];
                        }
                        else
                        {
                            if($route_parameter !== $parametersSent[$key])
                            {
                                $errors++;
                            }
                        }
                    }

                    var_dump($parameters);

                    if($errors === 0)
                    {
                        if(is_callable($route["action"]))
                        {
                            call_user_func_array($route["action"], $parameters);
                        }
                        else
                        {
                            //@todo create script to understand string actions 
                        }
                    }
                    
                }

            }

        } 

        //@TODO Create a method to display all set routes
        public function showRoutes(){

            var_dump($this->_routes);

        }

    }
