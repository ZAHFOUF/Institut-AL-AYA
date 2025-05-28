<?php

namespace AlAya\Agent\PrestationBundle\Controller;
use AlAya\Agent\CommonBundle\Controller\Controller;
use AlAya\Common\Controller\BaseController;
use AlAya\Common\Entity\Group;
use AlAya\Common\Entity\Prestation;
use AlAya\Common\Entity\PrestationLine;
use AlAya\Common\Entity\Session;
use AlAya\Common\Entity\Student;
use AlAya\Common\Form\PrestationFormType;
use AlAya\Agent\PrestationBundle\Form\SessionAddType;
use AlAya\Agent\PrestationBundle\Form\PrestationLineAddType;
use Doctrine\Persistence\ManagerRegistry;
use Dompdf\Dompdf;
use Dompdf\Options;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\HttpFoundation\Request;

#[Route("/prestations")]
class PrestationController extends BaseController
{


    #[Route("/", name: "back_prestation_index")]
    public function index(Request $request): Response
    {
        // Assurez-vous d'importer PrestationFormType et l'entité Prestation
        // use AlAya\Agent\PrestationBundle\Form\PrestationFormType;
        // use AlAya\Agent\PrestationBundle\Entity\Prestation;

        $prestations = $this->repo(Prestation::class)->findAll();
        $prestation = new Prestation();
        $form = $this->createForm(PrestationFormType::class, $prestation);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $prestation->setCreatedAt(new \DateTime());
            $this->doctrine->persist($prestation);
            $this->doctrine->flush();

            return $this->redirectToRoute('back_prestation_index');
        }

        return $this->render('@AgentPrestationBundle/index.html.twig', [
            'form' => $form->createView(),
            'prestations' => $prestations,
        ]);
    }

    #[Route("/show/{prestation}",name:"back_prestation_show")]
    public function show(Request $request, Prestation $prestation): Response
    { 
        // Formulaire d'ajout de session
        $session = new Session();
        $session->setPrestation($prestation);
        $form = $this->createForm(SessionAddType::class, $session);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->doctrine->persist($session);
            $this->doctrine->flush();
            return $this->redirectToRoute('back_prestation_show', ['prestation' => $prestation->getId()]);
        }
        // Formulaire d'ajout de prestation supplémentaire
        $prestationLine = new PrestationLine();
        $prestationLine->setPrestation($prestation);
        $formPrestationLine = $this->createForm(PrestationLineAddType::class, $prestationLine);
        $formPrestationLine->handleRequest($request);
        if ($formPrestationLine->isSubmitted() && $formPrestationLine->isValid()) {
            $this->doctrine->persist($prestationLine);
            $this->doctrine->flush();
            return $this->redirectToRoute('back_prestation_show', ['prestation' => $prestation->getId()]);
        }
        return $this->render('@AgentPrestationBundle/show.html.twig',[
            'prestation' => $prestation,
            'sessions' => $this->repo(Session::class)->findBy(['prestation' => $prestation]),
            'prestationLines' => $this->repo(PrestationLine::class)->findBy(['prestation' => $prestation]),
            'form' => $form->createView(),
            'formPrestationLine' => $formPrestationLine->createView(),
        ]);
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
        $html = $this->renderView('@AgentPrestationBundle/pdf.twig', [
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
    $html = $this->renderView('@AgentPrestationBundle/pdf.twig', [
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
        ->html($this->renderView('@AgentPrestationBundle/mail.twig'))
        ->attach($pdfOutput, 'facture_'.$facture['numero'].'.pdf', 'application/pdf');

    $mailer->send($email);

    return $this->redirectToRoute('back_prestation_show', [
        'id' => 1,
    ]);
}

    #[Route("/show/{prestation}/add-session", name:"back_prestation_add_session")]
    public function addSession(Request $request, Prestation $prestation): Response
    {
        $session = new Session();
        $session->setPrestation($prestation);
        $form = $this->createForm(SessionAddType::class, $session);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->doctrine->persist($session);
            $this->doctrine->flush();
            return $this->redirectToRoute('back_prestation_show', ['prestation' => $prestation->getId()]);
        }

        return $this->render('@AgentPrestationBundle/add_session.html.twig', [
            'form' => $form->createView(),
            'prestation' => $prestation
        ]);
    }
    
}

