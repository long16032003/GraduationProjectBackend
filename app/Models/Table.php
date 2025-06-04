<?php

namespace App\Models;

use App\Models\ModelFilters\TableFilter;
use EloquentFilter\Filterable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Table extends Model
{
    use HasFactory , Filterable;
    protected $table = 'tables';
    public $timestamps = true;

    const STATUS_AVAILABLE = 'available';
    const STATUS_OCCUPIED = 'occupied';
    const STATUS_RESERVED = 'reserved';
    const STATUS_MAINTENANCE = 'maintenance';

    const AREA_1ST_FLOOR = '1st floor';
    const AREA_2ND_FLOOR = '2nd floor';
    const AREA_3RD_FLOOR = '3rd floor';
    const AREA_ROOFTOP = 'rooftop';

    const AREA_LIST = [
        self::AREA_1ST_FLOOR,
        self::AREA_2ND_FLOOR,
        self::AREA_3RD_FLOOR,
        self::AREA_ROOFTOP
    ];

    const STATUS_LIST = [
        self::STATUS_AVAILABLE,
        self::STATUS_OCCUPIED,
        self::STATUS_RESERVED,
        self::STATUS_MAINTENANCE
    ];

    protected $fillable = ['name', 'creator_id', 'capacity', 'area', 'status'];

    // public function reservations()
    // {
    //     return $this->hasMany(Reservation::class, 'table_id', 'id');
    // }

    public function creator()
    {
        return $this->belongsTo(User::class, 'creator_id', 'id');
    }

    public function modelFilter()
    {
        return $this->provideFilter(TableFilter::class);
    }

}
