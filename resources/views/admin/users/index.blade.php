@extends('admin.layouts.master')

@section('content')
    <!-- Main Content -->
    <div class="container-xxl flex-grow-1 container-p-y">

        <section class="section">
            <div class="section-header">
                <h4 class="py-3 mb-4">Users</h4>
            </div>

            <div class="section-body">
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <h4>Users Table</h4>
                            </div>
                            <div class="card-body">
                                <!-- Table for DataTables -->
                                {{ $dataTable->table(['class' => 'table table-striped table-bordered']) }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
@endsection

@push('scripts')
    <!-- DataTable Scripts -->
    {!! $dataTable->scripts() !!}
@endpush
