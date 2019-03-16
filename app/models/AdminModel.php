<?php
namespace App\models;

use App\models\Database as Database;
use App\models\UserModel as UserModel;
use App\models\Pagination as Pagination;

class AdminModel extends UserModel{

    public $table = "users";

    public function getProfileInfo($userId){

        $database = Database::openConnection();

        $database->getById("users", $userId);

        if($database->countRows() !== 1){
            echo "User ID " .  $userId . " doesn't exists";
        }

        $user = $database->fetchAssociative();

        $user["id"]    = (int)$user["id"];
        $user["image"] = PUBLIC_ROOT . "img/profile_pictures/" . $user['profile_picture'];
        $user["email"] = empty($user['is_email_activated'])? null : $user['email'];

        return $user;
      }

    public function getAllUsers(){
        $database = Database::openConnection();

        $database->prepare("SELECT name, role, email, is_email_activated FROM users");
        $database->execute();

        $users = $database->fetchAllAssociative();
        $cols  = array("User Name", "Role", "Email", "is Email Activated?");

        //var_dump(["rows" => $users, "cols" => $cols, "filename" => "users"]);
        return ["rows" => $users, "cols" => $cols, "filename" => "users"];
    }
    public function getUsers($name = null, $email = null, $role = null, $pageNum = 1){

        $validation = new Validation();
        if(!$validation->validate([
            'User Name' => [$name,  'alphaNumWithSpaces|maxLen(30)'],
            'Email'     => [$email, 'email|maxLen(50)'],
            'Role'      => [$role,  'inArray(admin, user)']])){
            $this->errors  = $validation->errors();
            return false;
        }

        $options = [
            $name      => "name LIKE :name ",
            $email     => "email = :email ",
            $role      => "role = :role "
        ];

        // get options query
        $options = self::applyOptions($options, "AND ");
        $options = empty($options)? "": "WHERE " . $options;

        $values = [];
        if (!empty($name))  $values[":name"]  = "%". $name ."%";
        if (!empty($email)) $values[":email"] = $email;
        if (!empty($role))  $values[":role"]  = $role;

        // get pagination object so that we can add offset and limit in our query
        $pagination = Pagination::pagination("users", $options, $values, $pageNum);
        $offset     = $pagination->getOffset();
        $limit      = $pagination->perPage;

        $database   = Database::openConnection();
        $query   = "SELECT id, name, email, role, is_email_activated FROM users ";
        $query  .= $options;
        //$query  .= "LIMIT $limit OFFSET $offset";

        $database->prepare($query);
        $database->execute($values);
        $users = $database->fetchAllAssociative();
        return array("users" => $users);
        //return array("users" => $users, "pagination" => $pagination);
    }
    public function deleteUser($adminId, $userId){

        // current admin can't delete himself
/*
        $validation = new Validation();
        if(!$validation->validate([ 'User ID' => [$userId, "notEqual(".$adminId.")"]])) {
            $this->errors  = $validation->errors();
            return false;
        }
*/
        $database = Database::openConnection();
        $database->deleteById("users", $userId);

        if ($database->countRows() !== 1) {
           // throw new Exception ("Couldn't delete user");
        }
    }
    public function updateUserInfo($userId, $adminId, $name, $password, $role){

         $user = $this->getProfileInfo($userId);

         $name = (!empty($name) && $name !== $user["name"])? $name: null;
         $role = (!empty($role) && $role !== $user["role"])? $role: null;

         // current admin can't change his role,
         // changing the role requires to logout or reset session,
         // because role is stored in the session
         if(!empty($role) && $adminId === $user["id"]){
             $this->errors[] = "You can't change your role";
             return false;
         }

        $validation = new Validation();
        if(!$validation->validate([
             "Name" => [$name, "alphaNumWithSpaces|minLen(4)|maxLen(30)"],
             "Password" => [$password, "minLen(6)|password"],
             'Role' => [$role, "inArray(admin, user)"]])){
             $this->errors = $validation->errors();
             return false;
         }

         if($password || $name || $role) {

             $options = [
                 $name     => "name = :name ",
                 $password => "hashed_password = :hashed_password ",
                 $role     => "role = :role "
             ];

             $database = Database::openConnection();
             $query   = "UPDATE users SET ";
             $query  .= $this->applyOptions($options, ", ");
             $query  .= "WHERE id = :id LIMIT 1 ";
             $database->prepare($query);

             if($name) {
                 $database->bindValue(':name', $name);
             }
             if($password) {
                 $database->bindValue(':hashed_password', password_hash($password, PASSWORD_DEFAULT, array('cost' => Config::get('HASH_COST_FACTOR'))));
             }
             if($role){
                 $database->bindValue(':role', $role);
             }

             $database->bindValue(':id', $userId);
             $result = $database->execute();

             if(!$result){
                 throw new Exception("Couldn't update profile");
             }
         }

         return true;
     }
}
