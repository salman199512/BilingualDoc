@extends('layouts.app')

@section('page-title', 'Dashboard')

@section('header-actions')
    <a href="{{ route('documents.create') }}" class="btn btn-primary" onclick="showLoader()">+ Create Document</a>
@endsection

@section('content')
<div class="animate-fade-in">
    <!-- Stats Grid -->
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-icon">📄</div>
            <div class="stat-info">
                <span class="stat-value">{{ $docsCount }}</span>
                <span class="stat-label">Total Documents</span>
            </div>
        </div>
        <div class="stat-card color-2">
            <div class="stat-icon">🗂️</div>
            <div class="stat-info">
                <span class="stat-value">{{ $templatesCount }}</span>
                <span class="stat-label">Templates</span>
            </div>
        </div>
        <div class="stat-card color-3">
            <div class="stat-icon">📥</div>
            <div class="stat-info">
                <span class="stat-value">{{ $submissionsCount }}</span>
                <span class="stat-label">Submissions</span>
            </div>
        </div>
    </div>

    <!-- Main Grid -->
    <div class="dashboard-grid">
        <!-- Recent Documents -->
        <div class="card">
            <div class="card-title">
                <span>Recent Documents</span>
                <a href="{{ route('documents.index') }}" class="text-link" style="font-size: 0.9rem;">View All</a>
            </div>
            
            <div class="table-container">
                @if($recentDocuments->count() > 0)
                    <table>
                        <thead>
                            <tr>
                                <th>Title</th>
                                <th>Created By</th>
                                <th>Last Updated</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($recentDocuments as $doc)
                                <tr>
                                    <td><strong>{{ $doc->title }}</strong></td>
                                    <td>{{ $doc->user->name ?? 'System' }}</td>
                                    <td>{{ $doc->updated_at->diffForHumans() }}</td>
                                     <td>
                                        <div class="action-btn-group">
                                            <a href="{{ route('documents.edit', $doc->id) }}" class="action-btn action-btn-edit" data-tooltip="Edit Document" data-tooltip-pos="bottom">
                                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path></svg>
                                            </a>
                                            <a href="{{ route('documents.export-pdf', $doc->id) }}" class="action-btn action-btn-pdf" data-tooltip="Download PDF" data-tooltip-pos="bottom">
                                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="12" y1="18" x2="12" y2="12"></line><line x1="9" y1="15" x2="12" y2="18"></line><line x1="15" y1="15" x2="12" y2="18"></line></svg>
                                            </a>
                                            <a href="{{ route('documents.export-docx', $doc->id) }}" class="action-btn action-btn-docx" data-tooltip="Download Word DOCX" data-tooltip-pos="bottom">
                                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M14.5 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7.5L14.5 2z"></path><polyline points="14 2 14 8 20 8"></polyline><path d="M8 13h2"></path><path d="M8 17h8"></path><path d="M14 13h2"></path></svg>
                                            </a>
                                        </div>
                                     </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @else
                    <p style="text-align: center; color: var(--text-muted); padding: 2rem;">No documents created yet. Get started by creating one!</p>
                @endif
            </div>
        </div>

        <!-- Recent Templates -->
        <div class="card">
            <div class="card-title">
                <span>Quick Templates</span>
                <a href="{{ route('templates.index') }}" class="text-link" style="font-size: 0.9rem;">View All</a>
            </div>

            <div style="display: flex; flex-direction: column; gap: 1rem;">
                @if($recentTemplates->count() > 0)
                    @foreach($recentTemplates as $tpl)
                        <div style="border: 1px solid var(--border-color); border-radius: 8px; padding: 0.85rem 1rem; display: flex; justify-content: space-between; align-items: center; transition: all 0.2s;" onmouseover="this.style.borderColor='#cbd5e1'" onmouseout="this.style.borderColor='var(--border-color)'">
                            <div>
                                <h4 style="font-size: 0.95rem; margin-bottom: 0.2rem; font-weight: 600;">{{ $tpl->title }}</h4>
                                <p style="font-size: 0.8rem; color: var(--text-muted); line-height: 1.4;">{{ Str::limit($tpl->description, 70) }}</p>
                            </div>
                            <a href="{{ route('templates.fill', $tpl->id) }}" class="action-btn action-btn-fill" data-tooltip="Fill & Generate Document" data-tooltip-pos="left" style="flex-shrink: 0;">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"><polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"></polygon></svg>
                            </a>
                        </div>
                    @endforeach
                @else
                    <p style="text-align: center; color: var(--text-muted); padding: 2rem;">No templates available.</p>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
