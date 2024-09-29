<div class="row">
   <div class="col-sm-12">
      <div id="post-modal-data" class="card card-block card-stretch card-height create-post-modal">
         <div class="card-header d-flex justify-content-between border-bottom">
            <div class="header-title">
               <h5 class="card-title">{{ __('translations.Create') }}</h5>
            </div>
            <div class="dropdown">
               <div class="lh-1" id="post-option" data-bs-toggle="dropdown">
                  <span class="material-symbols-outlined">
                     more_horiz
                  </span>
               </div>
               <div class="dropdown-menu dropdown-menu-right" aria-labelledby="post-option">
                  <a class="dropdown-item" href="#" onclick="showForm('post')">{{ __('translations.Post') }}</a>
                  <a class="dropdown-item" href="#" onclick="showForm('event')">{{ __('translations.Event') }}</a>
                  <!-- <a class="dropdown-item" href="#" onclick="showForm('story')">{{ __('translations.Story') }}</a> -->
                  @if(auth()->user()->role === 'admin')
                       <a class="dropdown-item" href="#" onclick="showForm('blog')">{{ __('translations.Blog post') }}</a>
                   @endif
               </div>
            </div>
         </div>

         <div class="card-body">
            <!-- Main Form for Posting -->
            <div id="post-form-container" style="display: block;">
               <form wire:submit.prevent="createpost">
                  <!-- Content Input -->
                  <div class="d-flex align-items-center">
                     <div class="user-img">
                       <img src="{{ auth()->user()->image ? asset('storage/' . auth()->user()->image) : asset('front/images/default.png') }}" alt="{{ __('translations.profile-img') }}" loading="lazy" class="img-fluid" style="height:50px; width:40px;border-radius:25px;">
                     </div>
                     <div class="post-text ms-3 w-100">
                        <input type="text" wire:model.lazy="content" class="form-control rounded" placeholder="..." style="border:none;">
                     </div>
                  </div>

                  @error('content')
                     <span class="error">{{ $message }}</span>
                  @enderror

                  <hr>

                  <!-- Image and Video Upload Options -->
                  <ul class="d-flex flex-wrap align-items-center list-inline m-0 p-0">
                     <!-- Photo Upload -->
                     <li class="col-md-6 mb-3">
                        <div class="bg-primary-subtle rounded p-2 pointer me-3 position-relative" onclick="document.getElementById('imageInput').click();">
                           <a href="javascript:void(0);" class="d-inline-block fw-medium text-body">
                              <span class="material-symbols-outlined align-middle font-size-20 me-1">add_a_photo</span>
                              {{ __('translations.Photos') }}
                           </a>
                        </div>
                        <input type="file" wire:model="images" accept="image/*" id="imageInput" style="display: none;" multiple>
                     </li>

                     <!-- Video Upload -->
                     <li class="col-md-6 mb-3">
                        <div class="bg-primary-subtle rounded p-2 pointer me-3 position-relative" onclick="document.getElementById('videoInput').click();">
                           <a href="javascript:void(0);" class="d-inline-block fw-medium text-body">
                              <span class="material-symbols-outlined align-middle font-size-20 me-1">live_tv</span>
                              {{ __('translations.Videos') }}
                           </a>
                        </div>
                        <input type="file" wire:model="video" accept="video/*" id="videoInput" style="display: none;" multiple>
                     </li>
                  </ul>

                  <hr>

                  <!-- Privacy Settings Dropdown -->
                  <div class="form-group">
                     <select id="is_public" class="form-select" wire:model="is_public">
                        <option value="0" selected>{{ __('translations.Public') }}</option>
                        <option value="1">{{ __('translations.Friends') }}</option>
                     </select>
                  </div>

                  <!-- Post Button -->
                  <button type="submit" class="btn btn-primary d-block w-100 mt-3">{{ __('translations.Post') }}</button>
               </form>

               <!-- Loading Indicators -->
               <div wire:loading wire:target="images">{{ __('translations.Uploading images...') }}</div>
               <div wire:loading wire:target="video">{{ __('translations.Uploading videos...') }}</div>

               <!-- Preview Section -->
               <div class="mt-3">
                 @if (!empty($images) && is_array($images))
                     <div class="row">
                         @foreach ($images as $image)
                             @if ($image)
                                 <div class="col-md-4 mb-3">
                                     <img src="{{ $image->temporaryUrl() }}" alt="{{ __('translations.Uploaded image preview') }}" class="img-fluid" style="max-width: 100%;">
                                 </div>
                             @endif
                         @endforeach
                     </div>
                 @endif

                 <!-- Videos Preview -->
                @if (!empty($video) && is_array($video))
                    <div class="mt-3">
                        @foreach ($video as $videoFile)
                            @if ($videoFile)
                                <div class="mb-3">
                                    <video src="{{ $videoFile->temporaryUrl() }}" controls class="w-100" style="height: auto;"></video>
                                </div>
                            @endif
                        @endforeach
                    </div>
                @endif
             </div>
            </div>

            <!-- Include the blog-post-modal Livewire component -->
            <div id="blog-form-container" style="display: none;">
               @livewire('components.blog-post-modal')
            </div>
            <div id="event-form-container" style="display: none;">
               @livewire('components.create-event')
            </div>
         </div>
      </div>
   </div>
</div>
