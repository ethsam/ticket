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

    #[Route('/', name: 'app_home')]
    public function index(): Response
    {
        return $this->render('home/index.html.twig');
    }

    #[Route('/admin/answers/export/{formId}', name: 'admin_answers_export')]
    public function exportXls(int $formId): Response
    {
        // Réponses triées du plus récent au plus ancien
        $answers = $this->answerRepo->findBy(
            ['form' => $formId],
            ['id' => 'DESC']
        );

        if (!$answers) {
            return new Response("Aucune réponse à exporter.");
        }

        /** @var Answer $first */
        $first = $answers[0];
        $form = $first->getForm();
        $fields = $form->getFields();

        // -------- Excel ----------
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // ---------------------------------------
        // TITRES
        // ---------------------------------------
        $col = 1;

        // 1) ID
        $sheet->setCellValue($this->colToLetter($col).'1', 'ID');
        $col++;

        // 2) Validé
        $sheet->setCellValue($this->colToLetter($col).'1', 'Validé');
        $col++;

        // 3) Validé le
        $sheet->setCellValue($this->colToLetter($col).'1', 'Validé le');
        $col++;

        // 4) Champs dynamiques
        foreach ($fields as $field) {
            $sheet->setCellValue($this->colToLetter($col).'1', $field->getLabel());
            $col++;
        }

        // 5) Date de création
        $sheet->setCellValue($this->colToLetter($col).'1', 'Date');
        $col++;

        // ---------------------------------------
        // LIGNES
        // ---------------------------------------
        $row = 2;

        foreach ($answers as $answer) {

            $col = 1;

            // 1) ID
            $sheet->setCellValue($this->colToLetter($col).$row, $answer->getId());
            $col++;

            // 2) Validé (Oui/Non)
            $sheet->setCellValue(
                $this->colToLetter($col).$row,
                $answer->isValidate() ? 'Oui' : 'Non'
            );
            $col++;

            // 3) Validé le (date ou vide)
            $sheet->setCellValue(
                $this->colToLetter($col).$row,
                $answer->getValidateAt() ? $answer->getValidateAt()->format('d/m/Y H:i') : ''
            );
            $col++;

            // 4) Champs dynamiques
            $data = $answer->getData();

            foreach ($fields as $field) {
                $name = $field->getName();

                if (!array_key_exists($name, $data)) {
                    $sheet->setCellValue($this->colToLetter($col).$row, 'ERREUR');
                } else {
                    $value = $data[$name];
                    if (is_array($value)) {
                        $value = implode(', ', $value);
                    }
                    $sheet->setCellValue($this->colToLetter($col).$row, $value);
                }

                $col++;
            }

            // 5) Date création réponse
            $sheet->setCellValue(
                $this->colToLetter($col).$row,
                $answer->getCreatedAt()->format('d/m/Y H:i')
            );

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

    // Conversion numéro -> lettre (A, B, C... AA, AB...)
    private function colToLetter(int $col): string
    {
        $letters = '';
        while ($col > 0) {
            $col--;
            $letters = chr(65 + ($col % 26)) . $letters;
            $col = intdiv($col, 26);
        }
        return $letters;
    }
}
