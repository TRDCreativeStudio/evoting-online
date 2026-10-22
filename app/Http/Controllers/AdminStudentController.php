<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\UserVote;

class AdminStudentController extends Controller
{
    private function checkAdmin()
    {
        if (!Auth::check() || Auth::user()->role !== 'admin') {
            abort(403, 'Akses khusus Admin.');
        }
    }

    public function index(Request $request)
    {
        $this->checkAdmin();

        $search = $request->query('search');

        $studentsQuery = User::where('role', 'student');
        if ($search) {
            $studentsQuery->where(function ($q) use ($search) {
                $q->where('nis', 'like', "%{$search}%")
                  ->orWhere('name', 'like', "%{$search}%");
            });
        }

        $students = $studentsQuery->orderBy('nis', 'asc')->paginate(30)->withQueryString();
        $studentCount = User::where('role', 'student')->count();
        $votedCount = User::where('role', 'student')->whereNull('password')->count();
        $admins = User::where('role', 'admin')->get();

        return view('admin.students', compact('students', 'studentCount', 'votedCount', 'admins', 'search'));
    }

    public function importCsv(Request $request)
    {
        $this->checkAdmin();
        set_time_limit(300);
        ini_set('max_execution_time', '300');

        $request->validate([
            'csv_file' => 'required|file',
        ]);

        $replaceAll = $request->boolean('replace_all');

        $file = $request->file('csv_file');
        $handle = fopen($file->getRealPath(), 'r');
        if (!$handle) {
            return back()->with('error', 'Gagal membuka file CSV.');
        }

        // Read first line to detect delimiter (, or ;)
        $firstLine = fgets($handle);
        $delimiter = (substr_count($firstLine, ';') > substr_count($firstLine, ',')) ? ';' : ',';
        rewind($handle);

        // Read headers
        $headerRow = fgetcsv($handle, 2000, $delimiter);
        if (!$headerRow) {
            fclose($handle);
            return back()->with('error', 'File CSV kosong.');
        }

        // Clean headers: lowercase and trim BOM / whitespace
        $headers = array_map(function ($h) {
            $h = preg_replace('/[\x00-\x1F\x80-\xFF]/', '', $h);
            return strtolower(trim($h));
        }, $headerRow);

        $nisIdx = array_search('nis', $headers);
        $nameIdx = array_search('name', $headers);
        if ($nameIdx === false) {
            $nameIdx = array_search('nama', $headers);
        }
        $roleIdx = array_search('role', $headers);
        $passIdx = array_search('password', $headers);
        if ($passIdx === false) {
            $passIdx = array_search('token', $headers);
        }

        if ($nisIdx === false || $nameIdx === false) {
            fclose($handle);
            return back()->with('error', 'Format CSV salah! Harus ada kolom "nis" dan "name" (atau "nama").');
        }

        $records = [];
        $now = now();

        while (($row = fgetcsv($handle, 2000, $delimiter)) !== false) {
            if (empty(array_filter($row))) {
                continue;
            }

            $nis = isset($row[$nisIdx]) ? trim($row[$nisIdx]) : '';
            $name = isset($row[$nameIdx]) ? trim($row[$nameIdx]) : '';

            if (empty($nis) || empty($name)) {
                continue;
            }

            $role = ($roleIdx !== false && !empty($row[$roleIdx])) ? trim($row[$roleIdx]) : 'student';

            // If token provided in CSV, use it; otherwise generate random 6-digit PIN
            $token = ($passIdx !== false && !empty(trim($row[$passIdx]))) 
                ? trim($row[$passIdx]) 
                : (string) random_int(100000, 999999);

            $records[] = [
                'nis'          => $nis,
                'name'         => $name,
                'role'         => $role,
                'password'     => password_hash($token, PASSWORD_BCRYPT, ['cost' => 4]),
                'password_row' => $token,
                'voted_at'     => null,
                'created_at'   => $now,
                'updated_at'   => $now,
            ];
        }
        fclose($handle);

        if (empty($records)) {
            return back()->with('error', 'Tidak ada data valid yang ditemukan dalam file CSV.');
        }

        DB::transaction(function () use ($replaceAll, $records) {
            if ($replaceAll) {
                // Wipe previous students and votes for a clean fresh election/gladi bersih
                User::where('role', 'student')->delete();
                UserVote::query()->delete();

                // Direct insert without requiring database UNIQUE constraint
                foreach (array_chunk($records, 100) as $chunk) {
                    DB::table('users')->insert($chunk);
                }
            } else {
                foreach ($records as $rec) {
                    DB::table('users')->updateOrInsert(
                        ['nis' => $rec['nis']],
                        $rec
                    );
                }
            }
        });

        $message = "Sukses mengimpor " . count($records) . " data siswa beserta token login!";
        if ($replaceAll) {
            $message .= " (Data siswa dan suara sebelumnya telah di-reset).";
        }

        return back()->with('success', $message);
    }

    public function exportCsv()
    {
        $this->checkAdmin();

        $students = User::where('role', 'student')->orderBy('nis', 'asc')->get();

        $fileName = 'rekap_token_pemilih_' . date('Ymd_His') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$fileName}\"",
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0',
        ];

        $callback = function () use ($students) {
            $file = fopen('php://output', 'w');
            // Write UTF-8 BOM for Excel compatibility
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));
            fputcsv($file, ['No', 'NIS', 'Nama Siswa', 'Role', 'Token Login', 'Status Memilih', 'Waktu Memilih'], ';');

            $i = 1;
            foreach ($students as $student) {
                fputcsv($file, [
                    $i++,
                    $student->nis,
                    $student->name,
                    $student->role,
                    $student->password_row,
                    $student->password === null ? 'Sudah Memilih' : 'Belum Memilih',
                    $student->voted_at ?? '-',
                ], ';');
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function resetVotes()
    {
        $this->checkAdmin();

        DB::transaction(function () {
            // Delete all voting records
            UserVote::query()->delete();

            // Reactivate all students who already voted by restoring their password hash from password_row
            $votedStudents = User::where('role', 'student')->whereNull('password')->get();
            foreach ($votedStudents as $student) {
                $student->password = password_hash($student->password_row, PASSWORD_BCRYPT, ['cost' => 4]);
                $student->voted_at = null;
                $student->save();
            }
        });

        return back()->with('success', 'Perolehan suara berhasil di-reset menjadi 0! Semua token siswa sudah aktif kembali untuk gladi bersih / pemilihan ulang.');
    }

    public function deleteAllStudents()
    {
        $this->checkAdmin();

        DB::transaction(function () {
            UserVote::query()->delete();
            User::where('role', 'student')->delete();
        });

        return back()->with('success', 'Seluruh data siswa dan suara berhasil dikosongkan.');
    }

    public function deleteStudent($id)
    {
        $this->checkAdmin();

        $student = User::where('role', 'student')->findOrFail($id);
        UserVote::where('nis_vote', $student->nis)->delete();
        $student->delete();

        return back()->with('success', "Data siswa {$student->name} ({$student->nis}) berhasil dihapus.");
    }

    public function saveAdmin(Request $request)
    {
        $this->checkAdmin();

        $request->validate([
            'nis' => 'required',
            'name' => 'required',
            'token' => 'required',
        ]);

        $admin = User::where('nis', $request->nis)->first();
        if ($admin && $admin->id !== Auth::id() && $admin->role !== 'admin') {
            return back()->with('error', 'NIS tersebut sudah digunakan oleh siswa.');
        }

        if (!$admin) {
            $admin = new User();
            $admin->role = 'admin';
        }

        $admin->nis = $request->nis;
        $admin->name = $request->name;
        $admin->password_row = $request->token;
        $admin->password = Hash::make($request->token);
        $admin->save();

        return back()->with('success', "Akun admin {$admin->name} (NIS: {$admin->nis}) berhasil diperbarui!");
    }
}
