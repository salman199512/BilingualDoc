@extends('layouts.app')

@section('page-title', 'Document Templates')

@section('header-actions')
    <a href="{{ route('templates.create') }}" class="btn btn-primary" onclick="showLoader()">+ Create Template</a>
@endsection

@section('content')
<!-- DataTables CSS -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/jquery.dataTables.min.css">
<style>
    .dataTables_wrapper {
        padding: 0.25rem 0;
    }
    .dataTables_wrapper .dataTables_length, 
    .dataTables_wrapper .dataTables_filter {
        margin-bottom: 0.85rem;
        font-weight: 500;
        font-size: 0.85rem;
    }
    .dataTables_wrapper .dataTables_length select,
    .dataTables_wrapper .dataTables_filter input {
        border: 1px solid #e2e8f0;
        border-radius: 6px;
        padding: 5px 10px;
        outline: none;
        font-family: 'Poppins', sans-serif;
        font-size: 0.82rem;
    }
    .dataTables_wrapper .dataTables_filter input {
        margin-left: 0.5rem;
        width: 220px;
    }
    .dataTables_wrapper .dataTables_info {
        margin-top: 1rem;
        font-size: 0.82rem;
        color: var(--text-muted);
        font-weight: 500;
    }
    .dataTables_wrapper .dataTables_paginate {
        margin-top: 1rem;
    }
    .dataTables_wrapper .dataTables_paginate .paginate_button {
        padding: 0.35rem 0.75rem !important;
        border-radius: 6px !important;
        border: 1px solid #e2e8f0 !important;
        margin-left: 4px !important;
        background: #ffffff !important;
        color: var(--text-primary) !important;
        font-size: 0.82rem !important;
        font-weight: 500 !important;
        cursor: pointer;
        transition: var(--transition-fast);
    }
    .dataTables_wrapper .dataTables_paginate .paginate_button.current,
    .dataTables_wrapper .dataTables_paginate .paginate_button.current:hover {
        background: var(--primary-gradient) !important;
        color: white !important;
        border-color: var(--primary-blue) !important;
        box-shadow: var(--shadow-sm);
    }
    .dataTables_wrapper .dataTables_paginate .paginate_button:hover {
        background: #f8fafc !important;
        border-color: #cbd5e1 !important;
        color: var(--text-primary) !important;
    }
    table.dataTable.no-footer {
        border-bottom: 1px solid var(--border-color) !important;
    }
    table.dataTable {
        border-collapse: collapse !important;
    }
</style>

<div class="card animate-fade-in" style="padding: 1.25rem 1.5rem;">
    <div class="table-container">
        <table id="templates-table" class="display" style="width:100%">
            <thead>
                <tr>
                    <th>Title</th>
                    <th>Description</th>
                    <th>Last Updated</th>
                    <th style="text-align: right;">Actions</th>
                </tr>
            </thead>
        </table>
    </div>
</div>
@endsection

@section('scripts')
<!-- jQuery & DataTables JS -->
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
<script>
    $(document).ready(function() {
        $('#templates-table').DataTable({
            processing: true,
            serverSide: true,
            ajax: "{{ route('templates.index') }}",
            columns: [
                { data: 'title', name: 'title', render: function(data, type, row) {
                    return '<a href="/templates/' + row.id + '/edit" class="text-link" style="font-weight:600; font-size:0.92rem;">' + data + '</a>';
                }},
                { data: 'description', name: 'description', render: function(data) {
                    if (!data) return 'No description provided.';
                    return data.length > 80 ? data.substr(0, 80) + '...' : data;
                }},
                { data: 'updated_at', name: 'updated_at' },
                { data: 'action', name: 'action', orderable: false, searchable: false, className: 'text-right' }
            ],
            language: {
                search: "Search Templates:",
                lengthMenu: "Show _MENU_ records",
                info: "Showing _START_ to _END_ of _TOTAL_ templates",
                paginate: {
                    first: "First",
                    last: "Last",
                    next: "Next",
                    previous: "Previous"
                }
            }
        });
    });
</script>
@endsection
