<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\Event;

class EventAuthorizationMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
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
