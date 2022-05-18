<?php

namespace src\controller\page\admin;

use Slim\Http\UploadedFile;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\RequestInterface;
use src\model\dao\DAOFactory;
use \src\controller\page\PageController;
use src\model\metier\CatalogueStock;

use src\model\metier\CatalogueStandard;
use src\model\ToolModel;
use src\model\metier\LP;
use src\model\metier\GA;
use src\model\metier\GP;
use src\model\metier\GPstock;

class AdminCatalogueStockController extends PageController
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
            'Gestion du catalogue stock'=> $this->getContainer()->router->pathFor('admin/catalogue-stock.index')

        ];
    }


    public function index(RequestInterface $request, ResponseInterface $response, array $args)
    {
        try {
            $daoFactory = new DAOFactory($this->getContainer()->db);
            $gpDAOstock = $daoFactory->getGPDAOstock();
            $gps = $gpDAOstock->findAll();
            $dataForView = [
                'gps' => $gps,
                'filAriane' => $this->filAriane,
                'footer' => $this->getFooterData()
            ];

            $this->render($response, 'admin-catalogue-stock-index.html.twig', ['data' => $dataForView]);
        }
        catch(\Exception $e){
            echo $e->getMessage();
        }

    }

    public function create(RequestInterface $request, ResponseInterface $response, array $args)
    {
        try{

            $daoFactory = new DAOFactory($this->getContainer()->db);
            $lpDAO = $daoFactory->getLPDAO();
            $lps = $lpDAO->findAllTrueLP();
            $gaDAO = $daoFactory->getGADAO();
            $gas = $gaDAO->findAll();
            $dataForView = [
                'lps' => $lps,
                'gas' => $gas,
                'filAriane' => $this->filAriane,
                'footer' => $this->getFooterData()
            ];
            $this->filAriane['Créer une nouveau article'] = $this->getContainer()->router->pathFor('admin/catalogue-stock.create');
            $this->render($response, 'admin-catalogue-stock-create.html.twig', ['data' => $dataForView]);

        }catch(\Exception $e){

            $daoFactory = new DAOFactory($this->getContainer()->db);
            $lpDAO = $daoFactory->getLPDAO();
            $lps = $lpDAO->findAllTrueLP();
            $dataForView = [
                'lps' => $lps,
                'error' => 'Un problème est survenu lors du traitement de cette page.',
                'filAriane' => $this->filAriane,
                'footer' => $this->getFooterData()
                





            ];

            $this->render($response, 'admin-catalogue-stock-article-create.html.twig', ['data' => $dataForView]);
        }
    }

    public function createbytn(RequestInterface $request, ResponseInterface $response, array $args)
    {
        try{

            $daoFactory = new DAOFactory($this->getContainer()->db);
            $lpDAO = $daoFactory->getLPDAO();
            $lps = $lpDAO->findAllTrueLP();
            $gaDAO = $daoFactory->getGADAO();
            $gas = $gaDAO->findAll();
            $dataForView = [
                'lps' => $lps,
                'gas' => $gas,
                'filAriane' => $this->filAriane,
                'footer' => $this->getFooterData()
            ];
            $this->filAriane['Trouver et ajouter un nouveau article'] = $this->getContainer()->router->pathFor('admin/catalogue-stock.createbytn');
            $this->render($response, 'admin-catalogue-stock-createbytn.html.twig', ['data' => $dataForView]);

        }catch(\Exception $e){

            $daoFactory = new DAOFactory($this->getContainer()->db);
            $lpDAO = $daoFactory->getLPDAO();
            $lps = $lpDAO->findAllTrueLP();

            $dataForView = [
                'lps' => $lps,
                'error' => 'Un problème est survenu lors du traitement de cette page.',
                'filAriane' => $this->filAriane,
                'footer' => $this->getFooterData()
            ];

            $this->render($response, 'admin-catalogue-stock-createbytn.html.twig', ['data' => $dataForView]);
        }
    }
    public function changePDF(RequestInterface $request, ResponseInterface $response, array $args)
    {
        try{

            $daoFactory = new DAOFactory($this->getContainer()->db);
            $lpDAO = $daoFactory->getLPDAO();
            $lps = $lpDAO->findAllTrueLP();
            $gaDAO = $daoFactory->getGADAO();
            $gas = $gaDAO->findAll();
            $tool = new ToolModel();
            $dataForView = [
                
               
                'filAriane' => $this->filAriane,
                'footer' => $this->getFooterData(),
                'fileMaxSize' => $tool->getMaxUploadedFileSizeBytes()
            ];
            $this->filAriane['changer le PDF du catalogue Stock'] = $this->getContainer()->router->pathFor('admin/catalogue-stock.changePDF');
            $this->render($response, 'admin-catalogue-stock-changePDF.html.twig', ['data' => $dataForView]);

        }catch(\Exception $e){

            $daoFactory = new DAOFactory($this->getContainer()->db);
            $lpDAO = $daoFactory->getLPDAO();
            $lps = $lpDAO->findAllTrueLP();
            $dataForView = [
                
                'error' => 'Un problème est survenu lors du traitement de cette page.',
                'filAriane' => $this->filAriane,
                'footer' => $this->getFooterData()
            ];

            $this->render($response, 'admin-catalogue-stock-changePDF.html.twig', ['data' => $dataForView]);
        }
    }
    public function storechangePDF(RequestInterface $request, ResponseInterface $response, array $args)
    {
        try{
            $this->filAriane['changer le PDF du catalogue Stock'] = $this->getContainer()->router->pathFor('admin/catalogue-stock.changePDF');
            $tool = new ToolModel();
            /* Recuperation des donnees du formulaire envoyé */
          
            $uploadedFiles = $request->getUploadedFiles();         
           
        
           
            if(isset($uploadedFiles['fichierPDF'])){
                $pdfFile = $uploadedFiles['fichierPDF'];
               
                if ($pdfFile->getError() != UPLOAD_ERR_OK) {
                    throw new \Exception("Un problème est survenu lors de l'upload du fichier PDF. (Vérifiez que vous avez bien sélectionné un fichier et que sa taille ne dépasse pas la taille maximale autorisée.)");
                }
                if($pdfFile->getClientMediaType() != "application/pdf"){
                    throw new \Exception("Le fichier n'est pas un fichier PDF.");
                }
            }
            else{
                throw new \Exception("Aucun fichier PDF.");
            }


           // Creation du fichier pdf sur le serveur 
            $tmpFileName = $pdfFile->file;
            $fileName = $pdfFile->getClientFilename();
            $dossier = 'entier/';
            $uploadDirPdf = CatalogueStock::DIR_PDF.$dossier.$fileName;
           
            if( strlen( $dossier ) <= 1 ){
                throw new \Exception( "Impossible de classer le nouveau fichier pdf: dossier indéterminé.");
            }
            if( ! move_uploaded_file( $tmpFileName, $tool->getSiteAbsolutePath().$uploadDirPdf) ){
                throw new \Exception("Impossible de déplacer le fichier uploadé à cet emplacement: ".$uploadDirPdf);
              }
            if( ! rename( $tool->getSiteAbsolutePath().$uploadDirPdf, $tool->getSiteAbsolutePath().CatalogueStock::DIR_PDF.$dossier."catastock".".pdf") ){
                throw new \Exception("Impossible de renomer le fichier uploadé.");
            }
           
            return $this->redirect($response, "admin/catalogue-stock.index");
             
        }catch(\Exception $e){
            
            
            $dataForView = [
                'fileMaxSize' => $tool->getMaxUploadedFileSizeBytes(),
                'error' => $e->getMessage(),
                'filAriane' => $this->filAriane,
                'footer' => $this->getFooterData()
            ];

        
            
            
            $this->render($response, 'admin-catalogue-stock-changePDF.html.twig', ['data' => $dataForView]);
        }
    }
    
    public function store(RequestInterface $request, ResponseInterface $response, array $args)
    {
        try{
            $this->filAriane['Créer un nouveau article '] = $this->getContainer()->router->pathFor('admin/catalogue-stock.create');
            $lpNum = $request->getParsedBody()['lp'];
            $gaNum = $request->getParsedBody()['ga'];
            $gpNum = $request->getParsedBody()['gp'];
     //       $tnnum = $request->getParseBody()['tn'];
            $tnNum = $_POST['tn'];
            $intitule = $request->getParsedBody()['intitule'];
            $description1 = htmlspecialchars($request->getParsedBody()['description1'], ENT_HTML5, "UTF-8");

            if(empty(trim($lpNum)) || intval($lpNum)==0){
                throw new \Exception("Numéro de LP incorrect.");
            }
            if(empty(trim($gaNum)) || intval($gaNum)==0){
                throw new \Exception("Numéro de GA incorrect.");
            }
            if(empty(trim($gpNum)) || intval($gpNum)==0){
                throw new \Exception("Numéro de GP incorrect.");
            }
            if(empty(trim($tnNum)) || intval($tnNum)==0){
                throw new \Exception("Numéro de TN incorrect.");
            }
            if (strlen(trim($tnNum)) > 8 )
            {
                throw new \Exception("Le TN ne peut dépasser 8 chiffres");
            }
            if(empty(trim($intitule))){
                throw new \Exception("Designation incorrect.");
            }

            $daoFactory = new DAOFactory($this->getContainer()->db);
            $lpDAO = $daoFactory->getLPDAO();
            $gaDAO = $daoFactory->getGADAO();
            $gpDAO = $daoFactory->getGPDAO();
            $tnExist = $daoFactory->getGPDAOstock();

            if(is_null($lpDAO->findByLp($lpNum))){
                throw new \Exception("Le numéro de LP ne correspond à aucune ligne de produit existante.");
            } 

            if(is_null($gaDAO->find(['lp'=>$lpNum, 'ga'=> $gaNum]))){
                throw new \Exception("Le couple de valeurs (#LP, #GA) ne correspond à aucun groupe article existant.");
            }

            if(!is_null($gpDAO->find(['lp'=>$lpNum, 'ga'=> $gaNum, 'gp'=>$gpNum]))){
                throw new \Exception("Le groupe produit correspondant au couple de valeurs (#LP, #GA, #GP) existe déjà.");
            }
            if(!is_null($tnExist->find(['tn'=>$tnNum]))){
                throw new \Exception("Le TN existe déjà.");
            }
            if(strlen($tnNum) != 8 ) {
                "Le TN doit être composé 8 caractères";
            }

            $gpDAOstock = $daoFactory->getGPDAOstock();
            $gp = new GPstock($tnNum, $lpNum, $gaNum, $gpNum, $intitule, $description1);
            $gpDAOstock->create($gp);
            return $this->redirect($response, "admin/catalogue-stock.index");

        }catch(\Exception $e){
            $daoFactory = new DAOFactory($this->getContainer()->db);
            $lpDAO = $daoFactory->getLPDAO();
            $gaDAO = $daoFactory->getGADAO();
            $lps = $lpDAO->findAllTrueLP();
            $gas = $gaDAO->findByLp($lpNum);
            $dataForView = [
                'error' => $e->getMessage(),
                'gp' => [
                    'lp'        => $lpNum,
                    'ga'        => $gaNum,
                    'gp'        => $gpNum,
                    'intitule'  => $intitule,
                    'description1' => htmlspecialchars_decode($description1, ENT_HTML5),
                ],
                'lps' => $lps,
                'gas' => $gas,
                'filAriane' => $this->filAriane,
                'footer' => $this->getFooterData()
            ];

            $this->render($response, 'admin-catalogue-stock-create.html.twig', ['data' => $dataForView]);
        }
    }

    public function storebytn(RequestInterface $request, ResponseInterface $response, array $args)
    {
        try{
            $this->filAriane['Ajouter un nouveau article '] = $this->getContainer()->router->pathFor('admin/catalogue-stock.createbytn');

            $tnNum = $_POST['tn'];

           

            if(empty(trim($tnNum)) || intval($tnNum)==0){
                throw new \Exception("Numéro de TN incorrect.");
            }
            if (strlen(trim($tnNum)) > 8 )
            {
                throw new \Exception("Le TN ne peut dépasser 8 chiffres");
            }
            $daoFactory = new DAOFactory($this->getContainer()->db);
            $lpDAO = $daoFactory->getLPDAO();
            $gaDAO = $daoFactory->getGADAO();
            $gpDAO = $daoFactory->getGPDAO();
            $tnExist = $daoFactory->getGPDAOstock();

            
            if(!is_null($tnExist->find(['tn'=>$tnNum]))){
                throw new \Exception("Le TN existe déjà.");
            }
            if(strlen(trim($tnNum)) != 8 ) {
                "Le TN doit être composé 8 caractères";
            }


            $server = "Driver={iSeries Access ODBC Driver};SYSTEM=DEJUMO00;";
            $user = "DVPGLN";
            $pw = "NOTES";
            $queryODBC = "SELECT * FROM jqfrlib.tha0010d ";  // table et bibliotheque données par Ali
            $connexionODBC = odbc_connect($server,$user, $pw);
    
            $resultODBC = odbc_exec($connexionODBC, $queryODBC) or die('Echec de la requete ODBC');

            $TNArrayTmp = array(); // Tableau qui va contenir tous les tn (avec doublons)
            $TNArray = array(); // Tableau qui va contenir tous les tn (sans doublons)
            $LPArray = array();
            $DEFArray = array();
            $TEXTArray = array();
            $GAArray = array();
            $GPArray = array();



            $tnNum2 = $_POST['tn'];
            $rechTN = "SELECT  * FROM jqfrlib.tha0010d where tn = $tnNum2"; 

            $resultTN = odbc_exec($connexionODBC, $rechTN) or die('Echec de la requete ODBC');
  //          odbc_result_all($resultTN);


            $row = trim( odbc_result( $resultTN, "TN" ) ); 
            $lpgagp = trim( odbc_result( $resultTN, "COOPRG" ) ); //récupère le produit (lp + ga + gp)
            $row2 = substr($lpgagp,0,2); // LP
            $row5 = substr($lpgagp,2,2); // GA
            $row6 = substr($lpgagp,4,2); // GP
            $row3 = trim( odbc_result( $resultTN, "ARDES" ) ); 
            $row4 = trim(  odbc_result( $resultTN, "ARTEX1" )." ". odbc_result( $resultTN, "ARTEX2" )." ". odbc_result($resultTN,"ARTEX3")." ". odbc_result($resultTN,"ARTEX4")." ". odbc_result($resultTN,"ARTEX5")
            ." ". odbc_result($resultTN,"ARTEX6")." ". odbc_result($resultTN,"ARTEX7")." ". odbc_result($resultTN,"ARTEX8")." ". odbc_result($resultTN,"ARTEX9")
            ." ". odbc_result($resultTN,"ARTE10")." ". odbc_result($resultTN,"ARTE11")." ". odbc_result($resultTN,"ARTE12")  );

            //pour s'y retrouver avec les variables
       //     $tn=str_pad($row,8,0,STR_PAD_LEFT); // pour ajouter des 0 si TN != 8
            $tn=$tnNum2;
            $lp=$row2;
            $def=$row3;
            $text=$row4;
            $ga=$row5;
            $gp=$row6;

        $TNArray = array_unique( $TNArrayTmp ); //on enleve les doublons
        $i=0;


            $bd = mysqli_connect('localhost', 'root', '', 'jumo-france-local');
            if(!$bd){
                die('Erreur de connexion mysql');
            }
         //   mysql_query('set names utf8');
           // mysqli_set_charset($bd, 'utf8');

           if(is_null($resultTN)){
            throw new \Exception("Le TN n'existe pas dans le fichier odbc.");
        }

            $def = utf8_encode($def);
            $text = utf8_encode($text);

/*
            $query = mysqli_prepare($bd, "INSERT INTO produit SET tn = ?, lp = ?, ga = ?, gp = ? ,designation = ?, texte = ?, date_ajout=NOW() 
            ON DUPLICATE KEY UPDATE lp = ?, ga = ?,gp = ?, designation = ?,texte = ?, date_ajout=NOW()");
            mysqli_stmt_bind_param($query,'ddddssdddss', $tn, $lp, $ga , $gp ,$def,$text, $lp, $ga , $gp, $def, $text);
            $res = mysqli_stmt_execute($query) or die(mysqli_error($bd));

            */
            $gpDAOstock = $daoFactory->getGPDAOstock();
            $gp = new GPstock($tn, $lp, $ga, $gp, $def, $text);
            $gpDAOstock->create($gp);
            return $this->redirect($response, "admin/catalogue-stock.index"); 

        }catch(\Exception $e){
            $daoFactory = new DAOFactory($this->getContainer()->db);
            $lpDAO = $daoFactory->getLPDAO();
            $gaDAO = $daoFactory->getGADAO();
            $lps = $lpDAO->findAllTrueLP();
            $tnExist = $daoFactory->getGPDAOstock();
           // $odbc = $tnExist->codbc($tnNum);




            $dataForView = [
                'error' => $e->getMessage(),
                'gp' => [

                ],
                'lps' => $lps,
                'filAriane' => $this->filAriane,
                'footer' => $this->getFooterData()
            ];

            $this->render($response, 'admin-catalogue-stock-createbytn.html.twig', ['data' => $dataForView]);
        }
    }

    public function delete(RequestInterface $request, ResponseInterface $response, array $args)
    {
        try{

            $lpNum = $args['lp'];
            $gaNum = $args['ga'];
            $gpNum = $args['gp'];
            $tnNum = $args['tn'];
            $daoFactory = new DAOFactory($this->getContainer()->db);
            $gpDAO = $daoFactory->getGPDAOstock();
            $gps = $gpDAO->findAll();
            $gpsEdited = $gpDAO->findAllEdited();
            $gp = $gpDAO->find2(['lp'=>$lpNum, 'ga'=>$gaNum, 'gp'=>$gpNum]);
           // $gp = $gpDAO->find(['tn'=>$tnNum]);
            $gpDAO->delete($gp);
            return $this->redirect($response, "admin/catalogue-stock.index");


        }catch(\Exception $e){
            $dataForView = [
                'gps' => $gps,
                'gp' => $gp,
                'gpsEdited' => $gpsEdited,
                'error' => 'L\'article  n\'a pas été supprimé.',
                'filAriane' => $this->filAriane,
                'footer' => $this->getFooterData()
            ];

            $this->render($response, 'admin-catalogue-stock-index.html.twig', ['data' => $dataForView]);
        }
    }

    public function edit(RequestInterface $request, ResponseInterface $response, array $args)
    {
        try{
            $lpNum = $args['lp'];
            $gaNum = $args['ga'];
            $gpNum = $args['gp'];
            $this->filAriane['Modifier un article'] = $this->getContainer()->router->pathFor('admin/catalogue-stock.edit', ['lp'=>$lpNum, 'ga'=>$gaNum, 'gp'=>$gpNum]);
            $daoFactory = new DAOFactory($this->getContainer()->db);
            $lpDAO = $daoFactory->getLPDAO();
            $gaDAO = $daoFactory->getGADAO();
            $gpDAO = $daoFactory->getGPDAOstock();
            $lppAll = $lpDAO->findAllPersonalizedLP();
            $lpps = $lpDAO->findAllPersonalizedLPByGP($lpNum, $gaNum, $gpNum);
            $lps = $lpDAO->findAllTrueLP();
            $gas = $gaDAO->findByLp($lpNum);
            $gp = $gpDAO->find2(['lp'=>$lpNum, 'ga'=>$gaNum, 'gp'=>$gpNum]);

            var_dump($lpps);

            var_dump($lppAll);
            $dataForView = [
                'lps'   => $lps,
                'lpps'  => $lpps,
                'lppAll'  => $lppAll,
                'gas'   => $gas,
                'gp'    => $gp,
                'filAriane' => $this->filAriane,
                'footer' => $this->getFooterData()
            ];

            $this->render($response, 'admin-catalogue-stock-edit.html.twig', ['data'  => $dataForView]);
        }catch (\Exception $e){
            $dataForView = [
              'error' => "Une erreur s'est produite lors de traitement de cette page.",
                'filAriane' => $this->filAriane,
                'footer' => $this->getFooterData()
            ];
            $this->render($response, 'admin-catalogue-stock-edit.html.twig', ['data'  => $dataForView]);
        }

    }

    public function update(RequestInterface $request, ResponseInterface $response, array $args)
    {
        try{
            $lpNum = $args['lp'];
            $gaNum = $args['ga'];
            $gpNum = $args['gp'];
            $this->filAriane['Modifier un article'] = $this->getContainer()->router->pathFor('admin/catalogue-stock.edit', ['lp'=>$lpNum, 'ga'=>$gaNum, 'gp'=>$gpNum]);
            $lpNum2 = $request->getParsedBody()['lpSave'];
            $gaNum2 = $request->getParsedBody()['gaSave'];
            $lpNumNew = $request->getParsedBody()['lp'];
            $gaNumNew = $request->getParsedBody()['ga'];
            $gpNumNew = $request->getParsedBody()['gp'];
            $intitule = $request->getParsedBody()['intitule'];
            $description1 = htmlspecialchars($request->getParsedBody()['description1'], ENT_HTML5, "UTF-8");

            if($lpNum != $lpNum2 || empty(trim($lpNum2))){
                throw new \Exception("Numéro de LP incorrect.");
            }

            if(empty(trim($lpNumNew)) || intval($lpNumNew)==0){
                throw new \Exception("Numéro de LP incorrect.");
            }

            if($gaNum != $gaNum2 || empty(trim($gaNum2))){
                throw new \Exception("Numéro de GA incorrect.");
            }

            if(empty(trim($gaNumNew)) || intval($gaNumNew)==0){
                throw new \Exception("Numéro de GA incorrect.");
            }

            if(empty(trim($gpNumNew)) || intval($gpNumNew)==0){
                throw new \Exception("Numéro de GP incorrect.");
            }
            if(empty(trim($intitule))){
                throw new \Exception("Intitulé incorrect.");
            }

            $daoFactory = new DAOFactory($this->getContainer()->db);
            $lpDAO = $daoFactory->getLPDAO();
            $gaDAO = $daoFactory->getGADAO();
            $gpDAO = $daoFactory->getGPDAOstock();
            $gpOld = $gpDAO->find2(['lp'=>$lpNum, 'ga'=>$gaNum, 'gp'=>$gpNum]);
            $gpNew = new GPstock("1", $lpNumNew, $gaNumNew, $gpNumNew, $intitule, $description1);

            $gpDAO->deleteFromPersonlizedLP($gpOld);
            $lpps = [];
            /*
            foreach($lppIds as $id){
                if($id != '-1'){
                    $lpps[] = $lpDAO->find($id);
                    $gpDAO-> addToPersonnalizedLP($gpNew, $id);
                }
            }
*/
            $gpDAO->updateTotal($gpOld, $gpNew);

            $lps = $lpDAO->findAllTrueLP();

            return $this->redirect($response, "admin/catalogue-stock.edit",['lp'=>$lpNumNew, 'ga'=>$gaNumNew,  'gp'=>$gpNumNew]);


        }catch (\Exception $e){
            $daoFactory = new DAOFactory($this->getContainer()->db);
            $lpDAO = $daoFactory->getLPDAO();
            $gaDAO = $daoFactory->getGADAO();
            $lps = $lpDAO->findAllTrueLP();
            $lppAll = $lpDAO->findAllPersonalizedLP();
            $gas = $gaDAO->findByLp($lpNum);
            $dataForView = [
                'error' => $e->getMessage(),
                'gp' => [
                    'lp'        => $lpNum,
                    'ga'        => $gaNum,
                    'gp'        => $gpNum,
                    'intitule'  => $intitule,
                    'description1' => htmlspecialchars_decode($description1, ENT_HTML5),
                ],
                'lps' => $lps,
                'gas' => $gas,
                //'lpps'  => $lpps,
                'lppAll'  => $lppAll,
                'filAriane' => $this->filAriane,
                'footer' => $this->getFooterData()
            ];
            $this->render($response, 'admin-catalogue-stock-edit.html.twig', ['data'  => $dataForView]);
        }

    }

}