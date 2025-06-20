<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\ApplicationRequest;
use App\Models\Application;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;

/**
 * Class ApplicationCrudController
 * @package App\Http\Controllers\Admin
 * @property-read \Backpack\CRUD\app\Library\CrudPanel\CrudPanel $crud
 */
class ApplicationCrudController extends CrudController
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
        CRUD::setModel(Application::class);
        CRUD::setRoute(config('backpack.base.route_prefix') . '/application');
        CRUD::setEntityNameStrings('application', 'applications');
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
        CRUD::column('offer_id')
            ->type('relationship')
            ->label('Offer')
            ->attribute('title');
        CRUD::column('applicant_id')
            ->type('relationship')
            ->label('Applicant')
            ->entity('applicant')
            ->attribute('name');
        CRUD::column('message')
            ->type('text')
            ->limit(50);
        CRUD::column('status')
            ->type('enum')
            ->options([
                'pending' => 'Pending',
                'approved' => 'Approved',
                'rejected' => 'Rejected',
                'retracted' => 'Retracted',
            ]);
        CRUD::column('is_read_by_applicant')
            ->type('boolean')
            ->label('Read by Applicant');
        CRUD::column('is_read_by_owner')
            ->type('boolean')
            ->label('Read by Owner');
        CRUD::column('is_archived_by_applicant')
            ->type('boolean')
            ->label('Archived by Applicant');
        CRUD::column('is_archived_by_owner')
            ->type('boolean')
            ->label('Archived by Owner');
        CRUD::column('responded_at')
            ->type('datetime');
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
        CRUD::setValidation(ApplicationRequest::class);

        CRUD::field('offer_id')
            ->type('relationship')
            ->label('Offer')
            ->entity('offer')
            ->attribute('title')
            ->inline_create(true)
            ->ajax(true);
        CRUD::field('applicant_id')
            ->type('relationship')
            ->label('Applicant')
            ->entity('applicant')
            ->attribute('name')
            ->inline_create(true)
            ->ajax(true);
        CRUD::field('message')
            ->type('textarea')
            ->allows_null(true);
        CRUD::field('status')
            ->type('enum')
            ->options([
                'pending' => 'Pending',
                'approved' => 'Approved',
                'rejected' => 'Rejected',
                'retracted' => 'Retracted',
            ])
            ->default('pending');
        CRUD::field('is_read_by_applicant')
            ->type('boolean')
            ->default(false);
        CRUD::field('is_read_by_owner')
            ->type('boolean')
            ->default(false);
        CRUD::field('is_archived_by_applicant')
            ->type('boolean')
            ->default(false);
        CRUD::field('is_archived_by_owner')
            ->type('boolean')
            ->default(false);
        CRUD::field('responded_at')
            ->type('datetime')
            ->allows_null(true);
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

        // Zeige den vollständigen Nachrichtentext ohne Begrenzung
        CRUD::modifyColumn('message', [
            'type' => 'text',
            'limit' => 0,
        ]);
    }
}
