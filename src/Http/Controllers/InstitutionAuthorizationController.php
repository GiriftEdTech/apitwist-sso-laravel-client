<?php

namespace Girift\SSO\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;

class InstitutionAuthorizationController extends Controller
{
    /**
     * This controller is for automated model syncronization.
     * The model must be defined in the application.
     * The controller handles the request from the Console Server.
     */
    protected $rules = [
        'id' => 'sometimes|nullable|uuid',
        'app_id' => 'required|uuid',
        'institution_id' => 'required|uuid',
        'started_at' => 'required|date',
        'ended_at' => 'required|date|after:started_at',
    ];

    public function store(Request $request)
    {
        // get validated data
        $data = $request->validate($this->rules);
        $data['created_at'] = now();
        $data['updated_at'] = now();

        // create
        DB::table('app_institution')->insert($data);
    }

    public function update(Request $request, $id)
    {
        // get validated data
        $data = $request->validate($this->rules);
        unset($data['id']);

        // update
        $institutionAuthorization = \App\Models\InstitutionAuthorization::find($id);
        if (! $institutionAuthorization) {
            return false;
        }

        return $institutionAuthorization->update($data);
    }

    public function destroy(Request $request, $id)
    {
        $institutionAuthorization = \App\Models\InstitutionAuthorization::find($id);
        if (! $institutionAuthorization) {
            return false;
        }

        $is_force_delete = $request->force_delete ?? false;

        return $is_force_delete ? $institutionAuthorization->forceDelete() : $institutionAuthorization->delete();
    }
}
