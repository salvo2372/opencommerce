<?php
namespace App\core;
/**
 * Model Class
 *
 * Main/Super class for model classes
 *
 * @license    http://opensource.org/licenses/MIT The MIT License (MIT)
 * @author     Omar El Gabry <omar.elgabry.93@gmail.com>
 */
use App\models\Database as Database;
use App\core\Session as Session;
class Model {

    /**
     * Oggetto publico tabella di riferimento
     *
     * @var string
    */
    public $table = false;

    /**
     * Array of validation errors
     *
     * @var array
     */
    protected $errors = [];

    /**
     * Constructor
     *
     */
    public function __construct(){
        if($this->table === false){
            $this->table = $this->pluralize(get_class($this));
            $pathSeparator = "\\";
            $this->table = str_replace("models","",explode($pathSeparator,$this->table)[2]);
        }
    }

    /**
     * pluralize for table names
     *
     * Automatically selects a database table name based on a pluralized lowercase object class name
     * (i.e. class 'User' => table 'users')
     *
     * @param   string $word
     * @return  string
     */
    private function pluralize($word){

        $word = strtolower($word);
        $plural = [
            //"newsfeed" => "newsfeed",
            "man" => "men",
            "woman" => "women"
        ];

        return isset($plural[$word])? $plural[$word]: $word . "s";
    }

    /**
     * delete record by id
     *
     * @param  string $id
     * @return bool
     * @throws Exception if feed couldn't be deleted
     */
    public function deleteById($id){

        $database = Database::openConnection();
        $database->deleteById($this->table, $id);

        if($database->countRows() !== 1){
            throw new Exception ("Couldn't delete news feed");
        }
    }

    /**
     * get errors
     *
     * @return array errors
     */
    public function errors(){
        return $this->errors;
    }

    /**
     *
     * Ritorna vero/falso se l'utente è già registrato
     *
     * @param  string  $id
     * @return bool
     */
    public function exists($id){

        $database = Database::openConnection();
        $database->getById($this->table, $id);

        return $database->countRows() === 1;
    }

    /**
     * Ritorna il numero di elementi nella tabella
     *
     * @return integer
     */
    public function countAll(){

        $database = Database::openConnection();
        return $database->countAll($this->table);
    }

    /**
     * @param  array  $options
     * @param  string $implodeBy
     * @return string
     */
    public function applyOptions(array $options, $implodeBy){

        $queries = [];

        foreach($options as $key => $value){
            if(!empty($key) || $key === false || $key === 0){
                $queries[] = $value;
            }
        }
        return implode($implodeBy, $queries);
    }
    public function getMenu(){

         $locale =(string) Session::getLang();      
         $database   = Database::openConnection();
         $query  = "(SELECT category.id, category.name FROM category WHERE category.sub_id = 0 AND category.lang = :lang)";
        
         $database->prepare($query);
         $database->bindValue(':lang', $locale);
         $database->execute(); 
         $itemsMenu = $database->fetchAllAssociative();

         $newItemsMenu = array();
         foreach ($itemsMenu as $item){
         foreach ($item as $key => $value){
            if ($key == "id"){
                //$information[$key] = Encryption::encryptId($value);
                $item[$key] = Encryption::encryNameId($value,"category");
            } else {
                $item[$key] = $value;
            }
         }
         array_push($newItemsMenu,$item);
         }

         return array("newItemMenu" => $newItemsMenu);                  
    }
}
