<?php

namespace Girift\SSO\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;

class UserController extends Controller
{
    /**
     * This controller is for automated model syncronization.
     * The model must be defined in the application.
     * The controller handles the request from the Console Server.
     */
    protected $rules = [
        'id' => 'sometimes|nullable|uuid',
        'name' => 'required|string|max:255',
        'surname' => 'required|string|max:255',
        'username' => 'required|string|max:255',
        'email' => 'required|email|max:255',
        'email_verified_at' => 'nullable|date',
        'phone' => 'nullable|string|max:255',
        'active' => 'required|boolean',
        'language_id' => 'nullable|uuid',
        'image' => 'nullable|string|max:255',
        'phone_code' => 'nullable|string|max:255',
        'institutions' => 'nullable|array',
    ];

    public function store(Request $request)
    {
        // get validated data
        $data = $request->validate($this->rules);
        $data['created_at'] = now();
        $data['updated_at'] = now();
        unset($data['institutions']);

        // create
        DB::table('users')->insert($data);

        // sync institutions
        if ($request->institutions) {
            // TODO: sync institutions
        }
    }

    public function update(Request $request, $id)
    {
        // get validated data
        $data = $request->validate($this->rules);
        unset($data['id'], $data['institutions']);

        // update
        $user = \App\Models\User::find($id);
        if (! $user) {
            return false;
        }

        $user->update($data);

        // sync institutions
        if ($request->institutions) {
            // TODO: sync institutions
        }

        return $user;
    }

    public function destroy(Request $request, $id)
    {
        $user = \App\Models\User::find($id);
        if (! $user) {
            return false;
        }

        $is_force_delete = $request->force_delete ?? false;

        return $is_force_delete ? $user->forceDelete() : $user->delete();
    }
}
