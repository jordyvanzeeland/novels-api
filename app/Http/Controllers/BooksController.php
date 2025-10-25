<?php

namespace App\Http\Controllers;

use App\Models\Book;
use Illuminate\Http\Request;

class BooksController extends Controller
{
	/**
      * Require authentication for all methods in this controller.
     */

    public function __construct(){
        $this->middleware('auth:api');
    }

    public function getReadingYears(){
        $years = Book::selectRaw('YEAR(readed) as year, COUNT(*) as count')
        ->groupByRaw('YEAR(readed)')
        ->orderBy('year')
        ->get();

        return response()->json($years);
    }

	/**
      * Get all readed books by given year
	 * Including backend filtering
     */

    public function getBooksByYear(Request $request, string $year){
		$filters = ['author', 'genre', 'year', 'rating', 'language'];
		$query = Book::whereYear('readed', $year);

		foreach ($filters as $filter) {
            $value = $request->header($filter);
            if ($value) {
                if ($filter === 'year') {
                    $query->whereYear('readed', $value);
                } else {
                    $query->where($filter, $value);
                }
            }
        }

        $books = $query->get();
        return response()->json($books, 200);
    }

	/**
      * Get all unreaded books
     */

    public function getUnreadedBook(){
        $books = Book::whereNull('readed')->get();
        return response()->json($books, 200);
    }

	/**
      * Get currently reading book
     */

	public function getCurrentReadingBookOfUser(){
        $books = Book::where('current', 1)->get();
        return response()->json($books, 200);
    }

	/**
    * Insert a book
	* This including validating the data
    */

    public function insertBook(Request $request){
        $validated = $request->validate([
			'title' => 'required|string|max:255',
			'author' => 'required|string|max:255',
			'readed' => 'nullable|date',
		]);

        $newBook = Book::create($validated);

        return response()->json([
            'message' => 'New book added',
            'newbook' => $newBook
        ], 201);
    }

	/**
    * Update a book, given by it's id
	* This including validating the data
    */

    public function updateBook(Request $request, int $bookid){
		$validated = $request->validate([
			'title' => 'sometimes|required|string|max:255',
			'author' => 'sometimes|required|string|max:255',
			'readed' => 'nullable|date',
		]);

		$book = Book::findOrFail($bookid);
        $book->update($validated);

        return response()->json([
            'message' => 'Book updated',
            'book' => $book
        ], 200);
    }

	/**
    * Delete a book given by it's id.
    */

    public function deleteBook(int $bookid){
        $book = Book::findOrFail($bookid);
        $book->delete();

        return response()->json([
            'message' => 'Book deleted'
        ], 200);
    }
}

?>