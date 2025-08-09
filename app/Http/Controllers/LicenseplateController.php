<?php

namespace App\Http\Controllers;

use Box\Spout\Reader\Common\Creator\ReaderEntityFactory;
use App\Http\Requests\LicensePlateRequest;
use Illuminate\Http\Request;
use App\Models\LicensePlate;
use Illuminate\Support\Facades\Auth;

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


        // Filter: Contain

        $cities = LicensePlate::select('city')->distinct()->get();
        $regions = LicensePlate::select('region')->distinct()->get();

        // Get the filtered plates
        $query->where('status',"Available"); // Ensure only plates of the authenticated user are fetched

        $plates = $query->get();
        return view('customer.plates', compact('plates', 'cities', 'regions'));
    }
    public function export()
    {
        $filename = "license_plates_" . date('Y-m-d_H-i-s') . ".csv";

        // Headers to force download
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Pragma: no-cache');
        header('Expires: 0');

        $file = fopen('php://output', 'w');

        // Write the header row
        fputcsv($file, ['Plate Number', 'Province', 'City', 'Price', 'Status', 'Owner']);

        // Fetch data from DB
        $plates = LicensePlate::all();

        foreach ($plates as $plate) {
            fputcsv($file, [
                $plate->plate_number,
                $plate->region,
                $plate->city,
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
                    ['plate_number' => $row[0]],
                    [
                        'region' => $row[1],
                        'city' => $row[2],
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
        
        if ($item->user_id != Auth::id()) {
            abort(403, 'Unauthorized action.');
        }
        return view('customer.edit',[
            'item' => $item,
            
            'provinces' => LicensePlate::select('region')->distinct()->get(),
            'cities' => LicensePlate::select('city')->distinct()->get(),
        ]);
    }
    public function update(Request $request, $id)
    {
        $item = LicensePlate::findOrFail($id);
        
        if ($item->user_id != Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        $request->validate([
            'plate_number' => 'required|string|max:255',
          
            'price' => 'required|numeric|min:0',
            'status' => 'nullable|string|max:50',
        ]);
        $item->update([
            'plate_number' => $request->plate_number,
         
            'price' => $request->price,
            'status' => $request->status,
        ]);
            
        return redirect()->route('plates.show', ['plate' => $item->id])->with('success', 'License Plate updated successfully!');    
    }        
}   

