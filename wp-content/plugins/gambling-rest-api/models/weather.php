<?php

use Dbout\WpOrm\Orm\AbstractModel;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Weather extends AbstractModel
{
    // Define the table name (if different from the model's name)
    protected $table = 'gambling_rest_api_weather';  // Define your table name

    // Define the primary key
    protected $primaryKey = 'id';  // The primary key for the City table

    // Define the foreign key for the relationship
    public $foreignKey = 'city_id';  // The foreign key pointing to the 'country' table

    // Define whether IDs are auto-incrementing
    public $incrementing = true;

    // Define the attributes that are mass assignable (fillable)
    protected $fillable = ['forecast'];  // Fields that you can insert

    // Disable timestamps (created_at, updated_at) if you don’t use them
    public $timestamps = false;

    // Define the relationship with the 'Country' model
    public function city(): BelongsTo
    {
        return $this->belongsTo(City::class, 'city_id', 'id');  // Defining the foreign key relation to 'Country'
    }
}