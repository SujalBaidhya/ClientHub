<x-layouts.app title="Set Password — ClientHub">

    <div class="dashboard" style="max-width: 480px; margin: 40px auto;">

        <div class="dashboard-welcome" style="text-align: center;">
            <div>
                <p class="dashboard-eyebrow">Welcome</p>
                <h1>Set Your Password</h1>
                <p>Hello <strong>{{ $user->name }}</strong>, choose a password to activate your account.</p>
            </div>
        </div>

        @if($errors->any())
            <x-card>
                <p style="color: #dc2626; font-weight: bold; margin: 0;">{{ $errors->first() }}</p>
            </x-card>
        @endif

        <x-card>
            <form method="POST" action="{{ route('invitations.update', $token) }}">
                @csrf

                <div style="margin-bottom: 16px;">
                    <label style="display:block; margin-bottom:4px; font-weight:bold;">New Password</label>
                    <input type="password" name="password" required minlength="8" style="width:100%; padding:8px; border-radius:6px; border:1px solid var(--border);">
                </div>

                <div style="margin-bottom: 20px;">
                    <label style="display:block; margin-bottom:4px; font-weight:bold;">Confirm Password</label>
                    <input type="password" name="password_confirmation" required minlength="8" style="width:100%; padding:8px; border-radius:6px; border:1px solid var(--border);">
                </div>

                <button type="submit" class="btn btn-success" style="width: 100%;">
                    Save Password & Continue
                </button>
            </form>
        </x-card>

    </div>

</x-layouts.app>