<?php

namespace App\Http\Controllers\Admin;

use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;
use Illuminate\Http\Request;

/**
 * Class ManagerCrudController
 * @package App\Http\Controllers\Admin
 * @property-read \Backpack\CRUD\app\Library\CrudPanel\CrudPanel $crud
 */
class ManagerCrudController extends CrudController
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
        CRUD::setRoute(config('backpack.base.route_prefix') . '/manager');
        CRUD::setEntityNameStrings('manager', 'managers');
    }

    /**
     * Define what happens when the List operation is loaded.
     * 
     * @see  https://backpackforlaravel.com/docs/crud-operation-list-entries
     * @return void
     */
    protected function setupListOperation()
    {
        $this->crud->query = $this->crud->query->role('Manager');
        // set columns from db columns.
        $this->crud->addColumns([
            ['name' => 'name', 'label' => 'Name'],
            ['name' => 'email', 'label' => 'Email'],
            ['name' => 'mobile', 'label' => 'Mobile'],

            // Show number of plates

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
        CRUD::setValidation([
            'name' => 'required|min:2',
        ]);
        // set fields from db columns.
        CRUD::addField([
            'label' => 'Name',
            'type' => 'text',
            'name' => 'name', // single role column in users table

        ]);
        CRUD::addField([
            'label' => 'Email',
            'type' => 'email',
            'name' => 'email', // single role column in users table

        ]);
        CRUD::addField([
            'label' => 'Mobile',
            'type' => 'text',
            'name' => 'mobile', // single role column in users table

        ]);
        CRUD::addField([
            'label' => 'Password',
            'type' => 'password',
            'name' => 'password', // single role column in users table

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
    public function store(Request $request)
    {
        // Save user using parent method
        $data = $request->validate([
            "name" => "required|unique:users,name",
            "mobile" => "required|unique:users,email",
            "password" => "required",
            "email" => "required"
        ]);
        if (!empty($data['email']) && str_contains($data['email'], '@')) {
            $domain = explode('@', $data['email'])[1] ?? '';

            // Remove .com if it exists
            $cleanedDomain = str_replace('.com', '', $domain);

            $data['email_domain'] = $cleanedDomain;
        }
        $data["package_id"]=3;
      
        $user = $this->crud->create($data);

        // Assign default role
        $user->assignRole('Manager');  // or Role::findByName('User')

        return redirect()->to(backpack_url('manager'));
    }
    protected function setupUpdateOperation()
    {
        $this->setupCreateOperation();
    }
     protected function setupShowOperation()
    {
        
        $this->crud->addColumns([
            ['name' => 'name', 'label' => 'Name'],
            ['name' => 'email', 'label' => 'Email'],
            ['name' => 'mobile', 'label' => 'Mobile'],

        
        ]);

    }   
}

