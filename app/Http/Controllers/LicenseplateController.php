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
use App\Models\City;
use App\Models\Region;

class LicenseplateController extends Controller
{

    public function store(LicensePlateRequest $request)
    {
        $plateData = $request->validated();
        $plateData['user_id'] = Auth::id();

        $plate =   LicensePlate::create($plateData);
        // Here you would typically create the license plate in the database
        // LicensePlate::create($request->validated());
        // return redirect()->route('home')->with('success', 'License Plate added successfully!');
        return view('customer.plate_detail', compact('plate'));
    }
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



        // Filter: Contain

        $cities = LicensePlate::select('city')
            ->whereNotNull('city')
            ->distinct()
            ->get();

        $regions = LicensePlate::select('region')
            ->whereNotNull('region')
            ->distinct()
            ->get();

   $user_ids=LicensePlate::select('user_id')
            ->whereNotNull('user_id')
            ->distinct()
            ->pluck('user_id')->toArray();
     $users=    \App\Models\User::select('id','name')->whereIn("id",$user_ids)->get();
        // Get the filtered plates
        $query->where('status', "Available"); // Ensure only plates of the authenticated user are fetched

        $plates = $query->paginate(10)->appends($request->query());
        return view('customer.plates', compact('plates', 'cities', 'regions','users'));
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
    public function import()
    {
        return view('customer.import_plate');
    }
    public function importStore(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:csv,txt',
        ]);

        $file = $request->file('file');
        $path = $file->getRealPath();

        if (($handle = fopen($path, 'r')) !== false) {
            $header = null;
            $rowIndex = 0;

            while (($row = fgetcsv($handle, 1000, ',')) !== false) {
                $rowIndex++;

                // Skip header row (usually first row)
                if ($rowIndex === 1) {
                    $header = $row;
                    continue;
                }

                // Assuming columns: plate_number, region, city, price, status

                LicensePlate::updateOrCreate(
                    ['plate_number' => $row[2]],
                    [
                        'region' => $row[0],
                        'city' => $row[1],
                        'price' => $row[3],
                        'status' => $row[4] ?? 'Available',
                        'user_id' => Auth::id(),
                    ]
                );
            }
            fclose($handle);
            return redirect(url('plates'))->with('success', 'Plates imported successfully!');
        } else {
            return back()->withErrors(['file' => 'Cannot open the file.']);
        }


        // For example, you could use a package like Maatwebsite Excel to handle the import
        // LicensePlate::import(new LicensePlateImport, $path);


    }
    public function multistore(Request $request)
    {


        $validated = $request->validate([
            'plate_number' => 'array',
            'plate_number.*' => 'required|string|max:255',

            'province' => 'array',
            'province.*' => 'required|string|max:255',

            'city' => 'array',
            'city.*' => 'required|string|max:255',

            'price' => 'array',
            'price.*' => 'required|numeric|min:0',

            'status' => 'array',
            'status.*' => 'nullable|string|max:50',
        ]);

        foreach ($validated['plate_number'] as $index => $plateNumber) {
            \App\Models\licenseplate::updateOrCreate(
                ['plate_number' => $plateNumber], // Unique identifier to check if record exists
                [
                    'region' => $validated['province'][$index],
                    'city' => $validated['city'][$index],
                    'price' => $validated['price'][$index],
                    'status' => $validated['status'][$index] ?? null,
                    'user_id' => Auth::id() // Assuming you want to associate the plate with the authenticated user
                ]
            );
        }







        return redirect(url('plates'))->with('success', 'Multiple plates added successfully!');
    }
    public function show(LicensePlate $plate)

    {
        // Assuming you have a LicensePlate model and a view to show the details
        // You can pass the $plate to the view to display its details
        // For example:
        if (!$plate) {
            abort(404, 'License Plate not found.');
        }
        return view('customer.plate_detail', compact('plate'));
    }
    public function edit($id)
    {
        $item = LicensePlate::findOrFail($id);
               print_r($item->toArray());
        if ($item->user_id != Auth::id()) {
            abort(403, 'Unauthorized action.');
        }
       $regions=LicensePlate::select('region')->distinct()->get();
    $cities= LicensePlate::select('city')->distinct()->get();
        return view('customer.edit', [
            'item' => $item,
             "cities"=>   $cities,
             "provinces"=> $regions
     
        ]);
    }
   public function getCities(Request $request){
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
            return response()->json(['status' => 'error',"message"=>"No Cities Found For This Region "], 404);
        }
        
        return response()->json(['cities' => $cities,"status"=>"success"]);

   }  
     

    public function exportPdf(Request $request)
    {
        // 5 minutes, adjust if needed  $query = LicensePlate::query();

        // Filter: Start with
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


        $plates = $query->where("status", "Available")->limit(1500)->get();


        if (count($plates) > 1500) {

            return redirect()->back()->with('success', 'Plates exported to CSV. Downloading PDF for more than 2000 plates is not allowed.');
        }
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
         

       $validated= $request->validate([
            'plate_number' => 'required|string|max:255',

            'price' => 'required|numeric|min:0',
            'status' => 'nullable|string|max:50',
            "city"=>"required",
            "region"=>"required"
        ]);
        $item->update([
            'plate_number' =>  $validated['plate_number'],
             "city"=>$validated['city'],
             "region"=>$validated["region"],
            'price' => $request->price,
            'status' => $request->status,
        ]);

        return redirect()->route('plates.show', ['plate' => $item->id])->with('success', 'License Plate updated successfully!');
    }


    public function importPDFForm()
    {
        return view('customer.import_pdf');
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


        foreach ($platesArray as $plate) {
            $plateModel =  LicensePlate::updateOrCreate(
                ['plate_number' => $plate['plate_number']],
                [
                    'region' => $plate['province'],
                    'city'     => $plate['city'],
                    'price'    => $plate['price'],
                    'status'   => $plate['status'],
                    'user_id'  => Auth::id() // Associate with the authenticated user
                ]
            );
            if ($plateModel->wasRecentlyCreated) {
                $imported++;
            }
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

        // Pass to view or return JSON
        return view('customer.edit_multiple', compact('plates'));
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
        ]);

        $ids = $request->input('id');
        $plateNumbers = $request->input('plate_number');
        $prices = $request->input('price');
        $statuses = $request->input('status');

        foreach ($ids as $index => $id) {
            $plate =    LicensePlate::find($id);
            if ($plate) {
                $plate->plate_number = $plateNumbers[$index];
                $plate->price = $prices[$index];
                $plate->status = $statuses[$index];
                $plate->save();
            }
        }

        return redirect(url('plates'))->with('success', 'Plates updated successfully!');
    }
}
