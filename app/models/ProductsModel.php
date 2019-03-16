<?php
namespace App\models;

use App\models\Database as Database;
use App\core\Model as Model;
use App\core\Session as Session;
use App\core\Encryption as Encryption;
use App\models\Pagination as Pagination;

class ProductsModel extends Model{

    /**
      * Table name for this & extending classes.
      *
      * @var string
      */
    public $table = "products";

     public function getById($productId){

         $database = Database::openConnection();
         $query  = "SELECT products.id AS id, products.name, products.slug, products.lang, products_description.summary, products_description.content, products.date, products_description.src, products_meta.meta_title,products_meta.meta_description, products_meta.meta_keywords, products_meta.price, products_meta.minimum, products_meta.quantity ";
         $query .= "FROM products, products_description, products_meta ";
         $query .= "WHERE products.id = :id ";
         $query .= "AND products_description.product_id = products.id AND products_meta.product_id = products.id LIMIT 1 ";

         $database->prepare($query);
         $database->bindValue(':id', (int)$productId);
         $database->execute();

         $product = $database->fetchAssociative();
         foreach ($product as $key => $value){
            if ($key == "id"){
                $product[$key] = Encryption::encryNameId($value,"products");
            } else {
                $product[$key] = $value;
            }
         }

        //$products["content"] = html_entity_decode($products["content"], ENT_COMPAT | ENT_HTML5,'utf-8');
        //$products["content"] = str_replace('\\r\\n','', $products["content"]);

         return $product;
     }

     public function pageMeta($productId){

         $database = Database::openConnection();
         $query  = "SELECT products.id AS id, products.name, products.slug, products.lang, products_meta.meta_title, products_meta.meta_description, products_meta.meta_keywords ";
         $query .= "FROM products, products_meta ";
         $query .= "WHERE products.id = :id ";
         $query .= "AND products_meta.product_id = products.id LIMIT 1 ";

         $database->prepare($query);
         $database->bindValue(':id', (int)$productId);
         $database->execute();

         $pagemeta = $database->fetchAssociative();
         return $pagemeta;
     }
     public function productOption($productId){

         $database = Database::openConnection();
         $query  = "SELECT option.id, option.option_type, option.product_id, option.option_name, option.option_order, option.option_required ";
         $query .= "FROM `option` ";
         $query .= "WHERE option.product_id = :id ";
         $query .= "ORDER BY option.option_order ";

         $database->prepare($query);
         $database->bindValue(':id', (int)$productId);
         $database->execute();

         $productoptions = $database->fetchAllAssociative();
         //var_dump($productoptions); echo "entra"; echo "<br />";

         $productOptions = array();
         $productOut = array();
         foreach ($productoptions as $productoption){
             foreach ($productoption as $key => $value){
                $productOptions[$key] = $value;
             }
             $query  = "SELECT option_value.id, option_value.option_id, option_value.option_name, option_value.option_price, option_value.option_date, option_value.option_value_order, option_value.option_quantity FROM `option_value` ";
             $query .= "WHERE option_value.option_id = :id1 ";
             $query .= "ORDER BY option_value.option_value_order ";

             $database->prepare($query);
             $database->bindValue(':id1', (int)$productoption["id"]);
             $database->execute();

             $optionsValue = $database->fetchAllAssociative();
             $productOptions["option_value"] = $optionsValue;
                  //echo "ciclo interno";    var_dump($optionsValue); echo "<br />";
             array_push($productOut,$productOptions);
         }

         return $productOut;

     }

     public function productOptionDate($productId){

         $database = Database::openConnection();


         $query = 'SELECT `option_value`.`option_date` FROM `option_value` WHERE `option_value`.`option_id` IN (SELECT `option`.`id` FROM `option` ';
         $query .= 'WHERE `option`.`product_id` = :id ';
         $query .= 'AND `option`.`option_type` = "DATE") ';
         $database->prepare($query);
         $database->bindValue(':id', (int)$productId);
         $database->execute();

         $productDate = $database->fetchAssociative();

         return $productDate;

     }

     public function getSearchProducts($pageNum = 1, $searchText){
       $searchText = "% ".$searchText." %";
       $pagination = Pagination::pagination("products,products_description", "WHERE products.id = products_description.product_id AND products.lang = :lang AND products_description.summary LIKE :searchText ", [':lang' => Session::getLang(), ':searchText' => $searchText], $pageNum);

       $offset     = $pagination->getOffset();
       $limit      = $pagination->perPage;
       //$searchText = "% stromboli %";
       $database   = Database::openConnection();
       $query  = "SELECT products.id As id, products.name, products.src, products.date, products_description.summary, products_meta.price";
       $query .= " FROM products, products_description, products_meta WHERE products.id= products_description.product_id AND products_description.product_id = products_meta.product_id ";
       $query .= "AND products_description.summary LIKE :searchText ";
       $query .= "AND products.lang = :lang AND products.active = '1'";
       $query .= "LIMIT $limit OFFSET $offset";

       $database->prepare($query);
       $database->bindParam(':searchText', $searchText);
       $database->bindValue(':lang',Session::getLang());
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

       return array("products" => $newProducts, "pagination" => $pagination);

     }

     public function productCategories(){
        $locale =(string) Session::getLang();

         $database   = Database::openConnection();

         $query  = "SELECT category.id AS id, category.name ";
         $query .= "FROM category, category_description ";
         $query .= "WHERE category.id = category_description.category_id ";
         $query .= "AND category.lang = :lang ";
         $database->prepare($query);
         $database->bindValue(':lang', $locale);
         $database->execute();
         $productCategories = $database->fetchAllAssociative();
         return $productCategories;
     }

     public function getAll($pageNum = 1){
         $pagination = Pagination::pagination("products", "", [], $pageNum);
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

         return $newProducts;
     }

     public function productOptionPrice($productId,$optionId,$optionValueId){

       $database   = Database::openConnection();

       $query  = "SELECT option.option_name as name, option_value.option_price as price, option_value.option_name as option_name";
       $query .= " FROM `option_value`, `option` WHERE option_value.option_id = option.id ";
       $query .= "AND option.product_id = :productid ";
       $query .= "AND option.id = :optionid ";
       $query .= "AND option_value.id = :optionvalueid ";
       $database->prepare($query);
       $database->bindValue(':productid', (int)$productId);
       $database->bindValue(':optionid', (int)$optionId);
       $database->bindValue(':optionvalueid', (int)$optionValueId);
       $database->execute();
       $optionValue = $database->fetchAssociative();

       return $optionValue;
     }
     public function getProducts($pageNum = 1){
       $pagination = Pagination::pagination("products", "WHERE products.lang = :lang", [':lang' => Session::getLang()], $pageNum);
       $offset     = $pagination->getOffset();
       $limit      = $pagination->perPage;
       $database   = Database::openConnection();

       $query  = "SELECT products.id As id, products.name, products.src, products.date, products_description.summary, products_meta.price";
       $query .= " FROM products, products_description, products_meta WHERE products.id= products_description.product_id AND products_description.product_id = products_meta.product_id ";
       $query .= "AND products.lang = :lang AND products.active = '1'";
       $query .= "LIMIT $limit OFFSET $offset";

       $database->prepare($query);
       $database->bindValue(':lang',Session::getLang());
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

       return array("products" => $newProducts, "pagination" => $pagination);
     }
     public function getAllProducts($pageNum = 1){
       $pagination = Pagination::pagination("products", "WHERE products.lang = :lang", [':lang' => Session::getLang()], $pageNum);
       $offset     = $pagination->getOffset();
       $limit      = $pagination->perPage;
       $database   = Database::openConnection();
       $query  = "SELECT products.id As id, products.name, products.src, products.date, products_description.summary, products_meta.price";
       $query .= " FROM products, products_description, products_meta WHERE products.id= products_description.product_id AND products_description.product_id = products_meta.product_id ";
       $query .= "AND products.lang = :lang ";
       $query .= "LIMIT $limit OFFSET $offset";

       $database->prepare($query);
       $database->bindValue(':lang',Session::getLang());
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
       return array("products" => $newProducts, "pagination" => $pagination);
     }
     public function create($userId, $productName, $productSlug, $productLang, $productSrc, $productActive, $productCategoryId, $productSummary, $productPrice, $productContent, $productMetaTitle, $productMetaDescription, $productMetaKeywords, $productTaxType, $productMenuName, $productQuantity, $productMinimum){

         $database = Database::openConnection();
         $query    = "INSERT INTO products (user_id, name, slug, lang, src, active, category_id) VALUES (:user_id, :productName, :productSlug, :productLang, :productSrc, :productActive, :productCategoryId)";

         $database->prepare($query);
         $database->bindValue(':user_id', (int)$userId);
         $database->bindValue(':productName', $productName);
         $database->bindValue(':productSlug', $productSlug);
         $database->bindValue(':productLang', (string)$productLang);
         $database->bindValue(':productSrc', $productSrc);
         $database->bindValue(':productActive', $productActive);
         $database->bindValue(':productCategoryId', (int)$productCategoryId);
         $database->execute();

         $productId = $database->lastInsertedId();

         if($database->countRows() !== 1){
             throw new Exception ("Couldn't add news products");
         }

         $query    = "INSERT INTO products_meta (user_id, product_id, meta_title, meta_description, meta_keywords, menu_name, price, quantity, minimum, tax_type, lang) VALUES (:user_id, :productId, :productMetaTitle, :productMetaDescription, :productMetaKeywords, :productMenuName, :productPrice, :productQuantity, :productMinimum, :productTaxType, :productLang)";

         $database->prepare($query);
         $database->bindValue(':user_id', (int)$userId);
         $database->bindValue(':productId', (int)$productId);
         $database->bindValue(':productMetaTitle', $productMetaTitle);
         $database->bindValue(':productMetaDescription', $productMetaDescription);
         $database->bindValue(':productMetaKeywords', $productMetaKeywords);
         $database->bindValue(':productMenuName', $productMenuName);
         $database->bindValue(':productPrice', (float)$productPrice);
         $database->bindValue(':productQuantity', (int)$productQuantity);
         $database->bindValue(':productMinimum', (int)$productMinimum);
         $database->bindValue(':productTaxType', (int)$productTaxType);
         $database->bindValue(':productLang', (string)$productLang);

         $database->execute();

         if($database->countRows() !== 1){
             throw new Exception ("Couldn't add news products");
         }

         $query    = "INSERT INTO products_description (user_id, product_id, summary, content, lang, src) VALUES (:user_id, :productId, :productSummary, :productContent, :productLang, :productSrc)";

         $database->prepare($query);
         $database->bindValue(':user_id', (int)$userId);
         $database->bindValue(':productId', (int)$productId);
         $database->bindValue(':productSummary', $productSummary);
         $database->bindValue(':productContent', $productContent);
         $database->bindValue(':productLang', (string)$productLang);
         $database->bindValue(':productSrc', $productSrc);

         $database->execute();

         if($database->countRows() !== 1){
             throw new Exception ("Couldn't add news products");
         }

         return true;
     }
     public function update($productId, $productName, $productSlug,  $productSummary, $productContent, $productMetaTitle, $productMetaDescription, $productPrice, $productMinimum, $productQuantity, $productOption){

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
         $query = "UPDATE products SET products.name = :name, products.slug = :slug WHERE products.id = :id1 LIMIT 1";

         $database->prepare($query);
         $database->bindValue(':name', $productName);
         $database->bindValue(':slug', $productSlug);
         $database->bindValue(':id1', (int)$productId);
         $result = $database->execute();

         if(!$result){
             throw new Exception("Couldn't update produt of ID: " . $productId);
         }

         $query = "UPDATE products_description SET products_description.summary = :summary, products_description.content = :content WHERE products_description.product_id = :id2 LIMIT 1";

         $database->prepare($query);
         $database->bindValue(':summary', $productSummary);
         $database->bindValue(':content', $productContent);
         $database->bindValue(':id2', (int)$productId);
         $result = $database->execute();

         if(!$result){
             throw new Exception("Couldn't update produt of ID: " . $productId);
         }

         $query = "UPDATE products_meta SET products_meta.price = :price, products_meta.minimum = :minimum, products_meta.quantity = :quantity, products_meta.meta_title = :metaTitle, products_meta.meta_description = :metaDescription WHERE products_meta.product_id = :id3 LIMIT 1";

         $database->prepare($query);
         $database->bindValue(':price', $productPrice);
         $database->bindValue(':minimum', (int)$productMinimum);
         $database->bindValue(':quantity', (int)$productQuantity);
         $database->bindValue(':metaTitle', $productMetaTitle);
         $database->bindValue(':metaDescription', $productMetaDescription);
         $database->bindValue(':id3', (int)$productId);
         $result = $database->execute();

         if(!$result){
             throw new Exception("Couldn't update produt of ID: " . $productId);
         }
         foreach ($productOption as $moreOption) {

           $query = "UPDATE option SET option.option_name = :optionName, option.option_required = :optionRequired, option.option_order = :optionOrder WHERE option.id = :opt LIMIT 1";
           $database->prepare($query);

           foreach ($moreOption as $key => $value) {

             if (strcmp(str_replace('\'','',$key),'option_name') == 0) {
               //echo $key; echo " :" . $value; echo "<br />";
               $database->bindValue(':optionName', (string)$value);
             }
             if (strcmp(str_replace('\'','',$key),'option_required') == 0){
                //echo $key; echo " :" . $value; echo "<br />";
                $database->bindValue(':optionRequired', (int)$value);
             }
             if (strcmp(str_replace('\'','',$key),'option_order') == 0){
                //echo $key; echo " :" . $value; echo "<br />";
                $database->bindValue(':optionOrder', (int)$value);
             }
             if (strcmp(str_replace('\'','',$key),'id') == 0){
                //echo $key; echo " :" . $value; echo "<br />";
                $database->bindValue(':opt',(int)$value);
             }

             if (strcmp(str_replace('\'','',$key),'option_value') == 0){
             }
          }
          $result = $database->execute();          
        }


         foreach ($productOption as $moreOption) {

           foreach ($moreOption as $key => $value) {

             if (strcmp(str_replace('\'','',$key),'option_name') == 0) {
               //echo $key; echo " :" . $value; echo "<br />";
               //$database->bindValue(':optionName', (string)$value);
             }
             if (strcmp(str_replace('\'','',$key),'option_required') == 0){
                //echo $key; echo " :" . $value; echo "<br />";
                //$database->bindValue(':optionRequired', (int)$value);
             }
             if (strcmp(str_replace('\'','',$key),'option_order') == 0){
                //echo $key; echo " :" . $value; echo "<br />";
                //$database->bindValue(':optionOrder', (int)$value);
             }
             if (strcmp(str_replace('\'','',$key),'id') == 0){
                //echo $key; echo " :" . $value; echo "<br />";
                //$database->bindValue(':opt',(int)$value);
             }

             if (strcmp(str_replace('\'','',$key),'option_value') == 0){

                foreach($value as $valueon)
                {
                  $query = "UPDATE option_value SET option_value.option_name = :optionValueName, option_value.option_price = :optionValuePrice, option_value.option_date = :optionValueDate, option_value.option_value_order = :optionValueOrder, option_value.option_quantity = :optionValueQuantity WHERE option_value.id = :optValue LIMIT 1";
                  $database->prepare($query);
                  foreach($valueon as $optionkey => $optionvalue)
                  {
                    if (strcmp(str_replace('\'','',$optionkey),'option_name') == 0) {
                      //echo $key; echo " :"; print_r($value);echo "<br />";
                      //echo $optionkey; echo " :" . $optionvalue; echo "<br />";
                      //sprintf("%s %s",$optionkey,$optionvalue);echo "<br />";
                      $database->bindValue(':optionValueName', $optionvalue);
                    }
                    if (strcmp(str_replace('\'','',$optionkey),'option_price') == 0) {
                      //echo $optionkey; echo " :" . $optionvalue; echo "<br />";
                      $database->bindValue(':optionValuePrice', $optionvalue);
                    }
                    if (strcmp(str_replace('\'','',$optionkey),'option_date') == 0) {
                      //echo $optionkey; echo " :" . $optionvalue; echo "<br />";
                      $database->bindValue(':optionValueDate', $optionvalue);
                    }
                    if (strcmp(str_replace('\'','',$optionkey),'option_value_order') == 0) {
                      //echo $optionkey; echo " :" . $optionvalue; echo "<br />";
                      $database->bindValue(':optionValueOrder', $optionvalue);
                    }
                    if (strcmp(str_replace('\'','',$optionkey),'option_quantity') == 0) {
                      //echo $optionkey; echo " :" . $optionvalue; echo "<br />";
                      $database->bindValue(':optionValueQuantity', (int)$optionvalue);
                    }
                    if (strcmp(str_replace('\'','',$optionkey),'id') == 0){
                      //echo $optionkey; echo " :" . $optionvalue; echo "<br />";
                      $database->bindValue(':optValue',(int)$optionvalue);
                    }
                  }
                  //echo "<br />entra";
                  $result = $database->execute();
               }
             }

           }
         }
         $product = $this->getById($productId);
         return $product;
     }
}
