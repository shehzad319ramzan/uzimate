<x-layouts.auth>
    <x-slot name="pageTitle">Edit Survey</x-slot>
    <x-auth.card card-header="Edit Survey" header-button="true">
        <x-slot name="headerCustom">
            <x-auth.href-link link-href="{{ route('surveys.index') }}" link-value="{{ __('Back to List') }}"
                link-class="btn btn-outline-primary me-2" />
        </x-slot>
        <x-auth.form form-action="{{ route('surveys.update', $data->id) }}" enctype="true">
            @method('PUT')
            <div class="row">
                <div class="col-md-12 mb-3">
                    <label class="form-label">Survey Image</label>
                    <x-auth.upload-file image="{{ $data->image() }}" name="{{ $data->title ?? '' }}" />
                </div>
                <div class="col-md-8 mb-3">
                    <x-auth.input-field type="text" name="title" id="title" required="true"
                        place="Survey title" val="{{ old('title', $data->title) }}" label="Title" />
                </div>
                <div class="col-md-4 mb-3">
                    <x-auth.input-field type="number" name="points" id="points" required="true" place="e.g. 750"
                        val="{{ old('points', $data->points) }}" label="Points" />
                </div>
                <div class="col-md-6 mb-3">
                    <x-auth.input-field type="number" name="estimated_minutes" id="estimated_minutes" required="true"
                        place="e.g. 3" val="{{ old('estimated_minutes', $data->estimated_minutes) }}"
                        label="Estimated minutes" />
                </div>
                <div class="col-md-6 mb-3">
                    <label for="merchant_id" class="form-label">Merchant</label>
                    <select class="form-select" name="merchant_id" id="merchant_id">
                        <option value="">— None —</option>
                        @foreach ($merchants as $m)
                            <option value="{{ $m->id }}"
                                {{ old('merchant_id', $data->merchant_id) == $m->id ? 'selected' : '' }}>
                                {{ $m->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-12 mb-3">
                    <label for="description" class="form-label">Description</label>
                    <textarea class="form-control" name="description" id="description" rows="4">{{ old('description', $data->description) }}</textarea>
                </div>
                <div class="col-md-12 mb-3">
                    <label for="status" class="form-label">Status</label>
                    <select class="form-select" name="status" id="status">
                        <option value="1" {{ old('status', $data->status) == '1' ? 'selected' : '' }}>Active
                        </option>
                        <option value="0" {{ old('status', $data->status) == '0' ? 'selected' : '' }}>Inactive
                        </option>
                    </select>
                </div>
            </div>
            <div class="row mb-4">
                <div class="col-md-12 d-flex justify-content-end">
                    <a href="{{ route('surveys.index') }}" class="btn btn-secondary me-2">Cancel</a>
                    <x-auth.input-button btn-class="btn-primary" btn-type="submit" btn-value="Update Survey" />
                </div>
            </div>
        </x-auth.form>

        <hr class="my-4">
        <h5 class="mb-3">Survey Questions</h5>
        @forelse($data->questions as $q)
            <div class="card mb-2">
                <div class="card-body py-2 d-flex justify-content-between align-items-center">
                    <div>
                        <strong>Q:</strong> {{ $q->question_text }}
                        <div class="small text-muted mt-1">
                            Options: {{ $q->options->pluck('option_text')->join(', ') }}
                        </div>
                    </div>
                    <form action="{{ route('surveys.questions.destroy', $q->id) }}" method="POST" class="d-inline"
                        onsubmit="return confirm('Remove this question?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-sm btn-outline-danger">Remove</button>
                    </form>
                </div>
            </div>
        @empty
            <p class="text-muted">No questions yet. Add one below.</p>
        @endforelse

        <form action="{{ route('surveys.questions.store', $data->id) }}" method="POST"
            class="mt-3 p-3 border rounded">
            @csrf
            <h6>Add question</h6>
            <div class="mb-2">
                <label class="form-label">Question text</label>
                <input type="text" name="question_text" class="form-control" required
                    placeholder="e.g. Which type of rewards excites you the most?" />
            </div>
            <div class="mb-2">
                <label class="form-label">Options (one per line)</label>
                <textarea name="options[0]" class="form-control mb-1" rows="1" placeholder="Option 1"></textarea>
                <textarea name="options[1]" class="form-control mb-1" rows="1" placeholder="Option 2"></textarea>
                <textarea name="options[2]" class="form-control mb-1" rows="1" placeholder="Option 3"></textarea>
                <small class="text-muted">Add at least one option.</small>
            </div>
            <button type="submit" class="btn btn-primary">Add question</button>
        </form>
    </x-auth.card>
</x-layouts.auth>
