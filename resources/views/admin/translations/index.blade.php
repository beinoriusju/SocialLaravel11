@extends('admin.layouts.master')

@section('content')
      <!-- Main Content -->
      <div class="container-xxl flex-grow-1 container-p-y">

        <section class="section">
          <div class="section-header">
            <h4 class="py-3 mb-4">Translations</h4>
          </div>

          <div class="section-body">

            <div class="row">
              <div class="col-12">
                <div class="card">
                  <div class="card-header">
                    <h4>Translations Table</h4>
                    <div class="card-header-action">
                        <a href="{{ route('admin.translations.create') }}" class="btn btn-primary"><i class="fas fa-plus"></i> Create New</a>
                    </div>
                  </div>
                  <div class="card-body">
                    {{ $dataTable->table() }}
                  </div>

                </div>
              </div>
            </div>

          </div>
        </section>
      </div>

@endsection

@push('scripts')
    {{ $dataTable->scripts(attributes: ['type' => 'module']) }}
@endpush
