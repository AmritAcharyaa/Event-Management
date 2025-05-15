<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\EventResource;
use App\Http\Traits\CanLoadRelationship;
use App\Models\Event;
use App\Notifications\TestNotification;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Facades\Gate;

class EventController extends Controller implements HasMiddleware
{

    use CanLoadRelationship;
    /**
     * Display a listing of the resource.
     */


    public static function middleware(): array
    {
        return [
            new Middleware(middleware: 'auth:sanctum', except: ['index', 'sh ow']),
        ];
    }

    public function index()
    {

        $relations = ['user', 'attendees', 'attendees.user'];
        $qer = $this->loadRelationship(Event::query()->latest(), $relations);
        return EventResource::collection($qer->paginate());



        // $include = request('include');
        // if(!$include){
        //     return EventResource::collection(Event::paginate());
        // }else{
        //     $supportedRelations=['user','attendees','attendees.user'];
        //     $relations = array_map('trim',explode(',',$include));
        //     $query = Event::query();
        //     foreach($relations as $relation){
        //         if(in_array($relation, $supportedRelations)){
        //             $query->with($relation);
        //         } 
        //     }
        //     return EventResource::collection($query->paginate());


        // }

    }


    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // if($request->user()->cannot('create',Event::class)){
        //         abort(401,"You are Unauthenticated");
        // }

        $validatedRequest = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'start_time' => 'required|date',
            'end_time' => 'required|date|after:start_time'
        ]);

        $event = Event::create([...$validatedRequest, 'user_id' => $request->user()->id]);
        return new EventResource($event->load('user'));
    }


    public function show(Event $event)
    {
        $relations = ['user', 'attendees', 'attendees.user'];
        $event = $this->loadRelationship($event, $relations);

        // $event->load(['user','attendees']);
        return new EventResource($event);
    }


    public function update(Request $request, Event $event)
    {
     
        
        Gate::authorize('update',$event);

        // if($request->user()->cannot('update',$event)){
        //    abort(403,"You don't have access for Updating an Event");

        // }
        $validatedRequest = $request->validate([
            'name' => 'sometimes|string|max:255',
            'description' => 'nullable|string',
            'start_time' => 'sometimes|date',
            'end_time' => 'sometimes|date|after:start_time'
        ]);

        $event->update($validatedRequest);

        return new EventResource($event);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Event $event)
    {
        // if(request()->user()->cannot('delete',$event)){
        //     abort(403,"You don't have access for Deleting an Event");
 
        //  }
        Gate::authorize('delete',$event);
        $event->delete();
        return response(status: 204);
    }
}
