<?php

namespace Tests\Unit\Http\Controllers;

use App\Http\Controllers\EventController;
use App\Repositories\Interfaces\EventRepositoryInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Mockery;
use Tests\TestCase;
use Illuminate\Pagination\LengthAwarePaginator;

class EventControllerTest extends TestCase
{
    
    private $eventController;

   
    private $eventRepositoryMock;

    protected function setUp(): void
    {
        parent::setUp();

        $this->eventRepositoryMock = Mockery::mock(EventRepositoryInterface::class);

        $this->eventController = new EventController($this->eventRepositoryMock);
    }

    protected function tearDown(): void
    {
        Mockery::close();

        parent::tearDown();
    }

    public function testCreateEvent()
    {
        $requestData = [
            "title"=> "Summit",
            "date_time" => "2023-09-02 14:30:00",
            "location" => "Anderlecht",
            "country_code" => "BE",
            "invitees" => [
                "name" => "Branko Kragovic",
                "email "=> "brankokragovic87@gmail.com"
            ]
        ];

        $createdEvent = new \App\Models\Event();

        $this->eventRepositoryMock->shouldReceive('create')
            ->once()
            ->with($requestData)
            ->andReturn($createdEvent);

        $requestMock = Mockery::mock(Request::class);
        $requestMock->shouldReceive('all')->andReturn($requestData);

        $response = $this->eventController->create($requestMock);

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(201, $response->getStatusCode());

        $this->assertEquals(json_encode($createdEvent), $response->getContent());
    }

    public function testUpdateEvent()
    {
        $eventId = 1;

        $requestData = [
            'title' => 'Updated Event',
        ];

        $existingEvent = new \App\Models\Event();

        $updatedEvent = new \App\Models\Event();

        $this->eventRepositoryMock->shouldReceive('getById')
            ->once()
            ->with($eventId)
            ->andReturn($existingEvent);

        $this->eventRepositoryMock->shouldReceive('update')
            ->once()
            ->with($existingEvent, $requestData)
            ->andReturn($updatedEvent);

        $requestMock = Mockery::mock(Request::class);
        $requestMock->shouldReceive('all')->andReturn($requestData);

        $response = $this->eventController->update($requestMock, $eventId);

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(200, $response->getStatusCode());

        $this->assertEquals(json_encode($updatedEvent), $response->getContent());
    }

    public function testGetEventById()
    {
        $eventId = 1;

        $existingEvent = new \App\Models\Event();

        $this->eventRepositoryMock->shouldReceive('getById')
            ->once()
            ->with($eventId)
            ->andReturn($existingEvent);

        $response = $this->eventController->getById($eventId);

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(200, $response->getStatusCode());

        $this->assertEquals(json_encode($existingEvent), $response->getContent());
    }

    public function testGetEventsByDateRange()
    {
        $requestData = [
            'start_date' => '2023-07-01',
            'end_date' => '2023-07-31',
        ];

        $events = new LengthAwarePaginator([], 0, 10); 

        $this->eventRepositoryMock->shouldReceive('getByDateRange')
            ->once()
            ->with($requestData)
            ->andReturn($events);

        $requestMock = Mockery::mock(Request::class);
        $requestMock->shouldReceive('all')->andReturn($requestData);

        $response = $this->eventController->getByDateRange($requestMock);

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(200, $response->getStatusCode());

        $this->assertEquals(json_encode($events), $response->getContent());
    }

    public function testGetLocationsByDateInterval()
    {
        $requestData = [
            'start_date' => '2023-07-01',
            'end_date' => '2023-07-31',
        ];

        $locations = new \Illuminate\Support\Collection();

        $this->eventRepositoryMock->shouldReceive('getLocationsByDateInterval')
            ->once()
            ->with($requestData)
            ->andReturn($locations);

        $requestMock = Mockery::mock(Request::class);
        $requestMock->shouldReceive('all')->andReturn($requestData);

        $response = $this->eventController->getLocationsByDateInterval($requestMock);

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(200, $response->getStatusCode());

        $this->assertEquals(json_encode($locations), $response->getContent());
    }

    public function testDeleteEvent()
    {
        $eventId = 1;
    
        $existingEvent = new \App\Models\Event();
    
        $this->eventRepositoryMock->shouldReceive('getById')
            ->once()
            ->with($eventId)
            ->andReturn($existingEvent);
    
        $this->eventRepositoryMock->shouldReceive('delete')
            ->once()
            ->with($existingEvent);
    
        $response = $this->eventController->delete($eventId);
    
        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(204, $response->getStatusCode());
    
    }

}
