<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Models\Module;

class ModuleController extends Controller
{
    public function getModules()
    {
        $modules = Module::all();
        return response()->json($modules, Response::HTTP_OK);
    }

    public function registerModule(Request $request)
    {
        $module = Module::create($request->all());
        return response()->json($module, Response::HTTP_CREATED);
    }

    public function getModuleById($id)
    {
        $module = Module::findOrFail($id);
        return response()->json($module, Response::HTTP_OK);
    }

    public function editModule(Request $request, $id)
    {
        $module = Module::findOrFail($id);
        $module->update($request->all());
        return response()->json($module, Response::HTTP_OK);
    }

    public function deleteModule($id)
    {
        $module = Module::findOrFail($id);
        $module->delete();
        return response()->json(null, Response::HTTP_NO_CONTENT);
    }
}
