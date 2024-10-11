<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Modality;

class ModalityController extends Controller
{
    public function getModalities()
    {
        $modalities = Modality::all();
        return response()->json($modalities, Response::HTTP_OK);
    }

    public function registerModality(Request $request)
    {
        $modality = Modality::create($request->all());
        return response()->json($modality, Response::HTTP_CREATED);
    }

    public function getModalityById($id)
    {
        $modality = Modality::findOrFail($id);
        return response()->json($modality, Response::HTTP_OK);
    }

    public function editModality(Request $request, $id)
    {
        $modality = Modality::findOrFail($id);
        $modality->update($request->all());
        return response()->json($modality, Response::HTTP_OK);
    }

    public function deleteModality($id)
    {
        $modality = Modality::findOrFail($id);
        $modality->delete();
        return response()->json(null, Response::HTTP_NO_CONTENT);
    }
}
