<?php

namespace src\controller\page\admin;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\RequestInterface;
use \src\controller\page\PageController;
use \src\model\ToolModel;


class AdminRobotsTxtController extends PageController
{
    const DROITS = 10;

    private $filAriane;
    private $fileName;
    private $path;
    private $fileData;
    /**
     * AdminRobotsTxtController constructor.
     */
     public function __construct($container)
     {
        $tool = new ToolModel();
        parent::__construct($container);
        $this->fileName = "robots.txt";
        $this->path = $tool->getProjectAbsolutePath().'/tmp';
        $this->fileData = ["User-agent: *".PHP_EOL, "Disallow: /admin".PHP_EOL, "Sitemap: https://www.jumo-france.fr/sitemap.xml".PHP_EOL];
        $this->filAriane = [
            'Accueil' => $this->getContainer()->router->pathFor('accueil.index'),
            'Menu administrateur'=> $this->getContainer()->router->pathFor('admin/home.index'),
            'Fichier robots.txt'=> $this->getContainer()->router->pathFor('admin/robots-txt.index')

        ];
     }
 
 
     public function index(RequestInterface $request, ResponseInterface $response, array $args)
     {
         try {
             $dataForView = [
                 'filAriane' => $this->filAriane,
                 'footer' => $this->getFooterData()
             ];
             $this->render($response, 'admin-robots-txt-index.html.twig', ['data' => $dataForView]);
         }
         catch(\Exception $e){
            $dataForView = [
                'filAriane' => $this->filAriane,
                'footer' => $this->getFooterData(),
                'error' => $e->getMessage()
            ];
            $this->render($response, 'admin-robots-txt-index.html.twig', ['data' => $dataForView]);
         }
 
     }

    public function download(RequestInterface $request, ResponseInterface $response, array $args)
    {
        try {
            $file = $this->makeFile();
            file_put_contents($file, $this->fileData);

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
           $this->render($response, 'admin-robots-txt-index.html.twig', ['data' => $dataForView]);
        }
    }

    public function replace(RequestInterface $request, ResponseInterface $response, array $args)
    {
        try {
            $file = $this->makeFile();
            $tool = new ToolModel();
            file_put_contents($file, $this->fileData);
            copy($file, $tool->getSiteAbsolutePath().'/'.$this->fileName);

            $dataForView = [
                'filAriane' => $this->filAriane,
                'footer' => $this->getFooterData(),
                'success' => "Le fichier robots.txt a bien été remplacé."
            ];
            $this->render($response, 'admin-robots-txt-index.html.twig', ['data' => $dataForView]);
        }
        catch(\Exception $e){
           $dataForView = [
               'filAriane' => $this->filAriane,
               'footer' => $this->getFooterData(),
               'error' => $this->$e->getMessage()
           ];
           $this->render($response, 'admin-robots-txt-index.html.twig', ['data' => $dataForView]);
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



}