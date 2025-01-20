<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\QuotationItem;  // Import the QuotationItem model

class Quotation extends Model
{
    use HasFactory;

    // Define the table name (optional if your table is plural and follows Laravel conventions)
    protected $table = 'quotations';

    // Define which fields can be mass-assigned (fillables)
    protected $fillable = [
        'quotation_number',
        'customer_name',
        'total_amount',
        'gst_amount',
        'total_with_gst',
        'due_date',
    ];

    // Define the relationship with the items (if applicable)
    public function items()
    {
        return $this->hasMany(QuotationItem::class);  // Adjust according to your model names
    }
}
