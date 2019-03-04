<?php

	class Post{

		public function showName($name){

			if(empty($name))
			{
				return "There's no name to show";
			}
			else
			{
				 return "The name is: ". $name;
			}

		}

	}