<?php 
   namespace App;

   class MenuHelper
   {
       public static function getMenuForRole($role)
       {
       //dd(config("menu"));
       
           $menuConfig = config("menu.$role");
        
           return $menuConfig;
       }
   }

?>