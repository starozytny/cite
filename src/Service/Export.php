<?php


namespace App\Service;


use Doctrine\ORM\EntityManagerInterface;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Exception;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\RouterInterface;

class Export
{
    private $exportDirectory;
    protected $router;
    protected $em;

    public function __construct($exportDirectory, RouterInterface $router, EntityManagerInterface $em)
    {
        $this->exportDirectory = $exportDirectory; 

        $this->router = $router;
        $this->em = $em;

        $this->createFolderIfNotExist($exportDirectory);
    }

    protected function setEmptyIfNull($val){
        if($val == null){
            return "";
        }else{
            return $val;
        }
    }

    public function createFile($type, $title, $filename, $header, $data, $max, $url, $folder="")
    {
        $spreadsheet = new Spreadsheet();

        $exportDirectory = $this->getExportDirectory() . '/' . $folder;
        $this->createFolderIfNotExist($exportDirectory);
        $file = $exportDirectory . $filename;
        if (file_exists($file)) {
            unlink($file);
        }

        try {
            $sheet = $spreadsheet->getActiveSheet();
        } catch (Exception $e) {
            return new JsonResponse(array(
                'code' => 0,
                'message' => 'Try catch active sheet : ' . $e
            ));
        }

        $sheet->setTitle($title);
        $sheet->setShowGridlines(false);

        // Fill excel header
        $this->fill($header, $max, $sheet, 1);
        $this->fill($data, $max, $sheet, 2);

        // Create your Office 2007 Excel (XLSX Format)
        if($type == 'excel'){
            $writer = new Xlsx($spreadsheet);
        }else{
            $writer = new \PhpOffice\PhpSpreadsheet\Writer\Csv($spreadsheet);
            $writer->setUseBOM(false);
            $writer->setDelimiter(';');
            $writer->setEnclosure('');
            $writer->setLineEnding("\r\n");
        }

        // Create the file
        try {
            $writer->save($file);
        } catch (\PhpOffice\PhpSpreadsheet\Writer\Exception $e) {
            return new JsonResponse(array(
                'code' => 0,
                'message' => 'Try catch save : ' . $e
            ));
        }

        // Return a text response to the browser saying that the excel was succesfully created
        return new JsonResponse(array(
            'code' => 1,
            'message' => 'Fichier généré.',
            'url' => $url
        ));
    }

    protected function fill($data, $max, $sheet, $begin){
        $letters = array(
            'A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z',
            'AA','AB','AC','AD','AE','AF','AG','AH','AI','AJ','AK','AL','AM','AN','AO','AP','AQ','AR','AS','AT','AU','AV','AW','AX','AY','AZ',
            'BA', 'BB', 'BC','BD','BE','BF','BG','BH','BI'
        );

        // DATA
        $styleArray = [
            'alignment' => [
                'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                ],
            ],
        ];

        $i = 0;
        foreach ($letters as $letter) {
            if($i < $max){
                $j = $begin;
                foreach ($data as $item) {
                    $sheet->setCellValueExplicit($letter . $j, $item[$i], DataType::TYPE_STRING);
                    $sheet->getStyle($letter . $j)->applyFromArray($styleArray);
                    $j++;
                }
                $i++;
            }
        }

        // STYLE HEADER
        $styleArray = [
            'font' => [
                'bold' => true,
            ],
            'alignment' => [
                'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
            ],
            'borders' => [
                'bottom' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_MEDIUM,
                ],
            ],
        ];

        $i = 0;
        $j = 1;
        foreach ($letters as $letter) {
            if($i < $max){
                $sheet->getColumnDimension($letter)->setAutoSize(true);
                $sheet->getRowDimension($j)->setRowHeight(25);
                $sheet->getStyle($letter . $j)->applyFromArray($styleArray);
                $sheet->getStyle($letter . $j)
                    ->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID);
                $sheet->getStyle($letter . $j)
                    ->getFill()->getStartColor()->setARGB('FF11ACE4');
            }
            $i++;
        }

        $this->styleMainCell($data, $sheet);

    }

    protected function styleMainCell($data, $sheet){
        //STYLE A COL
        for($i=2 ; $i<count($data)+2; $i++){
            $sheet->getRowDimension($i)->setRowHeight(25);
            $sheet->getStyle('A' . $i)
                ->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID);
            $sheet->getStyle('A' . $i)
                ->getFill()->getStartColor()->setARGB('FFBFFFFF');
        }
    }

    protected function createFolderIfNotExist($folder){
        if (!file_exists($folder)) {
            mkdir($folder, 0755, true);
        }
    }

    /**
     * @return mixed
     */
    public function getExportDirectory()
    {
        return $this->exportDirectory;
    }
}
