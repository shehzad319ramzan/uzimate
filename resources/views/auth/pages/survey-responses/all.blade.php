@php
    $filters = $filters ?? [];
    $searchValue = $filters['search'] ?? '';
    $filterSurvey = $filters['survey_id'] ?? '';
    $filterDateFrom = $filters['date_from'] ?? '';
    $filterDateTo = $filters['date_to'] ?? '';
@endphp

<x-layouts.auth>
    <x-slot name="pageTitle">Survey Responses</x-slot>

    <div class="row mt-3">
        <div class="col-md-12">
            <x-all-list title="Survey Responses (filled by users)" :data="$data['all']">
                <x-slot name="headerCustom">
                    <form method="GET" action="{{ route('surveyresponses.index') }}"
                        class="row g-2 align-items-end w-100 mb-3">
                        <div class="col-lg-2 col-md-6">
                            <label for="search" class="form-label text-muted small">Search user</label>
                            <input type="text" name="search" id="search" class="form-control"
                                placeholder="Name or email" value="{{ $searchValue }}">
                        </div>
                        <div class="col-lg-2 col-md-6">
                            <label for="survey_filter" class="form-label text-muted small">Survey</label>
                            <select name="survey_id" id="survey_filter" class="form-select">
                                <option value="">All Surveys</option>
                                @foreach ($surveys as $survey)
                                    <option value="{{ $survey->id }}"
                                        {{ (string) $filterSurvey === (string) $survey->id ? 'selected' : '' }}>
                                        {{ $survey->title }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-lg-2 col-md-6">
                            <label for="date_from" class="form-label text-muted small">Completed from</label>
                            <input type="date" name="date_from" id="date_from" class="form-control"
                                value="{{ $filterDateFrom }}">
                        </div>
                        <div class="col-lg-2 col-md-6">
                            <label for="date_to" class="form-label text-muted small">Completed to</label>
                            <input type="date" name="date_to" id="date_to" class="form-control"
                                value="{{ $filterDateTo }}">
                        </div>
                        <div class="col-lg-12 col-md-12 d-flex gap-2">
                            <button type="submit" class="btn btn-primary mt-3">Apply Filters</button>
                            <a href="{{ route('surveyresponses.index') }}"
                                class="btn btn-outline-secondary mt-3">Reset</a>
                        </div>
                    </form>

                    <div class="alert alert-info mb-3 p-2 small">
                        <i class="fas fa-info-circle me-2"></i>
                        <strong>Survey Responses</strong> — When a user completes a survey in the app, their submission
                        appears here. Click <strong>View</strong> to see their answers.
                    </div>
                </x-slot>

                <x-auth.datatable>
                    <thead>
                        <tr>
                            <th>Sr#</th>
                            <th>User</th>
                            <th>Survey</th>
                            <th>Completed at</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($data['all'] as $key => $response)
                            <tr>
                                <td>{{ $key + 1 + ($data['all']->currentPage() - 1) * $data['all']->perPage() }}</td>
                                <td>
                                    <div class="fw-semibold">{{ $response->user->first_name ?? '' }}
                                        {{ $response->user->last_name ?? '' }}</div>
                                    <small class="text-muted">{{ $response->user->email ?? '-' }}</small>
                                </td>
                                <td>
                                    <div class="fw-semibold">{{ $response->survey->title ?? '-' }}</div>
                                    <small class="text-muted">{{ $response->survey->points ?? 0 }} pts</small>
                                </td>
                                <td>{{ $response->completed_at ? $response->completed_at->format('d M Y, H:i') : '-' }}
                                </td>
                                <td>
                                    <a href="{{ route('surveyresponses.show', $response->id) }}"
                                        class="btn btn-sm btn-outline-primary">View</a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td class="text-center text-muted py-4">No survey responses yet. Users will appear here
                                    after they complete a survey in the app.</td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                            </tr>
                        @endforelse
                    </tbody>
                </x-auth.datatable>
            </x-all-list>
        </div>
    </div>
</x-layouts.auth>
