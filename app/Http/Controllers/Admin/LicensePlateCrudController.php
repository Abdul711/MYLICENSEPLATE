<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\LicensePlateRequest;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;

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
        $this->crud->addColumn([
            'name' => 'challan.image_path', // relation_name.column_name
            'label' => 'License Plate Image',
            'type' => 'image',
            'prefix' => 'plates/', // no prefix, since in public_path
            'height' => '100px',
            'width' => '200px',
        ]);
        //   $this->crud->addColumn([
        //     'name' => 'challan.status', // relation_name.column_name
        //     'label' => 'Challan Status', // better label
        //     'type' => 'text',
        // ]);
        $this->crud->addColumn([
            'name' => 'challan_status', // unique name
            'label' => 'Plate Fee Status',
            'type' => 'closure',
            "escaped"=>false,
            'function' => function ($entry) {
                if (!$entry->challan) {
                    return '<span class="badge bg-secondary">N/A</span>';
                }

                if ($entry->challan->status === "paid") {
                    return '<span class="badge bg-success">Paid</span>';
                } elseif ($entry->challan->status === "unpaid") {
                    return '<span class="badge bg-danger">Unpaid</span>';
                }

                return ucfirst($entry->challan->status);
            },
        ]);
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
        CRUD::setFromDb(); // set fields from db columns.

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
    protected function setupUpdateOperation() {}
}
