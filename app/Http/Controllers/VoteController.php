<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\UserVote;
use App\Models\User;

class VoteController extends Controller
{
    //
    public function index()
    {
        if (!\Illuminate\Support\Facades\Auth::check() || \Illuminate\Support\Facades\Auth::user()->role !== 'admin') {
            return redirect()->route('index');
        }
        // Mengambil jumlah vote berdasarkan vote_number
        // $voteCounts = UserVote::select('vote_number', DB::raw('count(*) as total'))
        //                     ->groupBy('vote_number')
        //                     ->get();

        // $voteCounts = DB::table('user_votes')
        //     ->select('vote_number', DB::raw('count(*) as total'))
        //     ->groupBy('vote_number')// or 'asc' for ascending order
        //     ->get();

        //New Logic Vote
        $candidates = [
            ['name' => 'Nayla Qur\'ainy Regitha Amalia', 'vote_number' => 1],
            ['name' => 'M. Qoirul Ulum', 'vote_number' => 2],
            ['name' => 'Mikaela Angely Wilson', 'vote_number' => 3],
        ];

        $voteCounts = UserVote::select('vote_number', DB::raw('count(*) as total'))
            ->groupBy('vote_number')
            ->get()
            ->keyBy('vote_number');

        // Gabungkan hasil voting dengan kandidat yang sesuai
        $results = array_map(function($candidate) use ($voteCounts) {
            return [
                'name' => $candidate['name'], 
                'total' => $voteCounts->get($candidate['vote_number'])->total ?? 0 // Default 0 jika tidak ada suara
            ];
        }, $candidates);

        // Data yang telah memilih
        $alreadyVote = DB::table('users')
            ->join('user_votes', 'users.nis', '=', 'user_votes.nis_vote')
            ->where('users.role', 'student')
            ->count();

        // Menghitung siswa yang dapat melakukan pemilihan
        $userCount = User::where('role', 'student')->count();
        // $userCount = User::count();

        //Total Percentage
        $overallPercentage = $userCount > 0 ? ($alreadyVote / $userCount) * 100 : 0;

        // Pass both counts to the Blade view
        return view('result', compact('results', 'userCount', 'alreadyVote', 'overallPercentage'));
    }

}
