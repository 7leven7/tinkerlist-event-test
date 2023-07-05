<?php

namespace App\Repositories;

use App\Models\Event;
use App\Models\Location;
use App\Models\Invitee;
use App\Repositories\Interfaces\EventRepositoryInterface;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use App\Services\EventMailService;
use App\Services\EventWeatherService;
use Illuminate\Database\Eloquent\Collection;
use App\Helpers\DateTimeHelper;

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

            $dateTime = $data['date_time'];
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

                if (!$event->invitees->contains($invitee->id)) {
                    $event->invitees()->attach($invitee->id);

                    $mailService = new EventMailService();
                    $mailService->sendInvitationEmail($inviteeData, $title, $dateTime, $locationName);
                }
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
                'invitees.*.email' => 'sometimes|required|email',
            ]);

            if ($validator->fails()) {
                throw new ValidationException($validator);
            }

            if (isset($data['date_time'])) {
                $conflictingEvent = Event::where('title', $event->title)
                    ->where('event_date_time', $data['date_time'])
                    ->first();
    
                if ($conflictingEvent) {
                    throw new \Exception('A conflicting event with the same date and name already exists.');
                }
            }

            if (isset($data['title'])) {
                $conflictingEvent = Event::where('event_date_time', $event->event_date_time)
                    ->where('title', $data['title'])
                    ->first();
    
                if ($conflictingEvent) {
                    throw new \Exception('A conflicting event with the same name and date already exists.');
                }
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

                $existingInviteeIds = $event->invitees->pluck('id')->toArray();

                foreach ($inviteesData as $inviteeData) {
                    $invitee = Invitee::firstOrCreate([
                        'name' => $inviteeData['name'],
                        'email' => $inviteeData['email'],
                    ]);

                    $inviteeIds[] = $invitee->id;

                    if (!in_array($invitee->id, $existingInviteeIds)) {
                        $mailService = new EventMailService();

                        $title = isset($data['title']) ? $data['title'] : $event->title;
                        $dateTime = $data['date_time'] ?? $event->event_date_time;
                        $locationName = $data['location'] ?? $event->location->name;

                        $mailService->sendInvitationEmail($inviteeData, $title, $dateTime, $locationName);
                    }
                }

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

    public function getByDateRange($data): Collection
    {
        try {
            $events =  Event::with('location')->whereBetween('event_date_time', [$data['startDate'], $data['endDate']])->get();
            $weatherService = new EventWeatherService();
            foreach($events as $event){
                $weatherData = $weatherService->getWeatherForecast($event->location->latitude,$event->location->longitude,$event->event_date_time);
               
                foreach ($weatherData['daily'] as $data){
                   
                   if(DateTimeHelper::unixToDateTime($data['dt'])['date'] == DateTimeHelper::getDateFromTimestamp($event->event_date_time)){
                       $event->weather = $data;
                   }
                }
            }
           
            return $events;
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage(), 500);
        }
    }

}

    

