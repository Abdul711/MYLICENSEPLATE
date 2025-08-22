<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\LicensePlateRequest;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;
use Carbon\Carbon;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\File;
use App\Models\Bank;
use Spatie\Browsershot\Browsershot;
use App\Models\Region;
use App\Models\City;
use Barryvdh\DomPDF\Facade\Pdf;
use Prologue\Alerts\Facades\Alert;
 use App\Models\plate_challan;


/**
 * Class LicensePlateCrudController
 * @package App\Http\Controllers\Admin
 * @property-read \Backpack\CRUD\app\Library\CrudPanel\CrudPanel $crud
 */
class LicensePlateCrudController extends CrudController
{
    use \Backpack\CRUD\app\Http\Controllers\Operations\ListOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\CreateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\UpdateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\DeleteOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\ShowOperation;

    /**
     * Configure the CrudPanel object. Apply settings to all operations.
     * 
     * @return void
     */
    public function setup()
    {
        CRUD::setModel(\App\Models\LicensePlate::class);
        CRUD::setRoute(config('backpack.base.route_prefix') . '/license-plate');
        CRUD::setEntityNameStrings('license plate', 'license plates');
    }

    /**
     * Define what happens when the List operation is loaded.
     * 
     * @see  https://backpackforlaravel.com/docs/crud-operation-list-entries
     * @return void
     */


    protected function setupShowOperation()
    {
        $this->crud->set('show.setFromDb', false); // don't auto-load all columns

        $this->crud->addColumn([
            'name'  => 'region',
            'label' => 'Province',
            'type'  => 'text',
        ]);

        $this->crud->addColumn([
            'name'  => 'city',
            'label' => 'City',
            'type'  => 'text',
        ]);

        $this->crud->addColumn([
            'name'  => 'plate_number',
            'label' => 'Plate Number',
            'type'  => 'text',
        ]);

        $this->crud->addColumn([
            'name'  => 'price',
            'label' => 'Price',
            'type'  => 'number',
            'prefix' => 'Rs. ',
        ]);

        $this->crud->addColumn([
            'name'  => 'status',
            'label' => 'Status',
            'type'  => 'closure',
            'function' => function ($entry) {
                if ($entry->status === 'Available') {
                    return '<span class="badge bg-success">Available</span>';
                } elseif ($entry->status === 'Sold') {
                    return '<span class="badge bg-danger">Sold</span>';
                }
                return '<span class="badge bg-warning">' . $entry->status . '</span>';
            },
            'escaped' => false, // allow HTML badges
        ]);


        $this->crud->addColumn([
            'name'  => 'featured',
            'label' => 'Featured',
            'type'  => 'closure',
            'function' => function ($entry) {
                if ($entry->featured == 1) {
                    return '<span class="badge bg-success">Yes</span>';
                } elseif ($entry->featured == 0) {
                    return '<span class="badge bg-danger">No</span>';
                }
                return '<span class="badge bg-warning">' . $entry->featured . '</span>';
            },
            'escaped' => false, // allow HTML badges
        ]);
        $this->crud->addColumn([
            'name'      => 'user_id',
            'label'     => 'User',
            'type'      => 'select',
            'entity'    => 'user',
            'attribute' => 'name',   // or email
            'model'     => "App\Models\User",
        ]);
        // $this->crud->addColumn([
        //     'name' => 'challan.image_path', // relation_name.column_name
        //     'label' => 'License Plate Image',
        //     'type' => 'image',
        //     'prefix' => 'plates/', // no prefix, since in public_path
        //     'height' => '100px',
        //     'width' => '200px',
        // ]);

        //   $this->crud->addColumn([
        //     'name' => 'challan.status', // relation_name.column_name
        //     'label' => 'Challan Status', // better label
        //     'type' => 'text',
        // ]);
        // $this->crud->addColumn([
        //     'name' => 'challan_status', // unique name
        //     'label' => 'Plate Fee Status',
        //     'type' => 'closure',
        //     "escaped" => false,
        //     'function' => function ($entry) {
        //         if (!$entry->challan) {
        //             return '<span class="badge bg-secondary">N/A</span>';
        //         }

        //         if ($entry->challan->status === "paid") {
        //             return '<span class="badge bg-success">Paid</span>';
        //         } elseif ($entry->challan->status === "unpaid") {
        //             return '<span class="badge bg-danger">Unpaid</span>';
        //         }

        //         return ucfirst($entry->challan->status);
        //     },
        // ]);
        $this->crud->addColumn([
            'name'  => 'created_at',
            'label' => 'Created At',
            'type'  => 'datetime',
        ]);
    }
    protected function setupListOperation()
    {
        // set columns from db columns.
        $this->crud->addButtonFromModelFunction('top', 'export_all', 'exportAllBtn', 'beginning');

        $this->crud->addColumn([
            'name'      => 'user_id',
            'label'     => 'User',
            'type'      => 'select',
            'entity'    => 'user',
            'attribute' => 'name',   // or email
            'model'     => "App\Models\User",
        ]);

        $this->crud->addColumn(['name' => 'region']);
        $this->crud->addColumn([
            'name'  => 'city',
            'type'  => 'text',
            'label' => 'City',
        ]);
        $this->crud->addColumn(['name' => 'price', 'label' => 'Price']);
        $this->crud->addColumn([
            'name' => 'status',
            'type' => 'closure',
            'label' => 'Status',
            'escaped'  => false,
            'function' => function ($entry) {
                $color = match ($entry->status) {
                    'Available' => 'green',
                    'Sold' => 'red',
                    'Pending' => 'orange',
                    'Overdue' => 'darkred',
                    default => 'gray'
                };
                return "<span class='badge bg-$color'>{$entry->status}</span>";
            }
        ]);




        /**
         * Columns can be defined using the fluent syntax:
         * - CRUD::column('price')->type('number');
         */
    }

    /**
     * Define what happens when the Create operation is loaded.
     * 
     * @see https://backpackforlaravel.com/docs/crud-operation-create
     * @return void
     */
    protected function setupCreateOperation()
    {
        CRUD::setValidation(LicensePlateRequest::class);

        CRUD::addField([
            'name'  => 'plate_number',
            'label' => 'Plate Number',
            'type'  => 'text',
        ]);

        // Price
        CRUD::addField([
            'name'  => 'price',
            'label' => 'Price',
            'type'  => 'number',
            'attributes' => ['step' => '0.01', 'min' => 1000, 'max' => 5000],
        ]);

        // Status
        CRUD::addField([
            'name'    => 'status',
            'label'   => 'Status',
            'type'    => 'select_from_array',
            'options' => [
                'Available' => 'Available',
                'Pending'   => 'Pending',
                'Sold'      => 'Sold',
            ],
            'default' => 'Available',
        ]);

        // Featured
        CRUD::addField([
            'name'    => 'featured',
            'label'   => 'Featured',
            'type'    => 'checkbox',
            'default' => 0,
        ]);

        $regions = \App\Models\Region::orderBy('region_name')->pluck('region_name', 'region_name')->toArray();
        CRUD::addField([
            'name'    => 'region',
            'label'   => 'Region',
            'type'    => 'select_from_array',
            'options' => $regions,
        ]);

        // Cities grouped by region
        $citiesByRegion = [];



        foreach (\App\Models\City::with('region')->get() as $city) {

            if ($city->region) {
                $citiesByRegion[$city->region->region_name][$city->city_name] = $city->city_name;
            }
        }
        CRUD::addField([
            'name'    => 'city',
            'label'   => 'City',
            'type'    => 'select_from_array',
            'options' => $citiesByRegion[array_key_first($citiesByRegion)] ?? [],
        ]);

        // JS for dynamic city dropdown
        $this->crud->addField([
            'type' => 'custom_html',
            'name' => 'region_city_js',
            'value' => '<script>
            document.addEventListener("DOMContentLoaded", function() {
                var citiesByRegion = ' . json_encode($citiesByRegion) . ';
                var regionSelect = document.querySelector("[name=\'region\']");
                var citySelect = document.querySelector("[name=\'city\']");
                if(regionSelect && citySelect){
                    // Populate cities on page load
                    var selectedRegion = regionSelect.value;
                    citySelect.innerHTML = "";
                    if(citiesByRegion[selectedRegion]){
                        for(var value in citiesByRegion[selectedRegion]){
                            var option = document.createElement("option");
                            option.value = value;
                            option.text = citiesByRegion[selectedRegion][value];
                            citySelect.appendChild(option);
                        }
                    }
                    // Update cities when region changes
                    regionSelect.addEventListener("change", function(){
                        var region = this.value;
                        citySelect.innerHTML = "";
                        if(citiesByRegion[region]){
                            for(var value in citiesByRegion[region]){
                                var option = document.createElement("option");
                                option.value = value;
                                option.text = citiesByRegion[region][value];
                                citySelect.appendChild(option);
                            }
                        }
                    });
                }
            });
        </script>'
        ]);

        // City dropdown (default = first region)

        // JS for dynamic city dropdown



        // set fields from db columns.

        /**
         * Fields can be defined using the fluent syntax:
         * - CRUD::field('price')->type('number');
         */
    }

    /**
     * Define what happens when the Update operation is loaded.
     * 
     * @see https://backpackforlaravel.com/docs/crud-operation-update
     * @return void
     */
    public function store(LicensePlateRequest $request)
    {
        $validated = $request->validated(); // get validated data

        // Append logged-in user id
        
        $provinceLogos = [
            'Punjab'      => public_path('glogo/punjab.jpeg'),
            'Sindh'       => public_path('glogo/sindh.png'),
            'KPK'         => public_path('glogo/KP_logo.png'),
            'Balochistan' => public_path('glogo/balochistan.jpeg'),
        ];
          $invoiceNumber = 'INV-' . rand(100000, 999999);



        // Generate PDF
        $paymentMthod = "Bank";

        $validated['user_id'] = backpack_auth()->id();
   $banks = Bank::get()->toArray();
        $dueDate = now()->addMonths(2)->format('d M Y');

        $provinceLogos = [
            'Punjab'      => public_path('glogo/punjab.jpeg'),
            'Sindh'       => public_path('glogo/sindh.png'),
            'KPK'         => public_path('glogo/KP_logo.png'),
            'Balochistan' => public_path('glogo/balochistan.jpeg'),
        ];
$user = backpack_auth()->user();
   $validated['user_id'] = backpack_auth()->id();
    
        $provinceLogo = $provinceLogos[$validated['region']] ?? null;
        $invoiceNumber = 'INV-' . rand(100000, 999999);
        // Generate PDF
        $paymentMthod = "Bank";
           dd($validated);
          $plate = $this->crud->create($validated);

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


         $challanDir = public_path('challans');
        if (!file_exists($challanDir)) {
            mkdir($challanDir, 0777, true);
        }

        $fileName = 'challan_' . $plate->id . '.pdf';
        $filePath = $challanDir . '/' . $fileName;

        // Save PDF
        $pdf->save($filePath);
       
          Alert::success('License plate created successfully!')->flash();
         return redirect()->to(backpack_url('license-plate'));
     
    }
    protected function setupUpdateOperation() {}
}
