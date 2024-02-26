<?php

namespace Girift\SSO\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;

class AppController extends Controller
{
    /**
     * This controller is for automated model syncronization.
     * The model must be defined in the application.
     * The controller handles the request from the Console Server.
     */
    protected $rules = [
        'name' => 'required|string|max:255',
        'description' => 'sometimes|string|max:255',
        'icon' => 'nullable|string|max:255',
        'web_link' => 'nullable|max:255|url',
        'studio_link' => 'nullable|max:255|url',
        'google_play_link' => 'nullable|max:255|url',
        'app_store_link' => 'nullable|max:255|url',
        'image' => 'nullable',
        'is_active' => 'nullable',
        'order' => 'nullable|integer|max:40|min:1',
    ];

    public function store(Request $request)
    {
        info('AppController@store', $request->all());
        // get validated data
        $data = $request->validate($this->rules);
        $data['created_at'] = now();
        $data['updated_at'] = now();

        // create
        DB::table('apps')->insert($data);
    }

    public function update(Request $request, $id)
    {
        // get validated data
        $data = $request->validate($this->rules);
        unset($data['id']);

        // update
        $app = \App\Models\App::find($id);
        if (! $app) {
            return false;
        }

        return $app->update($data);
    }

    public function destroy(Request $request, $id)
    {
        $app = \App\Models\App::find($id);
        if (! $app) {
            return false;
        }

        $is_force_delete = $request->force_delete ?? false;

        return $is_force_delete ? $app->forceDelete() : $app->delete();
    }
}
