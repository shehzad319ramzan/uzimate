@php
    $filters = $filters ?? [];
    $searchValue = $filters['search'] ?? '';
    $filterMerchant = $filters['merchant_id'] ?? '';
    $filterStatus = $filters['status'] ?? '';
    $hasSurveyActions =
        auth()->user()?->can('view_survey') ||
        auth()->user()?->can('edit_survey') ||
        auth()->user()?->can('delete_survey');
@endphp

<x-layouts.auth>
    <x-slot name="pageTitle">Surveys</x-slot>

    <div class="row mt-3">
        <div class="col-md-12">
            <x-all-list title="Surveys" :data="$data['all']">
                <x-slot name="headerCustom">
                    <form method="GET" action="{{ route('surveys.index') }}" class="row g-2 align-items-end w-100 mb-3">
                        <div class="col-lg-3 col-md-6">
                            <label for="search" class="form-label text-muted small">Search</label>
                            <input type="text" name="search" id="search" class="form-control"
                                placeholder="Title or description" value="{{ $searchValue }}">
                        </div>
                        <div class="col-lg-2 col-md-6">
                            <label for="merchant_filter" class="form-label text-muted small">Merchant</label>
                            <select name="merchant_id" id="merchant_filter" class="form-select">
                                <option value="">All Merchants</option>
                                @foreach ($merchants as $merchant)
                                    <option value="{{ $merchant->id }}"
                                        {{ (string) $filterMerchant === (string) $merchant->id ? 'selected' : '' }}>
                                        {{ $merchant->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-lg-2 col-md-6">
                            <label for="status_filter" class="form-label text-muted small">Status</label>
                            <select name="status" id="status_filter" class="form-select">
                                <option value="">All</option>
                                <option value="1" {{ $filterStatus === '1' ? 'selected' : '' }}>Active</option>
                                <option value="0" {{ $filterStatus === '0' ? 'selected' : '' }}>Inactive</option>
                            </select>
                        </div>
                        <div class="col-lg-12 col-md-12 d-flex gap-2 align-items-center">
                            <button type="submit" class="btn btn-primary mt-3">Apply Filters</button>
                            <a href="{{ route('surveys.index') }}" class="btn btn-outline-secondary mt-3">Reset</a>
                            @can('add_survey')
                                <x-auth.href-link link-href="{{ route('surveys.create') }}"
                                    link-value="{{ __('Create Survey') }}" link-class="btn btn-success mt-3" />
                            @endcan
                        </div>
                    </form>
                </x-slot>

                <x-auth.datatable>
                    <thead>
                        <tr>
                            <th>Sr#</th>
                            <th>Image</th>
                            <th>Title</th>
                            <th>Merchant</th>
                            <th>Points</th>
                            <th>Est. mins</th>
                            <th>Status</th>
                            @if ($hasSurveyActions)
                                <th>Action</th>
                            @endif
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($data['all'] as $key => $survey)
                            <tr>
                                <td>{{ $key + 1 + ($data['all']->currentPage() - 1) * $data['all']->perPage() }}</td>
                                <td>
                                    @php $img = $survey->image(); @endphp
                                    @if (!empty($img))
                                        <img src="{{ $img }}" alt="{{ $survey->title }}" class="rounded"
                                            width="50" height="50" style="object-fit: cover;" />
                                    @else
                                        <div class="rounded d-inline-flex align-items-center justify-content-center text-white fw-bold"
                                            style="width: 50px; height: 50px; background-color: #4A148D; font-size: 18px;">
                                            {{ strtoupper(substr($survey->title ?? 'S', 0, 1)) }}
                                        </div>
                                    @endif
                                </td>
                                <td>
                                    <div class="text-dark fw-semibold">{{ $survey->title ?? '-' }}</div>
                                    @if ($survey->description)
                                        <small class="text-muted">{{ Str::limit($survey->description, 50) }}</small>
                                    @endif
                                </td>
                                <td>{{ $survey->merchant->name ?? '-' }}</td>
                                <td><span class="badge bg-info">{{ $survey->points ?? 0 }}</span></td>
                                <td>{{ $survey->estimated_minutes ?? 1 }} min</td>
                                <td>
                                    @if ($survey->status == '1')
                                        <span class="badge bg-success">Active</span>
                                    @else
                                        <span class="badge bg-danger">Inactive</span>
                                    @endif
                                </td>
                                @if ($hasSurveyActions)
                                    <td class="text-center">
                                        @can('view_survey')
                                            <button type="button" class="btn btn-sm btn-outline-info check-questions-btn"
                                                data-survey-id="{{ $survey->id }}" data-survey-title="{{ e($survey->title) }}" title="Check questions">
                                                <i class="fas fa-list-check me-1"></i> Check Questions
                                            </button>
                                            <a href="{{ route('surveys.show', $survey->id) }}"
                                                class="btn btn-sm btn-outline-primary">View</a>
                                        @endcan
                                        @can('edit_survey')
                                            <a href="{{ route('surveys.edit', $survey->id) }}"
                                                class="btn btn-sm btn-outline-secondary">Edit</a>
                                        @endcan
                                        @can('delete_survey')
                                            <form action="{{ route('surveys.destroy', $survey->id) }}" method="POST"
                                                class="d-inline" onsubmit="return confirm('Delete this survey?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-outline-danger">Delete</button>
                                            </form>
                                        @endcan
                                    </td>
                                @endif
                            </tr>
                        @endforeach
                    </tbody>
                </x-auth.datatable>
            </x-all-list>
        </div>
    </div>

    {{-- Survey Questions modal (content loaded via AJAX) --}}
    <div class="modal fade" id="surveyQuestionsModal" tabindex="-1" aria-labelledby="surveyQuestionsModalLabel" aria-hidden="true" data-bs-backdrop="true" data-bs-keyboard="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content shadow-lg border-0">
                <div class="modal-header border-0 pb-0">
                    <h5 class="modal-title fw-bold" id="surveyQuestionsModalLabel">
                        <i class="fas fa-clipboard-list text-primary me-2"></i> Survey Questions
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div id="surveyQuestionsModalContent" class="min-vh-20">
                    <div class="modal-body text-center py-5">
                        <div class="spinner-border text-primary" role="status"><span class="visually-hidden">Loading...</span></div>
                        <p class="text-muted mt-2 mb-0">Loading questions…</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('auth_scripts')
    <script>
(function() {
    const modal = document.getElementById('surveyQuestionsModal');
    const contentEl = document.getElementById('surveyQuestionsModalContent');
    const questionsPreviewUrlTemplate = '{{ route("surveys.questions.preview", ["id" => "__ID__"]) }}';

    document.querySelectorAll('.check-questions-btn').forEach(function(btn) {
        btn.addEventListener('click', function() {
            const id = this.getAttribute('data-survey-id');
            if (!id) return;
            contentEl.innerHTML = '<div class="modal-body text-center py-5"><div class="spinner-border text-primary" role="status"></div><p class="text-muted mt-2 mb-0">Loading questions…</p></div>';
            const modalInstance = new bootstrap.Modal(modal);
            modalInstance.show();
            fetch(questionsPreviewUrlTemplate.replace('__ID__', encodeURIComponent(id)))
                .then(function(r) { return r.text(); })
                .then(function(html) {
                    contentEl.innerHTML = html;
                })
                .catch(function() {
                    contentEl.innerHTML = '<div class="modal-body text-center py-5"><p class="text-danger mb-0">Could not load questions.</p></div>';
                });
        });
    });
})();
    </script>
    @endpush
</x-layouts.auth>
