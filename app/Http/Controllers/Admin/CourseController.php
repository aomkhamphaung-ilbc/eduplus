<?php

namespace App\Http\Controllers\Admin;

use App\Models\Level;
use App\Models\Course;
use App\Models\Section;
use App\Models\Category;
use App\Models\Classroom;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Teacher;
use App\Models\Meeting;
use App\Models\Subcategory;
use Google_Client;
use Google_Service_Calendar;
use Google_Service_Calendar_Event;
use Google\Service\Calendar\EventDateTime;
use Google_Service_Calendar_ConferenceSolutionKey;
use Google_Service_Calendar_ConferenceData;
use Google_Service_Calendar_CreateConferenceRequest;

class CourseController extends Controller
{
    public function index()
    {
        $categories = Category::all();
        $levels = Level::all();
        $classrooms = Classroom::all();
        $sections = Section::all();
        $subcategories = Subcategory::all();
        $courses = Course::with('categories', 'levels', 'classrooms', 'sections', 'subcategories')
            ->orderBy('id', 'desc')
            ->paginate(10);

        return view('admin.courses.index', compact('courses'));
    }

    public function create()
    {

        $categories = Category::all();
        $levels = Level::all();
        $classrooms = Classroom::all();
        $sections = Section::all();
        $teachers = Teacher::all();
        $subcategories = Subcategory::all();

        return view('admin.courses.create', compact('categories', 'levels', 'sections', 'classrooms', 'teachers', 'subcategories'));
    }

    public function store(Request $request)

    {

        // $data = $request->validate([

        //     'course_name' => 'required|string|max:255',

        //     'description' => 'nullable|string',

        //     'price' => 'required|numeric|min:0',

        //     'period' => 'required|string|max:255',

        //     'announce_date' => 'required|date',

        //     'category_id' => 'required|array',

        //     'category_id.*' => 'exists:categories,id',

        //     'subcategory_id' => 'required|array',

        //     'subcategory_id.*' => 'exists:subcategories,id',

        //     'level_id' => 'required|array',

        //     'level_id.*' => 'exists:levels,id',

        //     'classroom_id' => 'required|array',

        //     'classroom_id.*' => 'exists:classrooms,id',

        //     'section_id' => 'required|array',

        //     'section_id.*' => 'exists:sections,id',

        //     'teacher_id' => 'required|array',

        //     'teacher_id.*' => 'exists:teachers,id',

        //     'start_time' => 'required|date',

        //     'end_time' => 'required|date|after:start_time',

        // ]);



        // Create the meeting

        $client = new Google_Client();

        $client->setAuthConfig('client_secrets.json');

        $client->addScope(Google_Service_Calendar::CALENDAR_EVENTS);



        $accessToken = $request->header('Authorization');

        $client->setAccessToken($accessToken);



        if ($client->isAccessTokenExpired()) {

            $client->fetchAccessTokenWithRefreshToken($client->getRefreshToken());

            $accessToken = $client->getAccessToken();
        }



        $service = new Google_Service_Calendar($client);



        $event = new Google_Service_Calendar_Event();

        $startDateTime = new EventDateTime();

        $endDateTime = new EventDateTime();

        $event->setSummary('Meeting');

        $event->setDescription('Google Meeting');

        $startDateTime->setDateTime($request->input('start_time') . ':00');

        $endDateTime->setDateTime($request->input('end_time') . ':00');

        $startDateTime->setTimeZone('Asia/Yangon');

        $endDateTime->setTimeZone('Asia/Yangon');

        $event->setStart($startDateTime);

        $event->setEnd($endDateTime);



        $conferenceRequest = new Google_Service_Calendar_CreateConferenceRequest();

        $conferenceRequest->setRequestId(uniqid());

        $solution_key = new Google_Service_Calendar_ConferenceSolutionKey();

        $solution_key->setType("hangoutsMeet");

        $conferenceRequest->setConferenceSolutionKey($solution_key);



        $conference = new Google_Service_Calendar_ConferenceData();

        $conference->setCreateRequest($conferenceRequest);



        $event->setConferenceData($conference);



        $calendarId = 'primary';

        $event = $service->events->insert(

            $calendarId,

            $event,

            ['conferenceDataVersion' => 1]

        );



        $meetLink = $event->getHangoutLink();



        // Store the meeting details in the database

        $meeting = new Meeting();

        $meeting->start_time = $request->input('start_time');

        $meeting->end_time = $request->input('end_time');

        $meeting->meet_link = $meetLink;

        $meeting->save();

        // Create the course

        // $course = Course::create($data);

        // // Connect the meeting with the corresponding course

        // $course->meeting()->save($meeting);

        // $course->categories()->attach($data['category_id']);

        // $course->subcategories()->attach($data['subcategory_id']);

        // $course->levels()->attach($data['level_id']);

        // $course->classrooms()->attach($data['classroom_id']);

        // $course->sections()->attach($data['section_id']);

        // $course->teachers()->attach($data['teacher_id']);

        $course = Course::create([
            'course_name' => $request->input('course_name'),
            'description' => $request->input('description'),
            'price' => $request->input('price'),
            'period' => $request->input('period'),
            'announce_date' => $request->input('announce_date'),
        ]);

        $course->categories()->attach($request->input('category_id'));
        $course->levels()->attach($request->input('level_id'));
        $course->classrooms()->attach($request->input('classroom_id'));
        $course->sections()->attach($request->input('section_id'));
        $course->teachers()->attach($request->input('teacher_id'));
        $course->subcategories()->attach($request->input('subcategory_id'));

        dd($course);

        // return redirect()->route('courses.index')->with('meeting', 'categories', 'subcategories', 'levels', 'classrooms', 'sections', 'teachers');
    }

    public function edit($id)
    {
        $course = Course::with('categories')->findOrFail($id);
        $categories = Category::all();
        $levels = Level::all();
        $classrooms = Classroom::all();
        $sections = Section::all();

        return view('admin.courses.edit', compact('course', 'categories', 'levels', 'classrooms', 'sections'));
    }

    public function update(Request $request, $id)
    {
        $course = Course::findOrFail($id);
        $course->update([
            'course_name' => $request->input('course_name'),
            'description' => $request->input('description'),
            'price' => $request->input('price'),
            'period' => $request->input('period'),
            'announce_date' => $request->input('announce_date'),
        ]);

        // Sync the chosen category
        $course->categories()->sync($request->input('category_id'));
        $course->categories()->sync($request->input('category_id'));
        $course->levels()->sync($request->input('level_id'));
        $course->classrooms()->sync($request->input('classroom_id'));
        $course->sections()->sync($request->input('section_id'));

        return redirect()->route('courses.index');
    }

    public function destroy($id)
    {
        $course = Course::findOrFail($id);
        $course->delete();

        return redirect()->route('courses.index');
    }
}
