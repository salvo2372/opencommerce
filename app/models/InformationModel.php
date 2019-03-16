<?php
namespace App\models;

use App\models\Database as Database;
use App\core\Model as Model;
use App\core\Session as Session;
use App\core\Encryption as Encryption;

class InformationModel extends Model{

    /**
      * Table name for this & extending classes.
      *
      * @var string
      */
    public $table = "information";

    /**
     * returns an associative array holds the user info(image, name, id, ...etc.)
     *
     * @access public
     * @param  integer $userId
     * @return array Associative array of current user info/data.
     * @throws Exception if $userId is invalid.
     */
     public function create($userId, $title, $content){

         $database = Database::openConnection();
         $query    = "INSERT INTO posts (user_id, title, content) VALUES (:user_id, :title, :content)";

         $database->prepare($query);
         $database->bindValue(':user_id', $userId);
         $database->bindValue(':title', $title);
         $database->bindValue(':content', $content);
         $database->execute();

         if($database->countRows() !== 1){
             throw new Exception ("Couldn't add news feed");
         }

         return true;
     }
     public function getInformation($subinfoId = 0){

         $locale =(string) Session::getLang();
         $database   = Database::openConnection();
         $query  = "SELECT information.id AS id, information.title, information.meta_title, information.meta_description, information.meta_keywords, information.summary, information.update, information.src ";
         $query .= "FROM information ";
         $query .= "WHERE information.lang = :lang ";
         $query .= "AND information.sub_infoid = :sub ";
         $database->prepare($query);
         $database->bindValue(':lang', $locale);
         $database->bindValue(':sub', (string)$subinfoId);
         $database->execute();
         $informations = $database->fetchAllAssociative();
         $newInformation = array();
         foreach ($informations as $information){
         foreach ($information as $key => $value){
            if ($key == "id"){
                //$information[$key] = Encryption::encryptId($value);
                $information[$key] = Encryption::encryNameId($value,"information");
            } else {
                $information[$key] = $value;
            }
         }
         array_push($newInformation,$information);
         }

         return $newInformation;
     }

     public function getById($informationId){

         $database = Database::openConnection();
         $query  = "SELECT information.id AS id, information.title, information.meta_title, information.meta_description, information.meta_keywords, information.summary, information.content, information.date, information.src ";
         $query .= "FROM information ";
         $query .= "WHERE information.id = :id ";
         $query .= "LIMIT 1 ";

         $database->prepare($query);
         $database->bindValue(':id', (int)$informationId);
         $database->execute();

         $information = $database->fetchAssociative();


         foreach ($information as $key => $value){
            if ($key == "id"){
                //$information[$key] = Encryption::encryptId($value);
                $information[$key] = Encryption::encryNameId($value,"information");
            } else {
                $information[$key] = $value;
            }
         }

         return $information;
         //$information["content"] = html_entity_decode($information["content"], ENT_COMPAT | ENT_HTML5,'utf-8');
         //$products["content"] = str_replace('\\r\\n','', $products["content"]);

     }
     public function pageMeta($informationId){

         $database = Database::openConnection();
         $query  = "SELECT information.id AS id, information.title, information.slug, information.lang, information.meta_title, information.meta_description, information.meta_keywords ";
         $query .= "FROM information ";
         $query .= "WHERE information.id = :id LIMIT 1 ";

         $database->prepare($query);
         $database->bindValue(':id', (int)$informationId);
         $database->execute();

         $pagemeta = $database->fetchAssociative();
         return $pagemeta;
     }     
     public function update($informationId, $title, $summary, $content, $metaTitle, $metaDescription){
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
         $query = "UPDATE information SET title = :title, summary = :summary, content = :content, meta_title = :metatitle, meta_description = :metadescription WHERE id = :id LIMIT 1";

         $database->prepare($query);
         $database->bindValue(':title', $title);
         $database->bindValue(':summary', $summary);
         $database->bindValue(':content', $content);
         $database->bindValue(':metatitle', $metaTitle);
         $database->bindValue(':metadescription', $metaDescription);
         $database->bindValue(':id', $informationId);
         $result = $database->execute();

         if(!$result){
             throw new Exception("Couldn't update post of ID: " . $informationId);
         }

         $information = $this->getById($informationId);
         return $information;
     }
}
