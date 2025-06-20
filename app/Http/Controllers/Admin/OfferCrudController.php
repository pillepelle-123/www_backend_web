<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\OfferRequest;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;

/**
 * Class OfferCrudController
 * @package App\Http\Controllers\Admin
 * @property-read \Backpack\CRUD\app\Library\CrudPanel\CrudPanel $crud
 */
class OfferCrudController extends CrudController
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
        CRUD::setModel(\App\Models\Offer::class);
        CRUD::setRoute(config('backpack.base.route_prefix') . '/offer');
        CRUD::setEntityNameStrings('offer', 'offers');

        $this->crud->setColumns([
            [
                'name' => 'id',
                'label' => 'ID',
            ],
            [
                'name' => 'title',
                'label' => 'Title',
            ],
            [
                'name' => 'offerer_type',
                'label' => 'Anbieter Rolle',
                'type' => 'model_function',
                'function_name' => 'getOffererTypeLabelAttribute',
            ],
            [
                'name' => 'offerer.name',
                'label' => 'User',
                'type' => 'text',
            ],
            [
                'name' => 'company.name',
                'label' => 'Unternehmen',
                'type' => 'text',
            ],
            [
                'name' => 'reward_total_cents',
                'label' => 'Gesamtprämie',
                'type' => 'model_function',
                'function_name' => 'getRewardTotalinEuro',
                'suffix' => ' Euro',
            ],
            [
                'name' => 'reward_offerer_percent',
                'label' => 'Prämienanteil Anbietender',
                'type' => 'model_function',
                'function_name' => 'getRewardOffererInPercent',
                'suffix' => ' %',
            ],
            [
                'name' => 'status',
                'label' => 'Status',
                'type' => 'model_function',
                'function_name' => 'getStatusLabelAttribute',
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
        $this->crud->modifyColumn('title', [
            'limit' => 20,
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
        CRUD::setValidation(OfferRequest::class);
        // CRUD::setFromDb(); // set fields from db columns.


        /**
         * Fields can be defined using the fluent syntax:
         * - CRUD::field('price')->type('number');
         */


        CRUD::field('title')->type('text');

        // offerer_type
        CRUD::field('offerer_type')
            ->label('Angebot von')
            ->type('select_from_array')
            ->options([
                'referrer' => 'Referrer (Weiterempfehler)',
                'referred' => 'Referred (Empfohlener)'
            ])
            ->allows_null(false)
            ->default('referrer');

        // offerer_id
        CRUD::field('offerer_id')
            ->label('Benutzer')
            ->type('select')
            ->entity('offerer')
            ->attribute('name')
            ->data_source(url('api/search/users'))
            ->placeholder('Benutzer suchen...')
            ->minimum_input_length(2);

        // company_id
        CRUD::field('company_id')
            ->label('Unternehmen')
            ->type('select')
            ->entity('company')
            ->attribute('name')
            ->data_source(url('api/search/companies'))
            ->placeholder('Unternehmen suchen...')
            ->minimum_input_length(2);

        CRUD::field('description')->label('Beschreibung')->type('textarea');
        CRUD::field('reward_total_cents')->type('number')->attributes(['step' => 1]);

        CRUD::field('reward_offerer_percent')
            ->type('number')
            ->attributes([
                'step' => '0.01', // Erlaubt 2 Dezimalstellen
            ])
            // ->prefix('Prämienanteil:')
            // ->suffix('%')
            ->store_in('reward_offerer_percent') // Standard, kann weggelassen werden
            ->setFromRequestUsing(function ($value) {
                // Ersetze Komma durch Punkt vor der Speicherung
                return str_replace(',', '.', $value);
            });

        // status
        CRUD::field('status')
            ->label('Status')
            ->type('select_from_array')
            ->options([
                'draft' => 'Entwurf',
                'live' => 'Live',
                'hidden' => 'Versteckt',
                'matched' => 'Zugewiesen',
                'deleted' => 'Gelöscht'
            ])
            ->allows_null(false)
            ->default('draft');

        // admin_status
        CRUD::field('admin_status')
            ->label('Admin Status')
            ->type('select_from_array')
            ->options([
                'active' => 'Active',
                'inactive' => 'Inactive',
                'review' => 'Review',
                'archived' => 'Archived'
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

    protected function setupShowOperation()
    {
        $this->setupListOperation();
    }

}
