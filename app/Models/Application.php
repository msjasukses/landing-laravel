<?php

namespace App\Models;

use App\Models\Concerns\Sortable;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'app_group_id', 'name', 'description', 'url', 'icon', 'color',
    'open_in_new_tab', 'sort_order', 'is_active',
])]
class Application extends Model
{
    use Sortable;

    /**
     * Urutan aplikasi dihitung per grup.
     */
    protected string $sortScope = 'app_group_id';

    protected function casts(): array
    {
        return [
            'open_in_new_tab' => 'boolean',
            'is_active' => 'boolean',
        ];
    }

    public function group(): BelongsTo
    {
        return $this->belongsTo(AppGroup::class, 'app_group_id');
    }
}
