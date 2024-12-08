<?php

use Dbout\WpOrm\Orm\AbstractModel;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Salary extends AbstractModel
{
    // Define the table name (if different from the model's name)
    protected $table = 'gambling_rest_api_salary';  // Define your table name

    // Define the primary key
    protected $primaryKey = 'id';  // The primary key for the City table

    // Define the foreign key for the relationship
    public $foreignKey = 'city_id';  // The foreign key pointing to the 'country' table

    // Define whether IDs are auto-incrementing
    public $incrementing = true;

    // Define the attributes that are mass assignable (fillable)
    protected $fillable = ['date', 'amount_in_eur'];  // Fields that you can insert

    // Disable timestamps (created_at, updated_at) if you don’t use them
    public $timestamps = false;

    // Define the relationship with the 'Country' model
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');  // Defining the foreign key relation to 'Country'
    }
}