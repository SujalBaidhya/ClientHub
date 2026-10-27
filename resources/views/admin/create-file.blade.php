<x-layouts.app title="Files — Admin">

    <style>
        .files-header {
            margin-bottom: 24px;
        }

        .file-upload-input {
            width: 100%;
            padding: 12px 14px;
            border-radius: 10px;
            border: 1px dashed var(--border);
            background: #fafafa;
            font-size: 14px;
            font-family: inherit;
            cursor: pointer;
        }

        .file-upload-input:hover {
            border-color: var(--primary);
        }

        .file-card-item {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
            padding: 16px;
            border: 1px solid var(--border);
            border-radius: 12px;
            background: #ffffff;
            transition: all 0.2s ease;
        }

        .file-card-item:hover {
            border-color: #cbd5e1;
            box-shadow: 0 4px 12px -2px rgba(0, 0, 0, 0.05);
        }

        .file-details {
            display: flex;
            align-items: center;
            gap: 14px;
            min-width: 0;
            flex: 1;
        }

        .file-badge-icon {
            flex-shrink: 0;
            width: 44px;
            height: 44px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 11px;
            font-weight: 700;
            letter-spacing: 0.04em;
        }

        .file-meta {
            min-width: 0;
            display: flex;
            flex-direction: column;
            gap: 3px;
        }

        .file-meta strong {
            font-size: 14px;
            color: #1e1b4b;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .file-meta span {
            font-size: 12px;
            color: var(--muted);
        }

        .file-delete-form {
            flex-shrink: 0;
            margin: 0;
        }

        [data-theme="dark"] .file-upload-input {
            background: #162032;
            color: var(--text);
            border-color: #243048;
        }

        [data-theme="dark"] .file-card-item {
            background: #162032;
            border-color: #223048;
        }

        [data-theme="dark"] .file-card-item:hover {
            border-color: #3b4d6e;
            box-shadow: 0 12px 28px -6px rgba(0, 0, 0, 0.5);
        }

        [data-theme="dark"] .file-meta strong {
            color: #e2e8f0;
        }

        [data-theme="dark"] .file-meta span {
            color: #94a3b8;
        }

        [data-theme="dark"] .dropzone-box {
            background: #162032;
            border-color: #334155;
        }

        [data-theme="dark"] .dropzone-box:hover,
        [data-theme="dark"] .dropzone-box.is-dragover {
            background: #1e293b;
            border-color: var(--primary);
        }

        [data-theme="dark"] .dropzone-icon-wrap {
            background: #312e81;
            color: #a5b4fc;
        }

        [data-theme="dark"] .dropzone-title {
            color: #e2e8f0;
        }

        [data-theme="dark"] .dropzone-hint {
            color: #94a3b8;
        }

        [data-theme="dark"] .dropzone-preview {
            background: #162032;
            border-color: #243048;
        }

        [data-theme="dark"] .dropzone-preview-name {
            color: #e2e8f0;
        }

        [data-theme="dark"] .dropzone-preview-size {
            color: #94a3b8;
        }

        [data-theme="dark"] .dropzone-remove-btn {
            background: #451a1a;
            color: #fca5a5;
        }

        [data-theme="dark"] .dropzone-remove-btn:hover {
            background: #7f1d1d;
        }

        [data-theme="dark"] .file-meta span[style*="f1f5f9"] {
            background: #243048 !important;
            color: #cbd5e1 !important;
        }

        [data-theme="dark"] select#uploader {
            background: #162032 !important;
            color: #e2e8f0 !important;
            border-color: #243048 !important;
        }

        [data-theme="dark"] select#uploader option {
            background: #162032;
            color: #e2e8f0;
        }

        [data-theme="dark"] .pagination-container a,
        [data-theme="dark"] .pagination-container span {
            background: #162032 !important;
            border-color: #243048 !important;
            color: #cbd5e1 !important;
        }

        [data-theme="dark"] .pagination-container span[style*="2563eb"] {
            background: #2563eb !important;
            border-color: #2563eb !important;
            color: #ffffff !important;
        }
        @media (max-width: 768px) {
            .files-header h1 {
                font-size: 26px;
            }

            .file-card-item {
                flex-direction: column;
                align-items: stretch;
                gap: 14px;
                padding: 14px;
            }

            .file-delete-form {
                width: 100%;
            }

            .file-delete-form button {
                width: 100%;
                text-align: center;
                justify-content: center;
                padding: 9px 14px;
            }
        }
        .dropzone-box {
            border: 2px dashed #cbd5e1;
            border-radius: 12px;
            background: #f8fafc;
            padding: 24px 16px;
            text-align: center;
            cursor: pointer;
            transition: all 0.2s ease;
            position: relative;
        }
        .dropzone-box:hover, .dropzone-box.is-dragover {
            border-color: var(--primary, #4f46e5);
            background: #eef2ff;
        }
        .dropzone-hidden-input {
            position: absolute;
            inset: 0;
            opacity: 0;
            cursor: pointer;
            width: 100%;
            height: 100%;
            z-index: 2;
        }
        .dropzone-content {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 6px;
            pointer-events: none;
        }
        .dropzone-icon-wrap {
            width: 44px;
            height: 44px;
            border-radius: 50%;
            background: #e0e7ff;
            color: var(--primary, #4f46e5);
            display: flex;
            align-items: center;
            justify-content: center;
            transition: transform 0.2s ease;
        }
        .dropzone-box:hover .dropzone-icon-wrap {
            transform: translateY(-2px);
        }
        .dropzone-title {
            font-size: 13.5px;
            font-weight: 600;
            color: #1e1b4b;
            display: block;
        }
        .dropzone-hint {
            font-size: 11.5px;
            color: #6b7280;
            display: block;
            margin-top: 2px;
        }
        .dropzone-preview {
            display: flex;
            align-items: center;
            gap: 12px;
            background: #ffffff;
            border: 1px solid var(--border, #e4e4f0);
            border-radius: 8px;
            padding: 10px 14px;
            text-align: left;
            position: relative;
            z-index: 3;
        }
        .dropzone-preview-info {
            flex: 1;
            min-width: 0;
        }
        .dropzone-preview-name {
            font-size: 13px;
            font-weight: 600;
            color: #1e1b4b;
            display: block;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        .dropzone-preview-size {
            font-size: 11px;
            color: #6b7280;
            display: block;
        }
        .dropzone-remove-btn {
            background: #fee2e2;
            color: #991b1b;
            border: none;
            border-radius: 6px;
            width: 28px;
            height: 28px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: background 0.15s ease;
        }
        .dropzone-remove-btn:hover {
            background: #fecaca;
        }
    </style>

    <div class="dashboard">

        <x-breadcrumbs :links="[
            ['label' => 'Projects', 'url' => route('admin.projects.index'), 'icon' => true],
            ['label' => $project->name, 'url' => route('admin.projects.edit', $project)],
            ['label' => 'Deliverables & Files'],
        ]" />

        <div class="files-header">
            <p class="dashboard-eyebrow">Admin Workspace</p>
            <h1 style="margin: 0 0 6px 0;">{{ $project->name }}</h1>
            <p style="margin: 0; color: #6b7280; font-size: 14px;">Upload deliverables and manage files for this project.</p>
        </div>

        <x-card title="Upload File">

            <form method="POST" action="{{ route('admin.files.store', $project) }}" enctype="multipart/form-data">
                @csrf

                <div class="dropzone-box" id="adminDropzoneBox">
                    <input type="file" name="file" id="adminDropzoneInput" required accept=".pdf,.jpg,.jpeg,.png,.gif,.webp,.doc,.docx,.xls,.xlsx,.ppt,.pptx,.txt,.csv,.zip" class="dropzone-hidden-input">
                    <div class="dropzone-content" id="adminDropzonePrompt">
                        <div class="dropzone-icon-wrap">
                            <x-icon name="upload-cloud" size="24" />
                        </div>
                        <div>
                            <span class="dropzone-title">Click to upload or drag & drop</span>
                            <span class="dropzone-hint">PDF, Images, Word, Excel, ZIP up to 20MB</span>
                        </div>
                    </div>
                    <div class="dropzone-preview" id="adminDropzonePreview" style="display: none;">
                        <div style="color: var(--primary);"><x-icon name="file-text" size="22" /></div>
                        <div class="dropzone-preview-info">
                            <span id="adminDropzoneFileName" class="dropzone-preview-name"></span>
                            <span id="adminDropzoneFileSize" class="dropzone-preview-size"></span>
                        </div>
                        <button type="button" id="adminDropzoneClearBtn" class="dropzone-remove-btn" title="Remove file">
                            <x-icon name="x" size="14" />
                        </button>
                    </div>
                </div>

                <div id="adminDropzoneActions" style="display: none; margin-top: 14px;">
                    <button type="submit" class="btn btn-success" style="padding: 9px 18px; display: inline-flex; align-items: center; gap: 6px;">
                        <x-icon name="upload-cloud" size="15" />
                        <span>Upload File</span>
                    </button>
                </div>

            </form>

            <script>
                (function() {
                    var box = document.getElementById("adminDropzoneBox");
                    var input = document.getElementById("adminDropzoneInput");
                    var prompt = document.getElementById("adminDropzonePrompt");
                    var preview = document.getElementById("adminDropzonePreview");
                    var nameEl = document.getElementById("adminDropzoneFileName");
                    var sizeEl = document.getElementById("adminDropzoneFileSize");
                    var clearBtn = document.getElementById("adminDropzoneClearBtn");
                    var actions = document.getElementById("adminDropzoneActions");

                    if (!input || !box) return;

                    function formatBytes(bytes) {
                        if (bytes === 0) return "0 Bytes";
                        var k = 1024, sizes = ["Bytes", "KB", "MB", "GB"];
                        var i = Math.floor(Math.log(bytes) / Math.log(k));
                        return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + " " + sizes[i];
                    }

                    input.addEventListener("change", function() {
                        if (input.files && input.files[0]) {
                            var file = input.files[0];
                            nameEl.textContent = file.name;
                            sizeEl.textContent = formatBytes(file.size);
                            prompt.style.display = "none";
                            preview.style.display = "flex";
                            actions.style.display = "block";
                        }
                    });

                    if (clearBtn) {
                        clearBtn.addEventListener("click", function(e) {
                            e.preventDefault();
                            e.stopPropagation();
                            input.value = "";
                            preview.style.display = "none";
                            prompt.style.display = "flex";
                            actions.style.display = "none";
                        });
                    }

                    ["dragenter", "dragover"].forEach(function(evt) {
                        box.addEventListener(evt, function(e) {
                            e.preventDefault();
                            box.classList.add("is-dragover");
                        });
                    });

                    ["dragleave", "drop"].forEach(function(evt) {
                        box.addEventListener(evt, function(e) {
                            e.preventDefault();
                            box.classList.remove("is-dragover");
                        });
                    });
                })();
            </script>

        </x-card>

        <x-card title="Existing Files">
            <form method="GET" action="{{ route('admin.files.create', $project) }}" 
            style="display: flex; align-items: center; gap: 10px; margin-bottom: 20px; flex-wrap: wrap;">

                <label for="uploader" style="font-size: 14px; font-weight: 600;">
                    Filter by uploader:
                </label>

                <select name="uploader"
                        id="uploader"
                        onchange="this.form.submit()"
                        style="padding: 10px 12px; border: 1px solid var(--border); border-radius: 8px; background: white; font-size: 14px; min-width: 200px;">

                    <option value="">All uploaders</option>

                    @foreach($uploaders as $uploader)
                        <option value="{{ $uploader->id }}"
                            {{ (string) $uploaderId === (string) $uploader->id ? 'selected' : '' }}>
                            {{ $uploader->name }} ({{ ucfirst($uploader->role) }})
                        </option>
                    @endforeach

                </select>

                @if($uploaderId !== null && $uploaderId !== '')
                    <a href="{{ route('admin.files.create', $project) }}"
                    class="btn btn-small"
                    style="text-decoration: none;">
                        Clear Filter
                    </a>
                @endif

            </form>

            <p style="margin-bottom: 14px; color: var(--muted); font-size: 13px;">
                Showing {{ $files->total() }} file(s)
                @if($uploaderId !== null && $uploaderId !== '')
                    uploaded by {{ $uploaders->firstWhere('id', (int) $uploaderId)?->name ?? 'selected user' }}
                @else
                    from all uploaders
                @endif
            </p>
            @if($files->isEmpty())
                <x-empty-state
                    title="No files yet"
                    message="Upload one above to get started."
                />
            @else
                <div style="display: flex; flex-direction: column; gap: 12px;">
                    @foreach($files as $file)
                        @php
                            $ext = strtolower(pathinfo($file->original_name, PATHINFO_EXTENSION));
                            $typeConfig = match(true) {
                                $ext === 'pdf' => ['bg' => '#fee2e2', 'color' => '#dc2626', 'label' => 'PDF'],
                                in_array($ext, ['png', 'jpg', 'jpeg', 'gif', 'webp', 'svg']) => ['bg' => '#d1fae5', 'color' => '#059669', 'label' => strtoupper($ext)],
                                in_array($ext, ['doc', 'docx', 'txt', 'rtf']) => ['bg' => '#dbeafe', 'color' => '#2563eb', 'label' => strtoupper($ext)],
                                in_array($ext, ['xls', 'xlsx', 'csv']) => ['bg' => '#fef3c7', 'color' => '#d97706', 'label' => strtoupper($ext)],
                                in_array($ext, ['zip', 'rar', 'tar', 'gz', '7z']) => ['bg' => '#ede9fe', 'color' => '#7c3aed', 'label' => 'ZIP'],
                                default => ['bg' => '#f1f5f9', 'color' => '#475569', 'label' => strtoupper($ext) ?: 'FILE'],
                            };
                            $formattedSize = $file->file_size ? ($file->file_size < 1048576 ? round($file->file_size / 1024, 1) . ' KB' : round($file->file_size / 1048576, 1) . ' MB') : null;
                        @endphp
                        <div class="file-card-item">
                            <div class="file-details">
                                <div class="file-badge-icon" style="background: {{ $typeConfig['bg'] }}; color: {{ $typeConfig['color'] }};">
                                    {{ $typeConfig['label'] }}
                                </div>
                                <div class="file-meta">
                                    <strong title="{{ $file->original_name }}">{{ $file->original_name }}</strong>
                                    <div style="display: flex; align-items: center; gap: 6px; flex-wrap: wrap; font-size: 12px; color: var(--muted); margin-top: 2px;">
                                        @if($formattedSize)
                                            <span style="background: #f1f5f9; padding: 1px 6px; border-radius: 4px; font-weight: 500; color: #475569;">{{ $formattedSize }}</span>
                                            <span>&middot;</span>
                                        @endif
                                        <span>Uploaded {{ $file->created_at->format('M d, Y') }}</span>
                                        @if($file->uploadedBy)
                                            <span>&middot;</span>
                                            <span>by {{ $file->uploadedBy->name }}</span>
                                            <span class="badge badge-{{ $file->uploadedBy->role === 'client' ? 'neutral' : 'success' }}" style="font-size: 10px; padding: 1px 7px;">
                                                {{ ucfirst($file->uploadedBy->role) }}
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <div style="display: flex; align-items: center; gap: 8px; flex-wrap: wrap;">
                                @if(strtolower(pathinfo($file->original_name, PATHINFO_EXTENSION)) === 'pdf')
                                    <a href="{{ route('files.view', $file) }}" target="_blank" rel="noopener noreferrer" class="btn btn-small"
                                        style="background: #2563eb; color: white; border: none; text-decoration: none; display: inline-flex; align-items: center; gap: 5px;">
                                        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg>
                                        View PDF
                                    </a>
                                @else
                                    <a href="{{ route('files.view', $file) }}" class="btn btn-small"
                                        style="background: #2563eb; color: white; border: none; text-decoration: none; display: inline-flex; align-items: center; gap: 5px;">
                                        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="7 10 12 15 17 10"></polyline><line x1="12" y1="15" x2="12" y2="3"></line></svg>
                                        Download
                                    </a>
                                @endif
                                
                                <form method="POST" action="{{ route('admin.files.destroy', [$project, $file]) }}" onsubmit="return confirm('Delete this file?');" class="file-delete-form">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-small" style="background: #dc2626; color: white; border: none; display: inline-flex; align-items: center; gap: 5px;">
                                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path></svg>
                                        Delete File
                                    </button>
                                </form>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
            <div class="pagination-container"
                style="margin-top: 20px; display: flex; justify-content: center; align-items: center; gap: 6px; flex-wrap: wrap;">

                {{-- Previous --}}
                @if($files->onFirstPage())
                    <span style="padding: 8px 12px; border: 1px solid var(--border); border-radius: 8px; color: #999; background: #f5f5f5;">
                        Previous
                    </span>
                @else
                    <a href="{{ $files->previousPageUrl() }}"
                    style="padding: 8px 12px; border: 1px solid var(--border); border-radius: 8px; text-decoration: none; color: inherit; background: white;">
                        Previous
                    </a>
                @endif

                {{-- Page Numbers --}}
                @foreach($files->getUrlRange(1, $files->lastPage()) as $page => $url)
                    @if($page == $files->currentPage())
                        <span style="padding: 8px 12px; border: 1px solid #2563eb; border-radius: 8px; background: #2563eb; color: white; font-weight: 600;">
                            {{ $page }}
                        </span>
                    @else
                        <a href="{{ $url }}"
                        style="padding: 8px 12px; border: 1px solid var(--border); border-radius: 8px; text-decoration: none; color: inherit; background: white;">
                            {{ $page }}
                        </a>
                    @endif
                @endforeach

                {{-- Next --}}
                @if($files->hasMorePages())
                    <a href="{{ $files->nextPageUrl() }}"
                    style="padding: 8px 12px; border: 1px solid var(--border); border-radius: 8px; text-decoration: none; color: inherit; background: white;">
                        Next
                    </a>
                @else
                    <span style="padding: 8px 12px; border: 1px solid var(--border); border-radius: 8px; color: #999; background: #f5f5f5;">
                        Next
                    </span>
                @endif

            </div>
        </x-card>

    </div>

</x-layouts.app>