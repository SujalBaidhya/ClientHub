<x-layouts.app title="Account Settings — ClientHub">

    <div class="dashboard">

        <div class="dashboard-welcome">
            <div>
                <p class="dashboard-eyebrow">Account</p>
                <h1>Account Settings</h1>
                <p>Update your name or change your password.</p>
            </div>
        </div>

        <x-card title="Profile">

            <form method="POST" action="{{ route('account.update') }}">
                @csrf
                @method('PUT')

                <div style="margin-bottom: 16px;">
                    <label style="display:block; margin-bottom:4px; font-weight:bold;">Name</label>
                    <input type="text" name="name" required value="{{ old('name', auth()->user()->name) }}" style="width:100%; padding:8px; border-radius:6px; border:1px solid var(--border);">
                </div>

                <div style="margin-bottom: 16px;">
                    <label style="display:block; margin-bottom:4px; font-weight:bold;">Email</label>
                    <input type="text" value="{{ auth()->user()->email }}" disabled style="width:100%; padding:8px; border-radius:6px; border:1px solid var(--border); background: #f3f4f6; color: var(--muted);">
                </div>

                <hr style="border: none; border-top: 1px solid var(--border); margin: 24px 0;">

                <p style="font-weight: bold; margin-bottom: 12px;">Change Password (optional)</p>

                <div style="margin-bottom: 16px;">
                    <label style="display:block; margin-bottom:4px; font-weight:bold;">Current Password</label>
                    <input type="password" name="current_password" style="width:100%; padding:8px; border-radius:6px; border:1px solid var(--border);">
                </div>

                <div style="margin-bottom: 16px;">
                    <label style="display:block; margin-bottom:4px; font-weight:bold;">New Password</label>
                    <input type="password" name="password" style="width:100%; padding:8px; border-radius:6px; border:1px solid var(--border);">
                </div>

                <div style="margin-bottom: 16px;">
                    <label style="display:block; margin-bottom:4px; font-weight:bold;">Confirm New Password</label>
                    <input type="password" name="password_confirmation" style="width:100%; padding:8px; border-radius:6px; border:1px solid var(--border);">
                </div>

                <button type="submit" class="btn btn-success">Save Changes</button>

            </form>

        </x-card>
                @if(auth()->user()->role === 'client')
            <x-card title="Email Notifications">
                <form method="POST" action="{{ route('account.notifications') }}">
                    @csrf
                    @method('PUT')

                    <p style="margin: 0 0 14px; color: var(--muted); font-size: 14px;">
                        Choose which emails you receive. Payment receipts, invitations and password resets are always sent.
                    </p>

                    @foreach([
                        'email_invoices' => ['New invoices', 'An email when a new invoice is issued to you.'],
                        'email_reminders' => ['Payment reminders', 'A reminder before an invoice is due, and a notice if it becomes overdue.'],
                        'email_milestones' => ['Milestone updates', 'An email when one of your milestones is marked complete.'],
                    ] as $field => [$label, $help])
                        <label style="display: flex; gap: 12px; align-items: flex-start; margin-bottom: 14px; cursor: pointer;">
                            <input type="checkbox" name="{{ $field }}" value="1" @checked(auth()->user()->{$field} ?? true) style="margin-top: 4px; width: 16px; height: 16px;">
                            <span>
                                <strong style="display: block;">{{ $label }}</strong>
                                <span style="font-size: 13px; color: var(--muted);">{{ $help }}</span>
                            </span>
                        </label>
                    @endforeach

                    <button type="submit" class="btn btn-success">Save Preferences</button>
                </form>
            </x-card>
        @endif

    </div>

</x-layouts.app>