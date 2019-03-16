<?php
namespace App\models;

use App\models\Database as Database;
use App\core\Model as Model;
use App\core\Session as Session;
use App\core\Config as Config;
use App\core\Email as Email;
use App\models\Validation as Validation;

class LoginModel extends Model{

    /**
      * Table name for this & extending classes.
      *
      * @var string
      */
    public $table = "users";

    /**
     * Login process (for DEFAULT user accounts).
     *
     * @param $user_name string The user's name
     * @param $user_password string The user's password
     * @param $set_remember_me_cookie mixed Marker for usage of remember-me cookie feature
     *
     * @return bool success state
     */

    public function login($email, $password, $rememberMe, $userIp, $userAgent)
    {
      // 2. validate only presence
      $validation = new Validation();
      if(!$validation->validate([
          "Your Email" => [$email, 'required'],
          "Your Password" => [$password, 'required']])){
          $this->errors = $validation->errors();
          return false;
      }
        // 3. check if user has previous failed login attempts
        $database = Database::openConnection();
        // 4. get user from database
        $database->prepare("SELECT * FROM users WHERE email = :email AND is_email_activated = 1 LIMIT 1");
        $database->bindValue(':email', $email);
        $database->execute();
        $user = $database->fetchAssociative();


        $userId = isset($user["id"])? $user["id"]: null;
        $hashedPassword = isset($user["hashed_password"])? $user["hashed_password"]: null;

        // 5. validate data returned from users table
        if(!$validation->validate([
            "Login" => [["user_id" => $userId, "hashed_password" => $hashedPassword, "password" => $password], 'credentials']])){

            // if not valid, then increment number of failed logins
            //$this->incrementFailedLogins($email, $failedLogin);

            // also, check if current IP address is trying to login using multiple accounts,
            // if so, then block it, if not, just add a new record to database
            //$this->handleIpFailedLogin($userIp, $email);

            $this->errors = $validation->errors();
            return false;
        }

        // reset session

        Session::reset(["user_id" => $userId, "role" => $user["role"], "ip" => $userIp, "user_agent" => $userAgent]);

        return true;


    }

    public function writeNewUserToDatabase($name, $email, $password, $confirmPassword, $captcha)
    {
        // write user data to database

        $database = Database::openConnection();

        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);


        $database->beginTransaction();
        $query = "INSERT INTO users (name, email, role, hashed_password, email_token, email_last_verification) ".
                 "VALUES (:name, :email, :role, :hashed_password, :email_token, :email_last_verification)";

        $database->prepare($query);
        $database->bindValue(':name', $name);
        $database->bindValue(':email', $email);
        $database->bindValue(':role', "user");
        $database->bindValue(':hashed_password', $hashedPassword);

        // email token and time of generating it
        $token = sha1(uniqid(mt_rand(), true));
        $database->bindValue(':email_token', $token);
        $database->bindValue(':email_last_verification', time());

        $database->execute();

        $id = $database->lastInsertedId();
        Email::sendEmail(Config::get('EMAIL_EMAIL_VERIFICATION'), $email, ["name" => $name, "id" => $id], ["email_token" => $token]);
        //Email::contactSend();

        $database->commit();

        return true;
        $count =  $database->countRows();
        if ($count == 1) {
            return true;
        }
        return false;

    }
    public function isEmailVerificationTokenValid($userId, $emailToken){

        if (empty($userId) || empty($emailToken)) {
            return false;
        }

        $database = Database::openConnection();
        $database->prepare("SELECT * FROM users WHERE id = :id LIMIT 1");
        $database->bindValue(':id', $userId);
        $database->execute();
        $user = $database->fetchAssociative();
        $isTokenValid = ($user["email_token"] === $emailToken)? true: false;

        // check if user is already verified
        if(!empty($user["is_email_activated"])){
            $this->resetEmailVerificationToken($userId, true);
            return false;
        }

        // setting expiry time on email verification is much better,
        // you can't be sure if the email will be secured,
        // also any user can register with email of another person,
        // so this person won't be able to register at all!.
        $expiry_time = (24 * 60 * 60);
        $time_elapsed = time() - $user['email_last_verification'];

        // token is usable only once.
        if($database->countRows() === 1 && $isTokenValid && $time_elapsed < $expiry_time) {

            $this->resetEmailVerificationToken($userId, true);
            return true;

        }else if($database->countRows() === 1 && $isTokenValid && $time_elapsed > $expiry_time) {

            $this->resetEmailVerificationToken($userId, false);
            return false;

        }else{

            // reset token if invalid,
            // But, if the user id was invalid, this won't make any affect on database
            $this->resetEmailVerificationToken($userId, false);
            //Logger::log("EMAIL TOKEN", "User ID ". $userId . " is trying to access using invalid email token " . $emailToken, __FILE__, __LINE__);
            return false;
        }
    }
    public function resetEmailVerificationToken($userId, $isValid){

        $database = Database::openConnection();

        if($isValid){
            $query = "UPDATE users SET email_token = NULL, " .
                "email_last_verification = NULL, is_email_activated = 1 ".
                "WHERE id = :id LIMIT 1";
        }else{
            $query = "DELETE FROM users WHERE id = :id";
        }

        $database->prepare($query);
        $database->bindValue(':id', $userId);
        $result = $database->execute();
        if(!$result){
            throw new Exception("Couldn't reset email verification token");
        }
    }
    public function getProfileInfo($userId){

        $database = Database::openConnection();
        $database->getById("users", $userId);

        if($database->countRows() !== 1){
            echo "User ID " .  $userId . " doesn't exists";
        }

        $user = $database->fetchAssociative();

        $user["id"]    = (int)$user["id"];
        $user["image"] = PUBLIC_ROOT . "img/profile_pictures/" . $user['profile_picture'];
        $user["email"] = empty($user['is_email_activated'])? null: $user['email'];

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
    /**
     * Logout by removing the Session and Cookies.
     *
     * @access public
     * @param  integer $userId
     *
     */
    public function logOut($userId){

        Session::remove();
    }
}
