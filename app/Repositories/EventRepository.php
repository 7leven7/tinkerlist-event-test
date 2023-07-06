<?php

namespace App\Repositories;

use App\Models\Event;
use App\Models\Location;
use App\Models\Invitee;
use App\Repositories\Interfaces\EventRepositoryInterface;
use Exception;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use App\Services\EventMailService;
use App\Services\EventWeatherService;
use App\Services\GeocodingService;
use Illuminate\Support\Collection;
use App\Helpers\DateTimeHelper;
use App\Helpers\PaginationHelper;
use Illuminate\Pagination\LengthAwarePaginator;
use Carbon\Carbon;

class EventRepository implements EventRepositoryInterface
{
    /**
     * @param array $data
     * @return Event
     * @throws Exception
     */
    public function create(array $data): Event
    {
        try {
            $validator = Validator::make($data, [
                'date_time' => 'required|date',
                'location' => 'required|string',
                'country_code' => 'required|string',
                'invitees' => 'required|array',
                'invitees.*.name' => 'required|string',
                'invitees.*.email' => 'required|email',
            ]);

            if ($validator->fails()) {
                throw new ValidationException($validator);
            }

            $dateTime = $data['date_time'];
            $locationName = $data['location'];
            $countryCode = $data['country_code'];
            $inviteesData = $data['invitees'];
            $title = $data['title'];

            $location = Location::where(['name' => $locationName])->first();

            if (!$location) {
                $geoService = new GeocodingService();
                $geoData = $geoService->getCoordinatesByCityName($locationName, $countryCode);
                $location = Location::create([
                    'name' => $locationName,
                    'longitude' => $geoData[0]['longitude'],
                    'latitude' => $geoData[0]['latitude']
                ]);
            }

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
        } catch (Exception $e) {
            throw new Exception($e->getMessage(), 500);
        }
    }

    /**
     * @param Event $event
     * @param array $data
     * @return Event
     * @throws Exception
     */
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
                    throw new Exception('A conflicting event with the same date and name already exists.');
                }
            }

            if (isset($data['title'])) {
                $conflictingEvent = Event::where('event_date_time', $event->event_date_time)
                    ->where('title', $data['title'])
                    ->first();

                if ($conflictingEvent) {
                    throw new Exception('A conflicting event with the same name and date already exists.');
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

                        $title = $data['title'] ?? $event->title;
                        $dateTime = $data['date_time'] ?? $event->event_date_time;
                        $locationName = $data['location'] ?? $event->location->name;

                        $mailService->sendInvitationEmail($inviteeData, $title, $dateTime, $locationName);
                    }
                }

                $newInviteeIds = array_diff($inviteeIds, $existingInviteeIds);

                $event->invitees()->attach($newInviteeIds);
            }

            return $event;
        } catch (Exception $e) {
            throw new Exception($e->getMessage(), 500);
        }
    }

    /**
     * @param Event $event
     * @return void
     * @throws Exception
     */
    public function delete(Event $event): void
    {
        try {
            $event->invitees()->detach();
            $event->delete();
        } catch (Exception $e) {
            throw new Exception($e->getMessage(), 500);
        }
    }

    /**
     * @param $id
     * @return Event
     * @throws Exception
     */
    public function getById($id): Event
    {
        try {
            $event = Event::with('location', 'invitees')->findOrFail($id);
            return $this->attachWeatherData($event);
        } catch (Exception $e) {
            throw new Exception($e->getMessage(), 500);
        }
    }

    /**
     * @param $data
     * @return LengthAwarePaginator
     * @throws Exception
     */
    public function getByDateRange($data): LengthAwarePaginator
    {
        try {
            $perPage = $data['perPage'] ?? 10;
            $currentPage = $data['page'] ?? 1;

            $events = $this->getEventsByDateRange($data['startDate'], $data['endDate']);

            $paginationHelper = new PaginationHelper();
            $paginatedEvents = $paginationHelper->paginate($events, $perPage, $currentPage);

            return $this->attachWeatherData(null, $paginatedEvents);
        } catch (Exception $e) {
            throw new Exception($e->getMessage(), 500);
        }
    }

    /**
     * @param $data
     * @return Collection
     */
    public function getLocationsByDateInterval($data): Collection
    {
        $events = $this->getEventsByDateRange($data['startDate'], $data['endDate']);
        $this->attachWeatherData(null, $events);

        $uniqueLocations = $events->groupBy('location')->map(function ($events) {
            $location = $events->first()->location;
            $weatherData = $events->sortBy('event_date_time')->map(function ($event) {
                return $event->weather;
            });

            return [
                'location' => [
                    $location->name
                ],
                'weather' => $weatherData
            ];
        });

        return $uniqueLocations->values();
    }

    /**
     * @param string $startDate
     * @param string $endDate
     * @return Collection
     */
    private function getEventsByDateRange(string $startDate, string $endDate): Collection
    {
        return Event::with('location')
            ->whereBetween('event_date_time', [$startDate, $endDate])
            ->get();
    }

    /**
     * @param $event
     * @param $events
     * @return mixed
     * @throws Exception
     */
    private function attachWeatherData($event, $events = null): mixed
    {
        $weatherService = new EventWeatherService();
        $eventData = [$event];

        if ($events !== null) {
            $eventData = $events;
        }
        foreach ($eventData as $eventItem) {
            $weatherData = $weatherService->getWeatherForecast($eventItem->location->latitude, $eventItem->location->longitude, $eventItem->event_date_time);

            foreach ($weatherData['daily'] as $data) {
                if (DateTimeHelper::unixToDateTime($data['dt'])['date'] == DateTimeHelper::getDateFromTimestamp($eventItem->event_date_time)) {
                    $eventItem->weather = [
                        'precipitation_chance' => $data['pop'] * 100 . '%',
                        'date_time' => Carbon::createFromTimestamp($data['dt'])->format('Y-m-d H:i:s'),
                        'temp' => $data['temp'],
                        'summary' => $data['summary'],
                        'description' => $data['weather'][0]['description']
                    ];
                }
            }
        }
        return ($events !== null) ? $eventData : $event;
    }

}



