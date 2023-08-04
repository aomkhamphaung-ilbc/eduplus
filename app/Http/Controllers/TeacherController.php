<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Teacher;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TeacherController extends Controller
{
    public function index(){
        $assignCourses = DB::table('teacher_courses')
            ->select('teacher_courses.*', 'teachers.name', 'courses.*')
            ->join('teachers', 'teachers.id', '=', 'teacher_courses.teacher_id')
            ->join('courses', 'courses.id', '=', 'teacher_courses.course_id')
            ->get();   
        
        return view('teacher.index', compact('assignCourses'));
    }

    public function create(){
        $teachers = DB::table('teachers')->get();
        $courses = DB::table('courses')->get();

        return view('teacher.create', compact('teachers', 'courses'));
    }

    public function store(Request $request){
        DB::table('teacher_courses')->insert([
            'teacher_id' => $request->teacher_id,
            'course_id' => $request->course_id
        ]);

        return redirect()->route('teacher.index')->with('success', 'Assign added successfully!');
    }

    public function destroy(Teacher $teacher, Course $course){
        $teacher->courses()->detach($course->id);

        return redirect()->back()->with('success', 'Course assign deleted successfully!');
    }
}
