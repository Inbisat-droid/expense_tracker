<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class TransactionController extends Controller
{
    // Show main page
    public function index()
    {
        $transactions = Transaction::where('user_id', auth()->id())->orderByDesc('created_at')->get();
        return view('index', compact('transactions'));
    }

    // Store new transaction (AJAX — multipart/form-data because of file upload)
    public function store(Request $request)
    {
        $validated = $request->validate([
            'desc' => 'required|string|max:255',
            'amount'      => 'required|numeric|min:0.01',
            'type'        => 'required|in:income,expense',
            'category'    => 'required|string|max:255',
            'date'        => 'required|date',
            'attachment'  => 'nullable|file|mimes:jpg,jpeg,png,pdf,doc,docx,xlsx,csv|max:2048',
        ]);

        // Handle file upload
        $attachmentPath = null;
        if ($request->hasFile('attachment')) {
            // Store in storage/app/public/attachments
            // Run: php artisan storage:link  (once, to create public/storage symlink)
            $attachmentPath = $request->file('attachment')->store('attachments', 'public');
        }

        $transaction = Transaction::create([
            'user_id'    => auth()->id(),
            'desc' => $validated['desc'],
            'amount'     => $validated['amount'],
            'type'       => $validated['type'],
            'category'   => $validated['category'],
            'date'       => $validated['date'],
            'attachment' => $attachmentPath,
        ]);

        return response()->json([
            'success'     => true,
            'transaction' => $transaction,
            'summary'     => $this->summary(),
        ]);
    }

    // Delete one transaction (AJAX)
    public function destroy($id)
    {
        $transaction = Transaction::where('user_id', auth()->id())->find($id);

        if ($transaction) {
            // Delete the file from storage if it exists
            if ($transaction->attachment) {
                Storage::disk('public')->delete($transaction->attachment);
            }
            $transaction->delete();
        }

        return response()->json([
            'success' => true,
            'summary' => $this->summary(),
        ]);
    }

    // Delete all transactions (AJAX)
    public function destroyAll()
    {
        // Delete all attachment files first
        $transactions = Transaction::where('user_id', auth()->id())->get();
        foreach ($transactions as $transaction) {
            if (!empty($transaction->attachment)) {
                Storage::disk('public')->delete($transaction->attachment);
            }
        }

        Transaction::where('user_id', auth()->id())->delete();

        return response()->json([
            'success' => true,
            'summary' => $this->summary(),
        ]);
    }

    // Get all transactions as JSON
    public function list()
    {
        return response()->json(Transaction::where('user_id', auth()->id())->orderByDesc('created_at')->get());
    }

    private function summary()
    {
        $income  = Transaction::where('user_id', auth()->id())->where('type', 'income')->sum('amount');
        $expense = Transaction::where('user_id', auth()->id())->where('type', 'expense')->sum('amount');

        return [
            'income'  => $income,
            'expense' => $expense,
            'balance' => $income - $expense,
        ];
    }
}
