<?php

namespace App\Models;

use App\Models\ModelFilters\ReservationFilter;
use EloquentFilter\Filterable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Reservation extends Model
{
    use Filterable;
    protected $table = 'reservations';
    public $timestamps = true;
    //
    protected $fillable = [
        'table_id',
        'customer_id',
        'phone',
        'name',
        'reservation_date',
        'number_of_guests',
        'status',
        'note',
        'creator_id',
        'creator_type',
    ];

    public function table()
    {
        return $this->belongsTo(Table::class, 'table_id', 'id');
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class, 'customer_id', 'id');
    }

    public function modelFilter()
    {
        return $this->provideFilter(ReservationFilter::class);
    }
}
