<?php

namespace App\Models;

/**
 * Backwards compatibility alias: keep `Skills` class name but delegate
 * to the canonical singular `Skill` model. This file is a compatibility
 * shim and can be removed once the codebase and autoload are fully
 * migrated to `Skill`.
 */

class Skills extends Skill
{
    // intentionally empty; alias to singular model
}
