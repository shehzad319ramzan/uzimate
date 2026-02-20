<x-layouts.auth>
    <x-slot name="pageTitle">Survey Detail</x-slot>
    <x-auth.card card-header="Survey: {{ $data->title }}" header-button="true">
        <x-slot name="headerCustom">
            <x-auth.href-link link-href="{{ route('surveys.index') }}" link-value="{{ __('Back') }}"
                link-class="btn btn-outline-primary me-2" />
            @can('edit_survey')
                <x-auth.href-link link-href="{{ route('surveys.edit', $data->id) }}" link-value="{{ __('Edit') }}"
                    link-class="btn btn-primary" />
            @endcan
        </x-slot>
        <div class="row">
            <div class="col-md-3">
                @php $img = $data->image(); @endphp
                @if (!empty($img))
                    <img src="{{ $img }}" alt="{{ $data->title }}" class="img-fluid rounded"
                        style="max-height: 200px; object-fit: cover;" />
                @else
                    <div class="rounded d-flex align-items-center justify-content-center text-white fw-bold bg-primary"
                        style="height: 150px;">{{ strtoupper(substr($data->title ?? 'S', 0, 1)) }}</div>
                @endif
            </div>
            <div class="col-md-9">
                <p><strong>Title:</strong> {{ $data->title }}</p>
                <p><strong>Points:</strong> <span class="badge bg-info">{{ $data->points }}</span></p>
                <p><strong>Estimated time:</strong> {{ $data->estimated_minutes }} min</p>
                <p><strong>Merchant:</strong> {{ $data->merchant->name ?? '—' }}</p>
                <p><strong>Status:</strong>
                    @if ($data->status == '1')
                    <span class="badge bg-success">Active</span>@else<span class="badge bg-danger">Inactive</span>
                    @endif
                </p>
                @if ($data->description)
                    <p><strong>Description:</strong><br>{{ $data->description }}</p>
                @endif
            </div>
        </div>
        <hr>
        <h5>Questions ({{ $data->questions->count() }})</h5>
        @forelse($data->questions as $i => $q)
            <div class="card mb-2">
                <div class="card-body">
                    <strong>Q{{ $i + 1 }}:</strong> {{ $q->question_text }}
                    <ul class="mb-0 mt-1">
                        @foreach ($q->options as $opt)
                            <li>{{ $opt->option_text }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        @empty
            <p class="text-muted">No questions. Add them in Edit.</p>
        @endforelse
    </x-auth.card>
</x-layouts.auth>
