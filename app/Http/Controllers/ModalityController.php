<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Models\Modality;
use App\Http\Requests\ModalityRequest;

class ModalityController extends Controller
{
    public function getModalities(Request $request)
    {
        $perPage = $request->input('per_page', 10);
        $query = $request->input('query');

        if ($query) {
            $modalities = Modality::search($query)->paginate($perPage);
        } else {
            $modalities = Modality::paginate($perPage);
        }
        
        return response()->json($modalities, Response::HTTP_OK);
    }

    public function registerModality(ModalityRequest $request)
    {
        $modality = Modality::create($request->all());
        return response()->json($modality, Response::HTTP_CREATED);
    }

    public function getModalityById($id)
    {
        $modality = Modality::findOrFail($id);
        return response()->json($modality, Response::HTTP_OK);
    }

    public function editModality(ModalityRequest $request, $id)
    {
        $modality = Modality::findOrFail($id);
        $modality->update($request->all());
        return response()->json($modality, Response::HTTP_OK);
    }

    public function deleteModality($id)
    {
        $modality = Modality::findOrFail($id);
        $modality->delete();
        return response()->json(Response::HTTP_OK);
    }
}
