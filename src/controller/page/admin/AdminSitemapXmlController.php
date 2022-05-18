<?php

namespace src\controller\page\admin;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\RequestInterface;
use \src\controller\page\PageController;
use \src\model\ToolModel;
use src\model\dao\DAOFactory;
use src\model\metier\Service;
use src\model\metier\LP;
use src\model\metier\GA;
use src\model\metier\GP;
use src\model\metier\CatalogueStandard;


class AdminSitemapXmlController extends PageController
{
    const DROITS = 10;

    private $filAriane;
    private $fileName;
    private $path;
    /**
     * AdminSitemapXmlController constructor.
     */
     public function __construct($container)
     {
        parent::__construct($container);
        $this->fileName = "sitemap.xml";
        $tool = new ToolModel();
        $this->path = $tool->getProjectAbsolutePath().'/tmp';
        $this->filAriane = [
            'Accueil' => $this->getContainer()->router->pathFor('accueil.index'),
            'Menu administrateur'=> $this->getContainer()->router->pathFor('admin/home.index'),
            'Fichier sitemap.xml'=> $this->getContainer()->router->pathFor('admin/sitemap-xml.index')

        ];
     }
 
 
     public function index(RequestInterface $request, ResponseInterface $response, array $args)
     {
         try {
             $dataForView = [
                 'filAriane' => $this->filAriane,
                 'footer' => $this->getFooterData()
             ];
             $this->render($response, 'admin-sitemap-xml-index.html.twig', ['data' => $dataForView]);
         }
         catch(\Exception $e){
            $dataForView = [
                'filAriane' => $this->filAriane,
                'footer' => $this->getFooterData(),
                'error' => $e->getMessage()
            ];
            $this->render($response, 'admin-sitemap-xml-index.html.twig', ['data' => $dataForView]);
         }
 
     }

    public function download(RequestInterface $request, ResponseInterface $response, array $args)
    {
        try {
            $file = $this->makeFile();
            $xmlContent = $this->makeXmlContent();
            file_put_contents($file, $xmlContent);

            $r = $response->withHeader('Content-Description', 'File Transfer')
            ->withHeader('Content-Type', 'application/octet-stream')
            ->withHeader('Content-Disposition', 'attachment;filename="'.basename($file).'"')
            ->withHeader('Expires', '0')
            ->withHeader('Cache-Control', 'must-revalidate')
            ->withHeader('Pragma', 'public')
            ->withHeader('Content-Length', filesize($file));
            readfile($file);
            return $r;
            

        }
        catch(\Exception $e){
           $dataForView = [
               'filAriane' => $this->filAriane,
               'footer' => $this->getFooterData(),
               'error' => $this->$e->getMessage()
           ];
           $this->render($response, 'admin-sitemap-xml-index.html.twig', ['data' => $dataForView]);
        }
    }

    public function replace(RequestInterface $request, ResponseInterface $response, array $args)
    {
        try {
            $tool = new ToolModel();
            $file = $this->makeFile();
            $xmlContent = $this->makeXmlContent();
            file_put_contents($file, $xmlContent);
            copy($file, $tool->getSiteAbsolutePath().'/'.$this->fileName);

            $dataForView = [
                'filAriane' => $this->filAriane,
                'footer' => $this->getFooterData(),
                'success' => "Le fichier sitemap.xml a bien été remplacé."
            ];
            $this->render($response, 'admin-sitemap-xml-index.html.twig', ['data' => $dataForView]);
            
        }
        catch(\Exception $e){
           $dataForView = [
               'filAriane' => $this->filAriane,
               'footer' => $this->getFooterData(),
               'error' => $this->$e->getMessage()
           ];
           $this->render($response, 'admin-sitemap-xml-index.html.twig', ['data' => $dataForView]);
        }
    }

    private function makeFile() : string
    {
        $file = $this->path.'/'.$this->fileName;
        if(file_exists($file)){
            unlink($file);
        }
        return $file;
    }

    private function makeXmlContent() : string
    {
        $tool = new ToolModel();
        $daoFactory = new DAOFactory($this->getContainer()->db);
        $services = $daoFactory->getServiceDAO()->findAll();
        $lps = $daoFactory->getLPDAO()->findAll();
        $gas = $daoFactory->getGADAO()->findAll();
        $gps = $daoFactory->getGPDAO()->findAll();
        $catalogues = $daoFactory->getCatalogueStandardDAO()->findAll();
        $actus = $daoFactory->getActualiteDAO()->findAll();
        $dpts = $daoFactory->getDepartementDAO()->findAll();

        $S = "https://www.jumo-france.fr";
        $xw = new \XMLWriter();
        $xw->openMemory();
        $xw->startDocument("1.0", "UTF-8");
            $xw->startElement("urlset");$xw->startAttribute('xmlns');$xw->text('http://www.sitemaps.org/schemas/sitemap/0.9');$xw->endAttribute();

                //Accueil
                $xw->startElement("url");
                    $xw->startElement("loc");
                        $xw->text($S);
                    $xw->endElement();
                    $xw->startElement("priority");
                        $xw->text("1");
                    $xw->endElement();
                $xw->endElement();

                //Catalogue Standard
                $xw->startElement("url");
                    $xw->startElement("loc");
                        $xw->text($S.$this->getContainer()->router->pathFor('catalogue-standard.index'));
                    $xw->endElement();
                    $xw->startElement("priority");
                        $xw->text("0.9");
                    $xw->endElement();
                $xw->endElement();

                foreach ($catalogues as $c) {
                    if(($c->getType() == CatalogueStandard::TYPE_LP) || ($c->getType() == CatalogueStandard::TYPE_GP)){
                        $xw->startElement("url");
                            $xw->startElement("loc");
                                if($c->getType() == CatalogueStandard::TYPE_LP){;
                                    $lp = $daoFactory->getLPDAO()->find($c->getLpId());
                                    $xw->text($S.$this->getContainer()->router->pathFor('catalogue-standard.showLP',['id'=>$c->getId(),'lp'=>$c->getLp(), 'nom'=>$tool->urlFriendly($lp->getIntitule())]));
                                }
                                elseif ($c->getType() == CatalogueStandard::TYPE_GP) {
                                    $xw->text($S.$this->getContainer()->router->pathFor('catalogue-standard.showGP',['lp'=>$c->getLp(),'ga'=>$c->getGa(),'gp'=>$c->getGp(), 'nom'=>$tool->urlFriendly($c->getIntitule())]));
                                }
                                
                            $xw->endElement();
                            $xw->startElement("priority");
                                $xw->text("0.9");
                            $xw->endElement();
                        $xw->endElement();
                    }
                }

                //Produit
                $xw->startElement("url");
                    $xw->startElement("loc");
                        $xw->text($S.$this->getContainer()->router->pathFor('ligne-produit.index'));
                    $xw->endElement();
                    $xw->startElement("priority");
                        $xw->text("0.8");
                    $xw->endElement();
                $xw->endElement();

                foreach ($lps as $lp) {
                    $xw->startElement("url");
                        $xw->startElement("loc");
                            $xw->text($S.$this->getContainer()->router->pathFor('ligne-produit.show',['id'=>$lp->getId(),'lp'=>$lp->getLp(), 'nom'=>$tool->urlFriendly($lp->getIntitule())]));
                        $xw->endElement();
                        $xw->startElement("priority");
                            $xw->text("0.8");
                        $xw->endElement();
                    $xw->endElement();
                }

                foreach ($gas as $ga) {
                    $xw->startElement("url");
                        $xw->startElement("loc");
                            $xw->text($S.$this->getContainer()->router->pathFor('groupe-article.show',['lp'=>$ga->getLp(),'ga'=>$ga->getGa(), 'nom'=>$tool->urlFriendly($ga->getIntitule())]));
                        $xw->endElement();
                        $xw->startElement("priority");
                            $xw->text("0.8");
                        $xw->endElement();
                    $xw->endElement();
                }

                foreach ($gps as $gp) {
                    $xw->startElement("url");
                        $xw->startElement("loc");
                            $xw->text($S.$this->getContainer()->router->pathFor('groupe-produit.show',['lp'=>$gp->getLp(),'ga'=>$gp->getGa(),'gp'=>$gp->getGp(), 'nom'=>$tool->urlFriendly($gp->getIntitule())]));
                        $xw->endElement();
                        $xw->startElement("priority");
                            $xw->text("0.8");
                        $xw->endElement();
                    $xw->endElement();
                }

                //Metrologie
                $xw->startElement("url");
                    $xw->startElement("loc");
                        $xw->text($S.$this->getContainer()->router->pathFor('metrologie.index'));
                    $xw->endElement();
                    $xw->startElement("priority");
                        $xw->text("0.6");
                    $xw->endElement();
                $xw->endElement();

                //Services
                $xw->startElement("url");
                    $xw->startElement("loc");
                        $xw->text($S.$this->getContainer()->router->pathFor('service.index'));
                    $xw->endElement();
                    $xw->startElement("priority");
                        $xw->text("0.5");
                    $xw->endElement();
                $xw->endElement();

                foreach ($services as $s) {
                    $xw->startElement("url");
                        $xw->startElement("loc");
                            $xw->text($S.$this->getContainer()->router->pathFor('service.show',['id'=>$s->getId(), 'nom'=>$tool->urlFriendly($s->getIntitule())]));
                        $xw->endElement();
                        $xw->startElement("priority");
                            $xw->text("0.5");
                        $xw->endElement();
                    $xw->endElement();
                }


                //ACtualites
                $xw->startElement("url");
                    $xw->startElement("loc");
                        $xw->text($S.$this->getContainer()->router->pathFor('actualite.index'));
                    $xw->endElement();
                    $xw->startElement("priority");
                        $xw->text("0.6");
                    $xw->endElement();
                $xw->endElement();

                foreach ($actus as $a) {
                    $xw->startElement("url");
                        $xw->startElement("loc");
                            $xw->text($S.$this->getContainer()->router->pathFor('actualite.show',['id'=>$a->getId(),'nom'=>$tool->urlFriendly($a->getIntitule())]));
                        $xw->endElement();
                        $xw->startElement("priority");
                            $xw->text("0.6");
                        $xw->endElement();
                    $xw->endElement();
                }

                //Entreprise
                $xw->startElement("url");
                    $xw->startElement("loc");
                        $xw->text($S.$this->getContainer()->router->pathFor('entreprise.index'));
                    $xw->endElement();
                    $xw->startElement("priority");
                        $xw->text("0.5");
                    $xw->endElement();
                $xw->endElement();

                //Contact
                $xw->startElement("url");
                    $xw->startElement("loc");
                        $xw->text($S.$this->getContainer()->router->pathFor('contact.index'));
                    $xw->endElement();
                    $xw->startElement("priority");
                        $xw->text("0.6");
                    $xw->endElement();
                $xw->endElement();

                foreach ($dpts as $d) {
                    $xw->startElement("url");
                        $xw->startElement("loc");
                            $xw->text($S.$this->getContainer()->router->pathFor('contact.show',['dept'=>$d->getNum()]));
                        $xw->endElement();
                        $xw->startElement("priority");
                            $xw->text("0.6");
                        $xw->endElement();
                    $xw->endElement();
                }


                //legal
                $xw->startElement("url");
                    $xw->startElement("loc");
                        $xw->text($S.$this->getContainer()->router->pathFor('legal.index'));
                    $xw->endElement();
                    $xw->startElement("priority");
                        $xw->text("0.4");
                    $xw->endElement();
                $xw->endElement();

                //Recherche
                $xw->startElement("url");
                    $xw->startElement("loc");
                        $xw->text($S.$this->getContainer()->router->pathFor('recherche.index'));
                    $xw->endElement();
                    $xw->startElement("priority");
                        $xw->text("0.6");
                    $xw->endElement();
                $xw->endElement();

            $xw->endElement();
        $xw->endDocument();
        return $xw->outputMemory();
    }



}