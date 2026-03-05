<x-settings title="Firebase / Push Notifications" sub-title="Configure Firebase Cloud Messaging (FCM) for mobile push notifications">
    <x-auth.card card-header="Firebase Configuration" header-button="true">
        <x-auth.form form-action="{{ route('settings.firebase_update') }}">
            @method('PUT')

            <div class="row">
                <div class="col-12 mb-3">
                    <label class="form-label" for="firebase_project_id">Project ID <span class="text-muted">(optional)</span></label>
                    <input type="text" name="firebase_project_id" id="firebase_project_id" class="form-control"
                        value="{{ old('firebase_project_id', $data->firebase_project_id ?? '') }}"
                        placeholder="e.g. my-app-12345">
                    <small class="text-muted">Your Firebase project ID from the Firebase Console.</small>
                </div>
            </div>

            <div class="row">
                <div class="col-12 mb-3">
                    <label class="form-label" for="firebase_credentials">Service Account JSON <span class="text-muted">(optional)</span></label>
                    <textarea name="firebase_credentials" id="firebase_credentials" class="form-control font-monospace" rows="8"
                        placeholder='Paste the full contents of your Firebase service account JSON file (e.g. from Firebase Console → Project settings → Service accounts → Generate new private key).'>{!! old('firebase_credentials', $data->firebase_credentials ?? '') !!}</textarea>
                    <small class="text-muted">Paste the entire JSON. If left empty, the app will use the file path from .env (FIREBASE_CREDENTIALS_FILE).</small>
                </div>
            </div>

            <div class="row mt-3">
                <div class="col-12">
                    <x-auth.input-button btn-class="btn-primary" btn-type="submit"
                        btn-value="{{ __('Update Firebase') }}" />
                </div>
            </div>

        </x-auth.form>
    </x-auth.card>
</x-settings>
