<?php

namespace App\Http\Controllers\Admin;

use App\Models\UserMatch;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;

/**
 * Class UserMatchCrudController
 * @package App\Http\Controllers\Admin
 * @property-read \Backpack\CRUD\app\Library\CrudPanel\CrudPanel $crud
 */
class UserMatchCrudController extends CrudController
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
        CRUD::setModel(UserMatch::class);
        CRUD::setRoute(config('backpack.base.route_prefix') . '/user-match');
        CRUD::setEntityNameStrings('user match', 'user matches');
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
        CRUD::column('application_id')
            ->type('relationship')
            ->label('Application')
            ->entity('application')
            ->attribute('id');
        CRUD::column('affiliate_link_id')
            ->type('relationship')
            ->label('Affiliate Link')
            ->entity('affiliateLink')
            ->attribute('url');
        CRUD::column('link_clicked')
            ->type('boolean');
        CRUD::column('status')
            ->type('enum')
            ->options([
                'opened' => 'Opened',
                'closed' => 'Closed',
            ]);
        CRUD::column('success_status')
            ->type('enum')
            ->options([
                'pending' => 'Pending',
                'successful' => 'Successful',
                'unsuccessful' => 'Unsuccessful',
            ]);
        CRUD::column('reason_unsuccessful_referrer')
            ->type('text')
            ->limit(50);
        CRUD::column('reason_unsuccessful_referred')
            ->type('text')
            ->limit(50);
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
        CRUD::field('application_id')
            ->type('relationship')
            ->label('Application')
            ->entity('application')
            ->attribute('id')
            ->inline_create(true)
            ->ajax(true);
        CRUD::field('affiliate_link_id')
            ->type('relationship')
            ->label('Affiliate Link')
            ->entity('affiliateLink')
            ->attribute('url')
            ->inline_create(true)
            ->ajax(true);
        CRUD::field('link_clicked')
            ->type('boolean')
            ->default(false);
        CRUD::field('status')
            ->type('enum')
            ->options([
                'opened' => 'Opened',
                'closed' => 'Closed',
            ])
            ->default('opened');
        CRUD::field('success_status')
            ->type('enum')
            ->options([
                'pending' => 'Pending',
                'successful' => 'Successful',
                'unsuccessful' => 'Unsuccessful',
            ])
            ->default('pending');
        CRUD::field('reason_unsuccessful_referrer')
            ->type('textarea')
            ->allows_null(true);
        CRUD::field('reason_unsuccessful_referred')
            ->type('textarea')
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
    }
}
