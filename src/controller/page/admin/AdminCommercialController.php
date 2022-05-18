<?php

namespace src\controller\page\admin;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\RequestInterface;
use src\model\dao\DAOFactory;
use \src\controller\page\PageController;
use src\model\metier\Commercial;
use src\model\metier\Departement;

class AdminCommercialController extends PageController
{
    const DROITS = 1;

    private $filAriane;
    /**
     * AdminCommercialControllerconstructor.
     */
    public function __construct($container)
    {
        parent::__construct($container);
        $this->filAriane = [
            'Accueil' => $this->getContainer()->router->pathFor('accueil.index'),
            'Menu administrateur'=> $this->getContainer()->router->pathFor('admin/home.index'),
            'Gestion des commerciaux'=> $this->getContainer()->router->pathFor('admin/commercial.index')

        ];
    }


    public function index(RequestInterface $request, ResponseInterface $response, array $args)
    {
        try {

            $daoFactory = new DAOFactory($this->getContainer()->db);
            $commercialDAO = $daoFactory->getCommercialDAO();
            $commerciaux = $commercialDAO->findAll();
            $dataForView = [
                'commerciaux' => $commerciaux,
                'filAriane' => $this->filAriane,
                'footer' => $this->getFooterData()
            ];

            $this->render($response, 'admin-commercial-index.html.twig', ['data' => $dataForView]);
        }
        catch(\Exception $e){
            echo $e->getMessage();
        }

    }

    public function create(RequestInterface $request, ResponseInterface $response, array $args)
    {
        try{
            $this->filAriane['Créer un nouveau commercial'] = $this->getContainer()->router->pathFor('admin/commercial.create');
            $dataForView = ['filAriane' => $this->filAriane, 'footer' => $this->getFooterData()];
            $this->render($response, 'admin-commercial-create.html.twig', ['data' => $dataForView]);

        }catch(\Exception $e){
            $dataForView = [
                'error' => 'Un problème est survenu lors du traitement de cette page.',
                'filAriane' => $this->filAriane,
                'footer' => $this->getFooterData()
            ];

            $this->render($response, 'admin-commercial-create.html.twig', ['data' => $dataForView]);
        }
    }

    public function store(RequestInterface $request, ResponseInterface $response, array $args)
    {
        try{
            $nom = $request->getParsedBody()['nom'];
            $prenom = $request->getParsedBody()['prenom'];
            $fonction = $request->getParsedBody()['fonction'];
            $mail = $request->getParsedBody()['mail'];
            $tel = $request->getParsedBody()['tel'];
            $fax = $request->getParsedBody()['fax'];
            $numRepr = $request->getParsedBody()['numRepr'];
            $image = $request->getParsedBody()['image'];

            if(empty(trim($nom)) ){
                throw new \Exception("Nom incorrect.");
            }
            if(empty(trim($prenom)) ){
                throw new \Exception("Prénom incorrect.");
            }
            if(empty(trim($mail)) ){
                throw new \Exception("Mail incorrect.");
            }
            if(empty(trim($tel)) ){
                throw new \Exception("Téléphone incorrect.");
            }
            if(empty(trim($fax)) ){
                throw new \Exception("Fax incorrect.");
            }
            if( (empty(trim($fonction))) || ($fonction == '-1')){
                throw new \Exception("Fonction incorrect.");
            }

            $daoFactory = new DAOFactory($this->getContainer()->db);
            $commercialDAO = $daoFactory->getCommercialDAO();

            $commercial = new Commercial('',$nom, $prenom, $numRepr, $fonction, $mail, $tel, $fax, $image);

            $commercialDAO->create($commercial);
            return $this->redirect($response, "admin/commercial.index");

        }catch(\Exception $e){
            $dataForView = [
                'error' => $e->getMessage(),
                'commercial' => [
                    'nom'       => $nom,
                    'prenom'    => $prenom,
                    'fonction'  => $fonction,
                    'mail'      => $mail,
                    'numRepr'   => $numRepr,
                    'tel'       => $tel,
                    'fax'       => $fax,
                    'image'     => $image
                ],
                'filAriane' => $this->filAriane,
                'footer' => $this->getFooterData()
            ];

            $this->render($response, 'admin-commercial-create.html.twig', ['data' => $dataForView]);
        }
    }

    public function edit(RequestInterface $request, ResponseInterface $response, array $args)
    {
        try{
            $id = $args['id'];
            $this->filAriane['Modifier un commercial'] = $this->getContainer()->router->pathFor('admin/commercial.edit', ['id'=>$id]);
            $daoFactory = new DAOFactory($this->getContainer()->db);
            $commercialDAO = $daoFactory->getCommercialDAO();
            $commercial = $commercialDAO->find($id);
            if(is_null($commercial)){
                throw new \Exception("Commercial inconnu.");
            }

            $dataForView = [
                'commercial'    => $commercial,
                'filAriane' => $this->filAriane,
                'footer' => $this->getFooterData()
            ];

            $this->render($response, 'admin-commercial-edit.html.twig', ['data'  => $dataForView]);
        }catch (\Exception $e){
            $dataForView = [
                'error' => $e->getMessage(),
                'filAriane' => $this->filAriane,
                'footer' => $this->getFooterData()
            ];
            $this->render($response, 'admin-commercial-edit.html.twig', ['data'  => $dataForView]);
        }

    }

    public function update(RequestInterface $request, ResponseInterface $response, array $args)
    {
        try{

            var_dump($request->getParsedBody());
            $id = $args['id'];
            $this->filAriane['Modifier un commercial'] = $this->getContainer()->router->pathFor('admin/commercial.edit', ['id'=>$id]);

            $id2 = $request->getParsedBody()['id'];
            $nom = $request->getParsedBody()['nom'];
            $prenom = $request->getParsedBody()['prenom'];
            $fonction = $request->getParsedBody()['fonction'];
            $mail = $request->getParsedBody()['mail'];
            $tel = $request->getParsedBody()['tel'];
            $fax = $request->getParsedBody()['fax'];
            $numRepr = $request->getParsedBody()['numRepr'];
            $image = $request->getParsedBody()['image'];

            if($id != $id2){
                throw new \Exception("Identifiant incorrect.");
            }
            if(empty(trim($nom)) ){
                throw new \Exception("Nom incorrect.");
            }
            if(empty(trim($prenom)) ){
                throw new \Exception("Prénom incorrect.");
            }
            if(empty(trim($mail)) ){
                throw new \Exception("Mail incorrect.");
            }
            if(empty(trim($tel)) ){
                throw new \Exception("Téléphone incorrect.");
            }
            if(empty(trim($fax)) ){
                throw new \Exception("Fax incorrect.");
            }
            if( (empty(trim($fonction))) || ($fonction == '-1')){
                throw new \Exception("Fonction incorrect.");
            }

            $daoFactory = new DAOFactory($this->getContainer()->db);
            $commercialDAO = $daoFactory->getCommercialDAO();
            $commercial = new Commercial($id, $nom, $prenom, $numRepr, $fonction, $mail, $tel, $fax, $image);
            $commercialDAO->update($commercial);
            return $this->redirect($response, "admin/commercial.edit",['id'=>$id]);


        }catch (\Exception $e){

            $dataForView = [
                'error' => $e->getMessage(),
                'commercial' => [
                    'id'        => $id,
                    'nom'       => $nom,
                    'prenom'    => $prenom,
                    'fonction'  => $fonction,
                    'mail'      => $mail,
                    'numRepr'   => $numRepr,
                    'tel'       => $tel,
                    'fax'       => $fax,
                    'image'     => $image
                ],
                'filAriane' => $this->filAriane,
                'footer' => $this->getFooterData()
            ];
            $this->render($response, 'admin-commercial-edit.html.twig', ['data'  => $dataForView]);
        }

    }

    public function delete(RequestInterface $request, ResponseInterface $response, array $args)
    {
        try{

            $id = $args['id'];
            $daoFactory = new DAOFactory($this->getContainer()->db);
            $commercialDAO = $daoFactory->getCommercialDAO();
            $commercial = $commercialDAO->find($id);
            $commerciaux = $commercialDAO->findAll();
            $commercialDAO->delete($commercial);
            return $this->redirect($response, "admin/commercial.index");


        }catch(\Exception $e){
            $dataForView = [
                'commerciaux' => $commerciaux,
                'error' => 'Le commercial  n\'a pas été supprimé.',
                'filAriane' => $this->filAriane,
                'footer' => $this->getFooterData()
            ];

            $this->render($response, 'admin-commercial-index.html.twig', ['data' => $dataForView]);
        }
    }

    public function departement(RequestInterface $request, ResponseInterface $response, array $args)
    {
        try {

            $this->filAriane['Selection d\'un commercial'] = $this->getContainer()->router->pathFor('admin/commercial.departement');

            $daoFactory = new DAOFactory($this->getContainer()->db);
            $commercialDAO = $daoFactory->getCommercialDAO();

            $dirsH = $commercialDAO->findAllByFonction("Directeur Commercial");
            $dirsF = $commercialDAO->findAllByFonction("Directrice Commerciale");
            $itcsH = $commercialDAO->findAllByFonction("Ingénieur Technico Commercial");
            $itcsF = $commercialDAO->findAllByFonction("Ingénieure Technico Commerciale");
            $tcssH = $commercialDAO->findAllByFonction("Technico Commercial Sédentaire");
            $tcssF = $commercialDAO->findAllByFonction("Technico Commerciale Sédentaire");
            $acsF = $commercialDAO->findAllByFonction("Assistante Commerciale");
            $acsH = $commercialDAO->findAllByFonction("Assistant Commercial");

            $dirs = array_merge($dirsF, $dirsH);
            $itcs = array_merge($itcsF, $itcsH);
            $tcss = array_merge($tcssF, $tcssH);
            $acs = array_merge($acsF, $acsH);

            $dataForView = [
                'dirs' => $dirs,
                'itcs' => $itcs,
                'tcss' => $tcss,
                'acs'   => $acs,
                'filAriane' => $this->filAriane,
                'footer' => $this->getFooterData()
            ];

            $this->render($response, 'admin-commercial-departement.html.twig', ['data' => $dataForView]);
        }
        catch(\Exception $e){
            echo $e->getMessage();
        }

    }

    public function editDepartement(RequestInterface $request, ResponseInterface $response, array $args)
    {
        try {


            $id = $args['id'];
            $fonction = $args['fonction'];

            if($id == 0){
                throw new \Exception("Veuillez sélectionner un commercial.");
            }

            if( !(($fonction == "dir") || ($fonction == "itc") || ($fonction == "tcs") || ($fonction == "ac")) ){
                throw new \Exception("Veuillez sélectionner un commercial.");
            }

            $this->filAriane['Selection d\'un commercial'] = $this->getContainer()->router->pathFor('admin/commercial.departement');
            $this->filAriane['Affectation des départements'] = $this->getContainer()->router->pathFor('admin/commercial.editDepartement', ['fonction' => $fonction, 'id'=>$id]);

            $daoFactory = new DAOFactory($this->getContainer()->db);
            $commercialDAO = $daoFactory->getCommercialDAO();
            $deptDAO = $daoFactory->getDepartementDAO();
            $depts = $deptDAO->findAll();
            $c = $commercialDAO->find($id);

            if(is_null($c)){
                throw new \Exception("Commercial inconnu.");
            }

            $cDepts = array();
            if( $fonction == "dir"){
                $cDepts = $deptDAO->findAllByDir($id);
            }
            if( $fonction == "itc"){
                $cDepts = $deptDAO->findAllByItc($id);
            }
            if( $fonction == "tcs"){
                $cDepts = $deptDAO->findAllByTcs($id);
            }
            if( $fonction == "ac"){
                $cDepts = $deptDAO->findAllByAc($id);
            }

            $cDeptsNum = [];
            foreach ($cDepts as $d){
                $cDeptsNum[] = $d->getNum();
            }



            $dataForView = [
                'commercial' => $c,
                'fonction'   => $fonction,
                'depts' => $depts,
                'commercialDepartementsNum' => $cDeptsNum,
                'filAriane' => $this->filAriane,
                'footer' => $this->getFooterData()
            ];

            $this->render($response, 'admin-commercial-edit-departement.html.twig', ['data' => $dataForView]);
        }
        catch(\Exception $e){

            $this->filAriane['Selection d\'un commercial'] = $this->getContainer()->router->pathFor('admin/commercial.departement');
            $daoFactory = new DAOFactory($this->getContainer()->db);
            $commercialDAO = $daoFactory->getCommercialDAO();

            $dirsH = $commercialDAO->findAllByFonction("Directeur Commercial");
            $dirsF = $commercialDAO->findAllByFonction("Directrice Commerciale");
            $itcsH = $commercialDAO->findAllByFonction("Ingénieur Technico Commercial");
            $itcsF = $commercialDAO->findAllByFonction("Ingénieure Technico Commerciale");
            $tcssH = $commercialDAO->findAllByFonction("Technico Commercial Sédentaire");
            $tcssF = $commercialDAO->findAllByFonction("Technico Commerciale Sédentaire");
            $acsF = $commercialDAO->findAllByFonction("Assistante Commerciale");
            $acsH = $commercialDAO->findAllByFonction("Assistant Commercial");

            $dirs = array_merge($dirsF, $dirsH);
            $itcs = array_merge($itcsF, $itcsH);
            $tcss = array_merge($tcssF, $tcssH);
            $acs = array_merge($acsF, $acsH);

            $dataForView = [
                'dirs' => $dirs,
                'itcs' => $itcs,
                'tcss' => $tcss,
                'acs'   => $acs,
                'filAriane' => $this->filAriane,
                'error' =>$e->getMessage(),
                'footer' => $this->getFooterData()
            ];
            $this->render($response, 'admin-commercial-departement.html.twig', ['data' => $dataForView]);
        }

    }

    public function updateDepartement(RequestInterface $request, ResponseInterface $response, array $args)
    {
        try{
            $id = $request->getParsedBody()['id'];
            $fonction = $request->getParsedBody()['fonction'];
            $cbDepts = $request->getParsedBody()['cbDepts'];

            $daoFactory = new DAOFactory($this->getContainer()->db);
            $deptDAO = $daoFactory->getDepartementDAO();
            $commercialDAO = $daoFactory->getCommercialDAO();
            $c = $commercialDAO->find($id);
            if(is_null($c)){
                throw new \Exception("Commercial inconnu.");
            }

            $depts = $deptDAO->findAll();

            //Parcours de tous les département
            foreach($depts as $d){
                //on genere un tableau contenant les numero de departements
                $deptsNum[] = $d->getNum();

                //Si le departement en traitement fait parti des departements selectionnes
                if(array_key_exists($d->getNum(), $cbDepts)){
                    //Traitements différents selon la fonction du commercial
                    if($fonction=="dir"){
                        $d->setIdDir($id);
                    }
                    elseif ($fonction =="tcs"){
                        $d->setIdTcs($id);
                    }
                    elseif ($fonction == "itc"){
                        $d->setIdItc($id);
                    }
                    elseif ($fonction == "ac"){
                        $d->setIdAc($id);
                    }
                    else{
                        throw new \Exception("Données invalides, traitement impossible.");
                    }
                    //On met a jour le departement avec le nouvel id
                    $deptDAO->update($d);
                }
                //si le departement en traitement ne fait pas partie des departements selectionnes
                else{
                    //et si il etait attribue au commercial en traitement
                    if($fonction=="dir"){
                        if( $d->getIdDir() == $id){
                            $d->setIdDir(null);
                        }
                    }
                    elseif ($fonction =="tcs"){
                        if( $d->getIdTcs() == $id){
                            $d->setIdTcs(null);
                        }
                    }
                    elseif ($fonction == "itc"){
                        if( $d->getIdItc() == $id){
                            $d->setIdItc(null);
                        }
                    }
                    elseif ($fonction == "ac"){
                        if( $d->getIdAc() == $id){
                            $d->setIdAc(null);
                        }
                    }
                    else{
                        throw new \Exception("Données invalides, traitement impossible.");
                    }
                    //On met a jour le departement
                    $deptDAO->update($d);
                }
            }
            return $this->redirect($response, "admin/commercial.editDepartement",['id'=>$id, 'fonction'=>$fonction]);

        }catch (\Exception $e){
            $this->filAriane['Selection d\'un commercial'] = $this->getContainer()->router->pathFor('admin/commercial.departement');
            $daoFactory = new DAOFactory($this->getContainer()->db);
            $commercialDAO = $daoFactory->getCommercialDAO();
            $c = $commercialDAO->find($id);
            $cDepts = array();
            if( $fonction == "dir"){
                $cDepts = $deptDAO->findAllByDir($id);
            }
            if( $fonction == "itc"){
                $cDepts = $deptDAO->findAllByItc($id);
            }
            if( $fonction == "tcs"){
                $cDepts = $deptDAO->findAllByTcs($id);
            }
            if( $fonction == "ac"){
                $cDepts = $deptDAO->findAllByAc($id);
            }

            $cDeptsNum = [];
            foreach ($cDepts as $d){
                $cDeptsNum[] = $d->getNum();
            }

            $dataForView = [
                'commercial' => $c,
                'fonction'   => $fonction,
                'depts' => $depts,
                'commercialDepartementsNum' => $cDeptsNum,
                'filAriane' => $this->filAriane,
                'footer' => $this->getFooterData()
            ];
            $this->render($response, 'admin/commercial.editDepartement', ['data' => $dataForView]);
        }

    }


}