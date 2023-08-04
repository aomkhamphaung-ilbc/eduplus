@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">Assign Courses</div>

                    <div class="card-body">
                        <a href="{{route('assign_courses.create')}}" class="btn btn-primary mb-2">Add Assign</a>

                        @if(session('success'))
                            <div class="alert alert-success">
                                {{ session('success') }}
                            </div>
                        @endif

                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Teacher Name</th>
                                    <th>Course Name</th>
                                    <th>Course Description</th>
                                    <th>Price</th>
                                    <th>Period</th>
                                    <th>Announce Date</th>
                                    <!-- Add more columns for other class attributes if needed -->
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($assignCourses as $assignCourse)
                                    <tr>
                                        <td>{{ $assignCourse->name }}</td>
                                        <td>{{ $assignCourse->course_name }}</td>
                                        <td>{{ $assignCourse->description }}</td>
                                        <td>{{ $assignCourse->price }}</td>
                                        <td>{{ $assignCourse->period }}</td>
                                        <td>{{ $assignCourse->announce_date }}</td>
                                        <td>
                                            <form action="{{route('assign-courses.destroy', ['teacher' => $assignCourse->teacher_id, 'course' => $assignCourse->course_id])}}" method="POST" class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this class?')">Delete</button>
                                            </form>
                                            <form action="{{route('meeting.view', $assignCourse->course_id)}}" method="post">
                                                @csrf
                                                <button type="submit">Create Meeting</button>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
