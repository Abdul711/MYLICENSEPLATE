<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Region;
use League\Csv\Writer;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\View;
use Spatie\Browsershot\Browsershot;

class GeneratePlatesCsv extends Command
{
    protected $signature = 'plates:csv';
    protected $description = 'Generate license plates CSV, PDFs, and optional PNG images per province';

    public function handle()
    {
        ini_set('memory_limit', '1024M'); // 1GB memory
        ini_set('max_execution_time', 0); // unlimited execution time

        $provinces = Region::with('cities')->get();
        $letters = range('A', 'Z');
        $exportData = [];
    $totalImages = 0; 
        // --- Folders ---
        $provinceLogos = [
            'Punjab'      => public_path('glogo/punjab.jpeg'),
            'Sindh'       => public_path('glogo/sindh.png'),
            'KPK'         => public_path('glogo/KP_logo.png'),
            'Balochistan' => public_path('glogo/balochistan.jpeg'),
        ];

        $exportCsvFolder = public_path('exports_csv');
        if (!file_exists($exportCsvFolder)) mkdir($exportCsvFolder, 0777, true);

        $exportPdfFolder = public_path('exports_pdf');
        if (!file_exists($exportPdfFolder)) mkdir($exportPdfFolder, 0777, true);

        $exportPngFolder = public_path('plates_image');
        if (!file_exists($exportPngFolder)) mkdir($exportPngFolder, 0777, true);

        foreach ($provinces as $province) {
            $this->info("Generating plates for {$province->region_name}...");

            $usedPlates = [];
            $cities = $province->cities->pluck('id', 'city_name')->toArray();
            $provincePlates = [];
            $provinceImages = 0;
            for ($i = 0; $i < 100; $i++) {
                $cityName = array_rand($cities);
                $cityId = $cities[$cityName];

                // --- Generate unique plate ---
                do {
                    $plateNumber = $letters[array_rand($letters)]
                        . $letters[array_rand($letters)]
                        . $letters[array_rand($letters)]
                        . '-'
                        . rand(100, 999);
                } while (in_array($plateNumber, $usedPlates));

                $usedPlates[] = $plateNumber;
                $price = rand(1000, 4000);

                $row = [
                    'Province' => $province->region_name,
                    'City' => $cityName,
                    'Plate Number' => $plateNumber,
                    'Price' => $price,
                    'Status' => 'Available'
                ];

                $exportData[] = $row;
                $provincePlates[] = $row;
            }

            // --- Generate PNG images (optional) ---
            $provinceLogo = $provinceLogos[$province->region_name] ?? null;
            foreach ($provincePlates as $row) {
                $html = View::make('plates.plate_templates', [
                    'plate' => $row,
                    'provinceLogo' => $provinceLogo
                ])->render();

                $fileNameImage = $row['Plate Number'] . '_' . date("d-F-Y") . time() . '.png';
                $imagePath = $exportPngFolder . '/' . $fileNameImage;

                Browsershot::html($html)
                    ->windowSize(400, 200)
                    ->timeout(60000)
                    ->save($imagePath);
                $this->info("Image Png Created for {$province->region_name} {$provinceImages} at {$imagePath}");
                $provinceImages++;
                   $totalImages++;
            }
            $this->info("Total  {$provinceImages} Png Image Created for {$province->region_name} ");
            // --- PDF per province ---
            $pdfPath = $exportPdfFolder . '/' . $province->region_name . '_plates.pdf';
            $pdf = Pdf::loadView('plates.export_pdf', ['plates' => $provincePlates]);
            $pdf->save($pdfPath);
            $this->info("PDF exported for {$province->region_name} at {$pdfPath}");
        }

        // --- Single CSV for all provinces ---
        $csvPath = $exportCsvFolder . '/license_plates.csv';
        $csv = Writer::createFromPath($csvPath, 'w+');
        $csv->insertOne(array_keys($exportData[0]));
        foreach ($exportData as $row) {
            $csv->insertOne($row);
        }

        $this->info("CSV file created successfully at: {$csvPath}");
        $this->info("All exports completed!");
           $this->info("Total PNG images created: {$totalImages}");
    }
}
