<?php

namespace App\Http\Controllers;

use App\Repositories\Interfaces\EventRepositoryInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class EventController extends Controller
{
    /**
     * @var EventRepositoryInterface
     */
    private $eventRepository;

    public function __construct(EventRepositoryInterface $eventRepository)
    {
        $this->eventRepository = $eventRepository;
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function create(Request $request): JsonResponse
    {
        try {
            $event = $this->eventRepository->create($request->all());
            return response()->json($event, 201);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * @param Request $request
     * @param $id
     * @return JsonResponse
     */
    public function update(Request $request, $id): JsonResponse
    {
        try {
            $event = $this->eventRepository->getById($id);
            $event = $this->eventRepository->update($event, $request->all());
            return response()->json($event, 200);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * @param $id
     * @return JsonResponse
     */
    public function delete($id): JsonResponse
    {
        try {
            $event = $this->eventRepository->getById($id);
            $this->eventRepository->delete($event);
            return response()->json(['success' => 'Event deleted'], 204);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * @param $id
     * @return JsonResponse
     */
    public function getById($id): JsonResponse
    {
        try {
            $event = $this->eventRepository->getById($id);
            return response()->json($event, 200);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function getByDateRange(Request $request): JsonResponse
    {
        try {
            $events = $this->eventRepository->getByDateRange($request->all());
            return response()->json($events, 200);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function getLocationsByDateInterval(Request $request): JsonResponse
    {
        try {
            $locations = $this->eventRepository->getLocationsByDateInterval($request->all());
            return response()->json($locations, 200);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
