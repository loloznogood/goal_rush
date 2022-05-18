<?php

namespace src\controller\page;

use http\Env\Request;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\RequestInterface;
use src\model\dao\DAOFactory;
use src\model\metier\Commercial;

class ContactController extends PageController
{
    private $filAriane;

    private $mapCoords;
    public function __construct($container)
    {
        parent::__construct($container);

        $this->filAriane = [
            'Accueil' => $this->getContainer()->router->pathFor('accueil.index'),
            'Contact'=> $this->getContainer()->router->pathFor('contact.index')
        ];

        //key = num département, coordonnees pour l'image /img/jumo_france_2016.png
        $this->mapCoords = [
            '01' => "274,203,276,195,279,192,280,189,282,185,281,184,278,186,274,188,270,188,267,189,262,186,255,182,252,182,250,186,248,191,248,196,247,199,248,203,251,206,255,208,258,207,262,205,266,207,268,210,271,213,275,206",
            '02' => "203,57,204,52,209,50,218,50,224,50,228,52,232,55,230,61,228,65,226,71,226,74,222,74,218,76,218,78,219,81,218,82,216,84,217,88,215,92,212,92,209,89,206,86,204,82,201,78,203,73,204,68,204,66,205,62",
            '03' => "204,198,211,199,216,201,220,203,222,202,222,197,221,192,223,191,226,190,227,184,223,182,221,179,219,175,216,174,214,176,211,175,209,176,207,176,203,174,201,171,199,173,195,175,192,176,194,175,190,180,189,183,185,183,183,185,184,188,190,195,194,193,198,192,199,193,202,194,201,197",
            '04' => "306,249,306,252,304,254,306,257,306,259,306,262,302,264,301,268,303,271,305,275,306,277,303,279,301,282,299,282,295,282,293,283,289,283,287,285,284,286,281,285,280,286,278,287,273,283,271,281,270,278,269,272,270,269,273,269,277,268,279,268,280,264,282,261,284,258,286,259,291,256,296,258",
            '05' => "274,267,271,263,269,261,267,259,269,256,272,255,272,253,274,250,276,248,280,245,284,243,288,243,290,241,290,237,288,235,288,232,293,234,296,238,292,242,287,246,281,250,279,254,283,258,284,264,285,267,288,272,288,275,287,276,293,275,297,276,302,275,305,233,298,236,300,237,302,241,305,243,307,245,308,244,310,248,298,255,296,258,294,257,292,255,291,254,287,257,285,255,278,263",
            '06' => "308,263,314,265,319,266,321,268,328,266,329,269,327,274,324,275,324,276,323,280,321,283,320,283,314,285,314,288,312,290,309,290,308,289,307,286,305,283,303,283,304,278,307,278,307,273,305,272,304,268,303,266,303,264,306,261",
            '07' => "225,248,229,244,232,241,238,238,239,232,241,228,244,227,246,225,249,229,249,234,249,240,249,244,248,249,246,254,246,258,245,264,241,263,240,264,236,263,235,265,232,262,229,261",
            '08' => "231,53,234,54,240,52,242,51,243,47,246,44,244,50,246,53,247,57,248,59,252,60,255,62,258,64,259,65,257,67,254,67,252,69,252,70,252,74,251,77,248,79,244,78,241,78,238,76,235,77,233,74,229,73",
            '09' => "142,318,141,315,144,313,147,311,146,309,149,306,153,308,154,306,157,305,161,303,164,305,166,306,170,308,172,309,172,313,172,317,170,319,172,321,173,322,175,324,177,327,171,327,168,329,168,330",
            '10' => "212,111,211,106,213,102,216,103,218,105,222,106,224,102,226,100,231,99,234,98,233,102,236,105,238,106,241,104,242,108,244,110,247,113,246,118,246,120,244,122,240,124,238,126,234,126,231,127,228,128,223,126",

            '11' => "178,327,180,326,183,324,181,321,185,319,192,321,197,317,201,319,203,314,203,310,206,308,204,304,201,302,198,300,195,303,190,302,189,298,185,298,182,298,179,299,174,298,171,298,169,299,166,302,167,305,169,307,172,307,174,313,173,317,172,320",
            '12' => "174,271,177,269,182,271,186,275,190,281,192,285,198,285,201,287,204,283,208,282,212,279,212,276,209,273,211,270,206,267,204,260,202,253,199,247,197,242,192,241,190,245,188,251,186,253,182,252,178,254,173,256,171,259",
            '13' => "238,295,245,289,250,281,255,283,262,286,269,289,273,291,273,296,273,302,272,304",
            '14' => "99,73,101,77,105,81,104,84,100,89,99,94,103,94,108,92,114,92,118,92,125,92,130,90,136,88,137,86,136,80,134,74,132,72,124,75,122,76",
            '15' => "179,252,185,251,187,249,189,245,191,241,196,240,198,243,201,247,202,246,203,242,207,242,208,239,208,235,206,231,204,228,199,227,192,224,191,223,189,222,187,225,185,225,183,227,181,231,179,236,177,239",
            '16' => "123,229,126,227,129,225,128,222,132,219,136,213,140,209,144,206,147,202,146,199,143,197,140,196,138,196,133,197,133,197,127,197,124,197,122,199,119,201,119,204,118,207,115,209,113,209,113,209,112,212,115,218,114,223",
            '17' => "93,190,93,188,96,184,99,186,103,187,104,191,107,194,113,196,116,198,119,201,116,204,114,206,112,208,110,210,110,214,112,216,113,220,113,223,116,226,120,230,121,232,118,234,113,230,111,228,109,226,104,222,100,217,95,211,92,211,94,198",
            '18' => "184,141,189,144,195,148,197,152,199,158,201,166,200,169,196,171,192,173,188,177,184,179,182,176,180,170,180,164,178,158,175,156,182,151,183,146",
            '19' => "162,238,158,237,156,231,155,226,155,221,161,218,166,215,173,213,177,213,182,213,186,211,188,214,186,216,188,220,181,226,180,230,178,233,177,237,175,240,170,240,167,238",

            '21' => "237,128,241,127,245,127,247,130,248,134,250,137,254,140,258,142,261,145,261,149,261,155,259,159,256,160,254,161,251,162,247,162,243,162,239,159,236,156,234,154,233,151,232,147",
            '22' => "35,93,38,99,37,104,37,109,40,113,48,113,54,115,59,118,64,115,69,115,72,111,77,109,76,102,67,97,59,100,52,93,50,89,41,88",
            '23' => "162,198,161,193,159,190,160,186,165,185,172,184,178,184,180,185,190,196,191,199,189,204,186,207,187,210,183,211,178,210,175,208,174,211,169,207,163,204",
            '24' => "127,230,131,225,135,219,139,216,144,216,148,219,150,222,153,226,153,232,155,235,156,241,154,246,152,248,148,250,143,248,138,247,134,248,131,245,127,240,124,239,125,233",
            '25' => "281,177,290,171,290,162,302,147,299,142,289,142,281,147,271,152,277,162,283,168",
            '26' => "247,263,249,251,252,242,251,228,250,225,254,225,258,229,260,234,264,236,267,236,267,240,268,245,274,248,270,253,267,257,267,262,269,264,272,265,272,266,269,268,261,265,260,263,255,260,252,264",
            '27' => "136,70,140,69,146,71,149,73,152,77,157,75,161,72,164,72,170,74,171,78,169,79,168,84,163,85,163,90,160,96,156,96,151,96,148,98,145,97,141,91,138,90",
            '28' => "147,120,158,126,166,124,172,122,175,116,174,108,170,104,166,99,164,92,160,96,154,97,148,100,150,104",
            '29' => "10,95,18,93,25,92,29,94,33,92,36,98,34,106,36,110,32,114,34,120,39,121,38,125,36,127,32,126,28,124,26,122,23,123,22,120,21,123,17,124,10,115,16,112,12,108,12,106,19,106,16,101,10,103,6,102",
            '30' => "213,280,214,275,211,272,211,270,214,271,220,270,222,270,227,269,226,265,227,260,229,263,231,265,236,265,240,264,245,265,246,267,249,272,250,275,250,276,248,278,244,280,241,285,238,290,234,293,233,292,233,288,231,284,226,281,222,277",

            '31' => "140,318,141,313,143,311,144,308,149,305,152,304,154,303,157,302,162,301,164,301,166,301,168,297,172,296,172,294,168,291,166,288,163,282,161,279,155,281,153,283,146,282,152,289,146,298,141,297,135,302,132,307,135,314,133,316,130,322,135,323",
            '32' => "134,300,128,298,123,297,123,294,118,289,111,290,114,282,115,277,120,278,122,274,126,274,132,273,138,273,142,274,143,279,146,283,148,287,149,290,146,294,146,296",
            '33' => "93,218,102,228,106,227,110,232,116,235,120,238,121,244,126,247,124,251,121,256,120,262,118,264,114,267,110,267,108,262,102,259,96,258,92,255,88,256",
            '34' => "208,307,209,304,215,303,217,301,222,297,229,293,232,291,230,288,226,284,222,281,218,281,213,284,212,281,208,284,205,285,204,289,200,289,197,291,195,291,192,291,191,293,192,297,191,301,195,300,197,298,202,301,206,303",
            '35' => "78,98,82,102,85,103,88,106,92,107,95,105,98,105,98,109,96,114,97,119,97,126,93,127,89,128,87,128,84,130,79,131,75,133,75,127,74,124,72,122,70,116,75,114,80,112",
            '36' => "153,187,158,186,164,184,173,183,180,183,177,176,175,171,176,168,176,162,170,160,170,157,167,154,163,155,160,156,159,159,157,163,153,163,152,166,151,169,150,173,148,175",
            '37' => "139,137,146,140,149,140,152,145,153,152,157,154,158,158,155,161,152,161,150,165,148,170,148,173,146,172,143,168,141,164,136,164,132,164,130,160,125,157,128,147,132,140",
            '38' => "249,226,248,224,248,220,250,218,251,215,254,214,258,211,256,209,259,209,262,208,264,208,266,210,267,213,270,215,272,217,274,221,277,223,279,220,284,221,285,225,284,228,286,232,288,236,288,239,287,242,284,243,279,244,276,246,275,247,270,244,269,240,269,238,270,236,269,234,262,232,260,228,258,225,253,224,253,223",
            '39' => "266,153,269,153,270,155,272,159,272,163,276,164,279,168,281,171,280,175,281,179,282,183,279,183,276,186,272,187,267,187,263,184,264,183,262,171,264,168,260,166",
            '40' => "86,257,79,290,109,291,111,291,111,285,112,282,112,279,114,275,118,277,120,275,122,272",

            '41' => "141,134,147,136,153,139,154,143,154,148,156,150,160,154,162,152,168,153,173,152,177,150,177,146,179,145,178,140,179,138,172,138,165,135,164,132,164,128,159,128,147,122",
            '42' => "224,192,227,193,230,194,237,193,238,194,235,197,234,201,238,206,236,211,240,215,246,218,247,219,245,224,239,227,237,225,230,224,224,223,226,219,221,210",
            '43' => "204,225,210,223,218,224,226,225,231,225,236,228,238,230,236,233,236,236,231,239,229,242,226,244,224,245,218,243,214,244",
            '44' => "72,135,82,134,86,132,92,131,96,134,97,139,100,146,90,147,94,155,87,161,83,163,73,158,68,153,72,147,63,148,62,143,69,140,71,140",
            '45' => "166,126,166,132,172,134,176,136,183,137,192,140,198,143,200,139,198,134,202,132,202,128,202,123,195,120,188,120,185,114,177,114,176,118,176,121,175,122",
            '46' => "161,239,166,242,174,242,176,245,177,250,177,252,174,253,169,255,169,260,169,263,163,265,156,266,150,262,149,257",
            '47' => "124,270,121,268,118,266,122,262,122,256,125,252,127,248,129,250,132,251,136,252,142,253,146,254,147,256,147,261,143,262,145,265,142,268,140,270,133,272,125,272",
            '48' => "206,263,205,257,204,251,202,248,204,244,207,244,210,240,212,245,216,245,221,246,223,248,224,254,227,257,226,259,224,261,224,264,225,266,223,268,220,267,216,268,214,268,211,267",
            '49' => "100,132,106,135,113,136,122,139,128,142,126,148,123,154,120,155,112,156,109,158,105,159,100,159,97,157,96,155,96,150,100,148,103,145,102,141,99,135",
            '50' => "107,100,103,95,99,96,96,91,97,84,102,82,98,76,98,70,95,64,95,60,81,58,88,95,92,100,102,101,104,103",

            '51' => "228,75,234,78,241,79,247,81,250,85,250,90,247,92,248,97,250,100,244,104,240,104,236,100,231,96,230,97,228,96,225,97,224,100,222,100,218,102,215,100,212,97,219,84,222,78",
            '52' => "251,101,258,106,262,111,269,117,268,120,272,127,270,131,270,135,262,136,260,137,256,135,253,134,246,123,250,118,248,110,246,106",
            '53' => "102,106,101,114,102,120,100,127,98,128,102,129,107,130,112,130,116,126,118,120,120,117,122,113,123,110,124,107,121,104",
            '54' => "262,68,265,70,268,72,272,86,270,89,270,105,275,108,298,106,302,102,291,98,280,90,274,78,273,71",
            '55' => "259,68,264,73,269,84,267,94,269,105,264,106,266,107,263,107,252,99,250,92,254,88,251,81,256,70",
            '56' => "40,127,40,120,37,118,37,113,47,116,52,118,58,121,65,118,67,118,70,124,69,131,70,136,66,138,62,140",
            '57' => "276,70,280,68,286,70,290,78,296,80,298,78,302,80,310,79,314,82,314,85,309,86,303,83,300,86,304,92,308,94,306,101,304,103,302,98,296,97,289,93,285,90,281,86,276,79",
            '58' => "202,170,206,172,211,172,214,172,221,172,225,171,228,169,228,164,227,160,230,156,228,151,224,149,221,148,215,145,214,143,211,146,207,144,203,141,201,144,203,152",
            '59' => "180,12,184,20,188,25,194,26,198,26,200,32,203,36,205,40,204,44,204,48,213,47,218,46,228,48,227,44,225,37,203,22,189,10",
            '60' => "171,71,170,63,171,60,184,63,192,66,202,62,202,68,199,73,200,78,202,82,202,84,197,85,191,83,182,81,178,80,174,80,172,80",

            '61' => "108,102,109,98,107,95,110,94,117,94,123,93,129,92,136,91,139,93,142,96,146,100,147,108,146,110,145,116,145,115,140,114,136,110,134,106,130,107,127,108,121,100,112,102",
            '62' => "171,16,178,15,181,20,187,26,194,29,198,32,200,38,201,44,201,46,195,46,189,43,186,41,178,40,175,36,169,33",
            '63' => "191,219,189,215,189,208,190,204,192,198,194,194,197,195,200,199,204,200,211,201,216,203,220,206,219,210,220,213,222,215,223,217,223,219,223,221,220,222,209,220,202,224",
            '64' => "78,291,115,291,118,299,107,316,106,319,81,307,73,296",
            '65' => "110,317,120,300,119,293,124,299,134,302,131,306,132,311,132,314,128,322,117,323",
            '66' => "167,331,184,326,184,321,202,320,204,333,189,338,173,336",
            '67' => "314,82,327,85,326,92,321,98,315,116,307,112,304,110,303,104,306,99,306,91,301,89,301,86,310,88,313,87",
            '68' => "300,130,305,133,306,138,312,142,316,137,317,126,314,117,305,113",
            '69' => "239,195,236,199,238,203,238,207,240,212,243,214,247,216,249,217,250,213,254,210,254,212,254,209,245,198,246,196,246,192,245,190,238,190",
            '70' => "266,152,262,146,264,140,266,137,269,136,271,131,279,125,286,127,290,127,294,128,297,133,296,137,294,141",

            '71' => "231,158,241,166,245,167,258,165,261,169,261,180,261,182,254,181,251,180,248,185,248,188,243,187,239,187,238,188,237,190,236,191,235,192,231,192,227,190,226,180,223,178,222,175,229,170,230,168",
            '72' => "116,130,123,133,130,139,138,134,144,122,136,114,133,109,128,111,126,110",
            '73' => "277,204,276,208,273,212,274,214,275,218,276,220,280,219,284,219,287,222,288,224,288,226,287,228,290,230,293,230,298,230,302,230,306,228,309,225,311,220,306,215,302,210,297,209,294,204,290,208,285,213",
            '74' => "276,198,286,192,286,188,291,187,294,185,300,187,301,193,303,198,307,201,302,205,299,207,294,203,290,205,288,210,283,209,278,206",
            '75' => "183,93,186,95",
            '76' => "169,54,168,70,162,69,152,75,142,67,134,69,129,66,132,60,161,48",
            '77' => "190,85,202,86,210,93,210,110,204,112,201,113,200,118,190,117,187,112,192,99",
            '78' => "167,85,173,87,180,89,179,91,180,95,178,98,176,101,175,104,174,105,169,100,166,89",
            '79' => "102,163,112,161,121,160,123,168,124,176,122,184,126,189,126,193,121,196,120,198,112,194,107,190,108,184,107,176,106,170",
            '80' => "169,40,173,43,179,45,186,48,195,50,200,52,201,57,197,60,190,61,181,59,174,58,170,49",

            '81' => "163,278,166,274,170,272,175,271,180,273,183,276,185,276,187,280,190,285,195,286,197,288,190,288,190,293,190,296,186,295,181,296,177,296,170,291",
            '82' => "147,263,150,265,157,268,165,266,170,264,170,268,166,270,164,272,162,275,161,277,157,277,154,278,153,280,150,280,148,280,145,277,144,272",
            '83' => "277,306,282,308,291,308,299,305,300,304,300,300,304,294,307,293,304,288,300,284,293,285,287,286,281,288,277,288",
            '84' => "248,266,256,265,264,267,266,270,267,276,273,284,271,285,269,286,264,284,257,281,252,278",
            '85' => "73,160,79,164,85,166,92,160,97,162,100,165,103,169,104,173,104,175,104,178,104,181,105,185,98,182,93,183,90,186,86,183,79,178,71,166",
            '86' => "125,159,130,164,139,166,142,172,145,175,147,178,150,182,149,186,146,188,142,190,144,194,142,193,138,194,135,193,131,194,128,192,128,185,126,185",
            '87' => "146,194,146,191,149,189,153,188,156,188,158,190,159,197,160,200,163,206,170,210,168,212,164,213,161,215,159,216,156,218,154,216,150,212,148,212,144,211,149,201",
            '88' => "263,110,272,110,289,110,299,109,302,105,304,113,300,120,297,127,283,122,275,122,273,123",
            '89' => "202,119,203,113,212,113,216,119,221,126,224,130,231,129,233,133,230,136,230,140,228,143,227,146,227,148,222,145,215,142,212,142,209,143,204,140,202,137,206,125",
            '90' => "306,142,303,143,298,140,298,135,301,135,305,139",

            '91' => "181,99,186,101,188,106,186,111,181,111,177,111,177,108",
            '92' => "183,97,181,94,181,92",
            '93' => "186,90,188,89,190,92,188,93,186,91",
            '94' => "186,97,186,95,189,94,189,98,186,97",
            '95' => "171,81,180,82,187,84,188,86,187,87,184,88,181,88,174,85,170,83",
            '2a' => "358,331,363,323,374,316,378,307,380,319,384,332,382,343,379,348",
            '2b' => "358,333,378,350,377,366,363,355"
        ];
    }

    public function index(RequestInterface $request, ResponseInterface $response, array $args)
    {


        if(!is_null($request->getParsedBody()['departement'])){
            $dept = $request->getParsedBody()['departement'];
            return $this->redirect($response, "contact.show", ['dept' => $dept]);
        }




        $daoFactory = new DAOFactory($this->getContainer()->db);
        $deptDAO = $daoFactory->getDepartementDAO();

        $depts = $deptDAO->findAll();

        $pageDAO = $daoFactory->getPageDAO();
        $page = $pageDAO->find('contact');

        $dataForView = array(
            'depts' => $depts,
            'coords' => $this->mapCoords,
            'filAriane' => $this->filAriane,
            'page'      => $page,
            'footer' => $this->getFooterData()
        );

        $this->render($response, 'contact.html.twig', ['data' => $dataForView]);

    }

    public function show(RequestInterface $request, ResponseInterface $response, array $args)
    {
        $deptNum = $args['dept'];
        $daoFactory = new DAOFactory($this->getContainer()->db);
        $deptDAO = $daoFactory->getDepartementDAO();
        $commercialDAO = $daoFactory->getCommercialDAO();
        $dept = $deptDAO->find($deptNum);
        $depts = $deptDAO->findAll();

        if(!$dept){
            return $this->redirect($response, "contact");
        }


        $dir = $commercialDAO->find($dept->getIdDir());
        $itc = $commercialDAO->find($dept->getIdItc());
        $tcs = $commercialDAO->find($dept->getIdTcs());
        $ac = $commercialDAO->find($dept->getIdAc());
        $departement = $dept->getNom();
        $secteur = $dept->getSecteur();



        $dataForView = [
            'depts' => $depts,
            'coords' => $this->mapCoords,
            'dir' => $dir,
            'itc' => $itc,
            'tcs' => $tcs,
            'ac'  => $ac,
            'secteur' => $secteur,
            'departement' => $departement,
            'filAriane' => $this->filAriane,
            'footer' => $this->getFooterData()
        ];


        $this->render($response, 'contact.html.twig', ['data' => $dataForView]);
    }
}