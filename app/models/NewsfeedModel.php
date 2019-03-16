<?php
namespace App\models;

 /**
  * NewsFeed Class
  *
  * @license    http://opensource.org/licenses/MIT The MIT License (MIT)
  * @author     Omar El Gabry <omar.elgabry.93@gmail.com>
  */
use App\models\Database as Database;
use App\core\Model as Model;
class NewsfeedModel extends Model{

    /**
     * get all news feed.
     *
     * @access public
     * @param  integer  $pageNum
     * @return array
     *
     */
    public function getAll($pageNum = 1){

        //$pagination = Pagination::pagination("newsfeed", "", [], $pageNum);
        //$offset     = $pagination->getOffset();
        //$limit      = $pagination->perPage;

        $database   = Database::openConnection();
        $query  = "SELECT newsfeed.id AS id, users.profile_picture, users.id AS user_id, users.name AS user_name, newsfeed.content, newsfeed.date ";
        $query .= "FROM users, newsfeed ";
        $query .= "WHERE users.id = newsfeed.user_id ";
        $query .= "ORDER BY newsfeed.date DESC ";
        //$query .= "LIMIT $limit OFFSET $offset";

        $database->prepare($query);
        $database->execute();
        $newsfeed = $database->fetchAllAssociative();

        //return array("newsfeed" => $newsfeed, "pagination" => $pagination);
        return array("newsfeed" => $newsfeed);
     }
    public function getById($newsfeedId){

        $database = Database::openConnection();
        $query  = "SELECT newsfeed.id AS id, users.profile_picture, users.id AS user_id, users.name AS user_name, newsfeed.content, newsfeed.date ";
        $query .= "FROM users, newsfeed ";
        $query .= "WHERE newsfeed.id = :id ";
        $query .= "AND users.id = newsfeed.user_id  LIMIT 1 ";

        $database->prepare($query);
        $database->bindValue(':id', (int)$newsfeedId);
        $database->execute();

        $feed = $database->fetchAllAssociative();
        return $feed;
     }
    public function create($userId, $content){
/*
        $validation = new Validation();
        if(!$validation->validate(['Content'   => [$content, "required|minLen(4)|maxLen(300)"]])) {
            $this->errors = $validation->errors();
            return false;
        }
*/
        $database = Database::openConnection();
        $query    = "INSERT INTO newsfeed (user_id, content) VALUES (:user_id, :content)";

        $database->prepare($query);
        $database->bindValue(':user_id', $userId);
        $database->bindValue(':content', $content);
        $database->execute();

        if($database->countRows() !== 1){
            throw new Exception("Couldn't add news feed");
        }

        $newsfeedId = $database->lastInsertedId();
        $feed = $this->getById($newsfeedId);
        return $feed;
     }
}
