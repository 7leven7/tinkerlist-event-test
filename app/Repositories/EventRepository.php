<?php

namespace App\Repositories;

use App\Models\Event;
use App\Models\Location;
use App\Models\Invitee;
use App\Repositories\Interfaces\EventRepositoryInterface;
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Illuminate\Http\JsonResponse;

class EventRepository implements EventRepositoryInterface
{
    public function create(array $data): Event
    {
        try {
            $validator = Validator::make($data, [
                'date_time' => 'required|date',
                'location' => 'required|string',
                'invitees' => 'required|array',
                'invitees.*.name' => 'required|string',
                'invitees.*.email' => 'required|email',
            ]);

            if ($validator->fails()) {
                throw new ValidationException($validator);
            }

            $dateTime = Carbon::parse($data['date_time']);
            $locationName = $data['location'];
            $inviteesData = $data['invitees'];
            $title = $data['title'];

            $location = Location::firstOrCreate(['name' => $locationName]);

            $event = Event::firstOrCreate([
                'title' => $title,
                'creator_id' => auth()->id(),
                'event_date_time' => $dateTime,
                'location_id' => $location->id,
            ]);

           
            foreach ($inviteesData as $inviteeData) {
                $invitee = Invitee::firstOrCreate([
                    'name' => $inviteeData['name'],
                    'email' => $inviteeData['email'],
                ]);

                $event->invitees()->attach($invitee->id);
            }
           
            return $event;
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage(), 500);
        }
    }

    public function update(Event $event, array $data): Event
{
    try {

    $validator = Validator::make($data, [
        'date_time' => 'sometimes|required|date',
        'location' => 'sometimes|required|string',
        'invitees' => 'sometimes|required|array',
        'invitees.*.name' => 'sometimes|required|string',
        'invitees.*.email' => 'sometimes|required|email|unique:invitees,email',
    ]);

    if ($validator->fails()) {   
        throw new ValidationException($validator);
        }

        $event->update($data);

        if (isset($data['location'])) {
            $locationName = $data['location'];
            $location = Location::updateOrCreate(['name' => $locationName]);
            $event->location()->associate($location);
        }

        if (isset($data['invitees'])) {
            $inviteesData = $data['invitees'];
            $inviteeIds = [];

            foreach ($inviteesData as $inviteeData) {
                $invitee = Invitee::firstOrCreate([
                    'name' => $inviteeData['name'],
                    'email' => $inviteeData['email'],
                ]);

                $inviteeIds[] = $invitee->id;
            }

            $existingInviteeIds = $event->invitees->pluck('id')->toArray();
            $newInviteeIds = array_diff($inviteeIds, $existingInviteeIds);

            $event->invitees()->attach($newInviteeIds);
        }
         return $event;
    } catch (\Exception $e) {
        throw new \Exception($e->getMessage(), 500);
    }
}

    public function delete(Event $event): void
    {
        try {
            $event->invitees()->detach();
            $event->delete();
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage(), 500);
        }
    }

    public function getById($id): Event
    {
        try {
            return Event::with('location', 'invitees')->findOrFail($id);
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage(), 500);
        }
    }
}
