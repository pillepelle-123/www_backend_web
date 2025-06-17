<?php

namespace App\Http\Controllers\Admin;

use App\Models\AffiliateLink;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;

/**
 * Class AffiliateLinkCrudController
 * @package App\Http\Controllers\Admin
 * @property-read \Backpack\CRUD\app\Library\CrudPanel\CrudPanel $crud
 */
class AffiliateLinkCrudController extends CrudController
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
        CRUD::setModel(AffiliateLink::class);
        CRUD::setRoute(config('backpack.base.route_prefix') . '/affiliate-link');
        CRUD::setEntityNameStrings('affiliate link', 'affiliate links');
    }

    /**
     * Define what happens when the List operation is loaded.
     * 
     * @see  https://backpackforlaravel.com/docs/crud-operation-list-entries
     * @return void
     */
    protected function setupListOperation()
    {
        CRUD::column('id');
        CRUD::column('company_id')
            ->type('relationship')
            ->label('Company')
            ->attribute('name');
        CRUD::column('url');
        CRUD::column('parameters');
        CRUD::column('click_count');
        CRUD::column('admin_status')
            ->type('enum')
            ->options([
                'pending' => 'Pending',
                'active' => 'Active',
                'inactive' => 'Inactive',
                'review' => 'Review',
                'archived' => 'Archived',
            ]);
        CRUD::column('created_at');
        CRUD::column('updated_at');
    }

    /**
     * Define what happens when the Create operation is loaded.
     * 
     * @see https://backpackforlaravel.com/docs/crud-operation-create
     * @return void
     */
    protected function setupCreateOperation()
    {
        CRUD::field('company_id')
            ->type('relationship')
            ->label('Company')
            ->entity('company')
            ->attribute('name')
            ->inline_create(true)
            ->ajax(true);
        CRUD::field('url')
            ->type('url');
        CRUD::field('parameters');
        CRUD::field('click_count')
            ->type('number')
            ->default(0);
        CRUD::field('admin_status')
            ->type('enum')
            ->options([
                'pending' => 'Pending',
                'active' => 'Active',
                'inactive' => 'Inactive',
                'review' => 'Review',
                'archived' => 'Archived',
            ])
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