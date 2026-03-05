<x-layouts.auth>
    <x-slot name="pageTitle">Create Survey</x-slot>
    <x-auth.card card-header="Create Survey" header-button="true">
        <x-slot name="headerCustom">
            <x-auth.href-link link-href="{{ route('surveys.index') }}" link-value="{{ __('Back') }}"
                link-class="btn btn-outline-primary me-2" />
        </x-slot>
        <x-auth.form form-action="{{ route('surveys.store') }}" enctype="true">
            <ul class="nav nav-tabs mb-3" id="createSurveyTabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="details-tab" data-bs-toggle="tab" data-bs-target="#details-pane"
                        type="button" role="tab">Survey Details</button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="questions-tab" data-bs-toggle="tab" data-bs-target="#questions-pane"
                        type="button" role="tab">Survey Questions</button>
                </li>
            </ul>

            <div class="tab-content" id="createSurveyTabContent">
                <div class="tab-pane fade show active" id="details-pane" role="tabpanel">
                    <div class="row">
                        <div class="col-md-12 mb-3">
                            <label class="form-label">Survey Image</label>
                            <x-auth.upload-file image="" name="{{ old('title') }}" />
                            @error('file')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="col-md-8 mb-3">
                            <x-auth.input-field type="text" name="title" id="title" required="true"
                                place="Survey title" val="{{ old('title') }}" label="Title" />
                        </div>
                        <div class="col-md-4 mb-3">
                            <x-auth.input-field type="number" name="points" id="points" required="true"
                                place="e.g. 750" val="{{ old('points', '750') }}" label="Points" />
                        </div>
                        <div class="col-md-6 mb-3">
                            <x-auth.input-field type="number" name="estimated_minutes" id="estimated_minutes"
                                required="true" place="e.g. 3" val="{{ old('estimated_minutes', '3') }}"
                                label="Estimated minutes" />
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="merchant_id" class="form-label">Merchant</label>
                            @if(!$isSuperAdmin && $selectedMerchantId)
                                <input type="hidden" name="merchant_id" value="{{ $selectedMerchantId }}">
                                <select class="form-select" id="merchant_id" disabled>
                                    <option value="{{ $selectedMerchantId }}" selected>
                                        {{ $merchants->firstWhere('id', $selectedMerchantId)?->name ?? $merchants->first()->name ?? 'N/A' }}
                                    </option>
                                </select>
                            @else
                                <select class="form-select" name="merchant_id" id="merchant_id">
                                    <option value="">— None —</option>
                                    @foreach ($merchants as $m)
                                        <option value="{{ $m->id }}"
                                            {{ ($selectedMerchantId == $m->id || old('merchant_id') == $m->id) ? 'selected' : '' }}>{{ $m->name }}
                                        </option>
                                    @endforeach
                                </select>
                            @endif
                        </div>
                        <div class="col-md-12 mb-3">
                            <label for="description" class="form-label">Description</label>
                            <textarea class="form-control" name="description" id="description" rows="4" placeholder="Survey description">{{ old('description') }}</textarea>
                        </div>
                        <div class="col-md-12 mb-3">
                            <label for="status" class="form-label">Status</label>
                            <select class="form-select" name="status" id="status">
                                <option value="1" {{ old('status', '1') == '1' ? 'selected' : '' }}>Active
                                </option>
                                <option value="0" {{ old('status') == '0' ? 'selected' : '' }}>Inactive</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="tab-pane fade" id="questions-pane" role="tabpanel">
                    <p class="text-muted small mb-3">Add questions and options. At least one option per question is
                        required. Questions are optional on create; you can add more later in Edit.</p>
                    <div id="questions-container">
                        {{-- First question block --}}
                        <div class="card mb-3 question-block" data-index="0">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <strong class="question-number">Question 1</strong>
                                    <button type="button" class="btn btn-sm btn-outline-danger remove-question"
                                        aria-label="Remove question">Remove</button>
                                </div>
                                <div class="mb-2">
                                    <label class="form-label">Question text</label>
                                    <input type="text" name="questions[0][question_text]"
                                        class="form-control form-control-sm"
                                        placeholder="e.g. Which type of rewards excites you the most?" />
                                </div>
                                <div class="mb-0">
                                    <label class="form-label">Options</label>
                                    <input type="text" name="questions[0][options][0]"
                                        class="form-control form-control-sm mb-1" placeholder="Option 1" />
                                    <input type="text" name="questions[0][options][1]"
                                        class="form-control form-control-sm mb-1" placeholder="Option 2" />
                                    <input type="text" name="questions[0][options][2]"
                                        class="form-control form-control-sm mb-1" placeholder="Option 3" />
                                    <input type="text" name="questions[0][options][3]"
                                        class="form-control form-control-sm" placeholder="Option 4" />
                                </div>
                            </div>
                        </div>
                    </div>
                    <button type="button" id="add-question-btn" class="btn btn-outline-primary btn-sm">
                        <i class="fas fa-plus me-1"></i> Add question
                    </button>
                </div>
            </div>

            <div class="row mt-4">
                <div class="col-md-12 d-flex justify-content-end">
                    <a href="{{ route('surveys.index') }}" class="btn btn-secondary me-2">Cancel</a>
                    <x-auth.input-button btn-class="btn-primary" btn-type="submit" btn-value="Create Survey" />
                </div>
            </div>
        </x-auth.form>
    </x-auth.card>

    {{-- @push('auth_scripts') --}}
        <script>
            (function() {
                const container = document.getElementById('questions-container');
                const addBtn = document.getElementById('add-question-btn');

                function updateQuestionLabels() {
                    container.querySelectorAll('.question-block').forEach((block, i) => {
                        const num = block.querySelector('.question-number');
                        if (num) num.textContent = 'Question ' + (i + 1);
                        const idx = block.getAttribute('data-index');
                        block.querySelectorAll('[name^="questions["]').forEach(function(input) {
                            input.name = input.name.replace(/questions\[\d+\]/, 'questions[' + i + ']');
                        });
                        block.setAttribute('data-index', String(i));
                    });
                }

                addBtn.addEventListener('click', function() {
                    const blocks = container.querySelectorAll('.question-block');
                    const nextIndex = blocks.length;
                    const first = container.querySelector('.question-block');
                    const clone = first.cloneNode(true);
                    clone.setAttribute('data-index', String(nextIndex));
                    clone.querySelector('.question-number').textContent = 'Question ' + (nextIndex + 1);
                    clone.querySelectorAll('input').forEach(function(input) {
                        input.value = '';
                        input.name = input.name.replace(/questions\[\d+\]/, 'questions[' + nextIndex + ']');
                    });
                    container.appendChild(clone);
                    clone.querySelector('.remove-question').addEventListener('click', function() {
                        clone.remove();
                        updateQuestionLabels();
                    });
                    updateQuestionLabels();
                });

                container.addEventListener('click', function(e) {
                    if (e.target.classList.contains('remove-question')) {
                        const block = e.target.closest('.question-block');
                        if (container.querySelectorAll('.question-block').length > 1) {
                            block.remove();
                            updateQuestionLabels();
                        }
                    }
                });
            })();
        </script>
    {{-- @endpush --}}
</x-layouts.auth>
