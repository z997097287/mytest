<?php
function em_getallheaders()  
{  
   foreach ($_SERVER as $name => $value)  
   {  
       if (substr($name, 0, 5) == 'HTTP_')  
       {  
           $headers[str_replace(' ', '-', ucwords(strtolower(str_replace('_', ' ', substr($name, 5)))))] = $value;  
       }  
   }  
   return $headers;  
} 
$header = em_getallheaders();

if($header['X-Coding-Event'] == 'push'){
//	if(file_exists("{$_SERVER['DOCUMENT_ROOT']}/git_tmp")){
		require_once("./model/git/pull.php");
//	}
}
file_put_contents("coding_event.log",json_encode($header));