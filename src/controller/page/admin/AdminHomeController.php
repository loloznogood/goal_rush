<?php

namespace src\controller\page\admin;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use src\model\AdminMenuModel;
use src\model\AdminMenuGroupe;
use src\model\CookieModel;
use src\controller\CookieController;
use src\model\AdminMenuItem;
use src\model\SecurityModel;
use \src\model\metier\Admin;
use src\model\dao\AdminDAO;
use src\model\dao\DAOFactory;
use \src\controller\page\PageController;

class AdminHomeController extends PageController
{

    public function __construct($container)
    {
        parent::__construct($container);
        $this->filAriane = [
            'Accueil' => $this->getContainer()->router->pathFor('accueil.index'),
            'Menu administrateur'=> $this->getContainer()->router->pathFor('admin/home.index')
        ];

    }

    public function index(RequestInterface $request, ResponseInterface $response, array $args)
    {

        $cookieCtrl = new CookieController();
        $adminId = $cookieCtrl->getCookie('admin-id')->getValue();
        $daoFactory = new DAOFactory($this->getContainer()->db);
        $adminDAO = $daoFactory->getAdminDAO();
        $admin = $adminDAO->find($adminId);


        $menu = new AdminMenuModel();

        $gestionFichier = new AdminMenuGroupe("Gestion des fichiers (pdf, doc, images...)");
        $gestionFichier->addItem(new AdminMenuItem("File manager", $this->getContainer()->router->pathFor('admin/filemanager.index'), \src\controller\page\admin\AdminFileManagerController::DROITS));
        $menu->addGroupe($gestionFichier);

        $gestionProduit = new AdminMenuGroupe("Gestion des produits");
        $gestionProduit->addItem(new AdminMenuItem("Gestion des lignes de produits", "/admin/lp", \src\controller\page\admin\AdminLPController::DROITS));
        $gestionProduit->addItem(new AdminMenuItem("Gestion des groupes articles", "admin/ga", \src\controller\page\admin\AdminGAController::DROITS));
        $gestionProduit->addItem(new AdminMenuItem("Gestion des groupes produit", "admin/gp", \src\controller\page\admin\AdminGPController::DROITS));
        $menu->addGroupe($gestionProduit);

        $gestionService = new AdminMenuGroupe("Gestion des services");
        $gestionService->addItem(new AdminMenuItem("Gestion des services", "admin/service", \src\controller\page\admin\AdminServiceController::DROITS));
        $menu->addGroupe($gestionService);

        $gestionActu = new AdminMenuGroupe("Gestion des actualites");
        $gestionActu->addItem(new AdminMenuItem("Gestion des actualites", $this->getContainer()->router->pathFor('admin/actualite.index'), \src\controller\page\admin\AdminActualiteController::DROITS));
        $menu->addGroupe($gestionActu);

        $gestionStatique = new AdminMenuGroupe("Gestion des pages");
        $gestionStatique->addItem(new AdminMenuItem("Gérer les pages", $this->getContainer()->router->pathFor('admin/page.index'), \src\controller\page\admin\AdminPageController::DROITS));
        $menu->addGroupe($gestionStatique);

        $gestionMail = new AdminMenuGroupe("Gestion des mailings");
        $gestionMail->addItem(new AdminMenuItem("Gérer les mailings", $this->getContainer()->router->pathFor('admin/mail.index'), \src\controller\page\admin\AdminMailController::DROITS));
        $menu->addGroupe($gestionMail);

        $gestionCom = new AdminMenuGroupe("Gestion des commerciaux");
        $gestionCom->addItem(new AdminMenuItem("Gérer les commerciaux", $this->getContainer()->router->pathFor('admin/commercial.index'), \src\controller\page\admin\AdminCommercialController::DROITS));
        $gestionCom->addItem(new AdminMenuItem("Gérer les départements des commerciaux", $this->getContainer()->router->pathFor('admin/commercial.departement'), \src\controller\page\admin\AdminCommercialController::DROITS));
        $menu->addGroupe($gestionCom);

        $gestionAdmin = new AdminMenuGroupe("Gestion des admins");
        $gestionAdmin->addItem(new AdminMenuItem("Gérer les admins", $this->getContainer()->router->pathFor('admin/admin.index'), \src\controller\page\admin\AdminAdminController::DROITS));
        $menu->addGroupe($gestionAdmin);

        $gestionCS = new AdminMenuGroupe("Gestion des catalogues standards");
        $gestionCS->addItem(new AdminMenuItem("Gérer les catalogues standards", $this->getContainer()->router->pathFor('admin/catalogue-standard.index'), \src\controller\page\admin\AdminCatalogueStandardController::DROITS));
        $menu->addGroupe($gestionCS);

        $gestionCatStock = new AdminMenuGroupe("Gestion du catalogue stock");
        $gestionCatStock->addItem(new AdminMenuItem("Gérer les produits du catalogue stock", $this->getContainer()->router->pathFor('admin/catalogue-stock.index'), \src\controller\page\admin\AdminCatalogueStockController::DROITS));
        $menu->addGroupe($gestionCatStock);

        $gestionRef = new AdminMenuGroupe("Autre gestion");
        $gestionRef->addItem(new AdminMenuItem("Générer le fichier robots.txt", $this->getContainer()->router->pathFor('admin/robots-txt.index'), \src\controller\page\admin\AdminRobotsTxtController::DROITS));
        $gestionRef->addItem(new AdminMenuItem("Générer le fichier sitemap.xml", $this->getContainer()->router->pathFor('admin/sitemap-xml.index'), \src\controller\page\admin\AdminSitemapXmlController::DROITS));
        $menu->addGroupe($gestionRef);

        $gestionLiens = new AdminMenuGroupe("Gestion des liens");
        $gestionLiens->addItem(new AdminMenuItem("Gérer les liens des produits", $this->getContainer()->router->pathFor('admin/lien.index'), \src\controller\page\admin\AdminLienController::DROITS));
        
        $menu->addGroupe($gestionLiens);


        $dataForView = [
            'admin' => $admin,
            'menu'      => $menu,
            'filAriane' => $this->filAriane,
            'footer' => $this->getFooterData()
        ];
        $this->render($response, 'admin-home.html.twig', ['data' => $dataForView]);
    }
}