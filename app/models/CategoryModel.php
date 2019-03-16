<?php
namespace App\models;

use App\models\Database as Database;
use App\core\Model as Model;
use App\core\Session as Session;
use App\core\Encryption as Encryption;

class CategoryModel extends Model{

    /**
      * Table name for this & extending classes.
      *
      * @var string
      */
    public $table = "category";


     public function subCategory($categoryId){
        $locale =(string) Session::getLang();
        $database   = Database::openConnection();

        $query  = "SELECT category.id AS id, category.src AS src, category.name, category.slug, category.sub_id, category_description.summary, category_description.content ";
        $query .= "FROM category, category_description ";
        $query .= "WHERE category.id = category_description.category_id ";
        $query .= "AND category.sub_id = :categoryId ";
        $query .= "AND category.lang = :lang ";
        $database->prepare($query);
        $database->bindValue(':categoryId', (int)$categoryId);
        $database->bindValue(':lang', $locale);
        $database->execute();
        $subCategories = $database->fetchAllAssociative();

         $newSubCategories = array();
         foreach ($subCategories as $category){
         foreach ($category as $key => $value){
            if ($key == "id"){
                //$information[$key] = Encryption::encryptId($value);
                $category[$key] = Encryption::encryNameId($value,"category");
            } else {
                $category[$key] = $value;
            }
         }
         array_push($newSubCategories,$category);
         }

         return $newSubCategories;

     }
     public function pageMeta($categoryId){

         $database = Database::openConnection();
         $query  = "SELECT category.id AS id, category.name, category.slug, category.lang, category_meta.meta_title, category_meta.meta_description, category_meta.meta_keywords ";
         $query .= "FROM category, category_meta ";
         $query .= "WHERE category.id = :id ";
         $query .= "AND category_meta.category_id = category.id LIMIT 1 ";

         $database->prepare($query);
         $database->bindValue(':id', (int)$categoryId);
         $database->execute();

         $pagemeta = $database->fetchAssociative();
         return $pagemeta;
     }
     public function getById($categoryId){

         $database = Database::openConnection();
         $query  = "SELECT category.id AS id, category.src AS src, category.name, category.slug, category.lang, category_description.summary, category_description.content, category.date, category_meta.meta_title, category_meta.meta_description ";
         $query .= "FROM category, category_description, category_meta ";
         $query .= "WHERE category.id = :id ";
         $query .= "AND category_description.category_id = category.id ";
         $query .= "AND category_meta.category_id = category.id LIMIT 1 ";

         $database->prepare($query);
         $database->bindValue(':id', (int)$categoryId);
         $database->execute();

         $category = $database->fetchAssociative();
         foreach ($category as $key => $value){
            if ($key == "id"){
                $category[$key] = Encryption::encryNameId($value,"category");
            } else {
                $category[$key] = $value;
            }
         }
         return $category;
     }

     public function getAll(){
        $locale =(string) Session::getLang();

         $database   = Database::openConnection();

         $query  = "SELECT category.id AS id, category.name, category.src, category.date, category_description.summary ";
         $query .= "FROM category, category_description ";
         $query .= "WHERE category.id = category_description.category_id ";
         $query .= "AND category.sub_id = 0 ";
         $query .= "AND category.lang = :lang ";
         $database->prepare($query);
         $database->bindValue(':lang', $locale);
         $database->execute();
         $categories = $database->fetchAllAssociative();
         $newCategories = array();
         foreach ($categories as $category){
         foreach ($category as $key => $value){
            if ($key == "id"){
                //$information[$key] = Encryption::encryptId($value);
                $category[$key] = Encryption::encryNameId($value,"category");
            } else {
                $category[$key] = $value;
            }
         }
         array_push($newCategories,$category);
         }

         return $newCategories;
     }

     public function subProducts($categoryId){

       $database   = Database::openConnection();

       $query  = "SELECT products.id As id, products.name, products.src, products.date, products_description.summary, products_meta.price";
       $query .= " FROM products, products_description, products_meta WHERE products.id= products_description.product_id AND products_description.product_id = products_meta.product_id ";
       $query .= "AND products.lang = :lang AND products.active = '1' ";
       $query .= "AND products.category_id = :subCategory ";
       $database->prepare($query);
       $database->bindValue(':lang',Session::getLang());
       $database->bindValue(':subCategory', $categoryId);
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
     public function getCategories(){

       $database   = Database::openConnection();

       $query  = "SELECT category.id As id, category.name, category.src, category.date, category_description.summary";
       $query .= " FROM category, category_description, category_meta WHERE category.id= category_description.category_id AND category_description.category_id = category_meta.category_id ";
       $query .= "AND category.lang = :lang ";

       $database->prepare($query);
       $database->bindValue(':lang',Session::getLang());
       $database->execute();
       $categories = $database->fetchAllAssociative();

       $newCategories = array();
       foreach ($categories as $category){
       foreach ($category as $key => $value){
          if ($key == "id"){
              //$information[$key] = Encryption::encryptId($value);
              $category[$key] = Encryption::encryNameId($value,"category");
          } else {
              $category[$key] = $value;
          }
       }
       array_push($newCategories,$category);
       }
       return $newCategories;
     }
     public function update($categoryId, $categoryName, $categorySlug,  $categorySummary, $categoryContent, $categoryMetaTitle,$categoryMetaDescription){

/*
         $validation = new Validation();
         if(!$validation->validate([
             'Title'   => [$title, "required|minLen(2)|maxLen(60)"],
             'Content' => [$content, "required|minLen(4)|maxLen(1800)"]])) {
             $this->errors = $validation->errors();
             return false;
         }
*/
         $database = Database::openConnection();
         $query = "UPDATE category SET category.name = :name, category.slug = :slug WHERE category.id = :id1 LIMIT 1";

         $database->prepare($query);
         $database->bindValue(':name', $categoryName);
         $database->bindValue(':slug', $categorySlug);
         $database->bindValue(':id1', (int)$categoryId);
         $result = $database->execute();

         if(!$result){
             throw new Exception("Couldn't update produt of ID: " . $categoryId);
         }

         $query = "UPDATE category_description SET category_description.summary = :summary, category_description.content = :content WHERE category_description.category_id = :id2 LIMIT 1";

         $database->prepare($query);
         $database->bindValue(':summary', $categorySummary);
         $database->bindValue(':content', $categoryContent);
         $database->bindValue(':id2', (int)$categoryId);
         $result = $database->execute();

         if(!$result){
             throw new Exception("Couldn't update produt of ID: " . $categoryId);
         }

         $query = "UPDATE category_meta SET category_meta.meta_title = :metaTitle, category_meta.meta_description = :metaDescription WHERE category_meta.category_id = :id3 LIMIT 1";

         $database->prepare($query);
         $database->bindValue(':metaTitle', $categoryMetaTitle);
         $database->bindValue(':metaDescription', $categoryMetaDescription);
         $database->bindValue(':id3', (int)$categoryId);
         $result = $database->execute();

         if(!$result){
             throw new Exception("Couldn't update produt of ID: " . $categoryId);
         }
         $category = $this->getById($categoryId);
         return $category;

     }
}
