<?php

use App\Models\Translation;

return Translation::where('language', 'ru')->pluck('value', 'key')->toArray();
