<x-layouts.app title="Edit Project — Admin">

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
            ['label' => $project->name],
            ['label' => 'Edit Settings'],
        ]" />

        <div class="form-header">
            <p class="dashboard-eyebrow">Admin Workspace</p>
            <h1 style="margin: 0 0 6px 0;">Edit Project</h1>
            <p style="margin: 0; color: #6b7280; font-size: 14px;">Update project settings, timeline, and client assignment.</p>
        </div>

        <x-card>

            <form method="POST" action="{{ route('admin.projects.update', $project) }}">
                @csrf
                @method('PUT')

                <div class="form-group">
                    <label class="form-label">Client</label>
                    <select name="client_id" required class="form-control">
                        @foreach($clients as $client)
                            <option value="{{ $client->id }}" @selected(old('client_id', $project->client_id) == $client->id)>
                                {{ $client->name }} ({{ $client->email }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group">
                    <label class="form-label">Project Name</label>
                    <input type="text" name="name" required value="{{ old('name', $project->name) }}" class="form-control">
                </div>

                <div class="form-group">
                    <label class="form-label">Description</label>
                    <textarea name="description" rows="3" class="form-control">{{ old('description', $project->description) }}</textarea>
                </div>

                <div class="form-group">
                    <label class="form-label">Status</label>
                    <select name="status" required class="form-control">
                        <option value="pending" @selected(old('status', $project->status) === 'pending')>Pending</option>
                        <option value="in_progress" @selected(old('status', $project->status) === 'in_progress')>In Progress</option>
                        <option value="completed" @selected(old('status', $project->status) === 'completed')>Completed</option>
                    </select>
                </div>

                <div class="form-grid-2">
                    <div class="form-group">
                        <label class="form-label">Start Date</label>
                        <input type="date" name="start_date" value="{{ old('start_date', $project->start_date?->format('Y-m-d')) }}" class="form-control">
                    </div>

                    <div class="form-group">
                        <label class="form-label">Due Date</label>
                        <input type="date" name="due_date" value="{{ old('due_date', $project->due_date?->format('Y-m-d')) }}" class="form-control">
                    </div>
                </div>

                <div class="form-actions">
                    <a href="{{ route('admin.projects.index') }}" class="btn" style="background: transparent; border: 1px solid var(--border); color: #374151;">
                        Cancel
                    </a>
                    <button type="submit" class="btn btn-success" style="padding: 10px 22px;">
                        Save Changes
                    </button>
                </div>

            </form>

        </x-card>

    </div>

</x-layouts.app>
