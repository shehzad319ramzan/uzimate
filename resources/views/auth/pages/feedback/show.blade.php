<x-layouts.auth>
    <x-slot name="pageTitle">Feedback Details</x-slot>

    <x-auth.card card-header="Feedback Details" header-button="true">
        <x-slot name="headerCustom">
            <x-auth.href-link link-href="{{ route('feedbacks.index') }}" link-value="{{ __('Back to List') }}"
                link-class="btn btn-outline-primary me-2" />
        </x-slot>

        <div class="table-responsive">
            <table class="table table-striped">
                <tbody>
                    <tr>
                        <th class="text-muted" style="width: 220px;">Name</th>
                        <td>{{ $data->name }}</td>
                    </tr>
                    <tr>
                        <th class="text-muted">Status</th>
                        <td>
                            @if($data->status == '1')
                                <span class="badge bg-success fs-6">Active</span>
                            @else
                                <span class="badge bg-danger fs-6">Inactive</span>
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <th class="text-muted">Created At</th>
                        <td>{{ optional($data->created_at)->format('d M Y, H:i:s') }}</td>
                    </tr>
                    <tr>
                        <th class="text-muted">Updated At</th>
                        <td>{{ optional($data->updated_at)->format('d M Y, H:i:s') }}</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </x-auth.card>
</x-layouts.auth>

