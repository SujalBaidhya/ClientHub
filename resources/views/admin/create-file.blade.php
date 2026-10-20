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
            border-radius: 10px;
            background: #ffffff;
            transition: border-color 0.15s ease;
        }

        .file-card-item:hover {
            border-color: #cbd5e1;
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
            width: 42px;
            height: 42px;
            border-radius: 10px;
            background: #eef2ff;
            color: var(--primary);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 10px;
            font-weight: 700;
            letter-spacing: 0.05em;
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

        <div class="files-header">
            <p class="dashboard-eyebrow">Admin</p>
            <h1 style="margin: 0 0 6px 0;">{{ $project->name }}</h1>
            <p style="margin: 0; color: #6b7280; font-size: 14px;">Upload and manage files for this project.</p>
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
                        <div class="file-card-item">
                            <div class="file-details">
                                <div class="file-badge-icon">
                                    {{ strtoupper(pathinfo($file->original_name, PATHINFO_EXTENSION)) ?: 'FILE' }}
                                </div>
                                <div class="file-meta">
                                    <strong title="{{ $file->original_name }}">{{ $file->original_name }}</strong>
                                                                        <span>
                                        Uploaded {{ $file->created_at->format('M d, Y') }}
                                        @if($file->uploadedBy)
                                            by {{ $file->uploadedBy->name }}
                                            <span class="badge badge-{{ $file->uploadedBy->role === 'client' ? 'neutral' : 'success' }}" style="margin-left: 6px; font-size: 10px; padding: 1px 7px;">
                                                {{ ucfirst($file->uploadedBy->role) }}
                                            </span>
                                        @endif
                                    </span>
                                </div>
                            </div>

                            @if(strtolower(pathinfo($file->original_name, PATHINFO_EXTENSION)) === 'pdf')
                                <a href="{{ route('files.view', $file) }}" target="_blank" rel="noopener noreferrer" class="btn btn-small"
                                    style="background: #2563eb; color: white; border: none; text-decoration: none;">
                                        View PDF
                                </a>
                            @else
                                <a href="{{ route('files.view', $file) }}" class="btn btn-small"
                                    style="background: #2563eb; color: white; border: none; text-decoration: none;">
                                        Download
                                </a>
                            @endif
                            
                            <form method="POST" action="{{ route('admin.files.destroy', [$project, $file]) }}" onsubmit="return confirm('Delete this file?');" class="file-delete-form">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-small" style="background: #dc2626; color: white; border: none;">
                                    Delete File
                                </button>
                            </form>
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