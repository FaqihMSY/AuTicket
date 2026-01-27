@extends('layouts.app')

@section('content')
    <div class="container-fluid">
        <div class="mb-3">
            <a href="{{ route('auditors.index') }}" class="btn btn-sm btn-outline-secondary">
                ← Back to List
            </a>
        </div>

        <!-- Header -->
        <div class="card mb-4">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col-md-9">
                        <h3 class="mb-2">{{ $auditor->user->name }}</h3>
                        <div class="text-muted">
                            @if($auditor->specialization)
                                <span class="me-3">{{ $auditor->specialization }}</span>
                            @endif
                        </div>
                    </div>
                    <div class="col-md-3 text-end">
                        <div class="mb-1">
                            <h1 class="mb-0">{{ number_format($auditor->performance_score, 0) }}</h1>
                            <small class="text-muted">Performance Score</small>
                        </div>
                        <span class="badge bg-{{ $auditor->getWorkloadColor() }}">
                            {{ $auditor->getWorkloadStatus() }}
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Key Metrics -->
        <div class="row mb-4">
            <div class="col-md-3">
                <div class="card">
                    <div class="card-body text-center">
                        <h2 class="mb-0">{{ $stats['total_projects'] }}</h2>
                        <small class="text-muted">Total Projects</small>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card">
                    <div class="card-body text-center">
                        <h2 class="mb-0">{{ number_format($stats['completion_rate'], 0) }}%</h2>
                        <small class="text-muted">Completion Rate</small>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card">
                    <div class="card-body text-center">
                        <h2 class="mb-0">{{ number_format($stats['on_time_rate'], 0) }}%</h2>
                        <small class="text-muted">On-Time Rate</small>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card">
                    <div class="card-body text-center">
                        <h2 class="mb-0">{{ number_format($stats['average_completion_days'], 0) }}</h2>
                        <small class="text-muted">Avg Days</small>
                    </div>
                </div>
            </div>
        </div>

        <!-- Project Breakdown & Charts -->
        <div class="row mb-4">
            <div class="col-md-4">
                <div class="card h-100">
                    <div class="card-body">
                        <h6 class="card-title text-muted mb-3">Completed by Type</h6>
                        @if(count($stats['project_type_breakdown']) > 0)
                            <table class="table table-sm table-borderless">
                                <tbody>
                                    @foreach($stats['project_type_breakdown'] as $breakdown)
                                        <tr>
                                            <td>{{ $breakdown['type'] }}</td>
                                            <td class="text-end"><strong>{{ $breakdown['count'] }}</strong></td>
                                        </tr>
                                    @endforeach
                                </tbody>
                                <tfoot>
                                    <tr class="border-top">
                                        <td><strong>Total</strong></td>
                                        <td class="text-end"><strong>{{ $stats['completed_projects'] }}</strong></td>
                                    </tr>
                                </tfoot>
                            </table>
                        @else
                            <p class="text-muted text-center py-3 mb-0">No completed projects</p>
                        @endif
                    </div>
                </div>
            </div>
            <div class="col-md-8">
                <div class="card h-100">
                    <div class="card-body">
                        <h6 class="card-title text-muted mb-3">Performance Trend (6 Months)</h6>
                        <div style="height: 250px;">
                            <canvas id="performanceTrendChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Rating Breakdown -->
        <div class="row mb-4">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body">
                        <h6 class="card-title text-muted mb-3">Rating Breakdown</h6>
                        <div style="height: 200px;">
                            <canvas id="ratingBreakdownChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Review History -->
        <div class="card">
            <div class="card-body">
                <h6 class="card-title text-muted mb-3">Review History</h6>
                <div class="table-responsive">
                    <table class="table table-sm table-hover">
                        <thead class="table-light">
                            <tr>
                                <th>Project</th>
                                <th>Date</th>
                                <th class="text-center">Overall</th>
                                <th class="text-center">Timeliness</th>
                                <th class="text-center">Completeness</th>
                                <th class="text-center">Quality</th>
                                <th class="text-center">Communication</th>
                                <th>Feedback</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($auditor->reviews as $review)
                                <tr>
                                    <td>
                                        @if($review->project)
                                            <a href="{{ route('projects.show', $review->project) }}">
                                                {{ $review->project->title }}
                                            </a>
                                        @else
                                            <span class="text-muted">[Deleted Project]</span>
                                        @endif
                                    </td>
                                    <td><small class="text-muted">{{ $review->created_at->format('d M Y') }}</small></td>
                                    <td class="text-center">
                                        <strong>{{ $review->overall_rating }}</strong>
                                    </td>
                                    <td class="text-center">{{ $review->timeliness_rating ?? '-' }}</td>
                                    <td class="text-center">{{ $review->completeness_rating ?? '-' }}</td>
                                    <td class="text-center">{{ $review->quality_rating ?? '-' }}</td>
                                    <td class="text-center">{{ $review->communication_rating ?? '-' }}</td>
                                    <td>
                                        <small
                                            class="text-muted">{{ $review->feedback ? Str::limit($review->feedback, 50) : '-' }}</small>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="text-center text-muted py-3">No reviews yet</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            fetch('{{ route('auditors.chart-data', $auditor) }}')
                .then(response => response.json())
                .then(data => {
                    renderPerformanceTrendChart(data.performance_trend);
                    renderRatingBreakdownChart(data.rating_breakdown);
                });
        });

        function renderPerformanceTrendChart(data) {
            const ctx = document.getElementById('performanceTrendChart').getContext('2d');

            new Chart(ctx, {
                type: 'line',
                data: {
                    labels: data.map(item => item.month),
                    datasets: [{
                        label: 'Performance Score',
                        data: data.map(item => item.score),
                        borderColor: '#0d6efd',
                        backgroundColor: 'rgba(13, 110, 253, 0.05)',
                        tension: 0.3,
                        fill: true,
                        borderWidth: 2,
                        pointRadius: 3,
                        pointHoverRadius: 5
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            beginAtZero: true,
                            max: 100,
                            ticks: {
                                callback: function (value) {
                                    return value;
                                }
                            },
                            grid: {
                                color: 'rgba(0, 0, 0, 0.05)'
                            }
                        },
                        x: {
                            grid: {
                                display: false
                            }
                        }
                    },
                    plugins: {
                        legend: {
                            display: false
                        },
                        tooltip: {
                            backgroundColor: '#000',
                            padding: 10,
                            callbacks: {
                                label: function (context) {
                                    return 'Score: ' + (context.parsed.y || 'No data');
                                }
                            }
                        }
                    }
                }
            });
        }

        function renderRatingBreakdownChart(data) {
            const ctx = document.getElementById('ratingBreakdownChart').getContext('2d');

            new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: ['Timeliness', 'Completeness', 'Quality', 'Communication'],
                    datasets: [{
                        label: 'Average Rating',
                        data: [
                            data.timeliness,
                            data.completeness,
                            data.quality,
                            data.communication
                        ],
                        backgroundColor: '#0d6efd',
                        borderWidth: 0
                    }]
                },
                options: {
                    indexAxis: 'y',
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        x: {
                            beginAtZero: true,
                            max: 100,
                            grid: {
                                color: 'rgba(0, 0, 0, 0.05)'
                            }
                        },
                        y: {
                            grid: {
                                display: false
                            }
                        }
                    },
                    plugins: {
                        legend: {
                            display: false
                        },
                        tooltip: {
                            backgroundColor: '#000',
                            padding: 10,
                            callbacks: {
                                label: function (context) {
                                    return 'Rating: ' + context.parsed.x;
                                }
                            }
                        }
                    }
                }
            });
        }
    </script>
@endsection