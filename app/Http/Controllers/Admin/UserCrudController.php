<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\UserRequest;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;

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

        $this->crud->setColumns([
            [
                'name' => 'id',
                'label' => 'ID',
            ],
            [
                'name' => 'name',
                'label' => 'Name',
            ],
            [
                'name' => 'email',
                'label' => 'E-Mail',
            ],
            [
                'name' => 'role',
                'label' => 'Rolle',
            ],
            [
                'name' => 'average_rating',
                'label' => 'Durchschnittliche Bewertung',
                'type' => 'number',
            ],
            [
                'name' => 'admin_status',
                'label' => 'Admin Status',
                'type' => 'enum',
                'options' => [
                    'active' => 'Active',
                    'inactive' => 'Inactive',
                    'review' => 'Review',
                    'archived' => 'Archived',
                ],
            ],
            [
                'name' => 'email_verified_at',
                'label' => 'E-Mail verifiziert',
                'type' => 'datetime',
            ],
            [
                'name' => 'created_at',
                'label' => 'Erstellt am',
                'type' => 'datetime',
            ],
        ]);
    }

    /**
     * Define what happens when the List operation is loaded.
     *
     * @see  https://backpackforlaravel.com/docs/crud-operation-list-entries
     * @return void
     */
    protected function setupListOperation()
    {
        // CRUD::setFromDb(); // set columns from db columns.

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
        CRUD::setValidation(UserRequest::class);

        CRUD::field('name')->type('text');
        CRUD::field('email')->type('email');
        CRUD::field('password')->type('password');
        CRUD::field('average_rating')
            ->type('number')
            ->attributes([
                'step' => '0.01',
                'min' => '0',
                'max' => '5',
            ])
            ->default(0);

        // admin_status
        CRUD::field('admin_status')
            ->label('Admin Status')
            ->type('select_from_array')
            ->options([
                'active' => 'Active',
                'inactive' => 'Inactive',
                'review' => 'Review',
                'archived' => 'Archived',
            ])
            ->allows_null(false)
            ->default('active');
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

    /**
     * Define what happens when the Show operation is loaded.
     *
     * @see https://backpackforlaravel.com/docs/crud-operation-show
     * @return void
     */
    protected function setupShowOperation()
    {
        $this->setupListOperation();
    }
}
