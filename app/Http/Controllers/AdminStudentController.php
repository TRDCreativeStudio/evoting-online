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

        $search = trim($request->query('search', ''));

        $studentsQuery = User::where('role', 'student')
            ->select('id', 'nis', 'name', 'kelas', 'role', 'password', 'password_row', 'voted_at');

        if ($search !== '') {
            $studentsQuery->where(function ($q) use ($search) {
                $q->where('nis', 'ilike', "%{$search}%")
                  ->orWhere('name', 'ilike', "%{$search}%")
                  ->orWhere('kelas', 'ilike', "%{$search}%");
            });
        }

        $students = $studentsQuery->orderBy('kelas', 'asc')
                                  ->orderBy('nis', 'asc')
                                  ->paginate(20)
                                  ->withQueryString();

        $studentCount = User::where('role', 'student')->count();
        $votedCount = User::where('role', 'student')->whereNull('password')->count();
        $admins = User::where('role', 'admin')->get();

        return view('admin.students', compact('students', 'studentCount', 'votedCount', 'admins', 'search'));
    }

    public function importCsv(Request $request)
    {
        $this->checkAdmin();
        set_time_limit(0);
        ini_set('max_execution_time', '0');

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
        $kelasIdx = array_search('kelas', $headers);
        if ($kelasIdx === false) {
            $kelasIdx = array_search('class', $headers);
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

            $kelas = ($kelasIdx !== false && isset($row[$kelasIdx]) && trim($row[$kelasIdx]) !== '') 
                ? trim($row[$kelasIdx]) 
                : null;

            $role = ($roleIdx !== false && !empty($row[$roleIdx])) ? trim($row[$roleIdx]) : 'student';

            // If token provided in CSV, use it; otherwise generate random 6-digit PIN
            $token = ($passIdx !== false && !empty(trim($row[$passIdx]))) 
                ? trim($row[$passIdx]) 
                : (string) random_int(100000, 999999);

            $records[] = [
                'nis'          => $nis,
                'name'         => $name,
                'kelas'        => $kelas,
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

                // Direct batch insert in chunks of 200 without individual round-trips
                foreach (array_chunk($records, 200) as $chunk) {
                    DB::table('users')->insert($chunk);
                }
            } else {
                // Batch query existing NIS to avoid 1,000+ sequential round-trips
                $allNis = array_column($records, 'nis');
                $existingNis = DB::table('users')
                    ->whereIn('nis', $allNis)
                    ->pluck('nis')
                    ->flip()
                    ->all();

                $toInsert = [];
                $toUpdate = [];

                foreach ($records as $rec) {
                    if (isset($existingNis[$rec['nis']])) {
                        $toUpdate[] = $rec;
                    } else {
                        $toInsert[] = $rec;
                    }
                }

                // Batch insert all new records in chunks of 200 (lightning fast)
                if (!empty($toInsert)) {
                    foreach (array_chunk($toInsert, 200) as $chunk) {
                        DB::table('users')->insert($chunk);
                    }
                }

                // Update existing records
                if (!empty($toUpdate)) {
                    foreach ($toUpdate as $rec) {
                        DB::table('users')->where('nis', $rec['nis'])->update([
                            'name'         => $rec['name'],
                            'kelas'        => $rec['kelas'],
                            'role'         => $rec['role'],
                            'password'     => $rec['password'],
                            'password_row' => $rec['password_row'],
                            'updated_at'   => $rec['updated_at'],
                        ]);
                    }
                }
            }
        });

        $message = "Sukses mengimpor " . count($records) . " data siswa beserta token login!";
        if ($replaceAll) {
            $message .= " (Data siswa dan suara sebelumnya telah di-reset).";
        }

        return back()->with('success', $message);
    }

    public function regenerateTokens(Request $request)
    {
        $this->checkAdmin();
        set_time_limit(0);
        ini_set('max_execution_time', '0');

        $confirmation = trim($request->input('confirm_text', ''));
        if ($confirmation !== 'UBAH TOKEN') {
            return back()->with('error', 'Konfirmasi dibatalkan. Anda harus mengetik kata "UBAH TOKEN" dengan tepat untuk mengubah token.');
        }

        $students = User::where('role', 'student')->select('id', 'nis', 'password_row')->get();
        if ($students->isEmpty()) {
            return back()->with('error', 'Tidak ada data siswa untuk diperbarui tokennya.');
        }

        DB::transaction(function () use ($students) {
            // Delete all previous votes so everyone can vote cleanly
            UserVote::query()->delete();

            $updates = [];
            foreach ($students as $student) {
                // Ensure new token is different from old token
                do {
                    $newToken = (string) random_int(100000, 999999);
                } while ($newToken === $student->password_row);

                $newHash = password_hash($newToken, PASSWORD_BCRYPT, ['cost' => 4]);
                $updates[] = [
                    'id'    => (int) $student->id,
                    'hash'  => $newHash,
                    'token' => $newToken,
                ];
            }

            // Batch update using PostgreSQL UPDATE ... FROM (VALUES ...) in chunks of 200
            foreach (array_chunk($updates, 200) as $chunk) {
                $values = [];
                $bindings = [];
                foreach ($chunk as $item) {
                    $values[] = "(?, ?, ?)";
                    $bindings[] = $item['id'];
                    $bindings[] = $item['hash'];
                    $bindings[] = $item['token'];
                }
                $valuesStr = implode(', ', $values);
                DB::statement("
                    UPDATE users AS u
                    SET password = c.new_hash,
                        password_row = c.new_token,
                        voted_at = NULL,
                        updated_at = NOW()
                    FROM (VALUES {$valuesStr}) AS c(id, new_hash, new_token)
                    WHERE u.id = c.id::bigint
                ", $bindings);
            }
        });

        return back()->with('success', 'Berhasil membuat token baru untuk ' . $students->count() . ' siswa! Seluruh suara lama telah di-reset untuk sesi pemilihan baru.');
    }

    public function exportCsv()
    {
        $this->checkAdmin();

        $students = User::where('role', 'student')
            ->orderBy('kelas', 'asc')
            ->orderBy('nis', 'asc')
            ->get();

        $months = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
            5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
            9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
        ];
        $now = now();
        $day = $now->format('d');
        $monthName = $months[(int)$now->format('n')];
        $year = $now->format('Y');
        $time = $now->format('H.i');

        $fileName = "TOKEN PEMILIHAN SMPN 22 Malang - {$day} {$monthName} {$year}, {$time}.csv";

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
            fputcsv($file, ['NO', 'NIS', 'NAMA', 'KELAS', 'TOKEN'], ';');

            $i = 1;
            foreach ($students as $student) {
                fputcsv($file, [
                    $i++,
                    $student->nis,
                    $student->name,
                    $student->kelas ?? '-',
                    $student->password_row,
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
