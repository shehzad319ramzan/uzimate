<x-layouts.auth>
    <x-slot name="pageTitle">Survey Response Details</x-slot>

    <x-auth.card card-header="Survey Response Details" header-button="true">
        <x-slot name="headerCustom">
            <x-auth.href-link link-href="{{ route('surveyresponses.index') }}" link-value="{{ __('Back to List') }}"
                link-class="btn btn-outline-primary me-2" />
        </x-slot>

        <div class="table-responsive mb-4">
            <table class="table table-striped">
                <tbody>
                    <tr>
                        <th class="text-muted" style="width: 200px;">User</th>
                        <td>
                            <div class="fw-semibold">{{ $data->user->first_name ?? '' }}
                                {{ $data->user->last_name ?? '' }}</div>
                            <small class="text-muted">{{ $data->user->email ?? '-' }}</small>
                        </td>
                    </tr>
                    <tr>
                        <th class="text-muted">Survey</th>
                        <td>
                            <div class="fw-semibold">{{ $data->survey->title ?? '-' }}</div>
                            <small class="text-muted">{{ $data->survey->points ?? 0 }} points ·
                                {{ $data->survey->estimated_minutes ?? 0 }} min</small>
                        </td>
                    </tr>
                    <tr>
                        <th class="text-muted">Completed at</th>
                        <td>{{ $data->completed_at ? $data->completed_at->format('d M Y, H:i:s') : '-' }}</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <h5 class="mb-3">Answers</h5>
        @forelse ($data->answers as $answer)
            <div class="card mb-2">
                <div class="card-body py-2">
                    <div class="fw-semibold text-dark">Q: {{ $answer->question->question_text ?? '-' }}</div>
                    <div class="mt-1">
                        <span class="badge bg-primary">A: {{ $answer->option->option_text ?? '-' }}</span>
                    </div>
                </div>
            </div>
        @empty
            <p class="text-muted">No answers recorded.</p>
        @endforelse
    </x-auth.card>
</x-layouts.auth>
