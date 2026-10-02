<?php

namespace App\Models\Concerns;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

/**
 * Urutan tampil (sort_order) dan status aktif untuk baris yang bisa diurutkan.
 *
 * Model dapat mendefinisikan $sortScope (mis. 'app_group_id') agar urutan
 * dihitung per kelompok, bukan untuk seluruh tabel.
 */
trait Sortable
{
    public static function bootSortable(): void
    {
        static::creating(function (self $model) {
            if (! $model->sort_order) {
                $model->sort_order = $model->siblings()->max('sort_order') + 1;
            }
        });
    }

    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderBy('sort_order')->orderBy('id');
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    /**
     * Baris lain dalam lingkup urutan yang sama.
     */
    public function siblings(): Builder
    {
        $query = static::query();

        if ($column = $this->sortScope ?? null) {
            $query->where($column, $this->{$column});
        }

        return $query;
    }

    /**
     * Menggeser baris naik/turun satu posisi, sekaligus merapikan nomor urut 1..n.
     */
    public function move(string $direction): void
    {
        $ids = $this->siblings()->ordered()->pluck('id')->all();
        $pos = array_search($this->getKey(), $ids, true);
        if ($pos === false) {
            return;
        }

        $swap = $direction === 'up' ? $pos - 1 : $pos + 1;
        if (! isset($ids[$swap])) {
            return;
        }

        [$ids[$pos], $ids[$swap]] = [$ids[$swap], $ids[$pos]];

        DB::transaction(function () use ($ids) {
            foreach ($ids as $index => $id) {
                static::whereKey($id)->update(['sort_order' => $index + 1]);
            }
        });
    }

    public function toggleActive(): void
    {
        $this->update(['is_active' => ! $this->is_active]);
    }
}
