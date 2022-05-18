<?php


/*
* Les instructions suivantes ont pour but de pallier le fait 
* que l'on ne peut pas modifier la configuration Apache DOCUMENT_ROOT 
* de façon à la faire pointer vers le dossier public.
*/
if(basename($_SERVER['DOCUMENT_ROOT']) != 'public') $_SERVER['DOCUMENT_ROOT'] .= '/public';
if(basename($_SERVER['CONTEXT_DOCUMENT_ROOT']) != 'public') $_SERVER['CONTEXT_DOCUMENT_ROOT'] .= '/public';
/* Fin instructions */


require '../vendor/autoload.php';

use src\controller\ConfigurationController;
use src\model\ConfigurationModel;
use src\controller\ContainerController;
use src\middleware\AdminNotLoggedInMiddleware;
use src\middleware\AdminAlreadyLoggedInMiddleware;
use src\middleware\AdminAuthorizedMiddleware;
use src\middleware\CatalogueStandardExistsMiddleware;


session_start();


//Chargement configurations
$configCtrl = new ConfigurationController();
$config = $configCtrl->checkMode(ConfigurationModel::MODE_DEV);
//$config = $configCtrl->checkMode(ConfigurationModel::MODE_PRODUCTION);

//Creation du site
$app = new \Slim\App([
    'settings' => $config->getSettings()
]);

//Initilalisation du container
$container = $app->getContainer();
$containerCtrl = new ContainerController($container);


/* ROUTES PAGES VISITEURS DU SITE */
//Accueil:

$app->get('/', \src\controller\page\AccueilController::class . ':index')
    ->setName('accueil.index');

//Métrologie
$app->get('/metrologie', \src\controller\page\MetrologieController::class . ':index')
    ->setName('metrologie.index');

//Silothermométrie
$app->get('/silothermometrie', \src\controller\page\SilothermometrieController::class . ':index')
    ->setName('silothermometrie.index');

//Services
$app->get('/services', \src\controller\page\ServiceController::class . ':index')
    ->setName('service.index');
$app->get('/services/{id:[0-9]+}[/{nom:.*}]', \src\controller\page\ServiceController::class . ':show')
    ->setName('service.show')
    ->add(new \src\middleware\ServiceExistsMiddleware($container));

//Actualites
$app->get('/news', \src\controller\page\ActualiteController::class . ':index')
->setName('actualite.index');
$app->get('/news/{id:[0-9]+}[/{nom:.*}]', \src\controller\page\ActualiteController::class . ':show')
    ->setName('actualite.show')
    ->add(new \src\middleware\ActualiteExistsMiddleware($container));

//Entreprise
$app->get('/entreprise', \src\controller\page\EntrepriseController::class . ':index')
    ->setName('entreprise.index');

//Contact
$app->get('/contact', \src\controller\page\ContactController::class . ':index')
    ->setName('contact.index');
$app->get('/contact/{dept}', \src\controller\page\ContactController::class . ':show')
    ->setName('contact.show')
    ->add(new \src\middleware\DepartementExistsMiddleware($container));
$app->post('/contact', \src\controller\page\ContactController::class . ':index');

//Catalogues standards
$app->get('/catalogue-standard', \src\controller\page\CatalogueStandardController::class . ':index')
    ->setName('catalogue-standard.index');
$app->get('/catalogue-standard/lp-{id:[0-9]+}[-{lp:[0-9]*}[/{nom:.*}]]', \src\controller\page\CatalogueStandardController::class . ':show')
    ->setName('catalogue-standard.showLP')
    ->add(new \src\middleware\CatalogueStandardExistsMiddleware($container));
$app->get('/catalogue-standard/gp-{lp:[0-9]+}-{ga:[0-9]+}-{gp:[0-9]+}[/{nom:.*}]', \src\controller\page\CatalogueStandardController::class . ':show')
    ->setName('catalogue-standard.showGP')
    ->add(new \src\middleware\CatalogueStandardExistsMiddleware($container));

//Catalogue Stock
$app->get('/catalogue-stock', \src\controller\page\CatalogueStockController::class . ':index')
    ->setName('catalogue-stock.index');
$app->get('/catalogue-stock/lp-{id:[0-9]+}-{lp:[0-9]*}[/{nom:.*}]', \src\controller\page\CatalogueStockController::class . ':show')
    ->setName('catalogue-stock.show')
    ->add(new \src\middleware\LPExistsMiddleware($container));
$app->get('/catalogue-stock/ga-{lp:[0-9]+}-{ga:[0-9]+}[/{nom:.*}]', \src\controller\page\CatalogueStockController::class . ':showGA')
    ->setName('catalogue-stock-GA.show')
    ->add(new \src\middleware\GAExistsMiddleware($container)); 
$app->get('/catalogue-stock/gp-{tn:[0-9]+}-{lp:[0-9]+}-{ga:[0-9]+}-{gp:[0-9]+}[/{nom:.*}]', \src\controller\page\CatalogueStockController::class . ':showGP')
    ->setName('catalogue-stock-GP.show')
    ->add(new \src\middleware\GPstockExistsMiddleware($container));


//Recherche
$app->get('/recherche', \src\controller\page\RechercheController::class . ':index')
    ->setName('recherche.index');
$app->post('/recherche', \src\controller\page\RechercheController::class . ':index')
    ->setName('recherche.index');

//Produits
$app->get('/produits', \src\controller\page\LigneProduitController::class . ':index')
    ->setName('ligne-produit.index');
    
$app->get('/produits/lp-{id:[0-9]+}-{lp:[0-9]*}[/{nom:.*}]', \src\controller\page\LigneProduitController::class . ':show')
    ->setName('ligne-produit.show')
    ->add(new \src\middleware\LPExistsMiddleware($container));
$app->get('/produits/ga-{lp:[0-9]+}-{ga:[0-9]+}[/{nom:.*}]', \src\controller\page\GroupeArticleController::class . ':show')
    ->setName('groupe-article.show')
    ->add(new \src\middleware\GAExistsMiddleware($container));
$app->get('/produits/gp-{lp:[0-9]+}-{ga:[0-9]+}-{gp:[0-9]+}[/{nom:.*}]', \src\controller\page\GroupeProduitController::class . ':show')
    ->setName('groupe-produit.show')
    ->add(new \src\middleware\GPExistsMiddleware($container));


//Liens de produits
$app->get('/lienProduits', \src\controller\page\LienController::class . ':index')
->setName('lien-produits.index');

//Legal
$app->get('/legal', \src\controller\page\LegalController::class . ':index')
    ->setName('legal.index');


/* ROUTES PAGES ADMINISTRATION */

//Routes pages de connexion et deconnexion:
$app->get('/admin', \src\controller\page\admin\AdminHomeController::class . ':index')
    ->setName('admin/home.index')
    ->add(new AdminNotLoggedInMiddleware($container));
$app->get('/admin/', \src\controller\page\admin\AdminHomeController::class . ':index')
    ->setName('admin/home.index')
    ->add(new AdminNotLoggedInMiddleware($container));
$app->get('/admin/connexion', \src\controller\page\admin\AdminConnexionController::class . ':show')
    ->setName('admin/connexion.show')
    ->add(new AdminAlreadyLoggedInMiddleware($container));
$app->post('/admin/connexion', \src\controller\page\admin\AdminConnexionController::class . ':connect')
    ->setName('admin/connexion.connect')
    ->add(new AdminAlreadyLoggedInMiddleware($container));
$app->get('/admin/deconnexion', \src\controller\page\admin\AdminDeconnexionController::class . ':disconnect')
    ->setName('admin/deconnexion.disconnect')
    ->add(new AdminNotLoggedInMiddleware($container));

//Routes pages de gestion des LP:
$app->get('/admin/lp', \src\controller\page\admin\AdminLPController::class . ':index')
    ->setName('admin/lp.index')
    ->add(new AdminNotLoggedInMiddleware($container))
    ->add(new \src\middleware\AdminAuthorizedMiddleware($container));
$app->get('/admin/lp/create', \src\controller\page\admin\AdminLPController::class . ':create')
    ->setName('admin/lp.create')
    ->add(new AdminNotLoggedInMiddleware($container));
$app->get('/admin/lp/order', \src\controller\page\admin\AdminLPController::class . ':order')
    ->setName('admin/lp.order')
    ->add(new AdminNotLoggedInMiddleware($container))
    ->add(new \src\middleware\AdminAuthorizedMiddleware($container));
$app->post('/admin/lp/order', \src\controller\page\admin\AdminLPController::class . ':orderWebService')
    ->setName('admin/lp.order')
    ->add(new AdminNotLoggedInMiddleware($container))
    ->add(new \src\middleware\AdminAuthorizedMiddleware($container));
$app->post('/admin/lp/create', \src\controller\page\admin\AdminLPController::class . ':store')
    ->setName('admin/lp.create')
    ->add(new AdminNotLoggedInMiddleware($container))
    ->add(new \src\middleware\AdminAuthorizedMiddleware($container));
$app->get('/admin/lp/{id:[0-9]+}', \src\controller\page\admin\AdminLPController::class . ':edit')
    ->setName('admin/lp.edit')
    ->add(new AdminNotLoggedInMiddleware($container))
    ->add(new \src\middleware\AdminAuthorizedMiddleware($container));
$app->put('/admin/lp/{id:[0-9]+}', \src\controller\page\admin\AdminLPController::class . ':update')
    ->setName('admin/lp.edit')
    ->add(new AdminNotLoggedInMiddleware($container))
    ->add(new \src\middleware\AdminAuthorizedMiddleware($container));
$app->delete('/admin/lp/{id:[0-9]+}', \src\controller\page\admin\AdminLPController::class . ':delete')
    ->setName('admin/lp.delete')
    ->add(new AdminNotLoggedInMiddleware($container))
    ->add(new \src\middleware\AdminAuthorizedMiddleware($container));

//Routes pages de gestion des GA:
$app->get('/admin/ga', \src\controller\page\admin\AdminGAController::class . ':index')
    ->setName('admin/ga.index')
    ->add(new AdminNotLoggedInMiddleware($container))
    ->add(new \src\middleware\AdminAuthorizedMiddleware($container));
$app->get('/admin/ga/create', \src\controller\page\admin\AdminGAController::class . ':create')
    ->setName('admin/ga.create')
    ->add(new AdminNotLoggedInMiddleware($container))
    ->add(new \src\middleware\AdminAuthorizedMiddleware($container));
$app->post('/admin/ga/create', \src\controller\page\admin\AdminGAController::class . ':store')
    ->setName('admin/ga.create')
    ->add(new AdminNotLoggedInMiddleware($container))
    ->add(new \src\middleware\AdminAuthorizedMiddleware($container));
$app->delete('/admin/ga/{lp:[0-9]+}-{ga:[0-9]+}', \src\controller\page\admin\AdminGAController::class . ':delete')
    ->setName('admin/ga.delete')
    ->add(new AdminNotLoggedInMiddleware($container))
    ->add(new \src\middleware\AdminAuthorizedMiddleware($container));
$app->delete('/admin/ga/{lp:[0-9]+}-{ga:[0-9]+}-save', \src\controller\page\admin\AdminGAController::class . ':deleteSave')
    ->setName('admin/ga.deleteSave')
    ->add(new AdminNotLoggedInMiddleware($container))
    ->add(new \src\middleware\AdminAuthorizedMiddleware($container));
$app->get('/admin/ga/{lp:[0-9]+}-{ga:[0-9]+}', \src\controller\page\admin\AdminGAController::class . ':edit')
    ->setName('admin/ga.edit')
    ->add(new AdminNotLoggedInMiddleware($container))
    ->add(new \src\middleware\AdminAuthorizedMiddleware($container));
$app->put('/admin/ga/{lp:[0-9]+}-{ga:[0-9]+}', \src\controller\page\admin\AdminGAController::class . ':update')
    ->setName('admin/ga.edit')
    ->add(new AdminNotLoggedInMiddleware($container))
    ->add(new \src\middleware\AdminAuthorizedMiddleware($container));
$app->post('/admin/ga/index', \src\controller\page\admin\AdminGAController::class . ':indexByLpWebService')
    ->setName('admin/ga.index')
    ->add(new AdminNotLoggedInMiddleware($container))
    ->add(new \src\middleware\AdminAuthorizedMiddleware($container));

//Routes pages de gestion des GP:
$app->get('/admin/gp', \src\controller\page\admin\AdminGPController::class . ':index')
    ->setName('admin/gp.index')
    ->add(new AdminNotLoggedInMiddleware($container))
    ->add(new \src\middleware\AdminAuthorizedMiddleware($container));
$app->get('/admin/gp/create', \src\controller\page\admin\AdminGPController::class . ':create')
    ->setName('admin/gp.create')
    ->add(new AdminNotLoggedInMiddleware($container))
    ->add(new \src\middleware\AdminAuthorizedMiddleware($container));
$app->post('/admin/gp/create', \src\controller\page\admin\AdminGPController::class . ':store')
    ->setName('admin/gp.create')
    ->add(new AdminNotLoggedInMiddleware($container))
    ->add(new \src\middleware\AdminAuthorizedMiddleware($container));
$app->delete('/admin/gp/{lp:[0-9]+}-{ga:[0-9]+}-{gp:[0-9]+}', \src\controller\page\admin\AdminGPController::class . ':delete')
    ->setName('admin/gp.delete')
    ->add(new AdminNotLoggedInMiddleware($container))
    ->add(new \src\middleware\AdminAuthorizedMiddleware($container));
$app->get('/admin/gp/{lp:[0-9]+}-{ga:[0-9]+}-{gp:[0-9]+}', \src\controller\page\admin\AdminGPController::class . ':edit')
    ->setName('admin/gp.edit')
    ->add(new AdminNotLoggedInMiddleware($container))
    ->add(new \src\middleware\AdminAuthorizedMiddleware($container));
$app->put('/admin/gp/{lp:[0-9]+}-{ga:[0-9]+}-{gp:[0-9]+}', \src\controller\page\admin\AdminGPController::class . ':update')
    ->setName('admin/gp.edit')
    ->add(new AdminNotLoggedInMiddleware($container))
    ->add(new \src\middleware\AdminAuthorizedMiddleware($container));
$app->delete('/admin/gp/{lp:[0-9]+}-{ga:[0-9]+}-{gp:[0-9]+}-save', \src\controller\page\admin\AdminGPController::class . ':deleteSave')
    ->setName('admin/gp.deleteSave')
    ->add(new AdminNotLoggedInMiddleware($container))
    ->add(new \src\middleware\AdminAuthorizedMiddleware($container));
$app->post('/admin/gp/index', \src\controller\page\admin\AdminGPController::class . ':indexByGaWebService')
    ->setName('admin/gp.index')
    ->add(new AdminNotLoggedInMiddleware($container))
    ->add(new \src\middleware\AdminAuthorizedMiddleware($container));

//Routes pages de gestion des service
$app->get('/admin/service', \src\controller\page\admin\AdminServiceController::class . ':index')
    ->setName('admin/service.index')
    ->add(new AdminNotLoggedInMiddleware($container))
    ->add(new \src\middleware\AdminAuthorizedMiddleware($container));
$app->get('/admin/service/create', \src\controller\page\admin\AdminServiceController::class . ':create')
    ->setName('admin/service.create')
    ->add(new AdminNotLoggedInMiddleware($container))
    ->add(new \src\middleware\AdminAuthorizedMiddleware($container));
$app->post('/admin/service/create', \src\controller\page\admin\AdminServiceController::class . ':store')
    ->setName('admin/service.create')
    ->add(new AdminNotLoggedInMiddleware($container))
    ->add(new \src\middleware\AdminAuthorizedMiddleware($container));
$app->get('/admin/service/{id:[0-9]+}', \src\controller\page\admin\AdminServiceController::class . ':edit')
    ->setName('admin/service.edit')
    ->add(new AdminNotLoggedInMiddleware($container))
    ->add(new \src\middleware\AdminAuthorizedMiddleware($container));
$app->put('/admin/service/{id:[0-9]+}', \src\controller\page\admin\AdminServiceController::class . ':update')
    ->setName('admin/service.update')
    ->add(new AdminNotLoggedInMiddleware($container))
    ->add(new \src\middleware\AdminAuthorizedMiddleware($container));
$app->delete('/admin/service/{id:[0-9]+}', \src\controller\page\admin\AdminServiceController::class . ':delete')
    ->setName('admin/service.delete')
    ->add(new AdminNotLoggedInMiddleware($container))
    ->add(new \src\middleware\AdminAuthorizedMiddleware($container));
$app->get('/admin/service/order', \src\controller\page\admin\AdminServiceController::class . ':order')
    ->setName('admin/service.order')
    ->add(new AdminNotLoggedInMiddleware($container))
    ->add(new \src\middleware\AdminAuthorizedMiddleware($container));
$app->post('/admin/service/order', \src\controller\page\admin\AdminServiceController::class . ':orderWebService')
    ->setName('admin/service.order')
    ->add(new AdminNotLoggedInMiddleware($container))
    ->add(new \src\middleware\AdminAuthorizedMiddleware($container));

//Routes pages de gestion des pages
$app->get('/admin/page', \src\controller\page\admin\AdminPageController::class . ':index')
    ->setName('admin/page.index')
    ->add(new AdminNotLoggedInMiddleware($container))
    ->add(new \src\middleware\AdminAuthorizedMiddleware($container));
$app->get('/admin/page/create', \src\controller\page\admin\AdminPageController::class . ':create')
    ->setName('admin/page.create')
    ->add(new AdminNotLoggedInMiddleware($container))
    ->add(new \src\middleware\AdminAuthorizedMiddleware($container));
$app->post('/admin/page/create', \src\controller\page\admin\AdminPageController::class . ':store')
    ->setName('admin/page.create')
    ->add(new AdminNotLoggedInMiddleware($container))
    ->add(new \src\middleware\AdminAuthorizedMiddleware($container));
$app->get('/admin/page/{id:.*}', \src\controller\page\admin\AdminPageController::class . ':edit')
    ->setName('admin/page.edit')
    ->add(new AdminNotLoggedInMiddleware($container))
    ->add(new \src\middleware\AdminAuthorizedMiddleware($container));
$app->put('/admin/page/{id:.*}', \src\controller\page\admin\AdminPageController::class . ':update')
    ->setName('admin/page.update')
    ->add(new AdminNotLoggedInMiddleware($container))
    ->add(new \src\middleware\AdminAuthorizedMiddleware($container));
$app->delete('/admin/page/{id:.*}', \src\controller\page\admin\AdminPageController::class . ':delete')
    ->setName('admin/page.delete')
    ->add(new AdminNotLoggedInMiddleware($container))
    ->add(new \src\middleware\AdminAuthorizedMiddleware($container));

//Routes pages de gestion des mailings
$app->get('/admin/mail', \src\controller\page\admin\AdminMailController::class . ':index')
    ->setName('admin/mail.index')
    ->add(new AdminNotLoggedInMiddleware($container))
    ->add(new \src\middleware\AdminAuthorizedMiddleware($container));
$app->get('/admin/mail/create', \src\controller\page\admin\AdminMailController::class . ':create')
    ->setName('admin/mail.create')
    ->add(new AdminNotLoggedInMiddleware($container))
    ->add(new \src\middleware\AdminAuthorizedMiddleware($container));
$app->post('/admin/mail/create', \src\controller\page\admin\AdminMailController::class . ':store')
    ->setName('admin/mail.create')
    ->add(new AdminNotLoggedInMiddleware($container))
    ->add(new \src\middleware\AdminAuthorizedMiddleware($container));
$app->get('/admin/mail/{id:.*}', \src\controller\page\admin\AdminMailController::class . ':edit')
    ->setName('admin/mail.edit')
    ->add(new AdminNotLoggedInMiddleware($container))
    ->add(new \src\middleware\AdminAuthorizedMiddleware($container));
$app->put('/admin/mail/{id:.*}', \src\controller\page\admin\AdminMailController::class . ':update')
    ->setName('admin/mail.update')
    ->add(new AdminNotLoggedInMiddleware($container))
    ->add(new \src\middleware\AdminAuthorizedMiddleware($container));
$app->delete('/admin/mail/{id:.*}', \src\controller\page\admin\AdminMailController::class . ':delete')
    ->setName('admin/mail.delete')
    ->add(new AdminNotLoggedInMiddleware($container))
    ->add(new \src\middleware\AdminAuthorizedMiddleware($container));


//Routes pages de gestion des liens
$app->get('/admin/lien', \src\controller\page\admin\AdminLienController::class . ':index')
    ->setName('admin/lien.index')
    ->add(new AdminNotLoggedInMiddleware($container))
    ->add(new \src\middleware\AdminAuthorizedMiddleware($container));
$app->get('/admin/lien/create', \src\controller\page\admin\AdminLienController::class . ':create')
    ->setName('admin/lien.create')
    ->add(new AdminNotLoggedInMiddleware($container))
    ->add(new \src\middleware\AdminAuthorizedMiddleware($container));
$app->post('/admin/lien/create', \src\controller\page\admin\AdminLienController::class . ':store')
    ->setName('admin/lien.create')
    ->add(new AdminNotLoggedInMiddleware($container))
    ->add(new \src\middleware\AdminAuthorizedMiddleware($container));

$app->get('/admin/lien/{id:.*}', \src\controller\page\admin\AdminLienController::class . ':edit')
    ->setName('admin/lien.edit')
    ->add(new AdminNotLoggedInMiddleware($container))
    ->add(new \src\middleware\AdminAuthorizedMiddleware($container));
$app->put('/admin/lien/{id:.*}', \src\controller\page\admin\AdminlienController::class . ':update')
    ->setName('admin/lien.update')
    ->add(new AdminNotLoggedInMiddleware($container))
    ->add(new \src\middleware\AdminAuthorizedMiddleware($container));
$app->delete('/admin/lien/{id:.*}', \src\controller\page\admin\AdminLienController::class . ':delete')
    ->setName('admin/lien.delete')
    ->add(new AdminNotLoggedInMiddleware($container))
    ->add(new \src\middleware\AdminAuthorizedMiddleware($container));
    

//Routes pages files manager
$app->get('/admin/filemanager', \src\controller\page\admin\AdminFileManagerController::class . ':index')
    ->setName('admin/filemanager.index')
    ->add(new AdminNotLoggedInMiddleware($container))
    ->add(new \src\middleware\AdminAuthorizedMiddleware($container));
$app->get('/admin/filemanager/connector', \src\controller\page\admin\AdminFileManagerController::class . ':connector')
    ->setName('admin/filemanager.connector')
    ->add(new AdminNotLoggedInMiddleware($container))
    ->add(new \src\middleware\AdminAuthorizedMiddleware($container));
$app->post('/admin/filemanager/connector', \src\controller\page\admin\AdminFileManagerController::class . ':connector')
    ->setName('admin/filemanager.connector')
    ->add(new AdminNotLoggedInMiddleware($container))
    ->add(new \src\middleware\AdminAuthorizedMiddleware($container));
$app->get('/admin/filemanager/show', \src\controller\page\admin\AdminFileManagerController::class . ':show')
    ->setName('admin/filemanager.show')
    ->add(new AdminNotLoggedInMiddleware($container))
    ->add(new \src\middleware\AdminAuthorizedMiddleware($container));

//Routes pages de gestion des commerciaux
$app->get('/admin/commercial', \src\controller\page\admin\AdminCommercialController::class . ':index')
    ->setName('admin/commercial.index')
    ->add(new AdminNotLoggedInMiddleware($container))
    ->add(new \src\middleware\AdminAuthorizedMiddleware($container));
$app->get('/admin/commercial/create', \src\controller\page\admin\AdminCommercialController::class . ':create')
    ->setName('admin/commercial.create')
    ->add(new AdminNotLoggedInMiddleware($container))
    ->add(new \src\middleware\AdminAuthorizedMiddleware($container));
$app->post('/admin/commercial/create', \src\controller\page\admin\AdminCommercialController::class . ':store')
    ->setName('admin/commercial.create')
    ->add(new AdminNotLoggedInMiddleware($container))
    ->add(new \src\middleware\AdminAuthorizedMiddleware($container));
$app->get('/admin/commercial/{id:[0-9]+}', \src\controller\page\admin\AdminCommercialController::class . ':edit')
    ->setName('admin/commercial.edit')
    ->add(new AdminNotLoggedInMiddleware($container))
    ->add(new \src\middleware\AdminAuthorizedMiddleware($container));
$app->put('/admin/commercial/{id:[0-9]+}', \src\controller\page\admin\AdminCommercialController::class . ':update')
    ->setName('admin/commercial.update')
    ->add(new AdminNotLoggedInMiddleware($container))
    ->add(new \src\middleware\AdminAuthorizedMiddleware($container));
$app->delete('/admin/commercial/{id:[0-9]+}', \src\controller\page\admin\AdminCommercialController::class . ':delete')
    ->setName('admin/commercial.delete')
    ->add(new AdminNotLoggedInMiddleware($container))
    ->add(new \src\middleware\AdminAuthorizedMiddleware($container));
$app->get('/admin/commercial/departement', \src\controller\page\admin\AdminCommercialController::class . ':departement')
    ->setName('admin/commercial.departement')
    ->add(new AdminNotLoggedInMiddleware($container))
    ->add(new \src\middleware\AdminAuthorizedMiddleware($container));
$app->get('/admin/commercial/departement/{id:[0-9]+}-{fonction}', \src\controller\page\admin\AdminCommercialController::class . ':editDepartement')
    ->setName('admin/commercial.editDepartement')
    ->add(new AdminNotLoggedInMiddleware($container))
    ->add(new \src\middleware\AdminAuthorizedMiddleware($container));
$app->put('/admin/commercial/departement', \src\controller\page\admin\AdminCommercialController::class . ':updateDepartement')
    ->setName('admin/commercial.updateDepartement')
    ->add(new AdminNotLoggedInMiddleware($container))
    ->add(new \src\middleware\AdminAuthorizedMiddleware($container));


//Routes pages de gestion des admins
$app->get('/admin/admin', \src\controller\page\admin\AdminAdminController::class . ':index')
    ->setName('admin/admin.index')
    ->add(new AdminNotLoggedInMiddleware($container))
    ->add(new \src\middleware\AdminAuthorizedMiddleware($container))
    ->add(new \src\middleware\AdminAuthorizedMiddleware($container));
$app->get('/admin/admin/create', \src\controller\page\admin\AdminAdminController::class . ':create')
    ->setName('admin/admin.create')
    ->add(new AdminNotLoggedInMiddleware($container))
    ->add(new \src\middleware\AdminAuthorizedMiddleware($container));
$app->post('/admin/admin/create', \src\controller\page\admin\AdminAdminController::class . ':store')
    ->setName('admin/admin.create')
    ->add(new AdminNotLoggedInMiddleware($container))
    ->add(new \src\middleware\AdminAuthorizedMiddleware($container));
$app->get('/admin/admin/{id}', \src\controller\page\admin\AdminAdminController::class . ':edit')
    ->setName('admin/admin.edit')
    ->add(new AdminNotLoggedInMiddleware($container))
    ->add(new \src\middleware\AdminAuthorizedMiddleware($container));
$app->put('/admin/admin/{id}', \src\controller\page\admin\AdminAdminController::class . ':update')
    ->setName('admin/admin.update')
    ->add(new AdminNotLoggedInMiddleware($container))
    ->add(new \src\middleware\AdminAuthorizedMiddleware($container));
$app->delete('/admin/admin{id}', \src\controller\page\admin\AdminAdminController::class . ':delete')
    ->setName('admin/admin.delete')
    ->add(new AdminNotLoggedInMiddleware($container))
    ->add(new \src\middleware\AdminAuthorizedMiddleware($container));

//Routes pages de gestion des actualites
$app->get('/admin/actualite', \src\controller\page\admin\AdminActualiteController::class . ':index')
    ->setName('admin/actualite.index')
    ->add(new AdminNotLoggedInMiddleware($container))
    ->add(new \src\middleware\AdminAuthorizedMiddleware($container));
$app->get('/admin/actualite/create', \src\controller\page\admin\AdminActualiteController::class . ':create')
    ->setName('admin/actualite.create')
    ->add(new AdminNotLoggedInMiddleware($container))
    ->add(new \src\middleware\AdminAuthorizedMiddleware($container));
$app->post('/admin/actualite/create', \src\controller\page\admin\AdminActualiteController::class . ':store')
    ->setName('admin/actualite.create')
    ->add(new AdminNotLoggedInMiddleware($container))
    ->add(new \src\middleware\AdminAuthorizedMiddleware($container));
$app->get('/admin/actualite/{id}', \src\controller\page\admin\AdminActualiteController::class . ':edit')
    ->setName('admin/actualite.edit')
    ->add(new AdminNotLoggedInMiddleware($container))
    ->add(new \src\middleware\AdminAuthorizedMiddleware($container));
$app->put('/admin/actualite/{id}', \src\controller\page\admin\AdminActualiteController::class . ':update')
    ->setName('admin/actualite.update')
    ->add(new AdminNotLoggedInMiddleware($container))
    ->add(new \src\middleware\AdminAuthorizedMiddleware($container));
$app->delete('/admin/actualite/{id}', \src\controller\page\admin\AdminActualiteController::class . ':delete')
    ->setName('admin/actualite.delete')
    ->add(new AdminNotLoggedInMiddleware($container))
    ->add(new \src\middleware\AdminAuthorizedMiddleware($container));

//Routes pages de gestion des catalogue-standards
$app->get('/admin/catalogue-standard', \src\controller\page\admin\AdminCatalogueStandardController::class . ':index')
    ->setName('admin/catalogue-standard.index')
    ->add(new AdminNotLoggedInMiddleware($container))
    ->add(new \src\middleware\AdminAuthorizedMiddleware($container));
$app->get('/admin/catalogue-standard/create', \src\controller\page\admin\AdminCatalogueStandardController::class . ':create')
    ->setName('admin/catalogue-standard.create')
    ->add(new AdminNotLoggedInMiddleware($container))
    ->add(new \src\middleware\AdminAuthorizedMiddleware($container));
$app->post('/admin/catalogue-standard/create', \src\controller\page\admin\AdminCatalogueStandardController::class . ':store')
    ->setName('admin/catalogue-standard.create')
    ->add(new AdminNotLoggedInMiddleware($container))
    ->add(new \src\middleware\AdminAuthorizedMiddleware($container));
$app->get('/admin/catalogue-standard/{id}', \src\controller\page\admin\AdminCatalogueStandardController::class . ':edit')
    ->setName('admin/catalogue-standard.edit')
    ->add(new AdminNotLoggedInMiddleware($container))
    ->add(new \src\middleware\AdminAuthorizedMiddleware($container));
$app->put('/admin/catalogue-standard/{id}', \src\controller\page\admin\AdminCatalogueStandardController::class . ':update')
    ->setName('admin/catalogue-standard.update')
    ->add(new AdminNotLoggedInMiddleware($container))
    ->add(new \src\middleware\AdminAuthorizedMiddleware($container));
$app->delete('/admin/catalogue-standard/{id}', \src\controller\page\admin\AdminCatalogueStandardController::class . ':delete')
    ->setName('admin/catalogue-standard.delete')
    ->add(new AdminNotLoggedInMiddleware($container))
    ->add(new \src\middleware\AdminAuthorizedMiddleware($container));

// Routes pages gestion catalogue stock
$app->get('/admin/catalogue-stock', \src\controller\page\admin\AdminCatalogueStockController::class . ':index')
    ->setName('admin/catalogue-stock.index')
    ->add(new AdminNotLoggedInMiddleware($container))
    ->add(new \src\middleware\AdminAuthorizedMiddleware($container));

    
$app->get('/admin/catalogue-stock/changePDF', \src\controller\page\admin\AdminCatalogueStockController::class . ':changePDF')
    ->setName('admin/catalogue-stock.changePDF')
    ->add(new AdminNotLoggedInMiddleware($container))
    ->add(new \src\middleware\AdminAuthorizedMiddleware($container));
$app->post('/admin/catalogue-stock/changePDF', \src\controller\page\admin\AdminCatalogueStockController::class . ':storechangePDF')
    ->setName('admin/catalogue-stock.changePDF')
    ->add(new AdminNotLoggedInMiddleware($container))
    ->add(new \src\middleware\AdminAuthorizedMiddleware($container));  

    
$app->get('/admin/catalogue-stock/create', \src\controller\page\admin\AdminCatalogueStockController::class . ':create')
    ->setName('admin/catalogue-stock.create')
    ->add(new AdminNotLoggedInMiddleware($container))
    ->add(new \src\middleware\AdminAuthorizedMiddleware($container));
$app->post('/admin/catalogue-stock/create', \src\controller\page\admin\AdminCatalogueStockController::class . ':store')
    ->setName('admin/catalogue-stock.create')
    ->add(new AdminNotLoggedInMiddleware($container))
    ->add(new \src\middleware\AdminAuthorizedMiddleware($container));
$app->delete('/admin/catalogue-stock/{lp:[0-9]+}-{ga:[0-9]+}-{gp:[0-9]+}', \src\controller\page\admin\AdminCatalogueStockController::class . ':delete')
    ->setName('admin/catalogue-stock.delete')
    ->add(new AdminNotLoggedInMiddleware($container))
    ->add(new \src\middleware\AdminAuthorizedMiddleware($container));
$app->get('/admin/catalogue-stock/{lp:[0-9]+}-{ga:[0-9]+}-{gp:[0-9]+}', \src\controller\page\admin\AdminCatalogueStockController::class . ':edit')
    ->setName('admin/catalogue-stock.edit')
    ->add(new AdminNotLoggedInMiddleware($container))
    ->add(new \src\middleware\AdminAuthorizedMiddleware($container));
$app->put('/admin/catalogue-stock/{lp:[0-9]+}-{ga:[0-9]+}-{gp:[0-9]+}', \src\controller\page\admin\AdminCatalogueStockController::class . ':update')
    ->setName('admin/catalogue-stock.edit')
    ->add(new AdminNotLoggedInMiddleware($container))
    ->add(new \src\middleware\AdminAuthorizedMiddleware($container));
$app->get('/admin/catalogue-stock/createbytn', \src\controller\page\admin\AdminCatalogueStockController::class . ':createbytn')
    ->setName('admin/catalogue-stock.createbytn')
    ->add(new AdminNotLoggedInMiddleware($container))
    ->add(new \src\middleware\AdminAuthorizedMiddleware($container));
$app->post('/admin/catalogue-stock/createbytn', \src\controller\page\admin\AdminCatalogueStockController::class . ':storebytn')
    ->setName('admin/catalogue-stock.createbytn')
    ->add(new AdminNotLoggedInMiddleware($container))
    ->add(new \src\middleware\AdminAuthorizedMiddleware($container));    

//Routes pages autre getsion
$app->get('/admin/robots-txt', \src\controller\page\admin\AdminRobotsTxtController::class . ':index')
    ->setName('admin/robots-txt.index')
    ->add(new AdminNotLoggedInMiddleware($container))
    ->add(new \src\middleware\AdminAuthorizedMiddleware($container));
$app->get('/admin/robots-txt/download', \src\controller\page\admin\AdminRobotsTxtController::class . ':download')
    ->setName('admin/robots-txt.download')
    ->add(new AdminNotLoggedInMiddleware($container))
    ->add(new \src\middleware\AdminAuthorizedMiddleware($container));
$app->get('/admin/robots-txt/replace', \src\controller\page\admin\AdminRobotsTxtController::class . ':replace')
    ->setName('admin/robots-txt.replace')
    ->add(new AdminNotLoggedInMiddleware($container))
    ->add(new \src\middleware\AdminAuthorizedMiddleware($container));

$app->get('/admin/sitemap-xml', \src\controller\page\admin\AdminSitemapXmlController::class . ':index')
    ->setName('admin/sitemap-xml.index')
    ->add(new AdminNotLoggedInMiddleware($container))
    ->add(new \src\middleware\AdminAuthorizedMiddleware($container));
$app->get('/admin/sitemap-xml/download', \src\controller\page\admin\AdminSitemapXmlController::class . ':download')
    ->setName('admin/sitemap-xml.download')
    ->add(new AdminNotLoggedInMiddleware($container))
    ->add(new \src\middleware\AdminAuthorizedMiddleware($container));
$app->get('/admin/sitemap-xml/replace', \src\controller\page\admin\AdminSitemapXmlController::class . ':replace')
    ->setName('admin/sitemap-xml.replace')
    ->add(new AdminNotLoggedInMiddleware($container))
    ->add(new \src\middleware\AdminAuthorizedMiddleware($container));




/* ROUTES PAGES VISITEURS DE L'ANCIEN SITE */
$app->get('/produits.htm', function ($request, $response, $args) use($app) {
    return $response->withStatus(302)->withHeader('Location', $app->getContainer()->router->pathFor('ligne-produit.index'));
});
$app->get('/{nom}-{lp:[0-9]+}-{id:[0-9]+}-lp.htm', function ($request, $response, $args) use($app) {
    return $response->withStatus(302)->withHeader('Location', $app->getContainer()->router->pathFor('ligne-produit.show',$args));
});
$app->get('/{nom}-0-{id:[0-9]+}-lt.htm', function ($request, $response, $args) use($app) {
    $args['lp'] = null;
    return $response->withStatus(302)->withHeader('Location', $app->getContainer()->router->pathFor('ligne-produit.show',$args));
});

$app->get('/{nom}-{lp:[0-9]+}-{ga:[0-9]+}-ga.htm', function ($request, $response, $args) use($app) {
    return $response->withStatus(302)->withHeader('Location', $app->getContainer()->router->pathFor('groupe-article.show',$args));
});
$app->get('/{nom}-{id:[0-9]+}-{ga:[0-9]+}-gat.htm', function ($request, $response, $args) use($app) {
    $args['lp'] = null;
    return $response->withStatus(302)->withHeader('Location', $app->getContainer()->router->pathFor('ligne-produit.show',$args));
});
$app->get('/{nom}-{lp:[0-9]+}-{ga:[0-9]+}-{gp:[0-9]+}-gp{id}.htm', function ($request, $response, $args) use($app) {
    return $response->withStatus(302)->withHeader('Location', $app->getContainer()->router->pathFor('groupe-produit.show',$args));
});
$app->get('/services.htm', function ($request, $response, $args) use($app) {
    return $response->withStatus(302)->withHeader('Location', $app->getContainer()->router->pathFor('service.index'));
});
$app->get('/{arg}-ls.htm', function ($request, $response, $args) use($app) {
    return $response->withStatus(302)->withHeader('Location', $app->getContainer()->router->pathFor('service.index'));
});
$app->get('/news.htm', function ($request, $response, $args) use($app) {
    return $response->withStatus(302)->withHeader('Location', $app->getContainer()->router->pathFor('actualite.index'));
});
$app->get('/entreprise.htm', function ($request, $response, $args) use($app) {
    return $response->withStatus(302)->withHeader('Location', $app->getContainer()->router->pathFor('entreprise.index'));
});
$app->get('/contact.htm', function ($request, $response, $args) use($app) {
    return $response->withStatus(302)->withHeader('Location', $app->getContainer()->router->pathFor('contact.index'));
});
$app->get('/contact-{arg1}-{arg2}-{dept}-{arg3}-{arg4}.htm', function ($request, $response, $args) use($app) {
    return $response->withStatus(302)->withHeader('Location', $app->getContainer()->router->pathFor('contact.index'));
});
$app->get('/catalogue-standard.htm', function ($request, $response, $args) use($app) {
    return $response->withStatus(302)->withHeader('Location', $app->getContainer()->router->pathFor('catalogue-standard.index'));
});
$app->get('/catalogue-standard-{lp:[0-9]+}-lp.htm', function ($request, $response, $args) use($app) {
    return $response->withStatus(302)->withHeader('Location', $app->getContainer()->router->pathFor('catalogue-standard.index'));
});
$app->get('/catalogue-standard-{lp:[0-9]+}-{ga:[0-9]+}-{gp:[0-9]+}-gp.htm', function ($request, $response, $args) use($app) {
    return $response->withStatus(302)->withHeader('Location', $app->getContainer()->router->pathFor('catalogue-standard.showGP', $args));
});
$app->get('/recherche.php', function ($request, $response, $args) use($app) {
    return $response->withStatus(302)->withHeader('Location', $app->getContainer()->router->pathFor('recherche.index'));
});
$app->get('/legal.htm', function ($request, $response, $args) use($app) {
    return $response->withStatus(302)->withHeader('Location', $app->getContainer()->router->pathFor('legal.index'));
});

// PARTIES
$app->get('/parties', \src\controller\page\PartieController::class . ':all')
    ->setName('partie.index');

//Test Ali
$app->get('/ali', \src\controller\page\AliController::class . ':index');


$app->run();
