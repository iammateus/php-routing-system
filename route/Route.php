<?php
    /* 
     *   This class is created by Mateus Soares <https://github.com/mateussoares1997> 
     *   version: 0.0.1
	*/
	
    class Route{

        //Routes list
		private $_routes = [];

		public function get($uri, $method)
		{
			$this->add($uri, "GET", $method);
		}

		public function post($uri, $method)
		{
			$this->add($uri, "POST", $method);
		}

        //Adds new route
        private function add($uri, $requestType, $method)
        {
			//Gets params from URL
			$parametersArray = $this->getParamsFromURL($uri);

			//Validates route structure
			$this->validateRouteSintax($parametersArray, $uri);
			
			//Checks if URI is unique
            $this->checkIfRouteIsUnique($parametersArray, $requestType);

			//Mounts route object and adds it in routes list
            $this->_routes[] = [
                "uri" => $uri,
                "parameters" => $parametersArray,
                "requestType" => $requestType,
                "action" => $method
            ];
        }

		//Submits route
        public function submit($requestedUri, $request_Type)
        {
			$parametersSent = $this->getParamsFromURL($requestedUri);
			
            foreach($this->_routes as $key => $route)
            {
                //Checks whether the current loop's route has the same quantity of arguments of requested url 
                if(count($route["parameters"]) === count($parametersSent))
                {
                    $errors = 0;
					$parameters = [];
					
                    foreach($route["parameters"] as $key => $route_parameter)
                    {
                        if(false !== strrpos($route_parameter, "{"))
                        {
							//Keeps sent route variable params values
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
					
					if($route["requestType"] !== $request_Type)
					{
						$errors++;
					}

					//if $errors equals to 0 it means that the current route and the requested route match in structure and request method
                    if($errors === 0)
                    {
                        if(is_callable($route["action"]))
                        {
							call_user_func_array($route["action"], $parameters);
							
							exit;
                        }
                        else
                        {
							$this->callExternalClassAction($route["action"], $parameters);

                            exit;
                        }
                    }
                    
                }
			}
			
			include_once("view/404.php");

        } 

        //@TODO Create a method to display all set routes
        public function showRoutes()
        {
            var_dump($this->_routes);
		}

		//Gets route params from URL
        private function getParamsFromURL($uri)
        {
            if(empty($uri))
            {
                echo 'Route error: The uri: "'.$uri.'" is not valid, check its syntax';
                exit;
            }

            //Creates an array with all parameters
            $parametersArray = explode("/", $uri);

            //Cleans all empty indexes from URI's parameters (the script does not count "/" as a parameter)
            while(false !== ($index = array_search("", $parametersArray)))
            {
                unset($parametersArray[$index]);
			}

			//Reseting array indexing
			$parametersArray = array_values($parametersArray);

            return $parametersArray;
        }

		//Checks if there's already a route using the same arguments
        private function checkIfRouteIsUnique($parametersArray, $requestType)
        {	
            $routes = $this->_routes;

			//Constant parameters array
            $newRouteConstantParameters = [];

            foreach($parametersArray as $key => $parameter)
            {
				//If the current param is a variable pushes it to $newRouteConstantParameters
                if(strpos($parameter, '{') === false)
                {
                    $newRouteConstantParameters[] = $parameter;
                }
            }

            foreach($routes as $key => $currentRoute) 
            {
				//If existing route has the same quantity of params of the new route:
                if(count($currentRoute["parameters"]) === count($parametersArray))
                {
					$currentRouteConstantParameters = [];
					
                    foreach($currentRoute["parameters"] as $key => $parameter)
                    {
                        if(strpos($parameter, '{') === false)
                        {
							$currentRouteConstantParameters[] = $parameter;
                        }
                    }
					
					//Checks if not variable params of existing route is the same of the new route
                    if($currentRouteConstantParameters === $newRouteConstantParameters && $currentRoute["requestType"] === $requestType)
                    {
						//If so displays an error
                        echo 'Route error: There\'s another route which already uses the same parameters with the "'.$requestType.'" request type: "'. $currentRoute["uri"].'".';
                        exit;
                    }  
                }
			}
        }

		//Validates route's sintax and params
        private function validateRouteSintax($parametersArray, $uri)
        {
            foreach($parametersArray as $key => $currentParam)
            {  
                //Checks whether the informed route structure is valid or not
                if( 
					    (strpos($currentParam, '{') != 0) 
                    ||  (strpos($currentParam, '{') === 0 && strpos($currentParam, '}') !== (strlen($currentParam) - 1))
                    ||  (strpos($currentParam, '{') === 0 && strlen($currentParam) < 3)
					||  (strpos($currentParam, '{') === false && strpos($currentParam, '}') !== false) 
				
				)
                {
                    echo 'Route error: "'.$currentParam.'" seems a variable parameter, but its syntax is not permitted.';
                    exit;
				}
				else
				{
					/*
					 *This block will run if current parameter is a valid syntax variable or a constant parameter
					*/

					//If current parameter is a url variable (Eg: /{name}) execute some more validations
					if(strpos($currentParam, '{') === 0)
					{
						
						if($key === 0)
						{
							echo 'Route error: The first route parameter cannot be a variable parameter, "'.$currentParam.'" given.';
							exit;
						}
						
						if(strpos($parametersArray[$key-1], "{") ===  0)
						{
							echo 'Route error: The route cannot have more than one variable for specifier constant parameter, "'. $uri.'" given.';
							exit;
						}

					}
				}	
			}  
		}
		
		//Call external class action
		private function callExternalClassAction($routeAction, $parameters){

			$routeActionArray = explode("@", $routeAction);

			$classFile = $routeActionArray[0];

			$classMethod = $routeActionArray[1];

			require 'class/'.$classFile.'.php';

			$classObject = new $classFile();

			call_user_func_array([$classObject, $classMethod], $parameters);

		}  
	}
