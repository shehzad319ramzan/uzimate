@php
    $filters = $filters ?? [];
    $searchValue = $filters['search'] ?? '';
    $filterMerchant = $filters['merchant_id'] ?? '';
    $filterActive = $filters['is_active'] ?? '';
@endphp

<x-layouts.auth>
    <x-slot name="pageTitle">Reward Rules</x-slot>

    <div class="row mt-3">
        <div class="col-md-12">
            <x-all-list title="Reward Rules" :data="$data['all']">
                <x-slot name="headerCustom">
                    <form method="GET" action="{{ route('reward-rules.index') }}" class="row g-2 align-items-end w-100 mb-3">
                        <div class="col-lg-2 col-md-6">
                            <label for="search" class="form-label text-muted small">Search</label>
                            <input type="text" name="search" id="search" class="form-control"
                                placeholder="Action type or label" value="{{ $searchValue }}">
                        </div>
                        <div class="col-lg-2 col-md-6">
                            <label for="merchant_filter" class="form-label text-muted small">Merchant</label>
                            <select name="merchant_id" id="merchant_filter" class="form-select">
                                <option value="">All</option>
                                <option value="global" {{ $filterMerchant === 'global' ? 'selected' : '' }}>Global only</option>
                                @foreach($merchants as $merchant)
                                    <option value="{{ $merchant->id }}" {{ (string)$filterMerchant === (string)$merchant->id ? 'selected' : '' }}>
                                        {{ $merchant->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-lg-2 col-md-6">
                            <label for="is_active" class="form-label text-muted small">Status</label>
                            <select name="is_active" id="is_active" class="form-select">
                                <option value="">All</option>
                                <option value="1" {{ $filterActive === '1' ? 'selected' : '' }}>Active</option>
                                <option value="0" {{ $filterActive === '0' ? 'selected' : '' }}>Inactive</option>
                            </select>
                        </div>
                        <div class="col-lg-12 col-md-12 d-flex gap-2">
                            <button type="submit" class="btn btn-primary mt-3">Apply</button>
                            <a href="{{ route('reward-rules.index') }}" class="btn btn-outline-secondary mt-3">Reset</a>
                        </div>
                    </form>

                    <div class="alert alert-info mb-3 p-2">
                        <h6 class="alert-heading mb-2"><i class="fas fa-cog me-2"></i>Reward Rules</h6>
                        <p class="mb-0 small">Define labels and points for each action type (e.g. Login, QR Scan). Use <strong>Global</strong> for all merchants or select a merchant. <strong>First time only</strong> = award points only the first time the user does this action.</p>
                    </div>

                    @can('add_reward_rule')
                    <x-auth.href-link link-href="{{ route('reward-rules.create') }}" link-value="{{ __('Add Reward Rule') }}"
                        link-class="btn btn-primary" />
                    @endcan
                </x-slot>

                <x-auth.datatable>
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Merchant</th>
                            <th>Action Type</th>
                            <th>Label</th>
                            <th>Points</th>
                            <th>Trigger</th>
                            <th>Status</th>
                            @canany(['view_reward_rule', 'edit_reward_rule', 'delete_reward_rule'])
                            <th class="text-center">Action</th>
                            @endcanany
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($data['all'] as $index => $rule)
                            <tr>
                                <td>{{ $index + 1 + (($data['all']->currentPage() - 1) * $data['all']->perPage()) }}</td>
                                <td>{{ $rule->merchant_id ? ($rule->merchant->name ?? '-') : 'Global' }}</td>
                                <td><code>{{ $rule->action_type }}</code></td>
                                <td>{{ $rule->label }}</td>
                                <td>{{ $rule->points !== null ? number_format($rule->points) : '-' }}</td>
                                <td>{{ \App\Models\RewardRule::triggerConditions()[$rule->trigger_condition] ?? $rule->trigger_condition }}</td>
                                <td>
                                    @if($rule->is_active)
                                        <span class="badge bg-success">Active</span>
                                    @else
                                        <span class="badge bg-secondary">Inactive</span>
                                    @endif
                                </td>
                                @canany(['view_reward_rule', 'edit_reward_rule', 'delete_reward_rule'])
                                <td class="text-center">
                                    <div class="d-inline-block dropdown">
                                        <a href="javascript:void(0)" data-bs-toggle="dropdown" data-bs-display="static">
                                            <i class="fas fa-ellipsis-v bg-light rounded p-2"></i>
                                        </a>
                                        <div class="dropdown-menu dropdown-menu-end">
                                            @can('view_reward_rule')
                                            <a class="dropdown-item" href="{{ route('reward-rules.show', $rule->id) }}">
                                                <i class="fas fa-eye me-2 text-primary"></i> View
                                            </a>
                                            @endcan
                                            @can('edit_reward_rule')
                                            <a class="dropdown-item" href="{{ route('reward-rules.edit', $rule->id) }}">
                                                <i class="fas fa-edit me-2 text-warning"></i> Edit
                                            </a>
                                            @endcan
                                            @can('delete_reward_rule')
                                            <form action="{{ route('reward-rules.destroy', $rule->id) }}" method="POST"
                                                onsubmit="return confirm('Delete this reward rule?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="dropdown-item text-danger">
                                                    <i class="fas fa-trash-alt me-2"></i> Delete
                                                </button>
                                            </form>
                                            @endcan
                                        </div>
                                    </div>
                                </td>
                                @endcanany
                            </tr>
                        @endforeach
                    </tbody>
                </x-auth.datatable>

                @if($data['all']->isEmpty())
                    <div class="alert alert-light text-center mb-0 px-3">
                        No reward rules found. Add a rule to define labels and points for actions (e.g. Login, QR Code Scanned).
                    </div>
                @endif
            </x-all-list>
        </div>
    </div>
</x-layouts.auth>
