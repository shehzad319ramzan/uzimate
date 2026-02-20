<div class="modal-body p-0">
    <div class="survey-questions-preview">
        <div class="p-4 border-bottom bg-light">
            <div class="d-flex align-items-center gap-3">
                @php $img = $survey->image(); @endphp
                @if(!empty($img))
                    <img src="{{ $img }}" alt="{{ $survey->title }}" class="rounded shadow-sm" width="56" height="56" style="object-fit: cover;" />
                @else
                    <div class="rounded d-flex align-items-center justify-content-center text-white fw-bold shadow-sm" style="width: 56px; height: 56px; background: linear-gradient(135deg, #4A148D 0%, #7B1FA2 100%); font-size: 22px;">
                        {{ strtoupper(substr($survey->title ?? 'S', 0, 1)) }}
                    </div>
                @endif
                <div>
                    <h6 class="mb-0 fw-bold text-dark">{{ $survey->title }}</h6>
                    <small class="text-muted">{{ $survey->questions->count() }} question(s) · {{ $survey->points ?? 0 }} pts · {{ $survey->estimated_minutes ?? 1 }} min</small>
                </div>
            </div>
        </div>
        <div class="p-4" style="max-height: 60vh; overflow-y: auto;">
            @forelse($survey->questions as $index => $question)
                <div class="question-item mb-4">
                    <div class="d-flex align-items-start gap-2 mb-2">
                        <span class="badge rounded-pill d-inline-flex align-items-center justify-content-center flex-shrink-0" style="width: 28px; height: 28px; background: linear-gradient(135deg, #4A148D 0%, #7B1FA2 100%); font-size: 0.8rem;">{{ $index + 1 }}</span>
                        <p class="mb-0 fw-semibold text-dark lh-sm">{{ $question->question_text }}</p>
                    </div>
                    <div class="ps-4">
                        @if($question->options->isEmpty())
                            <span class="text-muted small">No options</span>
                        @else
                            <div class="d-flex flex-wrap gap-2">
                                @foreach($question->options as $optIndex => $option)
                                    <span class="option-pill px-3 py-1 rounded-pill small" style="background: rgba(74, 20, 141, 0.12); color: #4A148D; border: 1px solid rgba(74, 20, 141, 0.25);">
                                        {{ $option->option_text }}
                                    </span>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>
            @empty
                <div class="text-center py-5">
                    <i class="fas fa-clipboard-list fa-3x text-muted mb-3 opacity-50"></i>
                    <p class="text-muted mb-0">No questions added yet.</p>
                    <small class="text-muted">Add questions in Edit.</small>
                </div>
            @endforelse
        </div>
    </div>
</div>
<div class="modal-footer border-0 bg-light">
    @can('edit_survey')
    <a href="{{ route('surveys.edit', $survey->id) }}" class="btn btn-outline-primary btn-sm">
        <i class="fas fa-edit me-1"></i> Edit survey
    </a>
    @endcan
    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Close</button>
</div>
