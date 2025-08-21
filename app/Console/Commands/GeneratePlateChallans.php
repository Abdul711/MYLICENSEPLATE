<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\LicensePlate;
use App\Models\Bank;
use Illuminate\Support\Facades\File;
use Barryvdh\DomPDF\Facade\Pdf;

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

                    // Delete old PDF if exists
                    if (!File::exists($filePath)) {
                       
                    

                    $pdf = Pdf::loadView('pdf.plate_challan', [
                        'plate'        => $plate,
                        'banks'        => $banks,
                        'dueDate'      => $dueDate,
                        'user'         => $user,
                        'provinceLogo' => $provinceLogo,
                        'paymentMethod'=> $paymentMethod,
                        'LatePaymentPenalty' => 500,
                        'invoiceNumber'=> $invoiceNumber
                    ])->setPaper('A4', 'portrait');

                    $pdf->save($filePath);
                    $this->info("PDF generated for Plate ID {$plate->id}");
                }
            }
            });

        $this->info("Plate challans generation completed.");
    }
}
