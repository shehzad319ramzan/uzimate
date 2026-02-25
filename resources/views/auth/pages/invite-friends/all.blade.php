@php
    $filters = $filters ?? [];
    $searchValue = $filters['search'] ?? '';
    $filterDateFrom = $filters['date_from'] ?? '';
    $filterDateTo = $filters['date_to'] ?? '';
@endphp

<x-layouts.auth>
    <x-slot name="pageTitle">Invite Friends</x-slot>

    <div class="row mt-3">
        <div class="col-md-12">
            <x-all-list title="Invite Friends (Referrals)" :data="$data['all']">
                <x-slot name="headerCustom">
                    <form method="GET" action="{{ route('invitefriends.index') }}"
                        class="row g-2 align-items-end w-100 mb-3">
                        <div class="col-lg-3 col-md-6">
                            <label for="search" class="form-label text-muted small">Search</label>
                            <input type="text" name="search" id="search" class="form-control"
                                placeholder="Referrer or referred user name/email" value="{{ $searchValue }}">
                        </div>
                        <div class="col-lg-2 col-md-6">
                            <label for="date_from" class="form-label text-muted small">From date</label>
                            <input type="date" name="date_from" id="date_from" class="form-control"
                                value="{{ $filterDateFrom }}">
                        </div>
                        <div class="col-lg-2 col-md-6">
                            <label for="date_to" class="form-label text-muted small">To date</label>
                            <input type="date" name="date_to" id="date_to" class="form-control"
                                value="{{ $filterDateTo }}">
                        </div>
                        <div class="col-lg-12 col-md-12 d-flex gap-2">
                            <button type="submit" class="btn btn-primary mt-3">Apply Filters</button>
                            <a href="{{ route('invitefriends.index') }}" class="btn btn-outline-secondary mt-3">Reset</a>
                        </div>
                    </form>

                    <div class="alert alert-info mb-3 p-2 small">
                        <i class="fas fa-info-circle me-2"></i>
                        <strong>Invite Friends</strong> — Records of users who joined using a referral code. The referrer
                        earns points when a friend signs up with their code.
                    </div>
                </x-slot>

                <x-auth.datatable id="inviteFriendsTable">
                    <thead>
                        <tr>
                            <th>Sr#</th>
                            <th>Referrer (who invited)</th>
                            <th>Referred user (who joined)</th>
                            <th>Points awarded</th>
                            <th>Joined at</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($data['all'] as $key => $row)
                            <tr>
                                <td>{{ $key + 1 + ($data['all']->currentPage() - 1) * $data['all']->perPage() }}</td>
                                <td>
                                    <div class="fw-semibold">{{ $row->referrer->first_name ?? '' }}
                                        {{ $row->referrer->last_name ?? '' }}</div>
                                    <small class="text-muted">{{ $row->referrer->email ?? '-' }}</small>
                                    @if($row->referrer->referral_code ?? null)
                                        <br><small class="text-primary">Code: {{ $row->referrer->referral_code }}</small>
                                    @endif
                                </td>
                                <td>
                                    <div class="fw-semibold">{{ $row->referredUser->first_name ?? '' }}
                                        {{ $row->referredUser->last_name ?? '' }}</div>
                                    <small class="text-muted">{{ $row->referredUser->email ?? '-' }}</small>
                                </td>
                                <td><span class="badge bg-success">{{ number_format($row->points_awarded ?? 0) }} pts</span>
                                </td>
                                <td>{{ $row->created_at ? $row->created_at->format('d M Y, H:i') : '-' }}</td>
                                <td>
                                    <a href="{{ route('invitefriends.show', $row->id) }}"
                                        class="btn btn-sm btn-outline-primary">View</a>
                                    @can('delete_invite_friend')
                                        <form action="{{ route('invitefriends.destroy', $row->id) }}" method="POST"
                                            class="d-inline" onsubmit="return confirm('Delete this referral record?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger">Delete</button>
                                        </form>
                                    @endcan
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td class="text-center text-muted py-4">No referral records yet.</td>
                                <td></td>
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
