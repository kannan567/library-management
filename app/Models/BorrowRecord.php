<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class BorrowRecord extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'book_id',
        'borrowed_at',
        'due_date',
        'returned_at',
        'fine_amount',
    ];

    public static function calculateFines($fineRate = 5)
    {
        $returnedAt = Carbon::now()->format('Y-m-d');

        $overdueRecords = self::where('due_date', '<', $returnedAt)
        ->whereNull('returned_at')
        ->orWhere('returned_at', '')
        ->get();
       
        foreach ($overdueRecords as $record) {
            $due_date = Carbon::parse($record->due_date)->format('Y-m-d');
            $currentDate = Carbon::createFromFormat('Y-m-d', $returnedAt);
            $dueDate = Carbon::createFromFormat('Y-m-d', $due_date);

            $daysDifference = $dueDate->diffInDays($currentDate);
            $fine = $daysDifference * $fineRate;

            $record->update(['fine_amount' => $fine]);
        }
    }
}
