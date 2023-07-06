<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\Event;

class EventAuthorizationMiddleware
{
    /**
     * @param Request $request
     * @param Closure $next
     * @return Response
     */
   public function handle(Request $request, Closure $next) : Response
    {
        $eventId = $request->route('id');
        $event = Event::findOrFail($eventId);

        if (auth()->user()->id !== $event->creator_id) {
            return response()->json(['error' => 'You are not authorized for this event.'], 403);
        }

        return $next($request);
    }

}
