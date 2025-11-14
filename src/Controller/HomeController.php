<?php

namespace App\Controller;

use App\Entity\Answer;
use App\Repository\AnswerRepository;
use Doctrine\ORM\EntityManagerInterface;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

final class HomeController extends AbstractController
{
    public function __construct(
        private AnswerRepository $answerRepo,
        private EntityManagerInterface $em
    ) {}

    #[Route('/home', name: 'app_home')]
    public function index(): Response
    {
        return $this->render('home/index.html.twig');
    }

    #[Route('/admin/answers/export/{formId}', name: 'admin_answers_export')]
    public function exportXls(int $formId): Response
    {
        // Récupération des réponses pour un formulaire donné
        $answers = $this->answerRepo->findBy(['form' => $formId]);

        if (!$answers) {
            return new Response("Aucune réponse à exporter.");
        }

        /** @var Answer $first */
        $first = $answers[0];
        $form = $first->getForm();
        $fields = $form->getFields(); // Champs définis dans l’admin

        // -------- Excel ----------
        $spreadsheet = new Spreadsheet();
        /** @var \PhpOffice\PhpSpreadsheet\Worksheet\Worksheet $sheet */
        $sheet = $spreadsheet->getActiveSheet();

        // Ligne des titres
        $col = 1;
        foreach ($fields as $field) {
            $sheet->setCellValueByColumnAndRow($col, 1, $field->getLabel());
            $col++;
        }

        // Colonnes supplémentaires
        $sheet->setCellValueByColumnAndRow($col, 1, 'Date');
        $col++;

        $sheet->setCellValueByColumnAndRow($col, 1, 'ID');
        $col++;

        // Remplir les réponses
        $row = 2;

        foreach ($answers as $answer) {

            $data = $answer->getData();
            $col = 1;

            foreach ($fields as $field) {
                $name = $field->getName();

                if (!array_key_exists($name, $data)) {
                    $sheet->setCellValueByColumnAndRow($col, $row, 'ERREUR');
                } else {
                    $value = $data[$name];

                    if (is_array($value)) {
                        $value = implode(', ', $value);
                    }

                    $sheet->setCellValueByColumnAndRow($col, $row, $value);
                }

                $col++;
            }

            // Date
            $sheet->setCellValueByColumnAndRow($col, $row, $answer->getCreatedAt()->format('d/m/Y H:i'));
            $col++;

            // ID réponse
            $sheet->setCellValueByColumnAndRow($col, $row, $answer->getId());

            $row++;
        }

        // ---------- Fichier ----------
        $writer = new Xlsx($spreadsheet);

        $filename = 'export_formulaire_' . $form->getSlug() . '.xlsx';
        $tempfile = sys_get_temp_dir() . '/' . $filename;

        $writer->save($tempfile);

        return new Response(file_get_contents($tempfile), 200, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);
    }
}
