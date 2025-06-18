<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\RatingRequest;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;

/**
 * Class RatingCrudController
 * @package App\Http\Controllers\Admin
 * @property-read \Backpack\CRUD\app\Library\CrudPanel\CrudPanel $crud
 */
class RatingCrudController extends CrudController
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
        CRUD::setModel(\App\Models\Rating::class);
        CRUD::setRoute(config('backpack.base.route_prefix') . '/rating');
        CRUD::setEntityNameStrings('rating', 'ratings');

        $this->crud->setColumns([
            [
                'name' => 'id',
                'label' => 'ID',
            ],
            [
                'name' => 'offer.title',
                'label' => 'Title',
            ],
            [
                'name' => 'fromUser.name',
                'label' => 'Bewertender',
            ],
            [
                'name' => 'toUser.name',
                'label' => 'Bewerteter',
            ],
            [
                'name' => 'score',
                'label' => 'Bewertung',
            ],
            [
                'name' => 'comment',
                'label' => 'Kommentar',
                'type' => 'text'
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

        $this->crud->modifyColumn('score', [
            'type' => 'model_function',
            'function_name' => 'getScoreInStars',
        ]);
        $this->crud->removeColumn('offer_id');
        $this->crud->removeColumn('from_user_id');
        $this->crud->removeColumn('to_user_id');

    }

    /**
     * Define what happens when the Create operation is loaded.
     *
     * @see https://backpackforlaravel.com/docs/crud-operation-create
     * @return void
     */
    protected function setupCreateOperation()
    {
        CRUD::setValidation(RatingRequest::class);
        // CRUD::setFromDb(); // set fields from db columns.

        /**
         * Fields can be defined using the fluent syntax:
         * - CRUD::field('price')->type('number');
         */
        $this->crud->addField([
            'name' => 'offer_id',
            'label' => 'Offer',
            'type' => 'select',
            'entity' => 'offer',
            'attribute' => 'title',
            'data_source' => url('api/search/offers'),
            'placeholder' => 'Angebot suchen...',
            'minimum_input_length' => 2,
        ]);

        $this->crud->addField([
            'name' => 'from_user_id',
            'label' => 'Bewertender',
            'type' => 'select',
            'entity' => 'fromUser',
            'attribute' => 'name',
            'data_source' => url('api/search/users'),
            'placeholder' => 'Bewertender suchen...',
            'minimum_input_length' => 2,
        ]);

        $this->crud->addField([
            'name' => 'to_user_id',
            'label' => 'Bewerteter',
            'type' => 'select',
            'entity' => 'toUser',
            'attribute' => 'name',
            'data_source' => url('api/search/users'),
            'placeholder' => 'Bewerteten suchen...',
            'minimum_input_length' => 2,
        ]);

        CRUD::field('score')
            ->type('number')
            ->label('Bewertung (1-5 Sterne)')
            ->attributes([
                'min' => 1,
                'max' => 5,
                'step' => 1,
                'style' => 'width: 100px' // Optional: Breite anpassen
            ])
            ->suffix('Sterne')
            ->rules('required|integer|between:1,5');

        CRUD::field('comment')->type('textarea');
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
        // $this->setupListOperation();
        $this->autoSetupShowOperation();
        // $this->crud->modifyColumn('comment', [
        //     'limit' => false, // Deaktiviert die Zeichenbegrenzung
        // ]);
        $this->crud->modifyColumn('score', [
            'type' => 'model_function',
            'function_name' => 'getScoreInStars',
        ]);
        $this->crud->addColumn( [
                'name' => 'comment',
                'label' => 'Kommentar',
                'type' => 'text',
                'limit' => false
            ],
        );

        $this->crud->modifyColumn('offer.title', [
            'limit' => false,
        ]);

        // $this->crud->addColumn('')
    }

}
