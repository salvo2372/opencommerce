<?php
namespace App\models;

use App\models\Database as Database;
use App\core\Model as Model;
use App\core\Session as Session;
use App\core\Encryption as Encryption;

class HomeModel extends Model{

    /**
      * Table name for this & extending classes.
      *
      * @var string
      */
    public $table = "products";

    /**
     * returns an associative array holds the user info(image, name, id, ...etc.)
     *
     * @access public
     * @param  integer $userId
     * @return array Associative array of current user info/data.
     * @throws Exception if $userId is invalid.
     */
     public function getHomeProducts(){
         $database   = Database::openConnection();
/*
         $query  = "SELECT products.id As id,users.profile_picture, users.id AS user_id, users.name AS user_name, products.name, products.src, products.date, products_description.summary, products_description.content";
         $query .= " FROM products_description LEFT JOIN products ON products_description.product_id=products.id LEFT JOIN users ON products.user_id= users.id";
*/
          $locale =(string) Session::getLang();

          $query  = "SELECT 'Milazzo' As target, products.id As id, products.name, products.src, products.date, products_description.summary, products_description.content FROM products_description,products LEFT JOIN category ON products.category_id = category.id ";
          $query .= "WHERE products.lang = :lang AND products.active = '1'";
          $query .= "AND products.id=products_description.product_id AND category.id IN (SELECT category.id FROM category WHERE (category.id = '3' OR category.id = '4') AND (category.sub_id != 0)) UNION ";

          $query .= "SELECT 'Lipari' As target, products.id As id, products.name, products.src, products.date, products_description.summary, products_description.content FROM products_description,products LEFT JOIN category ON products.category_id = category.id ";
          $query .= "WHERE products.lang = :lang1 AND products.active = '1'";
          $query .= "AND products.id=products_description.product_id AND category.id IN (SELECT category.id FROM category WHERE (category.id = '5' OR category.id = '6') AND (category.sub_id != 0)) UNION ";

          $query .= "SELECT 'Vulcano' As target, products.id As id, products.name, products.src, products.date, products_description.summary, products_description.content FROM products_description,products LEFT JOIN category ON products.category_id = category.id ";
          $query .= "WHERE products.lang = :lang2 AND products.active = '1'";
          $query .= "AND products.id=products_description.product_id AND category.id IN (SELECT category.id FROM category WHERE (category.id = '7' OR category.id = '8') AND (category.sub_id != 0))";
         $database->prepare($query);
         $database->bindValue(':lang', $locale);
         $database->bindValue(':lang1', $locale);
         $database->bindValue(':lang2', $locale);
         $database->execute();
         $products = $database->fetchAllAssociative();
         $newProducts = array();
         foreach ($products as $product){
         foreach ($product as $key => $value){
            if ($key == "id"){
                //$information[$key] = Encryption::encryptId($value);
                $product[$key] = Encryption::encryNameId($value,"products");
            } else {
                $product[$key] = $value;
            }
         }
         array_push($newProducts,$product);
         }
         return $newProducts;
     }


     public function getMenuNew(){
         $locale =(string) Session::getLang();
         $database   = Database::openConnection();
         $query  = "(SELECT category.id, category.name FROM category WHERE category.sub_id = 0 AND category.lang = :lang)";

         $database->prepare($query);
         $database->bindValue(':lang', $locale);
         $database->execute();
         $itemsMenu = $database->fetchAllAssociative();

         $itemMenu = array("itemsMenu" => $itemsMenu);
         $itemsNew = array();
             foreach ($itemMenu as $key => $value){
                foreach($value as $subkey => $subvalue){
                var_dump($subvalue);echo "<br />";
                   $query  = "(SELECT category.id, category.name FROM category WHERE category.sub_id = :categoryId AND category.lang = :lang)";

                   $database->prepare($query);
                   $database->bindValue(':lang', $locale);
                   $database->bindValue(':categoryId', $subvalue["id"]);
                   $database->execute();
                   $itemsNew[$subvalue["id"]] = $database->fetchAllAssociative();
              }
             }

         return $itemsNew;
         }
}
