<?php

namespace Girift\SSO\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;

class InstitutionController extends Controller
{
    /**
     * This controller is for automated model syncronization.
     * The model must be defined in the application.
     * The controller handles the request from the Console Server.
     */
    protected $rules = [
        'id' => 'sometimes|nullable|uuid',
        'name' => 'required|string|max:255',
        'phone' => 'nullable|string|max:255',
        'email' => 'required|email|max:255',
        'manager_id' => 'nullable|uuid',
        'active' => 'required|boolean',
        'country_id' => 'nullable|uuid',
        'state_id' => 'nullable|uuid',
        'city_id' => 'nullable|uuid',
        'image' => 'nullable|string|max:255',
        'address' => 'nullable|string|max:255',
        'zipcode' => 'nullable',
        'is_individual' => 'nullable|boolean',
        'parent_id' => 'nullable|uuid',
        'institution_category_id' => 'required|exists:institution_categories,id',
    ];

    public function store(Request $request)
    {
        // get validated data
        $data = $request->validate($this->rules);
        $data['created_at'] = now();
        $data['updated_at'] = now();

        // create
        DB::table('institutions')->insert($data);
    }

    public function update(Request $request, $id)
    {
        // get validated data
        $data = $request->validate($this->rules);
        unset($data['id']);

        // update
        $institution = \App\Models\Institution::find($id);
        if (! $institution) {
            return false;
        }

        return $institution->update($data);
    }

    public function destroy(Request $request, $id)
    {
        $institution = \App\Models\Institution::find($id);
        if (! $institution) {
            return false;
        }

        $is_force_delete = $request->force_delete ?? false;

        return $is_force_delete ? $institution->forceDelete() : $institution->delete();
    }
}
