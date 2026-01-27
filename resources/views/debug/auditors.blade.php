@extends('layouts.app')

@section('content')
<div class="container">
    <h2>Auditor Debug Info</h2>
    
    <div class="card mb-4">
        <div class="card-body">
            <h5 class="card-title">Auditor-User Mapping</h5>
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Auditor ID</th>
                        <th>User ID</th>
                        <th>User Name</th>
                        <th>Email</th>
                        <th>Performance Score</th>
                        <th>Profile Link</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($auditors as $auditor)
                    <tr>
                        <td>{{ $auditor->id }}</td>
                        <td>{{ $auditor->user_id }}</td>
                        <td>{{ $auditor->user->name }}</td>
                        <td>{{ $auditor->user->email }}</td>
                        <td>{{ $auditor->performance_score }}</td>
                        <td>
                            <a href="{{ route('auditors.show', $auditor) }}" target="_blank">
                                /auditors/{{ $auditor->id }}
                            </a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    
    <div class="card">
        <div class="card-body">
            <h5 class="card-title">Project Assignments</h5>
            @php
                $projects = \App\Models\Project::with('auditors.user')->get();
            @endphp
            
            @foreach($projects as $project)
            <div class="mb-3">
                <h6>Project #{{ $project->id }}: {{ $project->title }}</h6>
                <ul>
                    @foreach($project->auditors as $auditor)
                    <li>Auditor ID: {{ $auditor->id }} | User: {{ $auditor->user->name }}</li>
                    @endforeach
                </ul>
            </div>
            @endforeach
        </div>
    </div>
</div>
@endsection
