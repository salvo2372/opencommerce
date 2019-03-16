<?php 
namespace App\models;

use App\models\Database as Database;
use App\core\Model as Model;
use App\models\Pagination as Pagination;
class PostModel extends Model{

    /**
      * Table name for this & extending classes.
      *
      * @var string
      */
    public $table = "posts";

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
     public function getAll($pageNum = 1){

         $pagination = Pagination::pagination("posts", "", [], $pageNum);
         $offset     = $pagination->getOffset();
         $limit      = $pagination->perPage;

         $database   = Database::openConnection();
         $query  = "SELECT posts.id AS id, users.profile_picture, users.id AS user_id, users.name AS user_name, posts.title, posts.content, posts.date ";
         $query .= "FROM users, posts ";
         $query .= "WHERE users.id = posts.user_id ";
         $query .= "LIMIT $limit OFFSET $offset";
         $database->prepare($query);
         $database->execute();
         $posts = $database->fetchAllAssociative();
         return array("posts" => $posts, "pagination" => $pagination);
     }
     public function getById($postId){

         $database = Database::openConnection();
         $query  = "SELECT posts.id AS id, users.profile_picture, users.id AS user_id, users.name AS user_name, posts.title, posts.content, posts.date ";
         $query .= "FROM users, posts ";
         $query .= "WHERE posts.id = :id ";
         $query .= "AND users.id = posts.user_id LIMIT 1 ";

         $database->prepare($query);
         $database->bindValue(':id', $postId);
         $database->execute();

         $post = $database->fetchAssociative();
         return $post;
     } 
    
     public function update($postId, $title, $content){
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
         $query = "UPDATE posts SET title = :title, content = :content WHERE id = :id LIMIT 1";

         $database->prepare($query);
         $database->bindValue(':title', $title);
         $database->bindValue(':content', $content);
         $database->bindValue(':id', $postId);
         $result = $database->execute();

         if(!$result){
             throw new Exception("Couldn't update post of ID: " . $postId);
         }

         $post = $this->getById($postId);
         return $post;
     }               
} 