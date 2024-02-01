<?php

namespace Girift\SSO\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;

class RoleController extends Controller
{
    /**
     * This controller is for automated model syncronization.
     * The model must be defined in the application.
     * The controller handles the request from the Console Server.
     */
    protected $rules = [
        'id' => 'sometimes|nullable|uuid',
        'name' => 'required|string|max:255',
        'level' => 'required|integer',
    ];

    public function store(Request $request)
    {
        // get validated data
        $data = $request->validate($this->rules);
        $data['created_at'] = now();
        $data['updated_at'] = now();

        // create
        DB::table('roles')->insert($data);
    }

    public function update(Request $request, $id)
    {
        // get validated data
        $data = $request->validate($this->rules);
        unset($data['id']);

        // update
        $role = \App\Models\Role::find($id);
        if (! $role) {
            return false;
        }

        return $role->update($data);
    }

    public function destroy(Request $request, $id)
    {
        $role = \App\Models\Role::find($id);
        if (! $role) {
            return false;
        }

        $is_force_delete = $request->force_delete ?? false;

        return $is_force_delete ? $role->forceDelete() : $role->delete();
    }
}
