<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\AttendeeResource;
use App\Http\Traits\CanLoadRelationship;
use App\Models\Attendee;
use App\Models\Event;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Facades\Gate;

class AttendeeController extends Controller implements HasMiddleware
{
    use CanLoadRelationship;
//   use CanLoadParents;
  protected $relations =['event', 'user'];
    /**
     * Display a listing of the resource.
     * 
     */
    public static function middleware():array{
        return [
            new Middleware(middleware: 'auth:sanctum', except: ['index', 'show']),
        ];
    }
    public function index(Event $event)
    {
        $attendees = $this->loadRelationship($event->attendees()->latest(),$this->relations);

        
        // $attendees = $this->loadParents($event->attendees()->latest(),$this->relations);
        // $attendees = $event->attendees()->latest();
        return AttendeeResource::collection($attendees->paginate());
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, Event $event)
    {
        $attendee = $event->attendees()->create([
            'user_id'=>2
        ]);
        return new AttendeeResource($attendee);
    }

    
    public function show( Event $event,Attendee $attendee)
    {
        $attendee = $this->loadRelationship($attendee,$this->relations);

        // $attendee = $this->loadParents($attendee,$this->relations);
        
    return new AttendeeResource(($attendee)); 
    }

  

    /**
     * Remove the specified resource from storage.
     */
    public function destroy( Event $event,Attendee $attendee)
    {
        // Gate::authorize('delete_attendee',[$event,$attendee]);
        Gate::authorize('delete', $attendee);
        $attendee->delete();
        return response(status: 204);
    }
}
