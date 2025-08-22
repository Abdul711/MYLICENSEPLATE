<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\CityRequest;
use App\Models\City;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;
use App\Models\LicensePlate;

/**
 * Class CityCrudController
 * @package App\Http\Controllers\Admin
 * @property-read \Backpack\CRUD\app\Library\CrudPanel\CrudPanel $crud
 */
class CityCrudController extends CrudController
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
        CRUD::setModel(\App\Models\City::class);
        CRUD::setRoute(config('backpack.base.route_prefix') . '/city');
        CRUD::setEntityNameStrings('city', 'cities');
    }

    /**
     * Define what happens when the List operation is loaded.
     * 
     * @see  https://backpackforlaravel.com/docs/crud-operation-list-entries
     * @return void
     */
    protected function setupListOperation()
    {
        // set columns from db columns.
        $this->crud->query->orderBy('region_id', 'DESC');


        $this->crud->addColumn([
            'name'      => 'region_id',
            'label'     => 'Province',
            'type'      => 'select',
            'entity'    => 'region',
            'attribute' => 'region_name',   // or email
            'model'     => "App\Models\Region",
        ]);
        $this->crud->addColumn([
            'name' => 'city_name',
            'label' => 'City Name',
            'type' => 'text',

        ]);
        $this->crud->addColumn([
            'name' => 'name_ur',
            'label' => 'City Urdu Name',
            'type' => 'text',

        ]);
        $this->crud->addColumn([
            'name' => 'plates_count',
            'label' => 'Number of Plates',
            'type' => 'closure',
            'function' => function ($entry) {
                return licenseplate::where("city", $entry->city_name)->count();
            },
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
        CRUD::setValidation(CityRequest::class);
        CRUD::setFromDb(); // set fields from db columns.
        $regions = \App\Models\Region::orderBy('region_name')->pluck('region_name', 'id')->toArray();
        CRUD::addField([
            'name'    => 'region_id',
            'label'   => 'Region',
            'type'    => 'select_from_array',
            'options' => $regions,
        ]);
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
    protected function setupUpdateOperation()
    {
        $this->setupCreateOperation();
    }
    protected function setupShowOperation()
    {



        $this->crud->addColumn([
            'name'      => 'region_id',
            'label'     => 'Province',
            'type'      => 'select',
            'entity'    => 'region',
            'attribute' => 'region_name',   // or email
            'model'     => "App\Models\Region",
        ]);
        $this->crud->addColumn([
            'name' => 'city_name',
            'label' => 'City Name',
            'type' => 'text',

        ]);
        $this->crud->addColumn([
            'name' => 'name_ur',
            'label' => 'City Urdu Name',
            'type' => 'text',

        ]);
        $this->crud->addColumn([
            'name' => 'plates_count',
            'label' => 'Number of Plates',
            'type' => 'closure',
            'function' => function ($entry) {
                return licenseplate::where("city", $entry->city_name)->count();
            },
        ]);
        // don't auto-load all columns


    }
   
}
