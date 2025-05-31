<?php

namespace AlAya\Agent\PrestationBundle\Service;

use AlAya\Common\Entity\PrestationLine;
use Dompdf\Dompdf;
use Dompdf\Options;

class BillGenerator
{

    public function __construct(private \Twig\Environment $twig)
    {
    }

    public function generateBill($prestation,$file = true) 
    {
       // 🔁 Simule des données pour l'exemple (à remplacer par la vraie entité Facture)
        $facture = [
            'numero' => 'FAC2025001',
            'date' => new \DateTime(),
            'total' => calculerTotalPrestation($prestation),
            'forfait' => [
                ['programme' => $prestation->getProgramme()->getName(), 'type' => $prestation->getFormula()->getName(), 
                'quantite' => calculerHeuresCours($prestation), 'tarif' => prixPrestation($prestation), 'total' => calculerTotalHeuresCours($prestation)],
            ],
            'extras' =>$prestation->getPrestationLines()->map(function(PrestationLine $line) {
                if (!$line->isPayed()) {
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
        return $dompdf;
    }


    public function renderView(string $template, array $context = []): string
    {
        return $this->twig->render($template, $context);
    }
}