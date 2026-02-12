@extends('backend.layouts.app', ['isBanner' => false])

@section('title', 'Movie Collections')

@section('content')

<style>
    /* Card Style */
    .stat-card {
        background: #1d2530;
        border-radius: 14px;
        padding: 22px;
        transition: 0.3s ease;
        border: 1px solid #2b3544;
    }
    .stat-card:hover {
        transform: translateY(-4px);
        border-color: #3f4b5f;
    }

    .stat-icon {
        width: 48px;
        height: 48px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 12px;
        font-size: 26px;
    }

    .table thead th {
        background: #121821 !important;
        border: none !important;
        font-weight: 600;
        padding: 14px;
    }

    .table tbody tr {
        border-bottom: 1px solid #2c3645;
    }

    .table tbody tr:hover {
        background: rgba(255, 255, 255, 0.02);
    }

    .card-section-header {
        background: #121821;
        padding: 14px 18px;
        border-bottom: 1px solid #232b36;
        border-radius: 12px 12px 0 0;
    }
</style>


<div class="container-fluid">

    {{-- Page Title --}}
    <div class="row mb-4">
        <div class="col-12">
            <h4 class="fw-bold">🎬 Movie Collections</h4>
        </div>
    </div>

    {{-- Summary Cards --}}
    <div class="row">

        {{-- Total Movies --}}
        <div class="col-lg-4 col-md-6 mb-4">
            <div class="stat-card d-flex align-items-center">
                <div class="stat-icon bg-primary text-white shadow">
                    <i class="ph ph-film-slate"></i>
                </div>
                <div class="ms-3">
                    <p class="text-muted small mb-1">Total Movies</p>
                    <h3 class="mb-0">{{ $collections->count() }}</h3>
                </div>
            </div>
        </div>

        {{-- Total Tickets --}}
        <div class="col-lg-4 col-md-6 mb-4">
            <div class="stat-card d-flex align-items-center">
                <div class="stat-icon bg-success text-white shadow">
                    <i class="ph ph-ticket"></i>
                </div>
                <div class="ms-3">
                    <p class="text-muted small mb-1">Total Tickets Sold</p>
                    <h3 class="mb-0">{{ $collections->sum('total_tickets') }}</h3>
                </div>
            </div>
        </div>

        {{-- Total Collection --}}
        <div class="col-lg-4 col-md-6 mb-4">
            <div class="stat-card d-flex align-items-center">
                <div class="stat-icon bg-warning text-dark shadow">
                    <i class="ph ph-coins"></i>
                </div>
                <div class="ms-3">
                    <p class="text-muted small mb-1">Total Collection</p>
                    <h3 class="mb-0">${{ number_format($collections->sum('total_collection'), 2) }}</h3>
                </div>
            </div>
        </div>

    </div>

    {{-- Table --}}
    <div class="row mt-3">
        <div class="col-12">
            <div class="card" style="background:#1a212c; border-radius:14px; border:1px solid #2a3240;">
                
                <div class="card-section-header">
                    <h6 class="fw-bold mb-0">Movie-wise Collections</h6>
                </div>

                <div class="table-responsive">
                    <table class="table table-hover mb-0 align-middle text-white">

                        <thead>
                            <tr>
                                <th>Movie</th>
                                <th>Total Tickets</th>
                                <th>Total Collection</th>
                                <th>Last Updated</th>
                            </tr>
                        </thead>

                        <tbody>
                            @forelse($collections as $row)
                            <tr>
                               <td class="d-flex align-items-center">
                                    <img src="{{ $row->poster_url }}" 
                                         alt="{{ $row->movie_name }}" 
                                         style="width: 70px; height: 90px; border-radius: 6px; margin-right: 12px;">
                                
                                    <span class="fw-semibold">{{ $row->movie_name }}</span>
                                </td>

                                <td>{{ $row->total_tickets }}</td>
                                <td>${{ number_format($row->total_collection, 2) }}</td>
                                <td>{{ \Carbon\Carbon::parse($row->updated_at)->format('d M Y, h:i A') }}</td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="text-center py-4 text-muted">
                                    No collections found.
                                </td>
                            </tr>
                            @endforelse
                        </tbody>

                    </table>
                </div>

            </div>
        </div>
    </div>

</div>

@endsection
