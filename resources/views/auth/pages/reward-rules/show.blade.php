<x-layouts.auth>
    <x-slot name="pageTitle">Reward Rule</x-slot>

    <x-auth.card card-header="Reward Rule Detail" header-button="true">
        <x-slot name="headerCustom">
            <x-auth.href-link link-href="{{ route('reward-rules.index') }}" link-value="{{ __('Back to List') }}"
                link-class="btn btn-outline-primary me-2" />
            @can('edit_reward_rule')
            <x-auth.href-link link-href="{{ route('reward-rules.edit', $data->id) }}" link-value="{{ __('Edit') }}"
                link-class="btn btn-primary" />
            @endcan
        </x-slot>

        <table class="table table-bordered">
            <tr>
                <th width="200">Merchant</th>
                <td>{{ $data->merchant_id ? ($data->merchant->name ?? '-') : 'Global' }}</td>
            </tr>
            <tr>
                <th>Action Type</th>
                <td><code>{{ $data->action_type }}</code></td>
            </tr>
            <tr>
                <th>Display Label</th>
                <td>{{ $data->label }}</td>
            </tr>
            <tr>
                <th>Points</th>
                <td>{{ $data->points !== null ? number_format($data->points) : '-' }}</td>
            </tr>
            <tr>
                <th>Trigger</th>
                <td>{{ \App\Models\RewardRule::triggerConditions()[$data->trigger_condition] ?? $data->trigger_condition }}</td>
            </tr>
            <tr>
                <th>Status</th>
                <td>
                    @if($data->is_active)
                        <span class="badge bg-success">Active</span>
                    @else
                        <span class="badge bg-secondary">Inactive</span>
                    @endif
                </td>
            </tr>
        </table>
    </x-auth.card>
</x-layouts.auth>
