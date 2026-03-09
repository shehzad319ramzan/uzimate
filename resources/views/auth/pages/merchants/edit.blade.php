<x-layouts.auth>
    <x-slot name="pageTitle">Edit Merchant</x-slot>
    <x-auth.card card-header="Edit Merchant" header-button="true">
        <x-auth.form form-action="{{ route('merchants.update', $data->id) }}" enctype="true">
            @method('PUT')
            <div class="row">
                <!-- Merchant Section -->
                <div class="col-md-12">
                    <h5 class="mb-3">Merchant</h5>
                </div>
                <div class="col-md-6">
                    <label class="form-label mb-2">Category</label>
                    <select name="merchant_category_id" class="form-select mb-3">
                        <option value="">Select Category</option>
                        @foreach($categories ?? [] as $cat)
                            <option value="{{ $cat->id }}" {{ old('merchant_category_id', $data->merchant_category_id) == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-6">
                    <x-auth.input-field type="text" name="merchant_name" id="merchant_name" required="true"
                        place="Enter merchant name" val="{{ old('merchant_name', $data->name) }}" extraclasses="mb-3"
                        label="Merchant Name" />
                </div>
                <div class="col-md-6">
                    <x-auth.upload-file image="{{ $data->logo() }}" name="{{ $data->name }}" />
                </div>
                <div class="col-md-6">
                    <x-auth.input-field type="number" name="max_sites" id="max_sites" required="true"
                        place="Enter max sites" val="{{ old('max_sites', $data->max_sites ?? '1') }}" extraclasses="mb-3"
                        label="Max Sites" />
                </div>
                {{-- <div class="col-md-6">
                    <x-auth.input-field type="number" name="spin_after_days" id="spin_after_days"
                        place="Enter spin after days" val="{{ old('spin_after_days', $data->spin_after_days ?? '1') }}" extraclasses="mb-3"
                        label="Spin After (days)" />
                </div>
                <div class="col-md-6">
                    <x-auth.input-field type="number" name="scan_after_hours" id="scan_after_hours"
                        place="Enter scan after hours" val="{{ old('scan_after_hours', $data->scan_after_hours ?? '6') }}" extraclasses="mb-3"
                        label="Scan After (hours)" />
                </div> --}}
                <div class="col-md-6">
                    <x-auth.input-field type="number" name="qr_scan_points" id="qr_scan_points"
                        place="e.g. 10" val="{{ old('qr_scan_points', $data->qr_scan_points ?? '') }}" extraclasses="mb-3"
                        label="Get Points (when customers scan your store QR)" />
                    <small class="text-muted">Points awarded when customers scan your merchant QR code.</small>
                </div>
                <div class="col-md-12">
                    <div class="form-check mb-3">
                        <input class="form-check-input" type="checkbox" name="use_other_merchant_points"
                            id="use_other_merchant_points" value="1"
                            {{ old('use_other_merchant_points', $data->use_other_merchant_points ?? false) ? 'checked' : '' }}>
                        <label class="form-check-label" for="use_other_merchant_points">
                            Use other merchant's points for offers
                        </label>
                    </div>
                </div>

                <div class="col-md-12 mt-3">
                    <x-auth.editor name="description" label="Info" :value="old('description', $data->description ?? '')" />
                </div>
            </div>

            <div class="row">
                <div class="col-md-12 d-flex justify-content-end">
                    <x-auth.input-button btn-class="btn-primary" btn-type="submit" btn-value="Update" />
                </div>
            </div>
        </x-auth.form>
    </x-auth.card>

    @if($data->qr_code ?? null)
        <x-auth.card card-header="Store QR Code – Customers scan to earn points" class="mt-3">
            <p class="text-muted small">Share this QR code with customers. When they scan it (via the app), they receive @if(isset($data->qr_scan_points) && $data->qr_scan_points !== null) <strong>{{ $data->qr_scan_points }} points</strong>@else points according to your reward rules (Merchant QR Scanned)@endif.</p>
            <div class="text-center mb-3">
                <img src="{{ $data->qrCodeImageUrl() }}" alt="Merchant QR Code" class="img-fluid" style="max-width: 220px;" />
            </div>
            <div class="d-flex flex-wrap gap-2 justify-content-center">
                <a href="{{ $data->qrCodeImageUrl() }}" download="merchant-qr-{{ \Illuminate\Support\Str::slug($data->name) }}.svg" class="btn btn-outline-primary">
                    <i class="fas fa-download me-1"></i> Download QR
                </a>
                <a href="{{ route('merchants.show', $data->id) }}" class="btn btn-outline-secondary"><i class="fas fa-eye me-1"></i> View full detail</a>
            </div>
        </x-auth.card>
    @else
        <x-auth.card card-header="Store QR Code – Customers scan to earn points" class="mt-3">
            <p class="text-muted small">Generate a unique QR code for this merchant. Customers scan it in the app to earn points (configure "Merchant QR Scanned" in Reward Rules or set "Get Points" above).</p>
            <form action="{{ route('merchants.generate-qr', $data->id) }}" method="POST" class="d-inline">
                @csrf
                <button type="submit" class="btn btn-primary"><i class="fas fa-qrcode me-1"></i> Generate QR Code</button>
            </form>
        </x-auth.card>
    @endif
</x-layouts.auth>
