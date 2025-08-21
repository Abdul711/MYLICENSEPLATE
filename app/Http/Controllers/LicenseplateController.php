<?php

namespace App\Http\Controllers;

use Box\Spout\Reader\Common\Creator\ReaderEntityFactory;
use App\Http\Requests\LicensePlateRequest;
use Illuminate\Http\Request;
use App\Models\LicensePlate;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf;
use Smalot\PdfParser\Parser;
use setasign\Fpdi\Fpdi;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\File;
use App\Models\City;
use App\Models\Region;
use Carbon\Carbon;
use App\Models\User;
use Alimranahmed\LaraOCR\Facades\OCR;
use App\Models\Bank;
use Illuminate\Support\Facades\Mail;
use Spatie\Browsershot\Browsershot;
use thiagoalessio\TesseractOCR\TesseractOCR;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;
use Illuminate\Support\Str;
use App\Models\plate_challan;
use App\Jobs\ProcessChallansJob;
use ZipArchive;
use App\Jobs\ImportChallansJob;

class LicenseplateController extends Controller
{



    
// $source = public_path('uploads/file.txt');       // Source file
// $destination = public_path('backup/file.txt');   // Destination file

// // Ensure destination directory exists
// if (!File::exists(dirname($destination))) {
//     File::makeDirectory(dirname($destination), 0755, true);
// }

// // Copy the file
// if (File::copy($source, $destination)) {
//     echo "File copied successfully!";
// } else {
//     echo "Failed to copy file.";
// }

    public function remain()
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

        // Create folder if it doesn't exist
        if (!File::exists($pdfFolder)) {
            File::makeDirectory($pdfFolder, 0755, true);
        }

        // Count total plates without challan
        $remainingCount = LicensePlate::doesntHave('challan')->count();
        echo "Total plates without challan: {$remainingCount}<hr>";

        // Process only plates without challan
        LicensePlate::with('user')
            ->doesntHave('challan')
            ->chunk(100, function ($plates) use ($provinceLogos, $banks, $dueDate, $pdfFolder) {

                foreach ($plates as $plate) {
                    $user = $plate->user;
                    $invoiceNumber = 'INV-' . rand(100000, 999999);
                    $provinceLogo = $provinceLogos[$plate->region] ?? null;
                    $paymentMethod = "Bank";

                    // PDF file path
                    $fileName = 'plate_challan_' . $plate->id . '.pdf';
                    $filePath = $pdfFolder . '/' . $fileName;

                    // Delete previous PDF if exists
                    if (File::exists($filePath)) {
                        File::delete($filePath);
                    }

                    // Generate PDF
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

                    // Save PDF
                    $pdf->save($filePath);

                    echo "PDF generated for Plate ID {$plate->id}: <a href='/pdfs/{$fileName}'>Download</a><hr>";
                }
            });
    }

    public function store(LicensePlateRequest $request)
    {
        $plateData = $request->validated();

        $plateData['user_id'] = Auth::id();

        $plate = LicensePlate::create($plateData);
        $banks = Bank::get()->toArray();
        $dueDate = now()->addMonths(2)->format('d M Y');

        $provinceLogos = [
            'Punjab'      => public_path('glogo/punjab.jpeg'),
            'Sindh'       => public_path('glogo/sindh.png'),
            'KPK'         => public_path('glogo/KP_logo.png'),
            'Balochistan' => public_path('glogo/balochistan.jpeg'),
        ];

        $user = Auth::user();
        $provinceLogo = $provinceLogos[$plate->region] ?? null;
        $invoiceNumber = 'INV-' . rand(100000, 999999);
        // Generate PDF
        $paymentMthod = "Bank";
        $pdf = Pdf::loadView('pdf.plate_challan', [
            'plate'        => $plate,
            'banks'        => $banks,
            'dueDate'      => $dueDate,
            'user'         => $user,
            'provinceLogo' => $provinceLogo,
            "paymentMethod" => $paymentMthod,
            "LatePaymentPenalty" => 500,
            "invoiceNumber" => $invoiceNumber
        ])->setPaper('A4', 'portrait');

        // Path to public/challans
        $challanDir = public_path('challans');
        if (!file_exists($challanDir)) {
            mkdir($challanDir, 0777, true);
        }

        $fileName = 'challan_' . $plate->id . '.pdf';
        $filePath = $challanDir . '/' . $fileName;

        // Save PDF
        $pdf->save($filePath);
        $downloadUrl = asset('challans/' . $fileName);



        $html = View::make('plates.plate_template', [
            'plate' => $plate,
            'provinceLogo' => $provinceLogo
        ])->render();


        $fileNameImage =  $plate->plate_number . date("d-F-Y") . time() . '.png';
        $imagePath = public_path('plates/' .  $fileNameImage);
        Browsershot::html($html)
            ->windowSize(400, 200) // adjust size
            ->timeout(60000)
            ->save($imagePath);
        plate_challan::updateOrCreate(
            ['licenseplate_id' => $plate->id],
            [
                'pdf_path'       =>   $fileName,
                'image_path'     => $fileNameImage,
                'invoice_number' => $invoiceNumber,
            ]
        );

        // Convert Blade HTML to PNG


        // Send email with attachment
        Mail::send('emails.plate_challan', compact('plate', 'dueDate', 'user', 'provinceLogo'), function ($message) use ($user, $filePath, $imagePath) {
            $message->to($user->email)

                ->subject('Your License Plate Challan')
                ->attach($imagePath, ['as' => 'Plate.png', 'mime' => 'image/png'])
                ->attach($filePath, [
                    'as'   => 'Plate_Challan.pdf',
                    'mime' => 'application/pdf',
                ]);
        });

        // Optional: make download link
        return redirect(url('plates/' . $plate->id . '/show'));
        // return redirect()->route('home')->with([
        //     'success'      => 'License Plate added successfully!',
        //     'downloadLink' => $downloadUrl
        // ]);
    }


    // return view('customer.plate_detail', compact('plate'));


    public function index(Request $request)
    {
        $query = LicensePlate::query();

        // Filter: Start with
        if ($request->filled('city')) {
            $query->where('city', '=', $request->city);
        }

        if ($request->filled('start_with')) {
            $query->where('plate_number', 'like', $request->start_with . '%');
        }
        if ($request->filled('region')) {
            $query->where('region', '=', $request->region);
        }


        if ($request->filled('contain')) {
            $query->where('plate_number', 'like', '%' . $request->contain . '%');
        }

        if ($request->filled('end_with')) {
            $query->where('plate_number', 'like', '%' . $request->end_with);
        }

        if ($request->filled('length')) {
            $length = (int) $request->length;
            $query->whereRaw("LENGTH(REPLACE(REPLACE(plate_number, ' ', ''), '-', '')) = ?", [$length]);
        }
        if ($request->filled('min_price')) {
            $query->where('price', '>=', $request->min_price);
        }

        // Filter by max price
        if ($request->filled('max_price')) {
            $query->where('price', '<=', $request->max_price);
        }
        if ($request->filled('user')) {
            $query->where('user_id', '=', $request->user);
        }

        if ($request->filled('featured')) {
            $feature = $request->featured;
            if ($feature == "Yes") {
                $featured = 1;
            } else {
                $featured = 0;
            }

            $query->where('featured', '=', $featured);
        }

        //   @if (request()->has('featured') && request('featured') != '')


        // Filter: Contain

        $cities = LicensePlate::select('city')
            ->whereNotNull('city')
            ->distinct()
            ->get();

        $regions = LicensePlate::select('region')
            ->whereNotNull('region')
            ->distinct()
            ->get();

        $user_ids = LicensePlate::select('user_id')
            ->whereNotNull('user_id')
            ->distinct()
            ->pluck('user_id')->toArray();
        $users =    \App\Models\User::select('id', 'name')->whereIn("id", $user_ids)->get();
        // Get the filtered plates
        $query->where('status', "Available"); // Ensure only plates of the authenticated user are fetched

        $plates = $query->paginate(1000)->appends($request->query());
        return view('customer.plates', compact('plates', 'cities', 'regions', 'users'));
    }
 public function exportdownloadadmin(Request $request)
    {
        $filename = "license_plates_" . date('Y-m-d_H-i-s') . ".csv";

        // Headers to force download
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Pragma: no-cache');
        header('Expires: 0');

        $file = fopen('php://output', 'w');

        // Write the header row
        fputcsv($file, ['Province', 'City', 'Plate Number', 'Price', 'Status', 'Owner']);

        // Fetch data from DB
        $page = $request->input('page', 1);
     

        $query = LicensePlate::query();
        if ($request->filled('city')) {
            $query->where('city', '=', $request->city);
        }

        if ($request->filled('start_with')) {
            $query->where('plate_number', 'like', $request->start_with . '%');
        }
        if ($request->filled('region')) {
            $query->where('region', '=', $request->region);
        }


        if ($request->filled('contain')) {
            $query->where('plate_number', 'like', '%' . $request->contain . '%');
        }

        if ($request->filled('end_with')) {
            $query->where('plate_number', 'like', '%' . $request->end_with);
        }

        if ($request->filled('length')) {
            $length = (int) $request->length;
            $query->whereRaw("LENGTH(REPLACE(REPLACE(plate_number, ' ', ''), '-', '')) = ?", [$length]);
        }
        if ($request->filled('min_price')) {
            $query->where('price', '>=', $request->min_price);
        }

        // Filter by max price
        if ($request->filled('max_price')) {
            $query->where('price', '<=', $request->max_price);
        }

        if ($request->filled('user')) {
            $query->where('user_id', '=', $request->user);
        }

        if ($request->filled('featured')) {
            $feature = $request->featured;
            if ($feature == "Yes") {
                $featured = 1;
            } else {
                $featured = 0;
            }

            $query->where('featured', '=', $featured);
        }
        $plates = $query->where("status", "Available")->get();

       


        foreach ($plates as $plate) {
            fputcsv($file, [

                $plate->region,
                $plate->city,
                $plate->plate_number,
                $plate->price,
                $plate->status,
                $plate->user ? $plate->user->name : 'N/A', // Assuming you have a user relationship
            ]);
        }

        fclose($file);
        exit;
    }




    public function export(Request $request)
    {
        $filename = "license_plates_" . date('Y-m-d_H-i-s') . ".csv";

        // Headers to force download
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Pragma: no-cache');
        header('Expires: 0');

        $file = fopen('php://output', 'w');

        // Write the header row
        fputcsv($file, ['Province', 'City', 'Plate Number', 'Price', 'Status', 'Owner']);

        // Fetch data from DB
        $page = $request->input('page', 1);
        $perPage = 1000;

        $query = LicensePlate::query();
        if ($request->filled('city')) {
            $query->where('city', '=', $request->city);
        }

        if ($request->filled('start_with')) {
            $query->where('plate_number', 'like', $request->start_with . '%');
        }
        if ($request->filled('region')) {
            $query->where('region', '=', $request->region);
        }


        if ($request->filled('contain')) {
            $query->where('plate_number', 'like', '%' . $request->contain . '%');
        }

        if ($request->filled('end_with')) {
            $query->where('plate_number', 'like', '%' . $request->end_with);
        }

        if ($request->filled('length')) {
            $length = (int) $request->length;
            $query->whereRaw("LENGTH(REPLACE(REPLACE(plate_number, ' ', ''), '-', '')) = ?", [$length]);
        }
        if ($request->filled('min_price')) {
            $query->where('price', '>=', $request->min_price);
        }

        // Filter by max price
        if ($request->filled('max_price')) {
            $query->where('price', '<=', $request->max_price);
        }

        if ($request->filled('user')) {
            $query->where('user_id', '=', $request->user);
        }

        if ($request->filled('featured')) {
            $feature = $request->featured;
            if ($feature == "Yes") {
                $featured = 1;
            } else {
                $featured = 0;
            }

            $query->where('featured', '=', $featured);
        }
        $plates = $query->where("status", "Available")->get();

        $plates = $query->paginate($perPage, ['*'], 'page', $page);


        foreach ($plates as $plate) {
            fputcsv($file, [

                $plate->region,
                $plate->city,
                $plate->plate_number,
                $plate->price,
                $plate->status,
                $plate->user ? $plate->user->name : 'N/A', // Assuming you have a user relationship
            ]);
        }

        fclose($file);
        exit;
    }
    public function import()
    {
        return view('customer.import_plate');
    }

    // public function importStore(Request $request)
    // {
    //     ini_set('max_execution_time', 0);
    //     set_time_limit(0);

    //     $request->validate([
    //         'file' => 'required|mimes:csv,txt',
    //     ]);

    //     $provinceLogos = [
    //         'Punjab'      => public_path('glogo/punjab.jpeg'),
    //         'Sindh'       => public_path('glogo/sindh.png'),
    //         'KPK'         => public_path('glogo/KP_logo.png'),
    //         'Balochistan' => public_path('glogo/balochistan.jpeg'),
    //     ];

    //     $user    = Auth::user();
    //     $banks   = Bank::all();
    //     $dueDate = now()->addMonths(2)->format('d M Y');

    //     $platesData   = [];
    //     $attachments  = [];
    //     $imageAttachment=[];
    //     $file         = $request->file('file');
    //     $path         = $file->getRealPath();

    //     if (($handle = fopen($path, 'r')) !== false) {
    //         $rowIndex = 0;

    //         while (($row = fgetcsv($handle, 1000, ',')) !== false) {
    //             $rowIndex++;

    //             // Skip header row
    //             if ($rowIndex === 1) {
    //                 continue;
    //             }

    //             // Ensure required fields exist
    //             if (!empty($row[0]) && !empty($row[1]) && !empty($row[2]) && !empty($row[3])) {

    //                 $plate = LicensePlate::updateOrCreate(
    //                     ['plate_number' => trim($row[2])],
    //                     [
    //                         'region'   => trim($row[0]),
    //                         'city'     => trim($row[1]),
    //                         'price'    => trim($row[3]),
    //                         'status'   => $row[4] ?? 'Available',
    //                         'user_id'  => $user->id,
    //                     ]
    //                 );

    //                 $provinceLogo = $provinceLogos[$plate->region] ?? null;

    //                 // Generate PDF challan
    //                 $pdf = Pdf::loadView('pdf.plate_challan', [
    //                     'plate'              => $plate,
    //                     'banks'              => $banks,
    //                     'dueDate'            => $dueDate,
    //                     'user'               => $user,
    //                     'provinceLogo'       => $provinceLogo,
    //                     'paymentMethod'      => "Bank",
    //                     'LatePaymentPenalty' => 500,
    //                     'invoiceNumber'      => 'INV-' . rand(100000, 999999)
    //                 ])->setPaper('A4', 'portrait');

    //                 $challanDir = public_path('challans');
    //                 if (!file_exists($challanDir)) {
    //                     mkdir($challanDir, 0777, true);
    //                 }

    //                 $filePath = $challanDir . '/challan_' . $plate->id . '.pdf';
    //                 $pdf->save($filePath);

    //                 // Collect data for final email
    //                 $platesData[] = [
    //                     'plate'        => $plate,
    //                     'provinceLogo' => $provinceLogo
    //                 ];
    //                 $attachments[] = $filePath;
    //                 $html = View::make('plates.plate_template', [
    //                     'plate' => $plate,
    //                     'provinceLogo' => $provinceLogo
    //                 ])->render();


    //                 $fileNameImage =  $plate->plate_number . date("d-F-Y") . time() . '.png';
    //                 $imagePath = public_path('plates/' .  $fileNameImage);
    //                 Browsershot::html($html)
    //                     ->windowSize(400, 200) // adjust size
    //                     ->timeout(60000)
    //                     ->save($imagePath);


    //             }
    //         }

    //         fclose($handle);
    //     }

    //     // ✅ Send single email with all challans attached
    //     if (!empty($attachments) &&  !empty($imageAttachment) ) {
    //         Mail::send('emails.plate_challan_multiple', [
    //             'platesData' => $platesData,
    //             'dueDate'    => $dueDate,
    //             'user'       => $user
    //         ], function ($message) use ($user, $attachments , $imageAttachment) {
    //             $message->to($user->email)
    //                 ->subject('Your License Plate Challans');

    //             foreach ($attachments as $filePath) {
    //                 $message->attach($filePath, [
    //                     'as'   => basename($filePath),
    //                     'mime' => 'application/pdf',
    //                 ]);
    //             }
    //             foreach($imageAttachment as $imageFileAttachment){
    //                  $message->attach( $imageFileAttachment, ['as' => 'Plate.png', 'mime' => 'image/png']);
    //             }
    //         });
    //     }

    //     return redirect(url('licenseplate'))->with('success', 'Plates imported and one email sent with all challans.');
    // }

    private $totalCreated = 0;
    private $totalUpdated = 0;
    private $insertedIds = []; // ✅ track inserted IDs
    //     public function importStore(Request $request)
    //     {
    //         ini_set('max_execution_time', 0);
    //         set_time_limit(0);
    //         ini_set('memory_limit', '-1');

    //         $request->validate([
    //             'file' => 'required|mimes:csv,txt|max:20480', // 20MB max
    //         ]);

    //         $user = Auth::user();
    //         $file = $request->file('file');
    //         $filePath = $file->getRealPath();

    //         $insertedIds = [];
    //         $zipPaths = [];

    //         $batch = 1;
    //         $batchSize = 5000;
    //         $rows = [];

    //         if (!file_exists(public_path('challans'))) {
    //             mkdir(public_path('challans'), 0777, true);
    //         }

    //         if (($handle = fopen($filePath, 'r')) !== false) {
    //             $rowIndex = 0;

    //             while (($row = fgetcsv($handle, 1000, ',')) !== false) {
    //                 $rowIndex++;
    //                 if ($rowIndex === 1) continue; // skip header

    //                 $rows[] = $row;

    //                 if (count($rows) >= $batchSize) {
    //                     $ids = $this->processBatch($rows, $batch, $zipPaths, $user);
    //                     $insertedIds = array_merge($insertedIds, $ids);
    //                     $rows = [];
    //                     $batch++;
    //                 }
    //             }

    //             if (!empty($rows)) {
    //                 $ids = $this->processBatch($rows, $batch, $zipPaths, $user);
    //                 $insertedIds = array_merge($insertedIds, $ids);
    //             }

    //             fclose($handle);
    //         }

    //         // Send email with links
    //         Mail::send('emails.challan_ready', [
    //             'user' => $user,
    //             'links' => collect($zipPaths)->map(fn($p) => url($p)),
    //             'insertedIds' => $insertedIds,
    //             'total' => count($insertedIds),
    //         ], function ($message) use ($user) {
    //             $message->to($user->email)
    //                 ->subject('Your License Plate Challans Are Ready');
    //         });
    //  return redirect()->back()->with('success', 'CSV uploaded. You will receive an email once challans are ready.');







    // }
    public function importStore(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:csv,txt|max:20480', // 20MB max
        ]);

        $user = Auth::user();
        $file = $request->file('file');

        // store uploaded file in storage/app/imports
        $fileName = time() . '_' . $request->file('file')->getClientOriginalName();
        $destinationPath = public_path('imports/' . $fileName);

        // Move file into public/imports
        $request->file('file')->move(public_path('imports'), $fileName);

        // Dispatch job
        ProcessChallansJob::dispatch($destinationPath, $user->id);

        return redirect()->back()->with(
            'success',
            'CSV uploaded. You will receive an email once challans are ready.'
        );
    }



    // public function importStore(Request $request)
    // {
    //     ini_set('max_execution_time', 0);
    //     set_time_limit(0);
    //     ini_set('memory_limit', '-1');

    //     $request->validate([
    //         'file' => 'required|mimes:csv,txt',
    //     ]);

    //     $user    = Auth::user();
    //     $dueDate = now()->addMonths(2)->format('d M Y');

    //     $provinceLogos = [
    //         'Punjab'      => public_path('glogo/punjab.jpeg'),
    //         'Sindh'       => public_path('glogo/sindh.png'),
    //         'KPK'         => public_path('glogo/KP_logo.png'),
    //         'Balochistan' => public_path('glogo/balochistan.jpeg'),
    //     ];

    //     $banks = Bank::all();
    //     $file  = $request->file('file');
    //     $path  = $file->getRealPath();

    //     $chunkSize   = 100;
    //     $rowIndex    = 0;
    //     $batchIndex  = 1;
    //     $batch       = [];
    //     $zipPaths    = [];

    //     $sessionDir = 'imports/session_' . time();
    //     @mkdir(public_path($sessionDir), 0777, true);

    //     if (($handle = fopen($path, 'r')) !== false) {
    //         while (($row = fgetcsv($handle, 1000, ',')) !== false) {
    //             $rowIndex++;
    //             if ($rowIndex === 1) continue; // skip header

    //             $batch[] = $row;

    //             if (count($batch) === $chunkSize) {
    //                 $zipPaths[] = $this->processBatch(
    //                     $batch, $batchIndex, $user, $banks, $provinceLogos, $dueDate, $sessionDir
    //                 );
    //                 $batch = [];
    //                 $batchIndex++;
    //             }
    //         }
    //         fclose($handle);

    //         if (!empty($batch)) {
    //             $zipPaths[] = $this->processBatch(
    //                 $batch, $batchIndex, $user, $banks, $provinceLogos, $dueDate, $sessionDir
    //             );
    //         }
    //     }

    //     // ✅ email with counts + inserted IDs
    //     Mail::send('emails.challan_ready', [
    //         'user'         => $user,
    //         'links'        => collect($zipPaths)->map(fn($p) => asset($p)),
    //         'totalCreated' => $this->totalCreated,
    //         'totalUpdated' => $this->totalUpdated,
    //         'insertedIds'  => $this->insertedIds,
    //     ], function ($message) use ($user) {
    //         $message->to($user->email)
    //             ->subject('Your Challans Are Ready');
    //     });

    //     return redirect()->back()
    //         ->with('success', "Import completed. {$this->totalCreated} plates created, {$this->totalUpdated} updated. Inserted IDs: " . implode(',', $this->insertedIds));
    // }

    // private function processBatch(
    //     array $rows,
    //     int $batchIndex,
    //     $user,
    //     $banks,
    //     $provinceLogos,
    //     $dueDate,
    //     $sessionDir
    // ) {
    //     $batchDir = "$sessionDir/batch_$batchIndex";
    //     @mkdir(public_path($batchDir), 0777, true);

    //     foreach ($rows as $row) {
    //         if (count($row) < 4) continue;

    //         [$region, $city, $plateNo, $price] = [
    //             trim($row[0]),
    //             trim($row[1]),
    //             trim($row[2]),
    //             trim($row[3]),
    //         ];
    //         $status = $row[4] ?? 'Available';

    //         if ($plateNo === '') continue;

    //         $plate = LicensePlate::updateOrCreate(
    //             ['plate_number' => $plateNo],
    //             [
    //                 'region'  => $region,
    //                 'city'    => $city,
    //                 'price'   => $price,
    //                 'status'  => $status,
    //                 'user_id' => $user->id,
    //             ]
    //         );

    //         if ($plate->wasRecentlyCreated) {
    //             $this->totalCreated++;
    //             $this->insertedIds[] = $plate->id; // ✅ store new ID
    //         } else {
    //             $this->totalUpdated++;
    //         }

    //         $provinceLogo = $provinceLogos[$plate->region] ?? null;

    //         // ✅ PDF
    //         $pdf = Pdf::loadView('pdf.plate_challan', [
    //             'plate'              => $plate,
    //             'banks'              => $banks,
    //             'dueDate'            => $dueDate,
    //             'user'               => $user,
    //             'provinceLogo'       => $provinceLogo,
    //             'paymentMethod'      => "Bank",
    //             'LatePaymentPenalty' => 500,
    //             'invoiceNumber'      => 'INV-' . rand(100000, 999999),
    //         ])->setPaper('A4', 'portrait');

    //         $pdfPath = public_path("$batchDir/challan_{$plate->id}.pdf");
    //         file_put_contents($pdfPath, $pdf->output());

    //         // ✅ PNG
    //         $html = View::make('plates.plate_template', [
    //             'plate'        => $plate,
    //             'provinceLogo' => $provinceLogo,
    //         ])->render();

    //         $pngPath = public_path("$batchDir/plate_{$plate->id}.png");
    //         Browsershot::html($html)
    //             ->windowSize(400, 200)
    //             ->timeout(60000)
    //             ->save($pngPath);
    //     }

    //     $zipName = "$sessionDir/batch_$batchIndex.zip";
    //     $zipFull = public_path($zipName);

    //     $zip = new \ZipArchive();
    //     if ($zip->open($zipFull, \ZipArchive::CREATE | \ZipArchive::OVERWRITE) === true) {
    //         foreach (glob(public_path("$batchDir/*")) as $f) {
    //             $zip->addFile($f, basename($f));
    //         }
    //         $zip->close();
    //     }

    //     return $zipName;
    // }
    // public function multistore(Request $request)
    // {
    //     $validated = $request->validate([
    //         'plate_number' => 'array',
    //         'plate_number.*' => 'required|string|max:255',
    //         'province' => 'array',
    //         'province.*' => 'required|string|max:255',
    //         'city' => 'array',
    //         'city.*' => 'required|string|max:255',
    //         'price' => 'array',
    //         'price.*' => 'required|numeric|min:0',
    //         'status' => 'array',
    //         'status.*' => 'nullable|string|max:50',
    //     ]);

    //     $provinceLogos = [
    //         'Punjab'      => public_path('glogo/punjab.jpeg'),
    //         'Sindh'       => public_path('glogo/sindh.png'),
    //         'KPK'         => public_path('glogo/KP_logo.png'),
    //         'Balochistan' => public_path('glogo/balochistan.jpeg'),
    //     ];

    //     foreach ($validated['plate_number'] as $index => $plateNumber) {
    //         // Insert or update
    //         $plateId = \App\Models\LicensePlate::updateOrInsert(
    //             ['plate_number' => $plateNumber],
    //             [
    //                 'region'  => $validated['province'][$index],
    //                 'city'    => $validated['city'][$index],
    //                 'price'   => $validated['price'][$index],
    //                 'status'  => $validated['status'][$index] ?? null,
    //                 'user_id' => Auth::id()
    //             ]
    //         );

    //         $plate = LicensePlate::with('user')->where("plate_number", $plateNumber)->first();
    //         $banks = Bank::all();
    //         $dueDate = now()->addMonths(2)->format('d M Y');

    //         $user = Auth::user();
    //         $provinceLogo = $provinceLogos[$plate->region] ?? null;

    //         // Generate PDF
    //         $pdf = Pdf::loadView('pdf.plate_challan', [
    //             'plate'             => $plate,
    //             'banks'             => $banks,
    //             'dueDate'           => $dueDate,
    //             'user'              => $user,
    //             'provinceLogo'      => $provinceLogo,
    //             'paymentMethod'     => "Bank",
    //             'LatePaymentPenalty' => 500,
    //             'invoiceNumber'     => 'INV-' . rand(100000, 999999)
    //         ])->setPaper('A4', 'portrait');

    //         $challanDir = public_path('challans');
    //         if (!file_exists($challanDir)) {
    //             mkdir($challanDir, 0777, true);
    //         }

    //         $fileName = 'challan_' . $plate->id . '.pdf';
    //         $filePath = $challanDir . '/' . $fileName;

    //         $pdf->save($filePath);

    //         // Send Email
    //         Mail::send('emails.plate_challan', compact('plate', 'dueDate', 'user', 'provinceLogo'), function ($message) use ($user, $filePath) {
    //             $message->to($user->email)
    //                 ->subject('Your License Plate Challan')
    //                 ->attach($filePath, [
    //                     'as'   => 'Plate_Challan.pdf',
    //                     'mime' => 'application/pdf',
    //                 ]);
    //         });
    //     }

    //     return redirect(url('plates'))->with('success', 'Multiple plates added successfully!');
    // }

    public function multistore(Request $request)
    {
        $validated = $request->validate([
            'plate_number'   => 'array',
            'plate_number.*' => 'required|string|max:255',
            'province'       => 'array',
            'province.*'     => 'required|string|max:255',
            'city'           => 'array',
            'city.*'         => 'required|string|max:255',
            'price'          => 'array',
            'price.*'        => 'required|numeric|min:0',
            'status'         => 'array',
            'status.*'       => 'nullable|string|max:50',
        ]);

        $provinceLogos = [
            'Punjab'      => public_path('glogo/punjab.jpeg'),
            'Sindh'       => public_path('glogo/sindh.png'),
            'KPK'         => public_path('glogo/KP_logo.png'),
            'Balochistan' => public_path('glogo/balochistan.jpeg'),
        ];

        $user = Auth::user();
        $banks = Bank::all();
        $dueDate = now()->addMonths(2)->format('d M Y');

        $platesData = [];
        $attachments = [];
        $insertedIds = [];

        foreach ($validated['plate_number'] as $index => $plateNumber) {
            // Insert/update plate
            \App\Models\LicensePlate::updateOrInsert(
                ['plate_number' => $plateNumber],
                [
                    'region'  => $validated['province'][$index],
                    'city'    => $validated['city'][$index],
                    'price'   => $validated['price'][$index],
                    'status'  => $validated['status'][$index] ?? null,
                    'user_id' => $user->id
                ]
            );

            $plate = LicensePlate::with('user')->where("plate_number", $plateNumber)->first();
            if (!$plate) continue;
            $insertedIds[] = $plate->id;
            $provinceLogo = $provinceLogos[$plate->region] ?? null;

            // Generate PDF for this plate
            $pdf = Pdf::loadView('pdf.plate_challan', [
                'plate'              => $plate,
                'banks'              => $banks,
                'dueDate'            => $dueDate,
                'user'               => $user,
                'provinceLogo'       => $provinceLogo,
                'paymentMethod'      => "Bank",
                'LatePaymentPenalty' => 500,
                'invoiceNumber'      => 'INV-' . rand(100000, 999999)
            ])->setPaper('A4', 'portrait');

            $challanDir = public_path('challans');
            if (!file_exists($challanDir)) {
                mkdir($challanDir, 0777, true);
            }

            $filePath = $challanDir . '/challan_' . $plate->id . '.pdf';
            $pdf->save($filePath);

            // Store plate info & attachment for later email
            $platesData[] = [
                'plate'        => $plate,
                'provinceLogo' => $provinceLogo
            ];
            $attachments[] = $filePath;
        }

        // Send ONE email with all plates
        Mail::send('emails.plate_challan_multiple', [
            'platesData' => $platesData,
            'dueDate'    => $dueDate,
            'user'       => $user
        ], function ($message) use ($user, $attachments) {
            $message->to($user->email)
                ->subject('Your License Plate Challans');

            foreach ($attachments as $filePath) {
                $message->attach($filePath, [
                    'as'   => basename($filePath),
                    'mime' => 'application/pdf',
                ]);
            }
        });
        return redirect(url('/plates/views?plates=' . implode(',', $insertedIds)));
    }




    public function show(LicensePlate $plate)

    {
        // Assuming you have a LicensePlate model and a view to show the details
        // You can pass the $plate to the view to display its details
        // For example:
        $plate->region;
        $banks = Bank::get()->toArray();
        $dueDate = now()->addMonths(2)->format('d M Y');
        $user = Auth::user();
        $provinceLogos = [
            'Punjab'      => public_path('glogo/punjab.jpeg'),
            'Sindh'       => public_path('glogo/sindh.png'),
            'KPK'         => public_path('glogo/KP_logo.png'),
            'Balochistan' => public_path('glogo/balochistan.jpeg'),
        ];

        $provinceLogo = $provinceLogos[$plate->region] ?? null;
        $featured = $plate->featured;

        $provinceLogosasset = [
            'Punjab'      => asset('glogo/punjab.jpeg'),
            'Sindh'       => asset('glogo/sindh.png'),
            'KPK'         => asset('glogo/KP_logo.png'),
            'Balochistan' => asset('glogo/balochistan.jpeg'),
        ];


        $challanExists = plate_challan::where("licenseplate_id", $plate->id)->count();
        $provinceLogoasset = $provinceLogosasset[$plate->region] ?? null;
        if ($challanExists == 0) {


            $fileNameImage =  $plate->plate_number . date("d-F-Y") . time() . '.png';
            $imagePath = public_path('plates/' .  $fileNameImage);
            if (!file_exists($imagePath)) {
                $html = View::make('plates.plate_template', [
                    'plate' => $plate,
                    'provinceLogo' => $provinceLogo
                ])->render();
                Browsershot::html($html)
                    ->windowSize(400, 200) // adjust size
                    ->save($imagePath);
            }

            $invoiceNumber = 'INV-' . rand(100000, 999999);
            // Generate PDF
            $paymentMthod = "Bank";
            $pdf = Pdf::loadView('pdf.plate_challan', [
                'plate'        => $plate,
                'banks'        => $banks,
                'dueDate'      => $dueDate,
                'user'         => $user,
                'provinceLogo' => $provinceLogo,
                "paymentMethod" => $paymentMthod,
                "LatePaymentPenalty" => 500,
                "invoiceNumber" => $invoiceNumber
            ])->setPaper('A4', 'portrait');

            // Path to public/challans
            $challanDir = public_path('challans');
            if (!file_exists($challanDir)) {
                mkdir($challanDir, 0777, true);
            }

            $filePDFName = 'challan_' . $plate->id . '.pdf';
            $filePath = $challanDir . '/' .  $filePDFName;
            plate_challan::updateOrCreate(
                ['licenseplate_id' => $plate->id],
                [
                    'pdf_path'       =>    $filePDFName,
                    'image_path'     =>  $fileNameImage,
                    'invoice_number' => $invoiceNumber,
                ]
            );

            $pdf->save($filePath);
            plate_challan::where("licenseplate_id", $plate->id)->first();
            // Check if image already exists

            if (!$plate) {
                abort(404, 'License Plate not found.');
            }
        }
        return view('customer.plate_detail', compact('plate'), [
            "provinceLogo" => $provinceLogoasset
        ]);
    }
    public function edit($id)
    {
        $item = LicensePlate::findOrFail($id);

        if ($item->user_id != Auth::id()) {
            abort(403, 'Unauthorized action.');
        }
        $regions = LicensePlate::select('region')->distinct()->get();
        $cities = LicensePlate::select('city')->distinct()->get();
        return view('customer.edit', [
            'item' => $item,
            "cities" =>   $cities,
            "provinces" => $regions

        ]);
    }
    public function getCities(Request $request)
    {
        $region = $request->input('province');
        if (!$region) {
            return response()->json(['error' => 'Region is required'], 400);
        }

        $region_id = Region::where('region_name', $region)->value('id');
        if (!$region_id) {
            return response()->json(['status' => 'error']);
        }
        // Fetch cities based on the region
        $cities = City::where('region_id', $region_id)->get();
        if ($cities->isEmpty()) {
            return response()->json(['status' => 'error', "message" => "No Cities Found For This Region "], 404);
        }

        return response()->json(['cities' => $cities, "status" => "success"]);
    }

    public function exportPdf(Request $request)
    {
        $query = LicensePlate::query();

        $page = $request->input('page', 1);
        $perPage = 1000;

        // --- Filters ---
        if ($request->filled('city')) {
            $query->where('city', $request->city);
        }
        if ($request->filled('start_with')) {
            $query->where('plate_number', 'like', $request->start_with . '%');
        }
        if ($request->filled('region')) {
            $query->where('region', $request->region);
        }
        if ($request->filled('contain')) {
            $query->where('plate_number', 'like', '%' . $request->contain . '%');
        }
        if ($request->filled('end_with')) {
            $query->where('plate_number', 'like', '%' . $request->end_with);
        }
        if ($request->filled('length')) {
            $length = (int)$request->length;
            $query->whereRaw("LENGTH(REPLACE(REPLACE(plate_number, ' ', ''), '-', '')) = ?", [$length]);
        }
        if ($request->filled('min_price')) {
            $query->where('price', '>=', $request->min_price);
        }
        if ($request->filled('max_price')) {
            $query->where('price', '<=', $request->max_price);
        }
        if ($request->filled('user')) {
            $query->where('user_id', $request->user);
        }
        if ($request->filled('featured')) {
            $featured = $request->featured === 'Yes' ? 1 : 0;
            $query->where('featured', $featured);
        }

        $query->where('status', 'Available');

        // --- Check total count before exporting ---
        // $total = $query->count();
        // if ($total > 1500) {
        //     return redirect()->back()->with('error', 'Cannot export more than 1500 plates to PDF.');
        // }

        // --- Get paginated results ---
        $plates = $query->paginate($perPage, ['*'], 'page', $page);

        $pdf = PDF::loadView('customer.plates_pdf', compact('plates'));
        return $pdf->download('license_plates.pdf');
    }

    public function myPdf()
    {
        // $myplates = LicensePlate::where('user_id', Auth::id())

        //     ->limit(100)
        //     ->get();

        $plates = LicensePlate::where('user_id', Auth::id())
            ->orderBy('created_at', 'desc')->limit(100)->get();

        $pdf = PDF::loadView('customer.my_plate_data_pdf', compact('plates'));
        return $pdf->download('my_license_plates.pdf');
    }
    public function myCsv()
    {
        $plates = LicensePlate::where('user_id', Auth::id())
            ->orderBy('created_at', 'desc')->limit(100)->get();

        $filename = "my_license_plates_" . date('Y-m-d_H-i-s') . ".csv";
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Pragma: no-cache');
        header('Expires: 0');

        $file = fopen('php://output', 'w');
        fputcsv($file, ['Province', 'City', 'Plate Number', 'Price', 'Status']);

        foreach ($plates as $plate) {
            fputcsv($file, [
                $plate->region,
                $plate->city,
                $plate->plate_number,
                $plate->price,
                $plate->status
                // Assuming you have a user relationship
            ]);
        }

        fclose($file);
        exit;
    }
    private function mergePdfs(array $files, string $outputFile)
    {
        $pdf = new Fpdi();

        foreach ($files as $file) {
            $pageCount = $pdf->setSourceFile($file);

            for ($pageNo = 1; $pageNo <= $pageCount; $pageNo++) {
                $tplId = $pdf->importPage($pageNo);
                $size = $pdf->getTemplateSize($tplId);

                $pdf->AddPage($size['orientation'], [$size['width'], $size['height']]);
                $pdf->useTemplate($tplId);
            }
        }

        $pdf->Output('F', $outputFile);
    }

    public function update(Request $request, $id)
    {
        $item = LicensePlate::findOrFail($id);

        if ($item->user_id != Auth::id()) {
            abort(403, 'Unauthorized action.');
        }


        $validated = $request->validate([
            'plate_number' => 'required|string|max:255',

            'price' => 'required|numeric|min:0',
            'status' => 'nullable|string|max:50',
            "city" => "required",
            "region" => "required"
        ]);
        $item->update([
            'plate_number' =>  $validated['plate_number'],
            "city" => $validated['city'],
            "region" => $validated["region"],
            'price' => $request->price,
            'status' => $request->status,
        ]);

        return redirect()->route('plates.show', ['plate' => $item->id])->with('success', 'License Plate updated successfully!');
    }


    public function importPDFForm()
    {
        return view('customer.import_pdf');
    }
    public function showOcrForm()
    {
        return view('ocr.ocr_upload');
    }
    public function ocrStore(Request $request)
    {
        $request->validate([
            'plate_image' => 'required|image|max:2048'
        ]);

        $user = Auth::user();

        // Save uploaded image
        $file = $request->file('plate_image');
        $filename = time() . '.' . $file->getClientOriginalExtension();
        $file->move(public_path('plates'), $filename);
        $imagePath = public_path('plates/' . $filename);

        // Run OCR
        $text = (new TesseractOCR($imagePath))
            ->executable(public_path('Tesseract-OCR/tesseract.exe'))
            ->lang('eng')
            ->run();

        // Normalize text
        $text = strtoupper(trim($text));
        $text = preg_replace('/\s*-\s*/', '-', $text); // normalize dash
        $text = preg_replace('/\s+/', ' ', $text);
        $parts = explode(' ', $text);

        $provinces = ['PUNJAB', 'SINDH', 'KPK', 'BALOCHISTAN'];
        $province = null;
        $city = null;
        $plateNumber = null;

        // Pattern 1: Province at start
        if (in_array($parts[0], $provinces)) {
            $province = ucfirst(strtolower($parts[0]));
            $plateNumber = strtoupper($parts[1]);
            $city = $parts[2] ?? null;
        } else {
            // Pattern 2: Province at end
            $lastWord = end($parts);
            if (in_array($lastWord, $provinces)) {
                $province = ucfirst(strtolower($lastWord));
                $plateNumber = strtoupper($parts[0]);
                // Fetch city from DB if missing
                $regionData = Region::where('region_name', $province)->first();
                if ($regionData) {
                    $cityData = City::where('region_id', $regionData->id)->first();
                    $city = $cityData->city_name ?? null;
                }
            }
        }

        // Add dash between letters and numbers
        if (preg_match('/^([A-Z]+)(\d+)$/i', str_replace(' ', '', $plateNumber), $matches)) {
            $plateNumber = strtoupper($matches[1]) . '-' . $matches[2];
        }

        // Check if plate already exists
        $existingPlate = LicensePlate::where('plate_number', $plateNumber)->first();
        if ($existingPlate) {
            return back()->with('error', "$plateNumber already exists");
        }

        // Create License Plate
        $plate = LicensePlate::create([
            'plate_number' => $plateNumber,
            'region'       => $province,
            'city'         => $city,
            'price'        => random_int(1000, 5000),
            'status'       => 'Available',
            'user_id'      => $user->id
        ]);

        $provinceLogos = [
            'Punjab'      => public_path('glogo/punjab.jpeg'),
            'Sindh'       => public_path('glogo/sindh.png'),
            'KPK'         => public_path('glogo/KP_logo.png'),
            'Balochistan' => public_path('glogo/balochistan.jpeg'),
        ];

        $provinceLogo = $provinceLogos[$province] ?? null;
        $banks = Bank::all();
        $dueDate = now()->addMonths(2)->format('d M Y');
        $paymentMethod = "Bank";
        $invoiceNumber = 'INV-' . rand(100000, 999999);

        // Generate PDF
        $pdf = Pdf::loadView('pdf.plate_challan', [
            'plate'            => $plate,
            'banks'            => $banks,
            'dueDate'          => $dueDate,
            'user'             => $user,
            'provinceLogo'     => $provinceLogo,
            'paymentMethod'    => $paymentMethod,
            'LatePaymentPenalty' => 500,
            'invoiceNumber'    => $invoiceNumber
        ])->setPaper('A4', 'portrait');

        $challanDir = public_path('challans');
        if (!file_exists($challanDir)) {
            mkdir($challanDir, 0777, true);
        }

        $fileNamePdf = 'challan_' . $plate->id . '.pdf';
        $pdfPath = $challanDir . '/' . $fileNamePdf;
        $pdf->save($pdfPath);

        // Generate Plate Image from Blade
        $html = View::make('plates.plate_template', [
            'plate' => $plate,
            'provinceLogo' => $provinceLogo
        ])->render();

        $fileNameImage = 'plate_' . $plate->id . '.png';
        $imagePath = public_path('plates/' . $fileNameImage);

        Browsershot::html($html)
            ->windowSize(400, 200)
            ->save($imagePath);

        // Save paths to plate_challan table
        plate_challan::updateOrCreate(
            ['licenseplate_id' => $plate->id],
            [
                'pdf_path'       => $fileNamePdf,
                'image_path'     => $fileNameImage,
                'invoice_number' => $invoiceNumber,
            ]
        );

        // Send email with PDF and image attachments
        Mail::send('emails.plate_challan', compact('plate', 'dueDate', 'user', 'provinceLogo'), function ($message) use ($user, $pdfPath, $imagePath) {
            $message->to($user->email)
                ->subject('Your License Plate Challan')
                ->attach($imagePath, ['as' => 'Plate.png', 'mime' => 'image/png'])
                ->attach($pdfPath, ['as' => 'Plate_Challan.pdf', 'mime' => 'application/pdf']);
        });
        return redirect(url('plates/' . $plate->id . '/show'));
    }





    public function importPDF(Request $request)
    {


        $request->validate([
            'pdf_file' => 'required|mimes:pdf|max:5120',
        ]);

        // Parse PDF
        $parser = new Parser();
        $pdf    = $parser->parseFile($request->file('pdf_file')->getPathName());
        $text   = $pdf->getText();

        // Split into lines & clean
        $lines = array_filter(array_map('trim', explode("\n", $text)));

        $platesArray = [];
        $imported = 0;
        foreach ($lines as $line) {
            // Skip any header or unrelated line
            if (stripos($line, 'Province') !== false && stripos($line, 'Plate') !== false) {
                continue;
            }

            /**
             * Flexible pattern:
             * 1 → Province: first word
             * 2 → City: first word after province (handles multi-word cities but takes first for DB)
             * 3 → Plate number: ABC-123
             * 4 → Price: numeric
             * 5 → Status: Available or Sold
             */
            if (preg_match('/^([A-Za-z]+)\s+([A-Za-z]+)(?:\s+[A-Za-z]+)*\s+([A-Z]{3}-\d{3})\s+(\d+)\s+(Available|Sold)$/i', $line, $matches)) {
                $platesArray[] = [
                    'province'     => $matches[1],
                    'city'         => $matches[2],
                    'plate_number' => $matches[3],
                    'price'        => (int)$matches[4],
                    'status'       => ucfirst(strtolower($matches[5])),
                ];
            }
        }
        // return $platesArray;
        //   die();
        foreach ($platesArray as $plate) {
            $inserted = LicensePlate::insertOrIgnore([
                [
                    'plate_number' => $plate['plate_number'],
                    'region'       => $plate['province'],
                    'city'         => $plate['city'],
                    'price'        => $plate['price'],
                    'status'       => $plate['status'],
                    'user_id'      => Auth::id(),
                ]
            ]);

            $imported += $inserted;
            // 
            //     $imported++;
            //

        }

        return back()->with('success', "$imported plates imported successfully from PDF.");
    }

    // return back()->with('success', 'Plates imported successfully from PDF.');


    public function ajaxProcess(Request $request)
    {




        $plates = $request->input('plates', []);

        if (empty($plates)) {
            return response()->json([
                'success' => false,
                'message' => 'No plates selected.',
            ]);
        }

        // Example: Mark all selected plates as "Sold"
        LicensePlate::whereIn('id', $plates)
            ->where('user_id', Auth::id()) // Ensure only the authenticated user's plates are processed
            ->delete();

        return response()->json([
            'success' => true,
            'message' => count($plates) . ' plate(s) deleted successfully.',
            'plates' => $plates,
        ]);
        // Process the action based on the token and selected plates
        // This is just a placeholder for your actual processing logic



    }
    public function delete($id)
    {
        $plate = LicensePlate::findOrFail($id);

        if ($plate->user_id != Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        $plate->delete();

        return redirect(url('profile'))->with('success', 'License Plate deleted successfully!');
    }
    public function summary(Request $request)
    {
        // Get all 'plates' query params as an array
        // This will be an array [56, 58, 60]
        $plateIds = $request->input('plates');
        $ids = explode(',', $plateIds); // If you want to split by comma
        // If you want to get all matching plates from DB:
        $plates = LicensePlate::whereIn('id', $ids)
            ->where('user_id', Auth::id())
            ->where('status', '!=', "Sold") // Ensure only the authenticated user's plates are fetched
            ->get();

        $regions = Region::select('region_name', 'full_form')->get();
        $cities = City::select("city_name")->get();
        // Pass to view or return JSON
        return view('customer.edit_multiple', compact('plates', 'regions', 'cities'));
    }
    public function viewAll(Request $request)
    {
        // Get all plates for the authenticated user
        $plateIds = $request->input('plates');
        $ids = explode(',', $plateIds);
        $plates = LicensePlate::whereIn('id', $ids)->get();

        // Pass to view
        return view('customer.view_all', compact('plates'));
    }
    public function updateMultiple(Request $request)
    {
        $validated = $request->validate([
            'id.*' => 'required|exists:licenseplates,id',
            'plate_number.*' => 'required|string|max:255',
            'price.*' => 'required|numeric|min:0',
            'status.*' => 'required|in:Available,Pending,Sold',
            "province.*" => "required",
            'city.*' => "required"
        ]);

        $ids =  $validated['id'];
        $plateNumbers = $validated['plate_number'];
        $prices = $validated['price'];
        $statuses = $validated['status'];
        $regions = $validated['province'];
        $cities = $validated['city'];
        foreach ($ids as $index => $id) {
            $plate =    LicensePlate::find($id);
            if ($plate) {
                $plate->plate_number = $plateNumbers[$index];
                $plate->price = $prices[$index];
                $plate->status = $statuses[$index];
                $plate->city = $cities[$index];
                $plate->region = $regions[$index];
                $plate->save();
            }
        }

        return redirect(url('profile'))->with('success', 'Plates updated successfully!');
    }
    public function downloadimage($id)
    {

        $plate_challan_data = plate_challan::where("licenseplate_id", $id)->first();

        if (!$plate_challan_data) {

            $provinceLogos = [
                'Punjab'      => public_path('glogo/punjab.jpeg'),
                'Sindh'       => public_path('glogo/sindh.png'),
                'KPK'         => public_path('glogo/KP_logo.png'),
                'Balochistan' => public_path('glogo/balochistan.jpeg'),
            ];
            $plate = LicensePlate::find($id);
            $provinceLogo = $provinceLogos[$plate->region];
            $filename  =  $plate->plate_number . date("d-F-Y") . time() . '.png';
            $imagePath = public_path('plates/' .    $filename);
            $html = View::make('plates.plate_template', [
                'plate' => $plate,
                'provinceLogo' => $provinceLogo
            ])->render();

            Browsershot::html($html)
                ->windowSize(400, 200) // adjust size
                ->timeout(60000)
                ->save($imagePath);
            plate_challan::create(

                [
                    'licenseplate_id' => $plate->id,

                    'image_path'     =>   $filename,

                ]
            );
        } else {
            $filename = $plate_challan_data->image_path;
        }
        $path = public_path('plates/' . $filename);

        return response()->download($path);
    }
    public function downloadChallan($id)
    {
        $banks = Bank::get()->toArray();
        $dueDate = now()->addMonths(2)->format('d M Y');
        $plate = LicensePlate::find($id);
        $provinceLogos = [
            'Punjab'      => public_path('glogo/punjab.jpeg'),
            'Sindh'       => public_path('glogo/sindh.png'),
            'KPK'         => public_path('glogo/KP_logo.png'),
            'Balochistan' => public_path('glogo/balochistan.jpeg'),
        ];

        $user = User::where("id", $plate->user_id)->first();
        $provinceLogo = $provinceLogos[$plate->region] ?? null;
        $invoiceNumber = 'INV-' . rand(100000, 999999);
        $paymentMethod = "Bank";

        $pdf = Pdf::loadView('pdf.plate_challan', [
            'plate'              => $plate,
            'banks'              => $banks,
            'dueDate'            => $dueDate,
            'user'               => $user,
            'provinceLogo'       => $provinceLogo,
            'paymentMethod'      => $paymentMethod,
            'LatePaymentPenalty' => 500,
            'invoiceNumber'      => $invoiceNumber
        ])->setPaper('A4', 'portrait');

        // Instead of saving, just download instantly
        $fileName = 'challan_' . $plate->id . '.pdf';
        return $pdf->download($fileName);
    }
}
