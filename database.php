<?php
   function connect_db(){
      $dbhost ="localhost";
	  $dbuser= "root";
	  $dbpass ="";
	  $dbname ="task_restapi";
	  $connection = new mysqli($dbhost,$dbuser,$dbpass,$dbname);
	  $connection->query("SET NAMES 'utf8'");
	  
	  if($connection->connect_error){
	     echo "Error".$connection->connect_error;
	  }
	  
	  else{
	     return $connection;
	  }    
   }
?>