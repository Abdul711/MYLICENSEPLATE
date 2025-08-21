<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\LicensePlate;
use App\Models\Bank;
use App\Models\plate_challan;
use Illuminate\Support\Facades\File;
use Barryvdh\DomPDF\Facade\Pdf;
use Spatie\Browsershot\Browsershot;
use Illuminate\Support\Facades\View;

class GeneratePlateChallans extends Command
{
    // Command signature
    protected $signature = 'plates:generate-challans';

    // Command description
    protected $description = 'Generate PDFs for license plates without existing challans';

    public function handle()
    {
        $provinceLogos = [
            'Punjab'      => public_path('glogo/punjab.jpeg'),
            'Sindh'       => public_path('glogo/sindh.png'),
            'KPK'         => public_path('glogo/KP_logo.png'),
            'Balochistan' => public_path('glogo/balochistan.jpeg'),
        ];

        $banks = Bank::all();
        $dueDate = now()->addMonths(2)->format('d M Y');
        $pdfFolder = public_path('pdfs');

        if (!File::exists($pdfFolder)) {
            File::makeDirectory($pdfFolder, 0755, true);
        }
        $imageFolder = public_path('licenseimages');

        if (!File::exists($imageFolder)) {
            File::makeDirectory($imageFolder, 0755, true);
        }

        $remainingCount = LicensePlate::doesntHave('challan')->count();
        $this->info("Total plates without challan: {$remainingCount}");

        LicensePlate::with('user')
            ->doesntHave('challan')
            ->chunk(100, function ($plates) use ($provinceLogos, $banks, $dueDate, $pdfFolder) {
                foreach ($plates as $plate) {
                    $user = $plate->user;
                    $invoiceNumber = 'INV-' . rand(100000, 999999);
                    $provinceLogo = $provinceLogos[$plate->region] ?? null;
                    $paymentMethod = "Bank";

                    $fileName = 'plate_challan_' . $plate->id . '.pdf';
                    $filePath = $pdfFolder . '/' . $fileName;
                    $fileNameImage =  $plate->plate_number . date("d-F-Y") . time() . '.png';
                    $imagePath = public_path('licenseimages/' .  $fileNameImage);

                      $challan = plate_challan::updateOrCreate(
                        [
                            'licenseplate_id' => $plate->id, // condition
                        ],
                        [
                            'status'   => 'unpaid',
                            // example
                            "invoice_number" =>  $invoiceNumber,
                            "pdf_path" => $fileName,
                            "image_path" =>     $fileNameImage,
                            'due_date' => $plate->created_at->copy()->addMonths(2),
                        ]
                    );

                    if (!File::exists($imagePath)) {
                        $html = View::make('plates.plate_template', [
                            'plate' => $plate,
                            'provinceLogo' => $provinceLogo
                        ])->render();
                        Browsershot::html($html)
                            ->windowSize(400, 200) // adjust size
                            ->timeout(60000)
                            ->save($imagePath);
                        $this->info("PNG Image generated for Plate ID {$plate->id}");
                    }
                    // Delete old PDF if exists
                    if (!File::exists($filePath)) {






                        $pdf = Pdf::loadView('pdf.plate_challan', [
                            'plate'        => $plate,
                            'banks'        => $banks,
                            'dueDate'      => $dueDate,
                            'user'         => $user,
                            'provinceLogo' => $provinceLogo,
                            'paymentMethod' => $paymentMethod,
                            'LatePaymentPenalty' => 500,
                            'invoiceNumber' => $invoiceNumber
                        ])->setPaper('A4', 'portrait');


                        $pdf->save($filePath);
                        $this->info("PDF generated for Plate ID {$plate->id}");
                    }
                  
                }
            });

        $this->info("Plate challans generation completed.");
    }
}
