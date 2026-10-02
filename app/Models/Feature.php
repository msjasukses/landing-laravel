<?php

namespace App\Models;

use App\Models\Concerns\Sortable;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['icon', 'title', 'subtitle', 'sort_order', 'is_active'])]
class Feature extends Model
{
    use Sortable;

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }
}
