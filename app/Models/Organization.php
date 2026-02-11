<?php

namespace App\Models;

// Lightweight alias to support code/tests using singular `Organization`.
// Extends the existing `Organizations` model to avoid duplicating logic.
class Organization extends Organizations
{
    // Intentionally empty - inherits everything from Organizations
}
