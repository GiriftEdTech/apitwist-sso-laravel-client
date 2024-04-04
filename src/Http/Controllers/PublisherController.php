<?php

namespace Girift\SSO\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;

class PublisherController extends Controller
{
    /**
     * This controller is for automated model syncronization.
     * The model must be defined in the application.
     * The controller handles the request from the Console Server.
     */
    protected $rules = [
        'id' => 'sometimes|nullable|uuid',
        'name' => 'required|string|max:255',
        'manager_id' => 'nullable|uuid',
        'book_amount' => 'nullable|integer',
        'user_amount' => 'nullable|integer',
        'image' => 'nullable|string|max:255',
    ];

    public function store(Request $request)
    {
        // get validated data
        $data = $request->validate($this->rules);
        $data['created_at'] = now();
        $data['updated_at'] = now();

        // create
        DB::table('publishers')->insert($data);
    }

    public function update(Request $request, $id)
    {
        // get validated data
        $data = $request->validate($this->rules);
        unset($data['id']);

        // update
        $publisher = \App\Models\Publisher::find($id);
        if (! $publisher) {
            return false;
        }

        return $publisher->update($data);
    }

    public function destroy(Request $request, $id)
    {
        $publisher = \App\Models\Publisher::find($id);
        if (! $publisher) {
            return false;
        }

        $is_force_delete = $request->force_delete ?? false;

        return $is_force_delete ? $publisher->forceDelete() : $publisher->delete();
    }
}
