@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">Add Assign</div>

                    <div class="card-body">
                        <form action="{{route('assign_courses.store')}}" method="POST">
                            @csrf

                            <div class="form-group">
                                <label for="teacher_id">Choose Teacher:</label>
                                <select name="teacher_id" id="teacher_id">
                                    <option value="" selected disabled>Choose Teacher</option>
                                    @foreach ($teachers as $teacher)
                                        <option value="{{ $teacher->id }}">{{ $teacher->name }}</option>
                                    @endforeach
                                </select><br>
                            </div>

                            <div class="form-group">
                                <label for="course_id">Choose Course:</label>
                                <select name="course_id" id="course_id">
                                    <option value="" selected disabled>Choose Course</option>
                                    @foreach ($courses as $course)
                                        <option value="{{ $course->id }}">{{ $course->course_name }}</option>
                                    @endforeach
                                </select><br>
                            </div>

                            <button type="submit" class="btn btn-primary">Save</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
