<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Shift;
use App\Models\Room;
use Illuminate\Http\Response;

class ShiftController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:50',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'room_id' => 'required|exists:rooms,id',
        ]);

        $shift = Shift::create([
            'name' => $request->name,
            'start_time' => $request->start_time,
            'end_time' => $request->end_time,
            'room_id' => $request->room_id,
        ]);

        return response()->json([
            'message' => 'Turno creado correctamente',
            'shift' => $shift
        ], Response::HTTP_CREATED);
    }

    public function index(Request $request)
    {
        $perPage = $request->input('per_page', 10);
        $query = $request->input('query');
        if ($query) {
            $shifts = Shift::search($query)->paginate($perPage);
        } else {
            $shifts = Shift::with('room')->paginate($perPage);
        }
        $shifts->load('room');
        return response()->json($shifts, Response::HTTP_OK);
    }

    public function show($id)
    {
        $shift = Shift::with('room')->findOrFail($id);
        return response()->json($shift, Response::HTTP_OK);
    }

    public function update(Request $request, $id)
    {
        $shift = Shift::findOrFail($id);

        $request->validate([
            'name' => 'nullable|string|max:50',
            'start_time' => 'nullable|date_format:H:i',
            'end_time' => 'nullable|date_format:H:i|after:start_time',
            'room_id' => 'nullable|exists:rooms,id',
        ]);

        $shift->update($request->all());

        return response()->json([
            'message' => 'Turno actualizado correctamente',
            'shift' => $shift
        ], Response::HTTP_OK);
    }

    public function destroy($id)
    {
        $shift = Shift::findOrFail($id);
        $shift->delete();

        return response()->json([
            'message' => 'Turno eliminado correctamente'
        ], Response::HTTP_OK);
    }
}
