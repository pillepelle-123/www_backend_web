<?php

namespace App\Http\Controllers\Admin;

use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;
use App\Http\Requests\CompanyRequest;

/**
 * Class CompanyCrudController
 * @package App\Http\Controllers\Admin
 * @property-read \Backpack\CRUD\app\Library\CrudPanel\CrudPanel $crud
 */
class CompanyCrudController extends CrudController
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
        CRUD::setModel(\App\Models\Company::class);
        CRUD::setRoute(config('backpack.base.route_prefix') . '/company');
        CRUD::setEntityNameStrings('company', 'companies');

        $this->crud->setColumns([
            [
                'name' => 'id',
                'label' => 'ID',
                'priority' => 0,
            ],
            [
                'name' => 'name',
                'label' => 'Name',
                'priority' => 1,
            ],
            // [
            //     'name' => 'logo_url',
            //     'label' => 'Logo URL',
            // ],
            [
                'name' => 'website_url',
                'label' => 'Webseite',
                'type' => 'text',
                'priority' => 3,
            ],
            [
                'name' => 'referral_program_url',
                'label' => 'Programm URL',
                'type' => 'text',
                'priority' => 2,
            ],
            // [
            //     'name' => 'description',
            //     'label' => 'Beschreibung',
            // ],
            [
                'name' => 'is_active',
                'label' => 'Aktiv',
                'type' => 'boolean',
                'priority' => 1,
            ],
            [
                'name' => 'admin_status',
                'label' => 'Admin Status',
                'type' => 'enum',
                'options' => [
                    'pending' => 'Pending',
                    'active' => 'Active',
                    'inactive' => 'Inactive',
                    'review' => 'Review',
                    'archived' => 'Archived',
                ],
                'priority' => 1,
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
        CRUD::setValidation(CompanyRequest::class);

        CRUD::field('name')->type('text');
        CRUD::field('domain')->type('text');
        CRUD::field('referral_program_url')->type('url');
        CRUD::field('logo_url')->type('url');
        CRUD::field('description')->type('textarea');
        CRUD::field('is_active')->type('boolean');
        CRUD::field('industry')->type('text');

        // admin_status
        CRUD::field('admin_status')
            ->label('Admin Status')
            ->type('select_from_array')
            ->options([
                'pending' => 'Pending',
                'active' => 'Active',
                'inactive' => 'Inactive',
                'review' => 'Review',
                'archived' => 'Archived',
            ])
            ->allows_null(false)
            ->default('pending');
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
