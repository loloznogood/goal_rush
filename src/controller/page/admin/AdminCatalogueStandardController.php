<?php

namespace src\controller\page\admin;

use Slim\Http\UploadedFile;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\RequestInterface;
use src\model\dao\DAOFactory;
use \src\controller\page\PageController;
use src\model\metier\CatalogueStandard;
use src\model\ToolModel;

class AdminCatalogueStandardController extends PageController
{
    const DROITS = 1;

    private $filAriane;
    /**
     * AdminLPController constructor.
     */
    public function __construct($container)
    {
        parent::__construct($container);
        $this->filAriane = [
            'Accueil' => $this->getContainer()->router->pathFor('accueil.index'),
            'Menu administrateur'=> $this->getContainer()->router->pathFor('admin/home.index'),
            'Gestion des catalogues standards'=> $this->getContainer()->router->pathFor('admin/catalogue-standard.index')

        ];
    }


    public function index(RequestInterface $request, ResponseInterface $response, array $args)
    {
        try {
            $daoFactory = new DAOFactory($this->getContainer()->db);
            $catalogueDAO = $daoFactory->getCatalogueStandardDAO();
            $cataloguesEntier = $catalogueDAO->findAllByType("entier");
            $cataloguesLp = $catalogueDAO->findAllByType("parLP");
            $cataloguesGp = $catalogueDAO->findAllByType("parGP");
            $dataForView = [
                'cataloguesEntier' => $cataloguesEntier,
                'cataloguesLp' => $cataloguesLp,
                'cataloguesGp' => $cataloguesGp,
                'filAriane' => $this->filAriane,
                'footer' => $this->getFooterData()
            ];

            $this->render($response, 'admin-catalogue-standard-index.html.twig', ['data' => $dataForView]);
        }
        catch(\Exception $e){
            echo $e->getMessage();
        }

    }

    public function create(RequestInterface $request, ResponseInterface $response, array $args)
    {
        try{
            $this->filAriane['Créer un nouveau catalogue standard'] = $this->getContainer()->router->pathFor('admin/catalogue-standard.create');
            $daoFactory = new DAOFactory($this->getContainer()->db);
            $lpDAO = $daoFactory->getLPDAO();
            $lps2 = $lpDAO->findAll();
            $lps = $lpDAO->findAllTrueLP();
            $tool = new ToolModel();
            $dataForView = [
                'fileMaxSize' => $tool->getMaxUploadedFileSizeBytes(),
                'filAriane' => $this->filAriane,
                'footer' => $this->getFooterData(),
                'lps'   => $lps,
                'lps2' => $lps2
            ];
            $this->render($response, 'admin-catalogue-standard-create.html.twig', ['data' => $dataForView]);

        }catch(\Exception $e){
            $tool = new ToolModel();
            $dataForView = [
                'fileMaxSize' => $tool->getMaxUploadedFileSizeBytes(),
                'error' => 'Un problème est survenu lors du traitement de cette page.',
                'filAriane' => $this->filAriane,
                'footer' => $this->getFooterData(),
                'lps'   => $lps,
                'lps2' => $lps2
            ];

            $this->render($response, 'admin-catalogue-standard-create.html.twig', ['data' => $dataForView]);
        }
    }

    public function store(RequestInterface $request, ResponseInterface $response, array $args)
    {
        try{
            $this->filAriane['Créer un nouveau catalogue standard'] = $this->getContainer()->router->pathFor('admin/catalogue-standard.create');
            $tool = new ToolModel();
            /* Recuperation des donnees du formulaire envoyé */
            $type = $request->getParsedBody()['type'];
            $intitule = $tool->urlFriendly($request->getParsedBody()['intitule']);
            $pdfActif = (isset($request->getParsedBody()['pdfActif']) && $request->getParsedBody()['pdfActif']=="on") ? true : false;
            $magActif = (isset($request->getParsedBody()['magActif']) && $request->getParsedBody()['magActif']=="on") ? true : false;
            $uploadedFiles = $request->getUploadedFiles();
            $lpNum =  $lpId = $gaNum = $gpNum = null;
            if(isset($request->getParsedBody()['lpId'])){
                $lpId = $request->getParsedBody()['lpId'];
            }
            if(isset($request->getParsedBody()['lp'])){
                $lpNum = $request->getParsedBody()['lp'];
                if(empty(trim($lpNum)) || intval($lpNum)==0){
                    throw new \Exception("Numéro de LP incorrect.");
                }
            }
            if(isset($request->getParsedBody()['ga'])){
                $gaNum = $request->getParsedBody()['ga'];
                if(empty(trim($gaNum)) || intval($gaNum)==0){
                    throw new \Exception("Numéro de GA incorrect.");
                }
            }
            if(isset($request->getParsedBody()['gp'])) {
                $gpNum = $request->getParsedBody()['gp'];
                if (empty(trim($gpNum)) || intval($gpNum) == 0) {
                    throw new \Exception("Numéro de GP incorrect.");
                }
            }

            /*
             * Verification que les donnees sont correctes.
             * Certaines donnees ont ete verifies ci-dessus pour des raisons d'efficacite.
             */
            if(empty(trim($type)) || $type == "-1" ){
                throw new \Exception("Type incorrect.");
            }
            if( ($type != CatalogueStandard::TYPE_ENTIER) && ($type != CatalogueStandard::TYPE_LP) && ($type != CatalogueStandard::TYPE_GP) ){
                throw new \Exception("Type incorrect.");
            }
            if(empty(trim($intitule)) ){
                throw new \Exception("Intitulé incorrect.");
            }
            if($type == CatalogueStandard::TYPE_ENTIER){
                $lpId = $lpNum = $gaNum = $gpNum = null;
            }
            if($type == "parLP"){
                $lpId = $request->getParsedBody()['lp'];
                if(empty(trim($lpId)) || $lpId == "-1" ){
                    throw new \Exception("Ligne de produit incorrecte.");
                }
                $lpNum = $gaNum = $gpNum = null;
            }
            if($type == "parGP"){
                $lpNum = $request->getParsedBody()['lp'];
                if(empty(trim($lpNum)) || $lpNum == "-1" ){
                    throw new \Exception("Ligne de produit incorrecte.");
                }

                $gaNum = $request->getParsedBody()['ga'];
                if(empty(trim($gaNum)) || $gaNum == "-1" ){
                    throw new \Exception("Groupe article incorrect.");
                }

                $gpNum = $request->getParsedBody()['gp'];
                if(empty(trim($gpNum)) || $gpNum == "-1" ){
                    throw new \Exception("Groupe produit incorrect.");
                }
                $lpId = null;
            }

            if(isset($uploadedFiles['fichierPDF'])){
                $pdfFile = $uploadedFiles['fichierPDF'];
                if ($pdfFile->getError() != UPLOAD_ERR_OK) {
                    throw new \Exception("Un problème est survenu lors de l'upload du fichier PDF. (Vérifiez que la taille du fichier ne dépasse pas ".$tool->getMaxUploadedFileSize()." Mo.)");
                }
                if($pdfFile->getClientMediaType() != "application/pdf"){
                    throw new \Exception("Le fichier n'est pas un fichier PDF.");
                }
            }
            else{
                throw new \Exception("Aucun fichier PDF.");
            }


            /* Creation du fichier pdf sur le serveur */
            $tmpFileName = $pdfFile->file;
            $fileName = $pdfFile->getClientFilename();
            $dossier = $type.'/';
            $uploadDirPdf = CatalogueStandard::DIR_PDF.$dossier.$fileName;
            if( strlen( $dossier ) <= 1 ){
                throw new \Exception( "Impossible de classer le nouveau fichier pdf: dossier indéterminé.");
            }
            if( ! move_uploaded_file( $tmpFileName, $tool->getSiteAbsolutePath().$uploadDirPdf) ){
                throw new \Exception("Impossible de déplacer le fichier uploadé à cet emplacement: ".$uploadDirPdf);
              }
            if( ! rename( $tool->getSiteAbsolutePath().$uploadDirPdf, $tool->getSiteAbsolutePath().CatalogueStandard::DIR_PDF.$dossier.$intitule.".pdf") ){
                throw new \Exception("Impossible de renomer le fichier uploadé.");
            }
            $fichierPdf = CatalogueStandard::DIR_PDF.$dossier.$intitule.".pdf";
            
            

            /* Creation du repertoire du magazine sur le serveur */
            $dossier = $type.'/';
            $uploadDirMag = CatalogueStandard::DIR_MAG.$dossier.$intitule;
            if( strlen( $dossier ) <= 1 ){
                throw new \Exception( "Impossible de classer le nouveau repertoire du magazine: dossier indéterminé.");
            }
            if( ! mkdir( $tool->getSiteAbsolutePath().$uploadDirMag ) ){
                throw new \Exception("Impossible de créer ce dossier: ".$uploadDirMag);
            }
            if( ! chmod( $tool->getSiteAbsolutePath().$uploadDirMag, 0777 ) ){
                throw new Exception("Impossible de donner les bons droits d'acces a ce dossier: ".$uploadDirMag);
            }
            $fichierMag = $uploadDirMag."/index.html";

            $daoFactory = new DAOFactory($this->getContainer()->db);
            $catalogueDAO = $daoFactory->getCatalogueStandardDAO();
            $catalogue = new CatalogueStandard(null, $lpId, $lpNum, $gaNum, $gpNum, $type, $intitule, date("Y-m-d H:i:s"), $fichierPdf, $fichierMag, $pdfActif, $magActif );
            $catalogueDAO->create($catalogue);
            return $this->redirect($response, "admin/catalogue-standard.index");

        }catch(\Exception $e){
            $daoFactory = new DAOFactory($this->getContainer()->db);
            $tool = new ToolModel();
            $lpDAO = $daoFactory->getLPDAO();
            $gaDAO = $daoFactory->getGADAO();
            $gpDAO = $daoFactory->getGPDAO();
            $lps2 = $lpDAO->findAll();
            $lps = $lpDAO->findAllTrueLP();
            //Les champs null seront completer apres
            $dataForView = [
                'fileMaxSize' => $tool->getMaxUploadedFileSizeBytes(),
                'error' => $e->getMessage(),
                'catalogue' => [
                    'lpId' => null,
                    'lp' => null,
                    'ga' => null,
                    'gp' => null,
                    'type' => $type,
                    'intitule' => $intitule,
                    'fichierPdf' => null,
                    'isActifPdf' => $pdfActif,
                    'isActifMag' => $magActif
                ],
                'lps' => $lps,
                'lps2' => $lps2,
                'gas' => null,
                'gps' => null,
                'filAriane' => $this->filAriane,
                'footer' => $this->getFooterData()
            ];
            if(isset($lpId)){
                $dataForView['catalogue']['lpId'] = $lpId;
            }
            if(isset($lpNum)){
                $dataForView['catalogue']['lp'] = $lpNum;
                $gas = $gaDAO->findByLp($lpNum);
                $dataForView['gas'] = $gas;
            }
            if(isset($lpNum) && isset($gaNum)){
                $dataForView['catalogue']['ga'] = $gaNum;
                $gps = $gpDAO->findByGa($lpNum, $gaNum);
                $dataForView['gps'] = $gps;
            }
            if(isset($gpNum)){
                $dataForView['catalogue']['gp'] = $gpNum;
            }

        

            $this->render($response, 'admin-catalogue-standard-create.html.twig', ['data' => $dataForView]);
        }
    }

    public function edit(RequestInterface $request, ResponseInterface $response, array $args)
    {
        try{
            $id = $args['id'];
            $this->filAriane['Modifier un catalogue standard'] = $this->getContainer()->router->pathFor('admin/catalogue-standard.edit', ['id'=>$id]);
            $daoFactory = new DAOFactory($this->getContainer()->db);
            $catalogueDAO = $daoFactory->getCatalogueStandardDAO();
            $catalogue = $catalogueDAO->find($id);

            $lpDAO = $daoFactory->getLPDAO();
            $gaDAO = $daoFactory->getGADAO();
            $gpDAO = $daoFactory->getGPDAO();
            $lps2 = $lpDAO->findAll();
            $lps = $lpDAO->findAllTrueLP();

            if(is_null($catalogue)){
                throw new \Exception("Catalogue Standard inconnu.");
            }
            $tool = new ToolModel();

            $dataForView = [
                'fileMaxSize' => $tool->getMaxUploadedFileSizeBytes(),
                'catalogue'    => $catalogue,
                'filAriane' => $this->filAriane,
                'footer' => $this->getFooterData(),
                'lps'   => $lps,
                'lps2' => $lps2
            ];

            if(!is_null($catalogue->getLp())){
                $dataForView['gas'] = $gaDAO->findByLp($catalogue->getLp());
            }
            if(!is_null($catalogue->getLp()) && !is_null($catalogue->getGa())){
                $dataForView['gps'] = $gpDAO->findByGa($catalogue->getLp(), $catalogue->getGa());
            }

            $this->render($response, 'admin-catalogue-standard-edit.html.twig', ['data'  => $dataForView]);
        }catch (\Exception $e){
            $tool = new ToolModel();
            $dataForView = [
                'fileMaxSize' => $tool->getMaxUploadedFileSizeBytes(),
                'error' => $e->getMessage(),
                'filAriane' => $this->filAriane,
                'footer' => $this->getFooterData(),
                'lps'   => $lps,
                'lps2' => $lps2
            ];
            $this->render($response, 'admin-catalogue-standard-edit.html.twig', ['data'  => $dataForView]);
        }

    }

    public function update(RequestInterface $request, ResponseInterface $response, array $args)
    {
        try{
            $tool = new ToolModel();

            /* Recuperation des donnees du formulaire envoyé */
            $id2 = $request->getParsedBody()['id'];
            $id = $args['id'];
            $this->filAriane['Modifier un catalogue standard'] = $this->getContainer()->router->pathFor('admin/catalogue-standard.edit', ['id'=>$id]);
            $type = $request->getParsedBody()['type'];
            $intitule = $tool->urlFriendly($request->getParsedBody()['intitule']);
            $pdfActif = (isset($request->getParsedBody()['pdfActif']) && $request->getParsedBody()['pdfActif']=="on") ? true : false;
            $magActif = (isset($request->getParsedBody()['magActif']) && $request->getParsedBody()['magActif']=="on") ? true : false;
            $uploadedFiles = $request->getUploadedFiles();
            $fichierPdf = $request->getParsedBody()['fichierPdf'];
            $fichierMag = $request->getParsedBody()['fichierMag'];
            $dateMaj = $request->getParsedBody()['dateMaj'];
            $lpNum =  $lpId = $gaNum = $gpNum = null;
            if(isset($request->getParsedBody()['lpId'])){
                $lpId = $request->getParsedBody()['lpId'];
            }
            if(isset($request->getParsedBody()['lp'])){
                $lpNum = $request->getParsedBody()['lp'];
                if(empty(trim($lpNum)) || intval($lpNum)==0){
                    throw new \Exception("Numéro de LP incorrect.");
                }
            }
            if(isset($request->getParsedBody()['ga'])){
                $gaNum = $request->getParsedBody()['ga'];
                if(empty(trim($gaNum)) || intval($gaNum)==0){
                    throw new \Exception("Numéro de GA incorrect.");
                }
            }
            if(isset($request->getParsedBody()['gp'])) {
                $gpNum = $request->getParsedBody()['gp'];
                if (empty(trim($gpNum)) || intval($gpNum) == 0) {
                    throw new \Exception("Numéro de GP incorrect.");
                }
            }

            /*
             * Verification que les donnees sont correctes.
             * Certaines donnees ont ete verifies ci-dessus pour des raisons d'efficacite.
             */
            if($id != $id2){
                throw new \Exception("Identifiant incorrect.");
            }
            if(empty(trim($type)) || $type == "-1" ){
                throw new \Exception("Type incorrect.");
            }
            if( ($type != CatalogueStandard::TYPE_ENTIER) && ($type != CatalogueStandard::TYPE_LP) && ($type != CatalogueStandard::TYPE_GP) ){
                throw new \Exception("Type incorrect.");
            }
            if(empty(trim($intitule)) ){
                throw new \Exception("Intitulé incorrect.");
            }
            if($type == "parLP"){
                $lpId = $request->getParsedBody()['lp'];
                if(empty(trim($lpId)) || $lpId == "-1" ){
                    throw new \Exception("Ligne de produit incorrecte.");
                }
                $lpNum = $gaNum = $gpNum = null;

                $daoFactory = new DAOFactory($this->getContainer()->db);
                $lpDAO = $daoFactory->getLPDAO();
                $llp = $lpDAO->find($lpId);
                $lpNum = $llp->getLp();
            }
            if($type == "parGP"){
                $lpNum = $request->getParsedBody()['lp'];
                if(empty(trim($lpNum)) || $lpNum == "-1" ){
                    throw new \Exception("Ligne de produit incorrecte.");
                }

                $gaNum = $request->getParsedBody()['ga'];
                if(empty(trim($gaNum)) || $gaNum == "-1" ){
                    throw new \Exception("Groupe article incorrect.");
                }

                $gpNum = $request->getParsedBody()['gp'];
                if(empty(trim($gpNum)) || $gpNum == "-1" ){
                    throw new \Exception("Groupe produit incorrect.");
                }
                $lpId = null;
            }

            $daoFactory = new DAOFactory($this->getContainer()->db);
            $catalogueDAO = $daoFactory->getCatalogueStandardDAO();
            $catalogueOld = $catalogueDAO->find($id);

            

            if(isset($uploadedFiles['fichierPDF']) && !empty($uploadedFiles['fichierPDF']->file)){
                $pdfFile = $uploadedFiles['fichierPDF'];
                if ($pdfFile->getError() != UPLOAD_ERR_OK) {
                    throw new \Exception("Un problème est survenu lors de l'upload du fichier PDF. Vérifiez que vous avez sélctionné un fihcier.");
                }
                if($pdfFile->getClientMediaType() != "application/pdf"){
                    throw new \Exception("Le fichier n'est pas un fichier PDF.");
                }

                if( explode(' ',$tool->byte2Size($pdfFile->getSize()))[0] > explode(' ',$tool->getMaxUploadedFileSize())[0] ){
                    throw new \Exception("La taille du fichier dépasse la taille maximale d'upload du serveur (".$tool->getMaxUploadedFileSize()."). Contacez le service informatique afin que le fichier soit uploadé vie FTP. ");
                }


                /* Supression ancien fichier PDF */
                if( ! self::checkBeforeDelete( $tool->getSiteAbsolutePath().$catalogueOld->getFichierPdf() ) ){
                    throw new Exception("Impossible de supprimer l'ancien fichier: ".$catalogueOld->getFichierPdf() );
                }
                if( ! unlink( $tool->getSiteAbsolutePath().$catalogueOld->getFichierPdf() ) ){
                    throw new Exception("Impossible de supprimer l'ancien fichier: ".$catalogueOld->getFichierPdf() );
                }

                /* Creation du fichier pdf sur le serveur */
                $tmpFileName = $pdfFile->file;
                $fileName = $pdfFile->getClientFilename();
                $dossier = $type.'/';
                $uploadDirPdf = CatalogueStandard::DIR_PDF.$dossier.$fileName;
                if( strlen( $dossier ) <= 1 ){
                    throw new \Exception( "Impossible de classer le nouveau fichier pdf: dossier indéterminé.");
                }
                if( ! move_uploaded_file( $tmpFileName, $tool->getSiteAbsolutePath().$uploadDirPdf) ){
                    throw new \Exception("Impossible de déplacer le fichier uploadé à cet emplacement: ".$uploadDirPdf);
                }
                if( ! rename( $tool->getSiteAbsolutePath().$uploadDirPdf, $tool->getSiteAbsolutePath().CatalogueStandard::DIR_PDF.$dossier.$intitule.".pdf") ){
                    throw new \Exception("Impossible de renomer le fichier uploadé.");
                }
                $fichierPdf = CatalogueStandard::DIR_PDF.$dossier.$intitule.".pdf";
                $catalogueOld->setFichierPdf($fichierPdf);

                /* Supression ancien repertoire magazine */
                $oldDirMag = substr( trim( $catalogueOld->getFichierMag() ), 0, -10 ); //on enleve "index.html";
                if( ! self::checkBeforeDelete( $tool->getSiteAbsolutePath().$oldDirMag ) ){
                    throw new Exception("Impossible de supprimer l'ancien r&eacute;pertoire du magazine: ".$oldDirMag );
                }
                if( ! $tool->rrmdir( $tool->getSiteAbsolutePath().$oldDirMag ) ){
                    throw new Exception("Impossible de supprimer l'ancien r&eacute;pertoire du magazine: ".$oldDirMag );
                }

                /* Creation du repertoire du magazine sur le serveur */
                $dossier = $type.'/';
                $uploadDirMag = CatalogueStandard::DIR_MAG.$dossier.$intitule;
                if( strlen( $dossier ) <= 1 ){
                    throw new \Exception( "Impossible de classer le nouveau repertoire du magazine: dossier indéterminé.");
                }
                if( ! mkdir( $tool->getSiteAbsolutePath().$uploadDirMag ) ){
                    throw new \Exception("Impossible de créer ce dossier: ".$uploadDirMag);
                }
                if( ! chmod( $tool->getSiteAbsolutePath().$uploadDirMag, 0777 ) ){
                    throw new Exception("Impossible de donner les bons droits d'acces a ce dossier: ".$uploadDirMag);
                }
                $fichierMag = $uploadDirMag."/index.html";
                $catalogueOld->setFichierMag($fichierMag);

                $dateMaj = date("Y-m-d H:i:s");
                $catalogueOld->setDateMaj($dateMaj);

            }
            else{

                if( $intitule != $catalogueOld->getIntitule()){
                    /* renomage fichier pdf */
                    $dossier = $type.'/';
                    if( strlen( $dossier ) <= 1 ){
                        throw new \Exception( "Impossible de classer le nouveau fichier pdf: dossier indéterminé.");
                    }
                    if( ! rename( $tool->getSiteAbsolutePath().$catalogueOld->getFichierPdf(), $tool->getSiteAbsolutePath().CatalogueStandard::DIR_PDF.$dossier.$intitule.".pdf" ) ){
                        throw new \Exception( "Impossible de renomer ce fichier: ".$catalogueOld->getFichierPdf());
                    }
                    $fichierPdf = CatalogueStandard::DIR_PDF.$dossier.$intitule.".pdf";
                    $catalogueOld->setFichierPdf($fichierPdf);
                    /* renomage repertoire magazine */
                    $dossier = $type.'/';
                    $oldDirMag = substr( trim( $catalogueOld->getFichierMag()  ), 0, -10 );
                    $uploadDirMag = CatalogueStandard::DIR_MAG.$dossier.$intitule;
                    if( strlen( $dossier ) <= 1 ){
                        throw new \Exception( "Impossible de classer le nouveau fichier pdf: dossier indéterminé.");
                    }
                    if( ! rename( $tool->getSiteAbsolutePath().$oldDirMag , $tool->getSiteAbsolutePath().$uploadDirMag ) ){
                        throw new \Exception( "Impossible de renomer ce dossier: ".$oldDirMag);
                    }
                    $fichierMag = $uploadDirMag."/index.html";
                    $catalogueOld->setFichierMag($fichierMag);
                    $dateMaj = date("Y-m-d H:i:s");
                    $catalogueOld->setDateMaj($dateMaj);

                }
            }
            var_dump($lpId, $lpNum);
            $catalogueOld->setIntitule($intitule);
            $catalogueOld->setLpId($lpId);
            $catalogueOld->setLp($lpNum);
            $catalogueOld->setGa($gaNum);
            $catalogueOld->setGp($gpNum);
            $catalogueOld->setActifPdf($pdfActif);
            $catalogueOld->setActifMag($magActif);
            $catalogueDAO->update($catalogueOld);


            $lpDAO = $daoFactory->getLPDAO();
            $gaDAO = $daoFactory->getGADAO();
            $gpDAO = $daoFactory->getGPDAO();
            $lps2 = $lpDAO->findAll();
            $lps = $lpDAO->findAllTrueLP();

            $dataForView = [
                'fileMaxSize' => $tool->getMaxUploadedFileSizeBytes(),
                'catalogue' => [
                    'id' => $id,
                    'lpId' => null,
                    'lp' => null,
                    'ga' => null,
                    'gp' => null,
                    'type' => $type,
                    'intitule' => $intitule,
                    'fichierPdf' => $fichierPdf,
                    'fichierMag' => $fichierMag,
                    'isActifPdf' => $pdfActif,
                    'isActifMag' => $magActif,
                    'dateMaj' => $dateMaj,
                ],
                'lps' => $lps,
                'lps2' => $lps2,
                'gas' => null,
                'gps' => null,
                'filAriane' => $this->filAriane,
                'footer' => $this->getFooterData()
            ];

            if(isset($lpId)){
                $dataForView['catalogue']['lpId'] = $lpId;
            }
            if(isset($lpNum)){
                $dataForView['catalogue']['lp'] = $lpNum;
                $gas = $gaDAO->findByLp($lpNum);
                $dataForView['gas'] = $gas;
            }
            if(isset($lpNum) && isset($gaNum)){
                $dataForView['catalogue']['ga'] = $gaNum;
                $gps = $gpDAO->findByGa($lpNum, $gaNum);
                $dataForView['gps'] = $gps;
            }
            if(isset($gpNum)){
                $dataForView['catalogue']['gp'] = $gpNum;
            }

            return $this->render($response, "admin-catalogue-standard-edit.html.twig", ['data'=>$dataForView]);

        }catch(\Exception $e){
            $daoFactory = new DAOFactory($this->getContainer()->db);
            $tool = new ToolModel();
            $lpDAO = $daoFactory->getLPDAO();
            $gaDAO = $daoFactory->getGADAO();
            $gpDAO = $daoFactory->getGPDAO();
            $lps2 = $lpDAO->findAll();
            $lps = $lpDAO->findAllTrueLP();
            $tool = new ToolModel();
            //Les champs null seront completer apres
            $dataForView = [
                'fileMaxSize' => $tool->getMaxUploadedFileSizeBytes(),
                'error' => $e->getMessage(),
                'catalogue' => [
                    'id' => $id,
                    'lpId' => null,
                    'lp' => null,
                    'ga' => null,
                    'gp' => null,
                    'type' => $type,
                    'intitule' => $intitule,
                    'fichierPdf' => null,
                    'isActifPdf' => $pdfActif,
                    'isActifMag' => $magActif,
                    'dateMaj' => $dateMaj,
                ],
                'lps' => $lps,
                'lps2' => $lps2,
                'gas' => null,
                'gps' => null,
                'filAriane' => $this->filAriane,
                'footer' => $this->getFooterData()
            ];
            if(isset($lpId)){
                $dataForView['catalogue']['lpId'] = $lpId;
            }
            if(isset($lpNum)){
                $dataForView['catalogue']['lp'] = $lpNum;
                $gas = $gaDAO->findByLp($lpNum);
                $dataForView['gas'] = $gas;
            }
            if(isset($lpNum) && isset($gaNum)){
                $dataForView['catalogue']['ga'] = $gaNum;
                $gps = $gpDAO->findByGa($lpNum, $gaNum);
                $dataForView['gps'] = $gps;
            }
            if(isset($gpNum)){
                $dataForView['catalogue']['gp'] = $gpNum;
            }

            $this->render($response, 'admin-catalogue-standard-edit.html.twig', ['data' => $dataForView]);
        }
    }

    public function delete(RequestInterface $request, ResponseInterface $response, array $args)
    {
        try{

            $id = $args['id'];
            $daoFactory = new DAOFactory($this->getContainer()->db);
            $catalogueDAO = $daoFactory->getCatalogueStandardDAO();
            $catalogue = $catalogueDAO->find($id);
            $catalogues = $catalogueDAO->findAll();
            $tool = new ToolModel();

            /* Supression repertoire magazine */
            $oldDirMag = substr( trim( $catalogue->getFichierMag() ), 0, -10 ); //on enleve "index.html";
            if( ! self::checkBeforeDelete( $tool->getSiteAbsolutePath().$oldDirMag ) ){
              throw new \Exception("Impossible de supprimer le répartoire du magazine: ".$oldDirMag );
            }
            if( ! $tool->rrmdir( $tool->getSiteAbsolutePath().$oldDirMag ) ){
              throw new \Exception("Impossible de supprimer le répertoire du magazine: ".$oldDirMag );
            }

            /* Supression pdf */
            $oldPdfFile = $catalogue->getFichierPdf();
            if( ! self::checkBeforeDelete( $tool->getSiteAbsolutePath().$oldPdfFile ) ){
              throw new \Exception("Impossible de supprimer le fichier: ".$oldPdfFile );
            }
            if( ! unlink( $tool->getSiteAbsolutePath().$oldPdfFile ) ){
              throw new \Exception("Impossible de supprimer le fichier: ".$oldPdfFile );
            }

            /* supression mysql */
            $catalogueDAO->delete($catalogue);

            return $this->redirect($response, "admin/catalogue-standard.index");


        }catch(\Exception $e){
            $dataForView = [
                'catalogues' => $catalogues,
                'error' => 'Le catalogue standard  n\'a pas été supprimée.',
                'filAriane' => $this->filAriane,
                'footer' => $this->getFooterData()
            ];

            $this->render($response, 'admin-catalogue-standard-index.html.twig', ['data' => $dataForView]);
        }
    }


    /**
    * Fonction verifiant l'emplecement d'un fichier ou dossier avec la Suppression
    * @param $path : chemin du fichier/dossier à vérifier
    * @return True si il peut y avoir suppression, False sinon
    */
    private static function checkBeforeDelete( $path ) {
        if( is_dir( $path ) || (is_file( $path) ) ){
            if( ($path != __DIR__) && ( $path != "" ) ){
                $dirs = explode( "/", $path );
                if( in_array( "pdf", $dirs) || in_array( "magazine", $dirs) ){
                    if( in_array( "entier", $dirs) || in_array( "parLP", $dirs) || in_array( "parTransvers", $dirs) || in_array( "parGP", $dirs) ){
                        return True;
                    }
                }
                else{
                    return False;
                }
            }
            else{
                return False;
            }
        }
        else{
            return False;
        }
    }


}