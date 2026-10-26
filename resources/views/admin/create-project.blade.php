<x-layouts.app title="Create Project — Admin">

    <style>
        .form-header {
            margin-bottom: 24px;
        }

        .form-group {
            margin-bottom: 18px;
        }

        .form-label {
            display: block;
            margin-bottom: 6px;
            font-size: 14px;
            font-weight: 600;
            color: #1e1b4b;
        }

        .form-control {
            width: 100%;
            padding: 10px 14px;
            border-radius: 10px;
            border: 1px solid var(--border);
            font-size: 14px;
            font-family: inherit;
            background: #ffffff;
            box-sizing: border-box;
            transition: border-color 0.15s ease;
        }

        .form-control:focus {
            outline: none;
            border-color: #2563eb;
        }

        .form-grid-2 {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 16px;
        }

        .form-actions {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-top: 8px;
        }

        @media (max-width: 768px) {
            .form-header h1 {
                font-size: 26px;
            }

            .form-grid-2 {
                grid-template-columns: 1fr;
                gap: 0;
            }

            .form-actions {
                flex-direction: column-reverse;
                align-items: stretch;
            }

            .form-actions .btn {
                width: 100%;
                text-align: center;
                justify-content: center;
                padding: 11px;
            }
        }
    </style>

    <div class="dashboard">

        <x-breadcrumbs :links="[
            ['label' => 'Projects', 'url' => route('admin.projects.index'), 'icon' => true],
            ['label' => 'Create New Project'],
        ]" />

        <div class="form-header">
            <p class="dashboard-eyebrow">Admin Workspace</p>
            <h1 style="margin: 0 0 6px 0;">Create New Project</h1>
            <p style="margin: 0; color: #6b7280; font-size: 14px;">Assign a new project to a client.</p>
        </div>

        <x-card>

            <form method="POST" action="{{ route('admin.projects.store') }}">
                @csrf

                <div class="form-group">
                    <label class="form-label">Client</label>
                    <select name="client_id" required class="form-control">
                        <option value="">Select a client</option>
                        @foreach($clients as $client)
                            <option value="{{ $client->id }}" @selected(old('client_id') == $client->id)>
                                {{ $client->name }} ({{ $client->email }})
                            </option>
                        @endforeach
                    </select>
                </div>
                <div style="margin-bottom: 16px;">
                    <label style="display:block; margin-bottom:4px; font-weight:bold;">Start from Template (optional)</label>
                    <select name="template" style="width:100%; padding:8px; border-radius:6px; border:1px solid var(--border);">
                        <option value="">No template — blank project</option>
                        @foreach($templates as $key => $template)
                            <option value="{{ $key }}">{{ $template['label'] }}</option>
                        @endforeach
                    </select>
                    <small style="color: var(--muted, #6b7280);">Automatically creates a standard set of milestones for this project type.</small>
                </div>

                <div class="form-group">
                    <label class="form-label">Project Name</label>
                    <input type="text" name="name" required value="{{ old('name') }}" placeholder="e.g. Mobile Banking App" class="form-control">
                </div>

                <div class="form-group">
                    <label class="form-label">Description</label>
                    <textarea name="description" rows="3" placeholder="Brief project scope and goals..." class="form-control">{{ old('description') }}</textarea>
                </div>

                <div class="form-group">
                    <label class="form-label">Status</label>
                    <select name="status" required class="form-control">
                        <option value="pending" @selected(old('status') === 'pending')>Pending</option>
                        <option value="in_progress" @selected(old('status') === 'in_progress')>In Progress</option>
                        <option value="completed" @selected(old('status') === 'completed')>Completed</option>
                    </select>
                </div>

                <div class="form-grid-2">
                    <div class="form-group">
                        <label class="form-label">Start Date</label>
                        <input type="date" name="start_date" value="{{ old('start_date') }}" class="form-control">
                    </div>

                    <div class="form-group">
                        <label class="form-label">Due Date</label>
                        <input type="date" name="due_date" value="{{ old('due_date') }}" class="form-control">
                    </div>
                </div>

                <div class="form-actions">
                    <a href="{{ route('admin.projects.index') }}" class="btn" style="background: transparent; border: 1px solid var(--border); color: #374151;">
                        Cancel
                    </a>
                    <button type="submit" class="btn btn-success" style="padding: 10px 22px;">
                        Create Project
                    </button>
                </div>

            </form>

        </x-card>

    </div>

</x-layouts.app>
