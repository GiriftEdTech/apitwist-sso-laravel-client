<?php

namespace Girift\SSO\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;

class RoleUserController extends Controller
{
    /**
     * This controller is for automated model syncronization.
     * The model must be defined in the application.
     * The controller handles the request from the Console Server.
     */
    protected $rules = [
        'id' => 'sometimes|nullable|uuid',
        'role_id' => 'required|uuid',
        'user_id' => 'required|uuid',
        'institution_id' => 'nullable|uuid',
    ];

    public function store(Request $request)
    {
        // get validated data
        $data = $request->validate($this->rules);
        $data['created_at'] = now();
        $data['updated_at'] = now();

        // create
        DB::table('role_user')->insert($data);
    }

    public function update(Request $request, $id)
    {
        // get validated data
        $data = $request->validate($this->rules);
        unset($data['id']);

        // update
        $roleUser = \App\Models\RoleUser::find($id);
        if (! $roleUser) {
            return false;
        }

        return $roleUser->update($data);
    }

    public function destroy(Request $request, $id)
    {
        $roleUser = \App\Models\RoleUser::find($id);
        if (! $roleUser) {
            return false;
        }

        $is_force_delete = $request->force_delete ?? false;

        return $is_force_delete ? $roleUser->forceDelete() : $roleUser->delete();
    }
}
