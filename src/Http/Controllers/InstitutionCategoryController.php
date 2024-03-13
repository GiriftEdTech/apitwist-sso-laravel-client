<?php

namespace Girift\SSO\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;

class InstitutionCategoryController extends Controller
{
    /**
     * This controller is for automated model syncronization.
     * The model must be defined in the application.
     * The controller handles the request from the Console Server.
     */
    protected $rules = [
        'id' => 'sometimes|nullable',
        'name' => 'required|array',
        'name.*' => 'required|string',
    ];

    public function store(Request $request)
    {
        // get validated data
        $data = $request->validate($this->rules);

        $data['name'] = json_encode($data['name']);
        $data['created_at'] = now();
        $data['updated_at'] = now();

        // create
        DB::table('institution_categories')->insert($data);
    }

    public function update(Request $request, $id)
    {
        // get validated data
        $data = $request->validate($this->rules);
        unset($data['id']);

        // update
        $institutionCategory = \App\Models\InstitutionCategory::find($id);
        if (! $institutionCategory) {
            return false;
        }

        return $institutionCategory->update($data);
    }

    public function destroy(Request $request, $id)
    {
        $institutionCategory = \App\Models\InstitutionCategory::find($id);
        if (! $institutionCategory) {
            return false;
        }

        $is_force_delete = $request->force_delete ?? false;

        return $is_force_delete ? $institutionCategory->forceDelete() : $institutionCategory->delete();
    }
}
