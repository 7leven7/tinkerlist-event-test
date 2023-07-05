<?php

namespace App\Http\Controllers;

use App\Repositories\Interfaces\EventRepositoryInterface;
use Illuminate\Http\Request;

class EventController extends Controller
{
    private $eventRepository;

    public function __construct(EventRepositoryInterface $eventRepository)
    {
        $this->eventRepository = $eventRepository;
    }

    public function create(Request $request)
    {
        try {
            $event = $this->eventRepository->create($request->all());
            return response()->json($event, 201);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $event = $this->eventRepository->getById($id);
            $event = $this->eventRepository->update($event, $request->all());
            return response()->json($event, 200);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function delete($id)
    {
        try {
            $event = $this->eventRepository->getById($id);
            $this->eventRepository->delete($event);
            return response()->json(null, 204);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function getById($id)
    {
        try {
            $event = $this->eventRepository->getById($id);
            return response()->json($event, 200);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function getByDateRange(Request $request) 
    {
        try{
            $events = $this->eventRepository->getByDateRange($request->all());
            return response()->json($events, 200);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
