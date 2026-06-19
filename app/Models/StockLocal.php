<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StockLocal extends Model
{
    protected $table = 'stock_local';

    protected $fillable = [
        'sede_id',
        'lote_local_id',
        'cantidad_disponible',
    ];

    protected function casts(): array
    {
        return [
            'cantidad_disponible' => 'integer',
        ];
    }

    public function sede()
    {
        return $this->belongsTo(Sede::class);
    }

    public function loteLocal()
    {
        return $this->belongsTo(LoteLocal::class);
    }
}
