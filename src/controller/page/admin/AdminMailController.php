<?php

namespace src\controller\page\admin;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\RequestInterface;
use src\model\dao\DAOFactory;
use \src\controller\page\PageController;
use src\model\metier\Mail;

class AdminMailController extends PageController
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
            'Menu administrateur' => $this->getContainer()->router->pathFor('admin/home.index'),
            'Gestion des mailings' => $this->getContainer()->router->pathFor('admin/mail.index')

        ];
    }


    public function index(RequestInterface $request, ResponseInterface $response, array $args)
    {
        try {

            $daoFactory = new DAOFactory($this->getContainer()->db);
            $mailDAO = $daoFactory->getMailDAO();
            $mails = $mailDAO->findAll();
            $dataForView = [
                'mails' => $mails,
                'filAriane' => $this->filAriane,
                'footer' => $this->getFooterData()
            ];

            $this->render($response, 'admin-mail-index.html.twig', ['data' => $dataForView]);
        } catch (\Exception $e) {
            echo $e->getMessage();
        }
    }

    public function create(RequestInterface $request, ResponseInterface $response, array $args)
    {
        try {
            $this->filAriane['Créer un nouveau mailing'] = $this->getContainer()->router->pathFor('admin/mail.create');
            $dataForView = ['filAriane' => $this->filAriane, 'footer' => $this->getFooterData()];
            $this->render($response, 'admin-mail-create.html.twig', ['data' => $dataForView, 'footer' => $this->getFooterData()]);
        } catch (\Exception $e) {
            $dataForView = [
                'error' => 'Un problème est survenu lors du traitement de ce mailing.',
                'filAriane' => $this->filAriane,
                'footer' => $this->getFooterData()
            ];

            $this->render($response, 'admin-mail-create.html.twig', ['data' => $dataForView]);
        }
    }

    public function store(RequestInterface $request, ResponseInterface $response, array $args)
    {
        try {
            $this->filAriane['Créer un nouveau mailing'] = $this->getContainer()->router->pathFor('admin/mail.create');

            $id = 0;
            $nom_camp = $request->getParsedBody()['nom_camp'];
            $comp_camp = 0;
            $nb_camp = $request->getParsedBody()['nb_camp'];

            if (empty(trim($nom_camp))) {
                throw new \Exception("Identifiant incorrect.");
            }

            if (empty(trim($nb_camp))) {
                throw new \Exception("Nombre d'envois incorrect.");
            }

            $daoFactory = new DAOFactory($this->getContainer()->db);
            $mailDAO = $daoFactory->getMailDAO();

            $mail = new Mail($id, $nom_camp, $comp_camp, $nb_camp);
            $mailDAO->create($mail);
            return $this->redirect($response, "admin/mail.index");
        } catch (\Exception $e) {
            $dataForView = [
                'error' => $e->getMessage(),
                'p' => [
                    'nom_camp'        => $nom_camp,
                    'comp_camp'     => $comp_camp,
                    'nb_camp'   => $nb_camp
                ],
                'filAriane' => $this->filAriane,
                'footer' => $this->getFooterData()
            ];

            $this->render($response, 'admin-mail-create.html.twig', ['data' => $dataForView]);
        }
    }

    public function edit(RequestInterface $request, ResponseInterface $response, array $args)
    {
        try {
            $id = $args['id'];
            $this->filAriane['Modifier un mail'] = $this->getContainer()->router->pathFor('admin/mail.edit', ['id' => $id]);
            $daoFactory = new DAOFactory($this->getContainer()->db);
            $mailDAO = $daoFactory->getMailDAO();
            $mail = $mailDAO->find($id);

            if (is_null($mail)) {
                throw new \Exception("Page inconnue.");
            }

            $dataForView = [
                'mail'    => $mail,
                'filAriane' => $this->filAriane,
                'footer' => $this->getFooterData()
            ];

            $this->render($response, 'admin-mail-edit.html.twig', ['data'  => $dataForView]);
        } catch (\Exception $e) {
            $dataForView = [
                'error' => $e->getMessage(),
                'filAriane' => $this->filAriane,
                'footer' => $this->getFooterData()
            ];
            $this->render($response, 'admin-mail-edit.html.twig', ['data'  => $dataForView]);
        }
    }

    public function update(RequestInterface $request, ResponseInterface $response, array $args)
    {
        try {
            $id = $args['id'];
            $this->filAriane['Modifier un mail'] = $this->getContainer()->router->pathFor('admin/mail.edit', ['id' => $id]);
            $id2 = $request->getParsedBody()['id'];
            $nom_camp = $request->getParsedBody()['nom_camp'];
            $comp_camp = $request->getParsedBody()['comp_camp'];
            $nb_camp = $request->getParsedBody()['nb_camp'];

            if ($id != $id2) {
                throw new \Exception("Identifiant incorrect.");
            }

            if (empty(trim($nom_camp))) {
                throw new \Exception("Identifiant incorrect.");
            }

            if (empty(trim($nb_camp))) {
                throw new \Exception("Nombre d'envois incorrect.");
            }

            $daoFactory = new DAOFactory($this->getContainer()->db);

            $mailDAO = $daoFactory->getMailDAO();
            $mailNew = new Mail($id2, $nom_camp, $comp_camp, $nb_camp);
            $mailDAO->update($mailNew);


            return $this->redirect($response, "admin/mail.edit", ['id' => $id]);
        } catch (\Exception $e) {

            $dataForView = [
                'error' => $e->getMessage(),
                'mail' => [
                    'nom_camp'        => $nom_camp,
                    'comp_camp'     => $comp_camp,
                    'nb_camp'   => $nb_camp
                ],
                'filAriane' => $this->filAriane,
                'footer' => $this->getFooterData()
            ];
            $this->render($response, 'admin-mail-edit.html.twig', ['data'  => $dataForView]);
        }
    }

    public function delete(RequestInterface $request, ResponseInterface $response, array $args)
    {
        try {

            $nom_camp = $args['id'];
            $daoFactory = new DAOFactory($this->getContainer()->db);
            $mailDAO = $daoFactory->getMailDAO();
            $mail = $mailDAO->find($nom_camp);
            $mails = $mailDAO->findAll();
            $mailDAO->delete($mail);
            return $this->redirect($response, "admin/mail.index");
        } catch (\Exception $e) {
            $dataForView = [
                'mails' => $mails,
                'error' => 'Le groupe produit  n\'a pas été supprimé.',
                'filAriane' => $this->filAriane,
                'footer' => $this->getFooterData()
            ];

            $this->render($response, 'admin-mail-index.html.twig', ['data' => $dataForView]);
        }
    }
}
