<?php
namespace App\core;

use Twig_Environment;
use Twig_Loader_Filesystem;
use Twig_Extensions_Extension_Text;
use Twig_Extensions_Extension_I18n;
class View{
	  private $folder;
	  private $loader;
	  private $twig;
	  private $template;
    private $file;
    private $data;
    public $controller;

    /**
     * Constructor
     *
     * @param Controller $controller
     */
    public function __construct(Controller $controller){		
        $this->controller = $controller;
        $this->init();
     }

	public function init(){

        $folder = APP . 'templates';
        $loader = new Twig_Loader_Filesystem($folder);

        $this->twig = new Twig_Environment($loader, array(
            'cache' => false,
        ));
        $this->twig->addExtension(new Twig_Extensions_Extension_Text());
        $this->twig->addExtension(new Twig_Extensions_Extension_I18n());

        $twig = $this->twig;

	}

    public function parseXml($data)
    {
            $this->controller->response->setContent($data);
            $this->controller->response->type('text/xmlns');
            //var_dump($this->controller->response);

    }

    public function parseView($file, $data)
    {
            $this->file = $file . '.html';
            $this->data = $data;
						$this->template = $this->twig->loadTemplate($this->file);

            $this->controller->response->setContent($this->template->render($this->data));
            return $this->template->render($this->data);
    }

    public function parseJson($data){
        $jsonData = $this->jsonEncode($data);
        $this->controller->response->type('application/json')->setContent($jsonData);
        return $jsonData;
    }

	public function parseErrors($errors){
         $html = $this->parseView('back/admin/alerts/errors', ["errors" => $errors]);
         if($this->controller->request->isAjax()){
             return $this->parseJson(array("error" => $html));
         }else{
             $this->controller->response->setContent($html);
             return $html;
         }
    }

	public function parseSuccess($message){
			$html = $this->parseView('back/admin/alerts/success', array("success" => $message));
            //return $this->response->setContent($this->view->parseView('back/admin/newsfeed/index', $this->setting));
            //$html = $this->render(Config::get('VIEWS_PATH') . 'alerts/success.php', array("success" => $message));
            if($this->controller->request->isAjax()){
                return $this->parseJson(array("success" => $html));
            }else{
                $this->controller->response->setContent($html);
                return $html;
            }
    }
    public function templateOrder($file, $data){
        $folder = APP . 'templates';
        $loader = new Twig_Loader_Filesystem($folder);
        $this->twig = new Twig_Environment($loader, array(
            'cache' => false,
        ));
        $this->twig->addExtension(new Twig_Extensions_Extension_Text());
        $this->twig->addExtension(new Twig_Extensions_Extension_I18n());

        $file = $file . '.html';
        $data = $data;
        $template = $this->twig->loadTemplate($file);
        $html = $template->render($data);
        return $html;
    }
    public function jsonEncode($data){
        return json_encode($data);
    }
}
