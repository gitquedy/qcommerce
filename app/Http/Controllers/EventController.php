<?php

namespace App\Http\Controllers;
use App\Event;
use App\Settings;
use Auth;
use Validator;
use DB;

use Illuminate\Http\Request;

class EventController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $events = Event::where('business_id', Auth::user()->business_id)->get();
        $setting = Settings::where('business_id', Auth::user()->business_id)->first();
        $filter = explode(',', $setting->calendar_filter);
        foreach ($events as $event) {
            if (!isset($event->url)) {
                $event->url = '';
            }
            if (!in_array(strtolower($event->label), $filter)) {
                $event->label = 'Others';
            }
            if (isset($event->guests)) {
                $event->guests = explode(',', $event->guests);
            }
        }
        return $events;
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string',
            'select-label' => 'required|string',
            'start-date' => 'required|date',
            'end-date' => 'required|date|after_or_equal:start_date',
            'event-url' => 'nullable|url',
            'event-guests' => 'nullable|array',
            'event-location' => 'nullable|string',
            'event-description-editor' => 'nullable|string|max:255'
        ]);

        if ($validator->fails()) {
            return response()->json(['msg' => 'Please check for errors' ,'error' => $validator->errors()]);
        }

        DB::beginTransaction();
        try {
            $data = $request->all();
            $event = new Event;
            $event->business_id = $request->user()->business_id;
            $event->title = $data['title'];
            $event->label = $data['select-label'];
            $event->start = $data['start-date'];
            $event->end = $data['end-date'];
            $event->url = $data['event-url'];
            $event->guests = isset($data['event-guests']) ? implode(',', $data['event-guests']) : null;
            $event->location = $data['event-location'];
            $event->description = $data['event-description-editor'];
            $event->save();

            $output = ['success' => 1,
                'msg' => 'Event added successfully!',
                'redirect' => action('CalenderAppController@calendarApp')
            ];
            DB::commit();
          
        } catch (\Exception $e) {
            \Log::emergency("File:" . $e->getFile(). " Line:" . $e->getLine(). " Message:" . $e->getMessage());
            $output = ['success' => 0,
                        'msg' => env('APP_DEBUG') ? $e->getMessage() : 'Sorry something went wrong, please try again later.'
                    ];
             DB::rollBack();
        }
        return response()->json($output);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string',
            'select-label' => 'required|string',
            'start-date' => 'required|date',
            'end-date' => 'required|date|after_or_equal:start_date',
            'event-url' => 'nullable|url',
            'event-guests' => 'nullable|array',
            'event-location' => 'nullable|string',
            'event-description-editor' => 'nullable|string|max:255'
        ]);

        if ($validator->fails()) {
            return response()->json(['msg' => 'Please check for errors' ,'error' => $validator->errors()]);
        }

        DB::beginTransaction();
        try {
            $event = Event::find($request->id);
            $data = $request->all();
            $event->business_id = $request->user()->business_id;
            $event->title = $data['title'];
            $event->label = $data['select-label'];
            $event->start = $data['start-date'];
            $event->end = $data['end-date'];
            $event->url = $data['event-url'];
            $event->guests = isset($data['event-guests']) ? implode(',', $data['event-guests']) : null;
            $event->location = $data['event-location'];
            $event->description = $data['event-description-editor'];
            $event->save();

            $output = ['success' => 1,
                'msg' => 'Event updated successfully!',
                'redirect' => action('CalenderAppController@calendarApp')
            ];
            DB::commit();
          
        } catch (\Exception $e) {
            \Log::emergency("File:" . $e->getFile(). " Line:" . $e->getLine(). " Message:" . $e->getMessage());
            $output = ['success' => 0,
                        'msg' => env('APP_DEBUG') ? $e->getMessage() : 'Sorry something went wrong, please try again later.'
                    ];
             DB::rollBack();
        }
        return response()->json($output);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request)
    {
        DB::beginTransaction();
        try {
            $event = Event::find($request->id);
            $event->delete();

            $output = ['success' => 1,
                'msg' => 'Event deleted successfully!',
                'redirect' => action('CalenderAppController@calendarApp')
            ];
            DB::commit();
          
        } catch (\Exception $e) {
            \Log::emergency("File:" . $e->getFile(). " Line:" . $e->getLine(). " Message:" . $e->getMessage());
            $output = ['success' => 0,
                        'msg' => env('APP_DEBUG') ? $e->getMessage() : 'Sorry something went wrong, please try again later.'
                    ];
             DB::rollBack();
        }
        return response()->json($output);
    }
}
