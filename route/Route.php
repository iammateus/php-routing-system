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
			//Gets params from URI
			$parametersArray = $this->getParamsFromURI($uri);
			
			//Checks if URI is unique
            $this->checkIfRouteIsUnique($parametersArray, $requestType);

			//Validates URI and creates an object of params to the route 
			$parameters = $this->parseParams($parametersArray, $requestType);
            
            //Mounts route object and adds it in routes list
            $this->_routes[] = [
                "uri" => $uri,
                "parameters" => $parameters,
                "requestType" => $requestType,
                "action" => $method
            ];
        }

		//Submits route
		//@TODO finish this method
        public function submit($requestedUri, $request_Type)
        {
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
                        if(false !== strrpos($route_parameter, "{"))
                        {
                            $parameters[str_replace("{ ","",$route_parameter)] = $parametersSent[$key];
                        }
                        else
                        {
                            if($route_parameter !== $parametersSent[$key])
                            {
                                $errors++;
                            }
                        }
                    }

                    //var_dump($parameters);

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
        public function showRoutes()
        {

            var_dump($this->_routes);

        }

		//Gets route params from URL
        private function getParamsFromURI($uri)
        {
            if(empty($uri))
            {
                echo "The uri: '$uri' is not valid, check its syntax";
                exit;
            }

            //Creates an array with all parameters
            $parametersArray = explode("/", $uri);

            //Cleans all empty indexes from URI's parameters (the script does not count "/" as a parameter)
            while(false !== ($index = array_search("", $parametersArray)))
            {
                unset($parametersArray[$index]);
            }

            return $parametersArray;
        }

		//Checks whether route is unique
        private function checkIfRouteIsUnique($parametersArray, $requestType)
        {
            //Checks if there's already a route using the same arguments
            $routes = $this->_routes;

			//Not variable params
            $clean_parameters = [];

            foreach($parametersArray as $key => $parameter)
            {
				//If the current param is a variable pushes it to $clean_parameters
                if(strpos($parameter, '{') === false)
                {
                    $clean_parameters[] = $parameter;
                }
            }

            foreach($routes as $key => $route) 
            {
				//If existing route has the same quantity of params of the new route:
                if(count($route["parameters"]) === count($parametersArray))
                {
					$route_clean_parameters = [];
					
                    foreach($route["parameters"] as $key => $parameter)
                    {
                        if(strpos($parameter, '{') === false)
                        {
							$route_clean_parameters[] = $parameter;
                        }
                    }
					
					//Checks if not variable params of existing route is the same of the new route
                    if($route_clean_parameters === $clean_parameters && $route["requestType"] === $requestType)
                    {
						//If so displays an error
                        echo "There's another route which already uses the same parameters with the $requestType request type: " . $route["uri"];
                        exit;
                    }
                    
                }
            }
        }

		//Create a object of params and validates params in the process
        private function parseParams($parametersArray, $requestType)
        {
            //Creates route parameters array and checks whether the route is valid
            $parameters = [];

            foreach($parametersArray as $key => $value)
            {  
                // Checks whether the informed route structure is valid or not
                if( (strpos($value, '{') != 0) || 
                    (strpos($value, '{') === 0 && strpos($value, '}') !== (strlen($value) - 1)) || 
                    (strpos($value, '{') === 0 && strlen($value) < 3) ||
                    (strpos($value, '{') === false && strpos($value, '}') !== false) )
                {
                    echo "Error: The route $uri is not valid, check the its syntax";
                    exit;
                }
                else
                {
                    /*
                        @TODO Work in a new updated explanation 
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
                            if(false === strrpos($parameters[count($parameters) - 1], "{"))
                            //if the array of parameters has a previous parameter and it's not a variable parameter then the route is valid
                            {
                                $parameters[] = $value;
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

            return $parameters;
        }

    }
