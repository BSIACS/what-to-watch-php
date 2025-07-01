<?php

namespace App\Constants;

class FilmValidationConstants
{
    const MAX_NANE_LENGTH = 255;
    const MAX_DESCRIPTION_LENGTH = 1000;
    const MAX_STARRING_ARRAY_LENGTH = 20;
    const MAX_RUNTIME_VALUE = 9999;
    const KB_IN_MB = 1024;
    const MAX_POSTER_IMAGE_SIZE = self::KB_IN_MB * 10;
    const MAX_PREVIEW_IMAGE_SIZE = self::KB_IN_MB * 10;
    const MAX_BACKGROUND_IMAGE_SIZE = self::KB_IN_MB * 10;
    const MAX_VIDEO_SIZE = self::KB_IN_MB * 1000;
    const MAX_PREVIEW_VIDEO_SIZE = self::KB_IN_MB * 50;
}
