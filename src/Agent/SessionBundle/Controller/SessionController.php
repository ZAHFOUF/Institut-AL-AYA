<?php

namespace AlAya\Agent\SessionBundle\Controller;
use AlAya\Agent\CommonBundle\Controller\Controller;
use Doctrine\Persistence\ManagerRegistry;
use Dompdf\Dompdf;
use Dompdf\Options;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\HttpFoundation\Request;

#[Route("/prestations")]
class SessionController extends Controller
{

    public $em;
    public $doctrine;

    public function __construct(ManagerRegistry $doctrine)
    {
        $this->doctrine = $doctrine;
        $this->em = $this->doctrine->getManager();
    }

    #[Route("/",name:"back_session_index")]
    public function index(): Response
    { 
       return $this->render('@AgentSessionBundle/index.html.twig');
    }

    #[Route("/show",name:"back_session_show")]
    public function show(): Response
    { 
       return $this->render('@AgentSessionBundle/show.html.twig');
    }

    #[Route('/facture/{id}/pdf', name: 'facture_pdf')]
    public function generatePdf(int $id): Response
    {
        // 🔁 Simule des données pour l'exemple (à remplacer par la vraie entité Facture)
        $facture = [
            'numero' => 'FAC2025001',
            'date' => new \DateTime(),
            'total' => 36,
            'forfait' => [
                ['programme' => 'Tuhfat Al-Atfâl', 'type' => 'Groupe', 'quantite' => 4, 'tarif' => 7, 'total' => 28],
            ],
            'extras' => [
                ['label' => 'PDF Tuhfat Al-Atfal', 'amount' => 8],
            ],
        ];

        $eleve = [
            'nom' => 'Zahra',
            'prenom' => 'Fatima',
            'email' => 'zahra@example.com',
        ];

        // 📄 Génération HTML via Twig
        $html = $this->renderView('@AgentSessionBundle/pdf.twig', [
            'facture' => $facture,
            'eleve' => $eleve
        ]);

        // ⚙️ Configuration DomPDF
        $options = new Options();
        $options->set('defaultFont', 'DejaVu Sans');
        $dompdf = new Dompdf($options);
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        // 📥 Retour en téléchargement
        return new Response(
            $dompdf->stream('facture_'.$facture['numero'].'.pdf', ["Attachment" => true]),
            Response::HTTP_OK,
            ['Content-Type' => 'application/pdf']
        );
    }

    
#[Route('/facture/send', name: 'facture_send')]
public function sendFacture(
    MailerInterface $mailer
): Response {
    // Simule des données (à remplacer par la vraie entité Facture)
    $facture = [
        'numero' => 'FAC2025001',
        'date' => new \DateTime(),
        'total' => 98,
        'forfait' => [
            ['programme' => 'Nouraniyyah', 'type' => 'Individuel', 'quantite' => 10, 'tarif' => 7, 'total' => 70],
        ],
        'extras' => [
            ['label' => 'Frais d’inscription', 'amount' => 8],
            ['label' => 'PDF Tuhfat Al-Atfal', 'amount' => 20],
        ],
    ];

    $eleve = [
        'nom' => 'Zahra',
        'prenom' => 'Fatima',
        'email' => 'zahra@example.com',
    ];

    // Génération HTML via Twig
    $html = $this->renderView('@AgentSessionBundle/pdf.twig', [
        'facture' => $facture,
        'eleve' => $eleve
    ]);

    // Génération PDF
    $options = new Options();
    $options->set('defaultFont', 'DejaVu Sans');
    $dompdf = new Dompdf($options);
    $dompdf->loadHtml($html);
    $dompdf->setPaper('A4', 'portrait');
    $dompdf->render();
    $pdfOutput = $dompdf->output();

    // Création de l'email
    $email = (new Email())
        ->from('no-reply@institut-al-aya.com')
        ->to($eleve['email'])
        ->subject('Votre facture')
        ->html($this->renderView('@AgentSessionBundle/mail.twig'))
        ->attach($pdfOutput, 'facture_'.$facture['numero'].'.pdf', 'application/pdf');

    $mailer->send($email);

    return $this->redirectToRoute('back_session_show', [
        'id' => 1,
    ]);
}
    
}

