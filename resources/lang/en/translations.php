<?php

use App\Models\Translation;

return Translation::where('language', 'en')->pluck('value', 'key')->toArray();
