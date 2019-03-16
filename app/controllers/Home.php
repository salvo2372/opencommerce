<?php
namespace App\controllers;
/**
 * The default home controller, called when no controller/method has been passed
 * to the application.
 */
use App\core\Controller as Controller;
use App\models\HomeModel as HomeModel;
use App\models\UserModel as UserModel;
use App\models\ProductsModel as ProductsModel;
use App\core\Config as Config;
use App\core\Session as Session;
use Cart\Cart as Cart;
class Home extends Controller
{
    public $setting = [];

    public $data = [];

    public $cart;
    /**
     * The default controller method.
     *
     * @return void
     */
    public function __construct($tommaso, $request, $response){
        parent::__construct($request,$response);
        $this->tommaso = $tommaso;
    }

    public function initialize(){

        $this->loadComponents([
            'Auth',
            'Security'
        ]);
    }

    public function beforeAction(){

        parent::beforeAction();

        Config::setJsConfig('csrfToken', Session::generateCsrfToken());
        Config::setJsConfig('curPage', "home");

    }
    public function index()
    {
        $this->i18n->setCachePath(APP . 'langcache/front/home/index/');
        $this->i18n->setFilePath(APP . 'templates/lang/front/home/index/lang_{LANGUAGE}.ini'); // language file path
        $this->i18n->setFallbackLang(Session::getLang());
        $this->i18n->init();
        $userModel = $this->model('UserModel');
        Config::setJsConfig('curMenu', "home");
        if ($this->Auth->isLoggedIn()) {
            $this->data = ($userModel->getProfileInfo(Session::get("user_id"))!= null) ? $userModel->getProfileInfo(Session::get("user_id")) : [];
        }

      $homeModel = $this->model('HomeModel');
      $itemsMenu = $homeModel->getMenu();
      //Session::changeLang();

      $products = $homeModel->getHomeProducts();
      $pagemeta = array("meta_title" => L("pagemeta_meta_title"), "meta_description" => L("pagemeta_meta_description"));

      $page = array("home" => L("page_home"), "service" => L("page_service"), "category" => L("page_category"), "shop" => L("page_shop"), "cart" => L("page_cart"), "contact" => L("page_contact"));

      $this->setting = array_merge(
                  array("products" => $products,"pagemeta" => $pagemeta,"page" => $page),
                  array("lang" =>array("page_title" => L("page_title"),"button_cart" => L("button_cart"), "heading_text" => L("heading_text"), "heading_title" => L("heading_title"), "button_description" => L("button_description"),"detail" => L("detail"),"minicruise" => L("minicruise"))),
                  array("setting" =>array( "locale" => Session::getLang(), "language" => Session::getLanguage(), "languages" => Config::get('LANGUAGES'),"url" => Config::get('URL'), "public_root" => PUBLIC_ROOT, "app" => APP)),
                  array("generateCsrfToken" => Session::generateCsrfToken(), "getUserRole" => Session::getUserRole(), "config" => json_encode(Config::getJsConfig())),
                  array("userLogged" => $this->data, "itemsMenu" => $itemsMenu)
      );

      return $this->response->setContent($this->view->parseView('front/home/index', $this->setting));

    }
    public function sitemaps()
    {
      $data = '<?xml version="1.0" encoding="UTF-8"?><?xml-stylesheet type="text/xsl" href="//www.booking-vacanze-isole-eolie.com/qtx-sitemap.xsl"?>
<urlset xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:image="http://www.google.com/schemas/sitemap-image/1.1" xsi:schemaLocation="http://www.sitemaps.org/schemas/sitemap/0.9 http://www.sitemaps.org/schemas/sitemap/0.9/sitemap.xsd" xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
  <url>
    <loc>https://www.minicrociereisoleeolie.it/</loc>
    <changefreq>daily</changefreq>
    <priority>1.00</priority>
    <lastmod>2017-02-09T07:39:01-01:00</lastmod>
  </url>
  <url>
    <loc>https://www.minicrociereisoleeolie.it/information/services</loc>
    <changefreq>daily</changefreq>
    <priority>1.00</priority>
    <lastmod>2017-02-09T07:39:01-01:00</lastmod>
  </url>
  <url>
    <loc>https://www.minicrociereisoleeolie.it/information/index/minicruise</loc>
    <changefreq>daily</changefreq>
    <priority>1.00</priority>
    <lastmod>2017-02-09T07:39:01-01:00</lastmod>
  </url>
  <url>
    <loc>https://www.minicrociereisoleeolie.it/information/index/minicrociere</loc>
    <changefreq>daily</changefreq>
    <priority>1.00</priority>
    <lastmod>2017-02-09T07:39:01-01:00</lastmod>
  </url>
  <url>
    <loc>https://www.minicrociereisoleeolie.it/information/index/ticket_office</loc>
    <changefreq>daily</changefreq>
    <priority>1.00</priority>
    <lastmod>2017-02-09T07:39:01-01:00</lastmod>
  </url>
  <url>
    <loc>https://www.minicrociereisoleeolie.it/information/index/biglietteria</loc>
    <changefreq>daily</changefreq>
    <priority>1.00</priority>
    <lastmod>2017-02-09T07:39:01-01:00</lastmod>
  </url>
  <url>
    <loc>https://www.minicrociereisoleeolie.it/information/index/parking</loc>
    <changefreq>daily</changefreq>
    <priority>1.00</priority>
    <lastmod>2017-02-09T07:39:01-01:00</lastmod>
  </url>
  <url>
    <loc>https://www.minicrociereisoleeolie.it/information/index/parcheggio</loc>
    <changefreq>daily</changefreq>
    <priority>1.00</priority>
    <lastmod>2017-02-09T07:39:01-01:00</lastmod>
  </url>
  <url>
    <loc>https://www.minicrociereisoleeolie.it/shop/index</loc>
    <changefreq>daily</changefreq>
    <priority>1.00</priority>
    <lastmod>2017-02-09T07:39:01-01:00</lastmod>
  </url>
  <url>
    <loc>https://www.minicrociereisoleeolie.it/shop/shop</loc>
    <changefreq>daily</changefreq>
    <priority>1.00</priority>
    <lastmod>2017-02-09T07:39:01-01:00</lastmod>
  </url>
  <url>
    <loc>https://www.minicrociereisoleeolie.it/contact/index</loc>
    <changefreq>daily</changefreq>
    <priority>1.00</priority>
    <lastmod>2017-02-09T07:39:01-01:00</lastmod>
  </url>
  <url>
    <loc>https://www.minicrociereisoleeolie.it/category/index/mini-crociere</loc>
    <changefreq>daily</changefreq>
    <priority>1.00</priority>
    <lastmod>2017-02-09T07:39:01-01:00</lastmod>
  </url>
  <url>
    <loc>https://www.minicrociereisoleeolie.it/category/index/mini-cruise</loc>
    <changefreq>daily</changefreq>
    <priority>1.00</priority>
    <lastmod>2017-02-09T07:39:01-01:00</lastmod>
  </url>
  <url>
    <loc>https://www.minicrociereisoleeolie.it/category/index/minicrociere-milazzo</loc>
    <changefreq>daily</changefreq>
    <priority>1.00</priority>
    <lastmod>2017-02-09T07:39:01-01:00</lastmod>
  </url>
  <url>
    <loc>https://www.minicrociereisoleeolie.it/category/index/minicruise-from-milazzo</loc>
    <changefreq>daily</changefreq>
    <priority>1.00</priority>
    <lastmod>2017-02-09T07:39:01-01:00</lastmod>
  </url>
  <url>
    <loc>https://www.minicrociereisoleeolie.it/products/index/mini-crociera-lipari-vulcano</loc>
    <changefreq>daily</changefreq>
    <priority>1.00</priority>
    <lastmod>2017-02-09T07:39:01-01:00</lastmod>
  </url>
  <url>
    <loc>https://www.minicrociereisoleeolie.it/products/index/mini-crociera-lipari-vulcano</loc>
    <changefreq>daily</changefreq>
    <priority>1.00</priority>
    <lastmod>2017-02-09T07:39:01-01:00</lastmod>
  </url>
  <url>
    <loc>https://www.minicrociereisoleeolie.it/products/index/mini-cruise-lipari-and-vulcano</loc>
    <changefreq>daily</changefreq>
    <priority>1.00</priority>
    <lastmod>2017-02-09T07:39:01-01:00</lastmod>
  </url>
  <url>
    <loc>https://www.minicrociereisoleeolie.it/products/index/tour-dei-vulcani</loc>
    <changefreq>daily</changefreq>
    <priority>1.00</priority>
    <lastmod>2017-02-09T07:39:01-01:00</lastmod>
  </url>
  <url>
    <loc>https://www.minicrociereisoleeolie.it/products/index/mini-cruise-volcanoes-in-tour</loc>
    <changefreq>daily</changefreq>
    <priority>1.00</priority>
    <lastmod>2017-02-09T07:39:01-01:00</lastmod>
  </url>
  <url>
    <loc>https://www.minicrociereisoleeolie.it/products/index/salina-tour-lipari</loc>
    <changefreq>daily</changefreq>
    <priority>1.00</priority>
    <lastmod>2017-02-09T07:39:01-01:00</lastmod>
  </url>
  <url>
    <loc>https://www.minicrociereisoleeolie.it/products/index/mini-cruise-salina-lipari-tour</loc>
    <changefreq>daily</changefreq>
    <priority>1.00</priority>
    <lastmod>2017-02-09T07:39:01-01:00</lastmod>
  </url>
  <url>
    <loc>https://www.minicrociereisoleeolie.it/products/index/mini-crociera-quattro-isole</loc>
    <changefreq>daily</changefreq>
    <priority>1.00</priority>
    <lastmod>2017-02-09T07:39:01-01:00</lastmod>
  </url>
  <url>
    <loc>https://www.minicrociereisoleeolie.it/products/index/four-aeolian-islands-in-mini-cruise</loc>
    <changefreq>daily</changefreq>
    <priority>1.00</priority>
    <lastmod>2017-02-09T07:39:01-01:00</lastmod>
  </url>
  <url>
    <loc>https://www.minicrociereisoleeolie.it/products/index/mini-crociera-panarea-stromboli-by-night</loc>
    <changefreq>daily</changefreq>
    <priority>1.00</priority>
    <lastmod>2017-02-09T07:39:01-01:00</lastmod>
  </url>
  <url>
    <loc>https://www.minicrociereisoleeolie.it/products/index/panarea-stromboli-by-night-mini-cruise</loc>
    <changefreq>daily</changefreq>
    <priority>1.00</priority>
    <lastmod>2017-02-09T07:39:01-01:00</lastmod>
  </url>
  <url>
    <loc>https://www.minicrociereisoleeolie.it/products/index/mini-crociera-alicudi-filicudi</loc>
    <changefreq>daily</changefreq>
    <priority>1.00</priority>
    <lastmod>2017-02-09T07:39:01-01:00</lastmod>
  </url>
  <url>
    <loc>https://www.minicrociereisoleeolie.it/products/index/mini-cruise-alicudi-filicudi</loc>
    <changefreq>daily</changefreq>
    <priority>1.00</priority>
    <lastmod>2017-02-09T07:39:01-01:00</lastmod>
  </url>
  <url>
    <loc>https://www.minicrociereisoleeolie.it/products/index/mini-crociera-panarea-stromboli-special</loc>
    <changefreq>daily</changefreq>
    <priority>1.00</priority>
    <lastmod>2017-02-09T07:39:01-01:00</lastmod>
  </url>
  <url>
    <loc>https://www.minicrociereisoleeolie.it/products/index/mini-cruise-panarea-stromboli-special</loc>
    <changefreq>daily</changefreq>
    <priority>1.00</priority>
    <lastmod>2017-02-09T07:39:01-01:00</lastmod>
  </url>
  <url>
    <loc>https://www.minicrociereisoleeolie.it/products/index/mini-crociera-panarea-stromboli-day</loc>
    <changefreq>daily</changefreq>
    <priority>1.00</priority>
    <lastmod>2017-02-09T07:39:01-01:00</lastmod>
  </url>
  <url>
    <loc>https://www.minicrociereisoleeolie.it/products/index/panarea-stromboli-all-day-mini-cruise</loc>
    <changefreq>daily</changefreq>
    <priority>1.00</priority>
    <lastmod>2017-02-09T07:39:01-01:00</lastmod>
  </url>
  <url>
    <loc>https://www.minicrociereisoleeolie.it/products/index/mini-crociera-day-and-night-stromboli</loc>
    <changefreq>daily</changefreq>
    <priority>1.00</priority>
    <lastmod>2017-02-09T07:39:01-01:00</lastmod>
  </url>
  <url>
    <loc>https://www.minicrociereisoleeolie.it/products/index/day-and-night-stromboli-mini-cruise</loc>
    <changefreq>daily</changefreq>
    <priority>1.00</priority>
    <lastmod>2017-02-09T07:39:01-01:00</lastmod>
  </url>
  <url>
    <loc>https://www.minicrociereisoleeolie.it/category/index/minicrociere-lipari</loc>
    <changefreq>daily</changefreq>
    <priority>1.00</priority>
    <lastmod>2017-02-09T07:39:01-01:00</lastmod>
  </url>
  <url>
    <loc>https://www.minicrociereisoleeolie.it/products/index/escursione-alicudi-filicudi-partenza-lipari</loc>
    <changefreq>daily</changefreq>
    <priority>1.00</priority>
    <lastmod>2017-02-09T07:39:01-01:00</lastmod>
  </url>
  <url>
    <loc>https://www.minicrociereisoleeolie.it/products/index/excursion-alicudi-filicudi-from-lipari</loc>
    <changefreq>daily</changefreq>
    <priority>1.00</priority>
    <lastmod>2017-02-09T07:39:01-01:00</lastmod>
  </url>
  <url>
    <loc>https://www.minicrociereisoleeolie.it/products/index/escursione-salina-partenza-lipari</loc>
    <changefreq>daily</changefreq>
    <priority>1.00</priority>
    <lastmod>2017-02-09T07:39:01-01:00</lastmod>
  </url>
  <url>
    <loc>https://www.minicrociereisoleeolie.it/category/index/minicruise-from-lipari</loc>
    <changefreq>daily</changefreq>
    <priority>1.00</priority>
    <lastmod>2017-02-09T07:39:01-01:00</lastmod>
  </url>
  <url>
    <loc>https://www.minicrociereisoleeolie.it/products/index/escursione-alicudi-filicudi-partenza-lipari</loc>
    <changefreq>daily</changefreq>
    <priority>1.00</priority>
    <lastmod>2017-02-09T07:39:01-01:00</lastmod>
  </url>
  <url>
    <loc>https://www.minicrociereisoleeolie.it/products/index/excursion-alicudi-filicudi-from-lipari</loc>
    <changefreq>daily</changefreq>
    <priority>1.00</priority>
    <lastmod>2017-02-09T07:39:01-01:00</lastmod>
  </url>
  <url>
    <loc>https://www.minicrociereisoleeolie.it/products/index/escursione-salina-partenza-lipari</loc>
    <changefreq>daily</changefreq>
    <priority>1.00</priority>
    <lastmod>2017-02-09T07:39:01-01:00</lastmod>
  </url>
  <url>
    <loc>https://www.minicrociereisoleeolie.it/products/index/excursion-salina-from-lipari</loc>
    <changefreq>daily</changefreq>
    <priority>1.00</priority>
    <lastmod>2017-02-09T07:39:01-01:00</lastmod>
  </url>
  <url>
    <loc>https://www.minicrociereisoleeolie.it/products/index/escursione-panarea-stromboli-partenza-lipari</loc>
    <changefreq>daily</changefreq>
    <priority>1.00</priority>
    <lastmod>2017-02-09T07:39:01-01:00</lastmod>
  </url>
  <url>
    <loc>https://www.minicrociereisoleeolie.it/products/index/excursion-panarea-stromboli-from-lipari</loc>
    <changefreq>daily</changefreq>
    <priority>1.00</priority>
    <lastmod>2017-02-09T07:39:01-01:00</lastmod>
  </url>
  <url>
    <loc>https://www.minicrociereisoleeolie.it/category/index/minicrociere-vulcano</loc>
    <changefreq>daily</changefreq>
    <priority>1.00</priority>
    <lastmod>2017-02-09T07:39:01-01:00</lastmod>
  </url>
  <url>
    <loc>https://www.minicrociereisoleeolie.it/category/index/minicruise-from-vulcano</loc>
    <changefreq>daily</changefreq>
    <priority>1.00</priority>
    <lastmod>2017-02-09T07:39:01-01:00</lastmod>
  </url>
  <url>
    <loc>https://www.minicrociereisoleeolie.it/products/index/escursione-alicudi-filicudi-partenza-vulcano</loc>
    <changefreq>daily</changefreq>
    <priority>1.00</priority>
    <lastmod>2017-02-09T07:39:01-01:00</lastmod>
  </url>
  <url>
    <loc>https://www.minicrociereisoleeolie.it/products/index/excursion-alicudi-filicudi-from-vulcano</loc>
    <changefreq>daily</changefreq>
    <priority>1.00</priority>
    <lastmod>2017-02-09T07:39:01-01:00</lastmod>
  </url>
  <url>
    <loc>https://www.minicrociereisoleeolie.it/products/index/escursione-salina-partenza-vulcano</loc>
    <changefreq>daily</changefreq>
    <priority>1.00</priority>
    <lastmod>2017-02-09T07:39:01-01:00</lastmod>
  </url>
  <url>
    <loc>https://www.minicrociereisoleeolie.it/products/index/excursion-salina-from-vulcano</loc>
    <changefreq>daily</changefreq>
    <priority>1.00</priority>
    <lastmod>2017-02-09T07:39:01-01:00</lastmod>
  </url>
  <url>
    <loc>https://www.minicrociereisoleeolie.it/products/index/escursione-panarea-stromboli-partenza-vulcano</loc>
    <changefreq>daily</changefreq>
    <priority>1.00</priority>
    <lastmod>2017-02-09T07:39:01-01:00</lastmod>
  </url>
  <url>
    <loc>https://www.minicrociereisoleeolie.it/products/index/excursion-panarea-stromboli-from-vulcano</loc>
    <changefreq>daily</changefreq>
    <priority>1.00</priority>
    <lastmod>2017-02-09T07:39:01-01:00</lastmod>
  </url>
  <url>
    <loc>https://www.minicrociereisoleeolie.it/category/index/vacanze-isole-eolie</loc>
    <changefreq>daily</changefreq>
    <priority>1.00</priority>
    <lastmod>2017-02-09T07:39:01-01:00</lastmod>
  </url>
  <url>
    <loc>https://www.minicrociereisoleeolie.it/category/index/holiday</loc>
    <changefreq>daily</changefreq>
    <priority>1.00</priority>
    <lastmod>2017-02-09T07:39:01-01:00</lastmod>
  </url>
  <url>
    <loc>https://www.minicrociereisoleeolie.it/category/index/parcheggio</loc>
    <changefreq>daily</changefreq>
    <priority>1.00</priority>
    <lastmod>2017-02-09T07:39:01-01:00</lastmod>
  </url>
  <url>
    <loc>https://www.minicrociereisoleeolie.it/category/index/parking</loc>
    <changefreq>daily</changefreq>
    <priority>1.00</priority>
    <lastmod>2017-02-09T07:39:01-01:00</lastmod>
  </url>
  <url>
    <loc>https://www.minicrociereisoleeolie.it/products/index/parcheggio-scoperto-milazzo</loc>
    <changefreq>daily</changefreq>
    <priority>1.00</priority>
    <lastmod>2017-02-09T07:39:01-01:00</lastmod>
  </url>
  <url>
    <loc>https://www.minicrociereisoleeolie.it/products/index/uncovered-parking-space</loc>
    <changefreq>daily</changefreq>
    <priority>1.00</priority>
    <lastmod>2017-02-09T07:39:01-01:00</lastmod>
  </url>
  <url>
    <loc>https://www.minicrociereisoleeolie.it/products/index/parcheggio-milazzo</loc>
    <changefreq>daily</changefreq>
    <priority>1.00</priority>
    <lastmod>2017-02-09T07:39:01-01:00</lastmod>
  </url>
  <url>
    <loc>https://www.minicrociereisoleeolie.it/products/index/parking-space</loc>
    <changefreq>daily</changefreq>
    <priority>1.00</priority>
    <lastmod>2017-02-09T07:39:01-01:00</lastmod>
  </url>
  </urlset>
  <!-- XML Sitemap generated by Yoast SEO -->'
      ;

       //header('Content-Type: text/xml');
       //echo $data;
      return $this->view->parseXml($data);
    }
}
