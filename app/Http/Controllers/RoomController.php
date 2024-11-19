<?php

namespace App\Http\Controllers;

use App\Models\Room;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class RoomController extends Controller
{
    public function index(Request $request)
    {
        $perPage = $request->input('per_page', 10);
        $query = $request->input('query');

        if ($query) {
            $rooms = Room::search($query)->paginate($perPage);
        } else {
            $rooms = Room::paginate($perPage);
        }
        
        return response()->json($rooms, Response::HTTP_OK);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:100',
        ]);

        $room = Room::create([
            'name' => $request->input('name'),
        ]);

        return response()->json([
            'message' => 'Aula creada exitosamente',
            'room' => $room,
        ], Response::HTTP_CREATED);
    }

    public function show($id)
    {
        $room = Room::findOrFail($id);
        return response()->json($room, Response::HTTP_OK);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:100',
        ]);

        $room = Room::findOrFail($id);
        $room->update([
            'name' => $request->input('name'),
        ]);

        return response()->json([
            'message' => 'Aula actualizada exitosamente',
            'room' => $room,
        ], Response::HTTP_OK);
    }

    public function destroy($id)
    {
        $room = Room::findOrFail($id);
        $room->delete();

        return response()->json([
            'message' => 'Aula eliminada exitosamente',
        ], Response::HTTP_OK);
    }
}
