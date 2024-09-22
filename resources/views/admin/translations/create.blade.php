@extends('admin.layouts.master')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">

<section class="section">
  <div class="section-header">
    <h4 class="py-3 mb-4">Create translations</h4>
  </div>

  <div class="section-body">
    <div class="row">
      <div class="col-lg-12">
        <div class="card">
          <div class="card-header">
            <h4>Create Translation</h4>
          </div>
          <div class="card-body">
            <form action="{{ route('admin.translations.store') }}" method="POST">
              @csrf
              <div class="mb-3">
                <label for="language" class="form-label">Language</label>
                <select class="form-select" id="language" name="language">
                  <option value="en" {{ old('language') == 'en' ? 'selected' : '' }}>English</option>
                  <option value="ru" {{ old('language') == 'ru' ? 'selected' : '' }}>Russian</option>
                  <option value="lt" {{ old('language') == 'lt' ? 'selected' : '' }}>Lithuanian</option>
                </select>
              </div>

              <div class="mb-3">
                <label for="key" class="form-label">Key</label>
                <input type="text" class="form-control" id="key" name="key" value="{{ old('key') }}">
              </div>

              <div class="mb-3">
                <label for="value" class="form-label">Value</label>
                <textarea class="form-control" id="value" name="value">{{ old('value') }}</textarea>
              </div>

              <button type="submit" class="btn btn-primary">Create</button>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>
</div>
@endsection
