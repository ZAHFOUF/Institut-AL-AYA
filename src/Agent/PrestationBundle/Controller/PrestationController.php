<?php

namespace AlAya\Agent\PrestationBundle\Controller;

use AlAya\Agent\CommonBundle\Attribute\Access;
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
use AlAya\Agent\PrestationBundle\Form\PayementAddType;
use AlAya\Agent\PrestationBundle\WorkFlow\WorkFlowPrestation;
use AlAya\Common\Entity\Bill;
use AlAya\Common\Entity\Payement;
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
    #[Access()]
    public function index(Request $request,WorkFlowPrestation $workFlowPrestation): Response
    {
        // Assurez-vous d'importer PrestationFormType et l'entité Prestation
        // use AlAya\Agent\PrestationBundle\Form\PrestationFormType;
        // use AlAya\Agent\PrestationBundle\Entity\Prestation;

        $prestations = $this->repo(Prestation::class)->all();
        $prestation = new Prestation();
        $prestation->setStatus("brouillon");
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
    #[Access()]
    public function show(Request $request, Prestation $prestation): Response
    { 
        // Formulaire d'ajout de session
        $session = new Session();
        $session->setPrestation($prestation);
        $form = $this->createForm(SessionAddType::class, $session);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            debiteHoursCours($prestation, $session->getHours());
            $this->doctrine->persist($prestation);
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
        // Formulaire d'ajout de paiement
        $payement = new \AlAya\Common\Entity\Payement();
        $formPayement = $this->createForm(PayementAddType::class, $payement);
        $formPayement->handleRequest($request);
        if ($formPayement->isSubmitted() && $formPayement->isValid()) {
            $this->doctrine->persist($payement);
            $this->doctrine->flush();
            return $this->redirectToRoute('back_prestation_show', ['prestation' => $prestation->getId()]);
        }
        // Paiements existants
        $payements = $this->repo(Payement::class)->getBills($prestation);
        $bills = $this->repo(\AlAya\Common\Entity\Bill::class)->findBy(['prestation' => $prestation],['id' => 'DESC']);
        return $this->render('@AgentPrestationBundle/show.html.twig',[
            'prestation' => $prestation,
            'sessions' => $this->repo(Session::class)->findBy(['prestation' => $prestation]),
            'prestationLines' => $this->repo(PrestationLine::class)->findBy(['prestation' => $prestation]),
            'form' => $form->createView(),
            'formPrestationLine' => $formPrestationLine->createView(),
            'formPayement' => $formPayement->createView(),
            'payements' => $payements,
            'total' => calculerTotalPrestation($prestation) ,
            'bills' => $bills
        ]);
    }

    #[Route('/facture/{bill}/pdf', name: 'facture_pdf')]
    public function generatePdf(Bill $bill): Response
    {
        // 🔁 Simule des données pour l'exemple (à remplacer par la vraie entité Facture)
        $prestation = $bill->getPrestation();
        $facture = [
            'numero' => 'FACTURE_'. $bill->getId(),
            'date' => new \DateTime(),
            'total' => amountBill($bill),
            'forfait' => [
                ['programme' => $prestation->getProgramme()->getName(), 'type' => $prestation->getFormula()->getName(), 
                'quantite' => calculerHeuresCours($prestation), 'tarif' => prixPrestation($prestation), 'total' => calculerTotalHeuresCours($prestation)],
            ],
            'extras' =>$prestation->getPrestationLines()->map(function(PrestationLine $line) use ($bill) {
                if ($line->getBill() == $bill) {
                    return [
                    'label' => $line->getFormula()->getName(),
                    'amount' => $line->getFormula()->getPrice() * $line->getQte()
                ];
                }
            })->filter(fn($item) => $item !== null)->toArray(),
        ];

        $eleve = [
            'nom' => 'Zahra',
            'prenom' => 'Fatima',
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
            $dompdf->stream('facture_'.$facture['numero'].'.pdf', ["Attachment" => false]),
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


    #[Route('/status/{prestation}', name: 'back_prestation_status' )]
    public function status(Prestation $prestation,WorkFlowPrestation $workFlowPrestation) {
        $workFlowPrestation->apply($prestation, $this->request->get('transition'));
        return $this->redirectToRoute('back_prestation_show',['prestation' => $prestation->getId()]);
    }
    
}

