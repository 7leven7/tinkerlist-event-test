<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Event;
use App\Models\Invitee;
use App\Models\Location;

class EventSeeder extends Seeder
{
    public function run()
    {
        // Create Events
        $events = [];
        $creatorId = 1;
        $locationId = 1;
        
        for ($i = 1; $i <= 30; $i++) {
            $date = date('Y-m-d H:i:s', strtotime('+'. $i .' days'));

            $events[] = [
                'title' => 'Event ' . $i,
                'event_date_time' => $date,
                'location_id' => $locationId,
                'invitees' => [
                    [
                        'name' => 'Invitee 1',
                        'email' => 'invitee1@example.com',
                    ],
                    [
                        'name' => 'Invitee 2',
                        'email' => 'invitee2@example.com',
                    ],
                ],
                'creator_id' => $creatorId,
            ];

            $creatorId++;
            if ($creatorId > 10) {
                $creatorId = 1;
            }

            $locationId++;
            if ($locationId > 3) {
                $locationId = 1;
            }
        }

        foreach ($events as $eventData) {
            $location = Location::find($eventData['location_id']);

            $event = Event::create([
                'title' => $eventData['title'],
                'event_date_time' => $eventData['event_date_time'],
                'creator_id' => $eventData['creator_id'],
                'location_id' => $eventData['location_id']
            ]);

            $event->location()->associate($location);
            $event->save();

            // Create Invitees and associate with the event
            foreach ($eventData['invitees'] as $inviteeData) {
                $invitee = Invitee::create([
                    'name' => $inviteeData['name'],
                    'email' => $inviteeData['email'],
                ]);

                $event->invitees()->attach($invitee->id);
            }
        }
    }
}
