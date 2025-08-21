<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\UserRequest;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;
use Spatie\Permission\Models\Role;

/**
 * Class UserCrudController
 * @package App\Http\Controllers\Admin
 * @property-read \Backpack\CRUD\app\Library\CrudPanel\CrudPanel $crud
 */
class UserCrudController extends CrudController
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
        CRUD::setModel(\App\Models\User::class);
        CRUD::setRoute(config('backpack.base.route_prefix') . '/user');
        CRUD::setEntityNameStrings('user', 'users');
    }

    /**
     * Define what happens when the List operation is loaded.
     * 
     * @see  https://backpackforlaravel.com/docs/crud-operation-list-entries
     * @return void
     */
    protected function setupListOperation()
    {

        //    $this->crud->enableExportButtons(); 


            $this->crud->query = $this->crud->query->role('User');
        // Only include users where email contains '@' and domain part is not empty
        $this->crud->query->where('email', 'like', '%@%')
            ->whereRaw("SUBSTRING_INDEX(email, '@', -1) <> ''");



        $this->crud->addColumns([
            ['name' => 'name', 'label' => 'Name'],
            ['name' => 'email', 'label' => 'Email'],
            ['name' => 'mobile', 'label' => 'Mobile'],

            // Show number of plates
            [
                'name'  => 'plates_count',   // virtual attribute
                'label' => 'Number of Plates',
                'type'  => 'model_function',
                'function_name' => 'getPlatesCount', // method in User model
            ],
        ]);
    }

    /**
     * Define what happens when the Create operation is loaded.
     * 
     * @see https://backpackforlaravel.com/docs/crud-operation-create
     * @return void
     */
    protected function setupCreateOperation()
    {
        CRUD::setValidation(UserRequest::class);
        CRUD::setFromDb(); // set fields from db columns.
        // CRUD::addField([
        //     'label' => 'Role',
        //     'type' => 'select_from_array',
        //     'name' => 'role', // single role column in users table
        //     'options' => \Spatie\Permission\Models\Role::pluck('name','id')->toArray(),
        //     'allows_null' => false,
        //     'default' => 1,
        // ]);

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

    public function store(UserRequest $request)
    {
        // Save user using parent method


        $user = $this->crud->create($request->validated());

        // Assign default role
        $user->assignRole('User');  // or Role::findByName('User')

        return redirect()->to(backpack_url('user'));
    }
    protected function setupUpdateOperation()
    {
        $this->setupCreateOperation();
    }
}
