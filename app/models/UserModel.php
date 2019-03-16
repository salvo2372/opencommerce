<?php
namespace App\models;

use App\models\Database as Database;
use App\core\Model as Model;
use App\models\UpLoader as UpLoader;

class UserModel extends Model{

    /**
      * Table name for this & extending classes.
      *
      * @var string
      */
    public $table = "users";

    /**
     * returns an associative array holds the user info(image, name, id, ...etc.)
     *
     * @access public
     * @param  integer $userId
     * @return array Associative array of current user info/data.
     * @throws Exception if $userId is invalid.
     */
    public static function getUserDataByUsername($user_email)
    {
        $database->prepare("SELECT * FROM users WHERE email = :email AND is_email_activated = 1 LIMIT 1");
        $database->bindValue(':email', $email);
        $database->execute();
        $user = $database->fetchAssociative();

        $userId = isset($user["id"])? $user["id"]: null;

        //return $user;
        return true;
    }   
    public function getProfileInfo($userId){

        $database = Database::openConnection();
        $database->getById("users", $userId);

        if($database->countRows() !== 1){
            echo "User ID " .  $userId . " doesn't exists";
        }

        $user = $database->fetchAssociative();

        $user["id"]    = (int)$user["id"];
        $user["name"] = $user["name"];
        $user["image"] = PUBLIC_ROOT . "images/profile_pictures/" . $user['profile_picture'];
        $user["email"] = empty($user['is_email_activated'])? null: $user['email'];

        return $user;
      }
   
    public function dashboard(){        
       $database = Database::openConnection();

        // 1. count
        $tables = ["newsfeed", "posts", "files", "users"];
        $stats  = [];

        foreach($tables as $table){
            $stats[$table] = $database->countAll($table);
            //echo "Tabella" . $table . $stats[$table];
        }
        $query  = "SELECT * FROM (";
        $query .= "SELECT 'newsfeed' AS target, content AS title, date, users.name FROM newsfeed, users WHERE user_id = users.id UNION ";
        $query .= "SELECT 'posts' AS target, title, date, users.name FROM posts, users WHERE user_id = users.id UNION ";
        $query .= "SELECT 'files' AS target, filename AS title, date, users.name FROM files, users WHERE user_id = users.id ";
        $query .= ") AS updates ORDER BY date DESC LIMIT 10";
        $database->prepare($query);
        $database->execute();
        $updates = $database->fetchAllAssociative();
        $data = array("stats" => $stats, "updates" => $updates);        
        return $data;  

    } 
    public function updateProfilePicture($userId, $fileData){

        $image = Uploader::uploadPicture($fileData, $userId);
    }                
}      