<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Book;
use App\Models\BorrowRecord;

class BorrowRecordController extends Controller
{
    public function borrow(Request $request, $book_id)
    {
        $book = Book::find($book_id);

        if (!$book || $book->status !== 'available') {
            return response()->json(['message' => 'Book not available.'], 400);
        }

        $book->update(['status' => 'borrowed']);

        BorrowRecord::create([
            'user_id' => auth()->id(),
            'book_id' => $book_id,
            'borrowed_at' => now(),
            'due_date' => now()->addDays(14),
        ]);

        return response()->json(['message' => 'Book borrowed successfully.']);
    }

    public function payFine($record_id)
    {
        $record = BorrowRecord::find($record_id);

        if (!$record || $record->fine_amount <= 0) {
            return response()->json(['message' => 'No fine to pay.'], 400);
        }

        $record->update(['fine_amount' => 0,'returned_at' => now()]);

        $book = Book::find($record->book_id);
        $book->update(['status' => 'available']);
        
        return response()->json(['message' => 'Fine paid successfully.']);
    }

    public function calculateFines()
    {
        try {
            BorrowRecord::calculateFines();
            return response()->json([
                'message' => 'Fines calculated successfully for overdue borrow records.',
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'An error occurred while calculating fines.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

}
