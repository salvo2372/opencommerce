<?php 
namespace App\models;

use App\models\Database as Database;
use App\core\Model as Model;

class ShopModel extends Model{

    /**
      * Table name for this & extending classes.
      *
      * @var string
      */
    public $table = "products";

	public function __construct(){
		parent::__construct();
	}

     public function getAll($pageNum = 1){
         $pagination = Pagination::pagination("shop", "", [], $pageNum);
         $offset     = $pagination->getOffset();
         $limit      = $pagination->perPage;
         
         $database   = Database::openConnection();

         $query  = "SELECT products.id AS id, users.profile_picture, users.id AS user_id, users.name AS user_name, products.name, products.src, products.date ";
         $query .= "FROM users, products ";
         $query .= "WHERE users.id = products.user_id ";
         $query .= "LIMIT $limit OFFSET $offset";
         $database->prepare($query);
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

         //return array("posts" => $posts, "pagination" => $pagination);
         return $newProducts;
     }	
}