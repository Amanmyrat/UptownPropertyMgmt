<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\PropertyRequest;
use App\Models\Property;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;
use Illuminate\Support\Arr;

/**
 * Class PropertyCrudController
 * @package App\Http\Controllers\Admin
 * @property-read \Backpack\CRUD\app\Library\CrudPanel\CrudPanel $crud
 */
class PropertyCrudController extends CrudController
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
        CRUD::setModel(\App\Models\Property::class);
        CRUD::setRoute(config('backpack.base.route_prefix') . '/property');
        CRUD::setEntityNameStrings('property', 'properties');

        $this->crud->addButtonFromView('line', 'clone', 'clone', 'end');
    }

    /**
     * Define what happens when the List operation is loaded.
     * 
     * @see  https://backpackforlaravel.com/docs/crud-operation-list-entries
     * @return void
     */
    protected function setupListOperation()
    {
        CRUD::column('property_id');
        CRUD::column('shortname');
        CRUD::column('name');
        CRUD::column('manager_name');
        CRUD::column('is_active');
        CRUD::column('property_type');
        CRUD::column('email');
        CRUD::column('total_area');
        CRUD::column('total_units');
        CRUD::column('market_rent');
        CRUD::column('current_rent');

        /**
         * Columns can be defined using the fluent syntax or array syntax:
         * - CRUD::column('price')->type('number');
         * - CRUD::addColumn(['name' => 'price', 'type' => 'number']); 
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
        CRUD::setValidation(PropertyRequest::class);

        CRUD::field('property_id')->tab('Main');
        CRUD::field('shortname')->tab('Main');
        CRUD::field('name')->tab('Main');
        CRUD::field('manager_name')->tab('Main');
        CRUD::field('is_active')->tab('Main');
        CRUD::field('property_type')->tab('Main');
        CRUD::field('email')->tab('Main');
        CRUD::field('total_area')->tab('Main');
        CRUD::field('total_units')->tab('Main');
        CRUD::field('market_rent')->tab('Extras');
        CRUD::field('current_rent')->name('current_rent')->label('Current Rents')
            ->tab('Extras')->type('repeatable')
            ->fields(
                [
                    [
                        'name'    => 'date',
                        'type'    => 'date',
                        'label'   => 'Month Date',
                        'wrapper' => ['class' => 'form-group col-md-4'],
                    ],
                    [
                        'name'    => 'price',
                        'type'    => 'text',
                        'label'   => 'Price',
                        'wrapper' => ['class' => 'form-group col-md-4'],
                    ],
                    
                ],
            );
        CRUD::field('vacancy_missing')->name('vacancy_missing')->label('Vacancy missing')
            ->tab('Extras')->type('repeatable')
            ->fields(
                [
                    [
                        'name'    => 'date',
                        'type'    => 'date',
                        'label'   => 'Month Date',
                        'wrapper' => ['class' => 'form-group col-md-4'],
                    ],
                    [
                        'name'    => 'price',
                        'type'    => 'text',
                        'label'   => 'Price',
                        'wrapper' => ['class' => 'form-group col-md-4'],
                    ],
                    
                ],
            );

        /**
         * Fields can be defined using the fluent syntax or array syntax:
         * - CRUD::field('price')->type('number');
         * - CRUD::addField(['name' => 'price', 'type' => 'number'])); 
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

    public function clone($id){

        $property = Property::where("id", $id)->get()->first();
        
        $new_product = $property->replicate();
        $new_product->push();
        $new_product->save();
        return redirect()->back();
    }
}
