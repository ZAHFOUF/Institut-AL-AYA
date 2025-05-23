<?php

namespace AlAya\Common\Service ;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Component\HttpFoundation\Response;

class Export
{

    
       /**
     * Export a result to excel file
     *
     * @param mixed             $list       list array 
     * @param string            $fileName   name of the file by default "export"
     *                          
     */

    public function toExcel($list , $fileName = "export", $response = true ,$options = []) 
    
    {

              // Generate Keys
              $head=array_keys($list[0]);

       
       
              $data[] = array_values( $head ) ;
              
              
              foreach ($list as  $row) {
                  $data[] = array_values($row);
              }
              


                   // Create a new Spreadsheet
                   $spreadsheet = new Spreadsheet();
        
                   // Set data
                   $spreadsheet->getActiveSheet()->fromArray($data, null, 'A1');
             
                   $styleArray = [
                     'borders' => [
                         'allBorders' => [
                             'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                         ],
                     ],
                 ];

                 $str =  "ABCDEFGHIJKLMNOPQRSTUVWXYZ" ;
                 $line = $str[count($list[0]) - 1]  ;

                
                 
                 $spreadsheet->getActiveSheet()->getStyle("A1:$line".count($list) + 1)->applyFromArray($styleArray);
                 
             
             
                 $styleArray = [
                     'alignment' => [
                         'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                     ],
                     'font' => [
                         'bold' => true,
                         'size' => 12,
                         'color' => ['rgb' => '000'],
                     ],
                     'borders' => [
                         'outline' => [
                             'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                         ],
                     ],
                 ];
                 
                 $spreadsheet->getActiveSheet()->getStyle("A1:$line"."1")->applyFromArray($styleArray);
                 $spreadsheet->getActiveSheet()->getStyle("A1:$line".count($list[0])+1)->getAlignment()->setWrapText(true);


                 // Options
                 
                 if ($options['numberRanges'] ?? false) {
                    foreach ($options['numberRanges'] as $range) {
                        $spreadsheet->getActiveSheet()->getStyle($range.count($list) + 1)->getNumberFormat()->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_NUMBER_00);
                    }
                 }

             
                 $spreadsheet->getActiveSheet()->getDefaultColumnDimension()->setWidth(150, 'pt');
             
             
                 if ($response) {
                     // Save the Excel file
                   $writer = new Xlsx($spreadsheet);
             
                   // Create a Response
                   $response = new Response();
             
                   // Set headers for Excel download
                   $response->headers->set('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
     
     
                   $response->headers->set('Content-Disposition', 'attachment;filename=' . $fileName . ".xlsx");
                   $response->headers->set('Cache-Control', 'max-age=0');
             
                   // Save the file content to the response
                   ob_start();
                   $writer->save('php://output');
                   $response->setContent(ob_get_clean());
             
                   return $response;
                 }else{
                    return [ 'spreadsheet' => $spreadsheet , "countList" => count($list) + 1 ] ;
                 }
                  
     
        
    }


      /**
     * Export a result to csv File (using temp memory)
     *
     * @param mixed             $list       list array 
     * @param string            $fileName   name of the file by default "export"
     *                          
     */

    public function toCsv($list,$fileName = "export") : Response
    {


        $data = [] ;

        foreach ($list as  $row) {
            $data[] = array_map(function ($value)  {return $this->removeNewlinesFromStr($value); }, $row);
        }

        // CSV header row
        $headerRow = array_keys($data[0]);

        // Start output buffering
        ob_start();

        // Open memory handle for writing
        $file = fopen('php://memory', 'w');

        // Write UTF-8 BOM to ensure proper encoding
        fwrite($file, "\xEF\xBB\xBF");

        // Write header row
        fputcsv($file, $headerRow,";");

        // Write data rows
        foreach ($data as $row) {
           fputcsv($file, $row,";");
        }

        // Rewind memory pointer
        rewind($file);

        // Read memory handle contents
        $csvContents = stream_get_contents($file);

        // Close memory handle
        fclose($file);

        // End output buffering and get contents
        $output = ob_get_clean();

        $response = new Response($csvContents);

        $response->headers->set('Content-Type', 'text/csv; charset=utf-8');
        $response->headers->set('Content-Disposition', "attachment;filename=$fileName" . ".csv");
        $response->headers->set('Cache-Control', 'max-age=0');

        return $response ;
        
    }

    private function removeNewlinesFromStr($str) {
       return str_replace("\r\n","",$str);
    }
    
}
