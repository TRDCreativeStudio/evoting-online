@extends('layout')

@section('title', 'Kelola Siswa & Token Pemilih')
@section('content')
<div class="bg-gray-100 min-h-screen py-6 px-4">
    <div class="max-w-6xl mx-auto space-y-6">

        <!-- Top Navigation Bar -->
        <div class="bg-white rounded-xl shadow-sm p-4 flex flex-col md:flex-row justify-between items-center gap-4">
            <div class="flex items-center gap-3">
                <img src="/img/logo_smpn22.png" alt="Logo" class="h-8">
                <div>
                    <h2 class="text-lg font-bold text-gray-800">Panel Manajemen Pemilih & Token</h2>
                    <p class="text-xs text-gray-500">Pemilihan Ketua OSIS SMPN 22 Malang</p>
                </div>
            </div>
            <div class="flex items-center gap-2">
                <a href="{{ route('result') }}" class="px-4 py-2 bg-blue-50 text-blue-700 hover:bg-blue-100 rounded-lg text-sm font-medium transition flex items-center gap-1.5">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                    Lihat Hasil Pemilihan
                </a>
                <a href="{{ route('logout') }}" class="px-3 py-2 text-gray-500 hover:text-red-600 text-sm font-medium transition">
                    Keluar
                </a>
            </div>
        </div>

        @if (session('success'))
            <div class="bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-lg text-sm flex items-center gap-2">
                <svg class="w-5 h-5 text-green-600 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                <span>{{ session('success') }}</span>
            </div>
        @endif

        @if (session('error'))
            <div class="bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-lg text-sm flex items-center gap-2">
                <svg class="w-5 h-5 text-red-600 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
                <span>{{ session('error') }}</span>
            </div>
        @endif

        <!-- Quick Statistics -->
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            <div class="bg-white p-4 rounded-xl shadow-sm border border-gray-100">
                <p class="text-xs text-gray-500 font-medium uppercase">Total Pemilih</p>
                <p class="text-2xl font-bold text-gray-800 mt-1">{{ $studentCount }} <span class="text-xs font-normal text-gray-500">siswa</span></p>
            </div>
            <div class="bg-white p-4 rounded-xl shadow-sm border border-gray-100">
                <p class="text-xs text-gray-500 font-medium uppercase">Sudah Memilih</p>
                <p class="text-2xl font-bold text-emerald-600 mt-1">{{ $votedCount }}</p>
            </div>
            <div class="bg-white p-4 rounded-xl shadow-sm border border-gray-100">
                <p class="text-xs text-gray-500 font-medium uppercase">Belum Memilih</p>
                <p class="text-2xl font-bold text-amber-600 mt-1">{{ max(0, $studentCount - $votedCount) }}</p>
            </div>
            <div class="bg-white p-4 rounded-xl shadow-sm border border-gray-100">
                <p class="text-xs text-gray-500 font-medium uppercase">Partisipasi</p>
                <p class="text-2xl font-bold text-indigo-600 mt-1">{{ $studentCount > 0 ? number_format(($votedCount / $studentCount) * 100, 1) : 0 }}%</p>
            </div>
        </div>

        <!-- Action Cards Grid -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

            <!-- Card 1: Upload CSV -->
            <div class="bg-white p-5 rounded-xl shadow-sm border border-gray-100 flex flex-col justify-between">
                <div>
                    <div class="flex items-center gap-2 mb-3">
                        <span class="p-2 bg-blue-50 text-blue-600 rounded-lg">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/></svg>
                        </span>
                        <h3 class="font-bold text-gray-800">Upload Data Siswa (CSV)</h3>
                    </div>
                    <p class="text-xs text-gray-500 mb-4">
                        Format CSV: kolom <strong>nis</strong>, <strong>nama</strong>, dan <strong>kelas</strong> (opsional: jika kelas/password kosong, tidak apa-apa). Sistem otomatis membuat PIN 6-digit jika password kosong.
                    </p>

                    <form id="uploadCsvForm" action="{{ route('admin.students.import') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                        @csrf
                        <div>
                            <input type="file" id="csvFileInput" name="csv_file" accept=".csv,text/csv" required
                                class="block w-full text-xs text-gray-500 file:mr-3 file:py-2 file:px-3 file:rounded-md file:border-0 file:text-xs file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 cursor-pointer">
                        </div>

                        <label class="flex items-start gap-2 cursor-pointer text-xs text-gray-600">
                            <input type="checkbox" name="replace_all" value="1" class="mt-0.5 rounded text-blue-600 focus:ring-blue-500">
                            <span><strong>Ganti seluruh data lama</strong> (Kosongkan siswa & suara lama sebelum import untuk occasion baru/gladi bersih)</span>
                        </label>

                        <button type="submit" id="btnSubmitCsv" class="w-full py-2.5 px-4 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg text-sm shadow-sm transition flex items-center justify-center gap-2">
                            <span>Unggah & Proses Siswa</span>
                        </button>
                    </form>
                </div>
            </div>

            <!-- Card 2: Kontrol Pemilihan & Export -->
            <div class="bg-white p-5 rounded-xl shadow-sm border border-gray-100 flex flex-col justify-between">
                <div>
                    <div class="flex items-center gap-2 mb-3">
                        <span class="p-2 bg-emerald-50 text-emerald-600 rounded-lg">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                        </span>
                        <h3 class="font-bold text-gray-800">Aksi & Rekap Panitia</h3>
                    </div>
                    <p class="text-xs text-gray-500 mb-4">
                        Download rekap seluruh token siswa untuk dicetak panitia, buat token baru untuk gladi bersih/hari H, atau reset suara pemilih.
                    </p>

                    <div class="space-y-2.5">
                        <a href="{{ route('admin.students.export') }}" class="w-full py-2.5 px-4 bg-emerald-600 hover:bg-emerald-700 text-white font-medium rounded-lg text-sm shadow-sm transition flex items-center justify-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                            Download Rekap Token (CSV)
                        </a>

                        <form id="regenerateTokensForm" action="{{ route('admin.students.regenerate_tokens') }}" method="POST">
                            @csrf
                            <input type="hidden" name="confirm_text" id="regenerateTokensInput">
                            <button type="button" onclick="confirmRegenerateTokens()" class="w-full py-2.5 px-4 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg text-sm shadow-sm transition flex items-center justify-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/></svg>
                                Generate Token Baru (Gladi Bersih / Hari H)
                            </button>
                        </form>

                        <form id="resetVotesForm" action="{{ route('admin.students.reset_votes') }}" method="POST">
                            @csrf
                            <button type="button" onclick="confirmResetVotes()" class="w-full py-2.5 px-4 bg-amber-500 hover:bg-amber-600 text-white font-medium rounded-lg text-sm shadow-sm transition flex items-center justify-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                                Reset Suara (Ulangi Gladi Bersih)
                            </button>
                        </form>

                        <form id="deleteAllForm" action="{{ route('admin.students.delete_all') }}" method="POST">
                            @csrf
                            <button type="button" onclick="confirmDeleteAll()" class="w-full py-2.5 px-4 bg-red-50 hover:bg-red-100 text-red-600 font-medium rounded-lg text-xs transition">
                                🗑️ Hapus Semua Siswa & Suara
                            </button>
                        </form>
                    </div>
                </div>
            </div>

        </div>

        <!-- Daftar Siswa Table -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="p-4 border-b border-gray-100 flex flex-col md:flex-row justify-between items-center gap-3">
                <h3 class="font-bold text-gray-800 text-base">Daftar Pemilih Terdaftar</h3>

                <form method="GET" action="{{ route('admin.students') }}" class="flex items-center gap-2">
                    <input type="text" name="search" value="{{ $search }}" placeholder="Cari NIS, Nama, atau Kelas..."
                        class="px-3 py-1.5 text-xs border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 w-64">
                    <button type="submit" class="px-3 py-1.5 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg text-xs font-medium">Cari</button>
                    @if($search)
                        <a href="{{ route('admin.students') }}" class="text-xs text-gray-400 hover:text-gray-600">Reset</a>
                    @endif
                </form>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs text-gray-600">
                    <thead class="bg-gray-50 text-gray-700 uppercase font-semibold border-b border-gray-100">
                        <tr>
                            <th class="py-3 px-4">NIS</th>
                            <th class="py-3 px-4">Nama Siswa</th>
                            <th class="py-3 px-4">Kelas</th>
                            <th class="py-3 px-4">Token Login</th>
                            <th class="py-3 px-4">Status Pemilihan</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse ($students as $student)
                            <tr class="hover:bg-gray-50 transition">
                                <td class="py-2.5 px-4 font-mono font-medium text-gray-900">{{ $student->nis }}</td>
                                <td class="py-2.5 px-4 font-medium text-gray-800">{{ $student->name }}</td>
                                <td class="py-2.5 px-4 font-medium text-gray-700">{{ $student->kelas ?? '-' }}</td>
                                <td class="py-2.5 px-4">
                                    <span class="font-mono bg-gray-100 px-2 py-0.5 rounded text-gray-800 font-semibold">{{ $student->password_row }}</span>
                                </td>
                                <td class="py-2.5 px-4">
                                    @if ($student->password === null)
                                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-medium bg-emerald-100 text-emerald-800">
                                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                            Sudah Memilih
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-600">
                                            <span class="w-1.5 h-1.5 rounded-full bg-gray-400"></span>
                                            Belum Memilih
                                        </span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="py-8 text-center text-gray-400">
                                    Belum ada data siswa. Silakan upload file CSV di atas.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($students->hasPages())
                <div class="p-4 border-t border-gray-100">
                    {{ $students->links() }}
                </div>
            @endif
        </div>

    </div>
</div>

<script>
    // CSV Upload Loader
    const uploadForm = document.getElementById('uploadCsvForm');
    if (uploadForm) {
        uploadForm.addEventListener('submit', function(e) {
            const fileInput = document.getElementById('csvFileInput');
            if (!fileInput.files || fileInput.files.length === 0) {
                return;
            }

            const btn = document.getElementById('btnSubmitCsv');
            btn.disabled = true;
            btn.classList.add('opacity-75', 'cursor-not-allowed');
            btn.innerHTML = `
                <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white inline-block" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                <span>Sedang Memproses CSV...</span>
            `;

            Swal.fire({
                title: 'Sedang Mengunggah & Memproses Siswa',
                html: `
                    <div class="space-y-3 py-2 text-left text-sm text-gray-600">
                        <div class="flex items-center gap-2">
                            <span class="relative flex h-3 w-3">
                                <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-blue-400 opacity-75"></span>
                                <span class="relative inline-flex rounded-full h-3 w-3 bg-blue-500"></span>
                            </span>
                            <span>Membaca data file CSV...</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <span class="relative flex h-3 w-3">
                                <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-blue-400 opacity-75"></span>
                                <span class="relative inline-flex rounded-full h-3 w-3 bg-blue-500"></span>
                            </span>
                            <span>Menghitung enkripsi token login & batch sinkronisasi...</span>
                        </div>
                        <p class="text-xs text-gray-400 pt-2 border-t border-gray-100">
                            Proses ini membutuhkan waktu beberapa detik tergantung jumlah siswa. Mohon tidak me-refresh atau menutup halaman.
                        </p>
                    </div>
                `,
                allowOutsideClick: false,
                allowEscapeKey: false,
                showConfirmButton: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });
        });
    }

    // Regenerate Tokens Confirmation & Loader
    function confirmRegenerateTokens() {
        Swal.fire({
            title: 'Generate Token Baru?',
            html: `
                <div class="text-left text-xs text-gray-600 space-y-2 mt-2">
                    <div class="p-3 bg-amber-50 border border-amber-200 rounded-lg text-amber-900">
                        <p class="font-bold flex items-center gap-1.5 mb-1.5 text-xs">
                            <svg class="w-4 h-4 text-amber-600 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
                            INFORMASI PENTING PERGANTIAN TOKEN:
                        </p>
                        <ul class="list-disc list-inside space-y-1 text-[11px] leading-relaxed">
                            <li>Setiap siswa akan mendapatkan <strong>PIN/Token 6-digit baru yang acak</strong> (berbeda dari sebelumnya).</li>
                            <li>Token lama otomatis hangus dan tidak dapat digunakan lagi.</li>
                            <li>Seluruh suara pemilihan yang sudah masuk akan <strong>di-reset (0)</strong> agar siswa bisa memilih kembali (ideal untuk simulasi/gladi bersih & hari H).</li>
                        </ul>
                    </div>
                    <p class="pt-2 text-gray-700 font-medium">Ketik <span class="text-blue-700 font-mono font-bold bg-blue-50 px-1 py-0.5 rounded border border-blue-200">UBAH TOKEN</span> di bawah untuk konfirmasi:</p>
                </div>
            `,
            input: 'text',
            inputPlaceholder: 'Ketik UBAH TOKEN di sini...',
            showCancelButton: true,
            confirmButtonColor: '#2563eb',
            cancelButtonColor: '#6b7280',
            confirmButtonText: 'Generate Token Sekarang',
            cancelButtonText: 'Batal',
            preConfirm: (inputValue) => {
                if (!inputValue || inputValue.trim() !== 'UBAH TOKEN') {
                    Swal.showValidationMessage('Teks konfirmasi salah! Anda harus mengetik persis "UBAH TOKEN".');
                    return false;
                }
                return inputValue.trim();
            }
        }).then((result) => {
            if (result.isConfirmed) {
                Swal.fire({
                    title: 'Sedang Membuat Token Baru...',
                    html: `
                        <div class="py-2 text-sm text-gray-600">
                            Men-generate token acak baru dan meng-update kredensial seluruh siswa... Mohon tunggu sebentar.
                        </div>
                    `,
                    allowOutsideClick: false,
                    allowEscapeKey: false,
                    showConfirmButton: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });
                document.getElementById('regenerateTokensInput').value = result.value;
                document.getElementById('regenerateTokensForm').submit();
            }
        });
    }

    // Reset Votes Confirmation & Loader
    function confirmResetVotes() {
        Swal.fire({
            title: 'Reset Semua Suara?',
            text: 'Seluruh perolehan suara akan dikosongkan (0) dan semua token login siswa akan diaktifkan kembali untuk gladi bersih berikutnya.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#f59e0b',
            cancelButtonColor: '#6b7280',
            confirmButtonText: 'Ya, Reset Suara',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                Swal.fire({
                    title: 'Mereset Suara & Token...',
                    text: 'Mohon tunggu sebentar...',
                    allowOutsideClick: false,
                    showConfirmButton: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });
                document.getElementById('resetVotesForm').submit();
            }
        });
    }

    // Delete All Confirmation & Loader
    function confirmDeleteAll() {
        Swal.fire({
            title: 'Hapus Seluruh Data Siswa & Suara?',
            text: 'PERINGATAN: Tindakan ini akan menghapus permanen seluruh data siswa dan suara yang ada. Akun admin tetap dipertahankan.',
            icon: 'error',
            showCancelButton: true,
            confirmButtonColor: '#ef4444',
            cancelButtonColor: '#6b7280',
            confirmButtonText: 'Ya, Hapus Semua',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                Swal.fire({
                    title: 'Menghapus Seluruh Data...',
                    text: 'Mohon tunggu sebentar...',
                    allowOutsideClick: false,
                    showConfirmButton: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });
                document.getElementById('deleteAllForm').submit();
            }
        });
    }
</script>
@endsection
