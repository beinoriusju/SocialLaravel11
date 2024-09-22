<?php

use App\Models\Translation;

return Translation::where('language', 'lt')->pluck('value', 'key')->toArray();
  
