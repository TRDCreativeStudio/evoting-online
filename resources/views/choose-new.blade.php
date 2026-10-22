@extends('layout')

@section('title', 'Kartu Suara Pemilihan')
@section('content')
    <div class="bg-gray-100 h-auto">
        <div class="flex items-center justify-center h-auto py-4">
            <div class="bg-white shadow-none rounded-lg p-2 w-[1200px]">
                <div class="flex flex-col">
                    <div class="flex justify-between pt-3 pb-4 px-2 items-end">
                        <img src="img/logo_smpn22.png" alt="" class="h-[20px]">

                        <h3 class="text-sm font-medium text-center">
                        Surat Suara Pemilihan Ketua OSIS 2024/2025
                        </h3>

                        <h3 class="text-sm font-medium text-center">
                        Hi, {{ Auth::user()->name }}!
                        </h3>
                    </div>

                    <div class="flex flex-row gap-x-2">

                        <!-- Card 1 -->
                        <div class="flex flex-col border border-gray-200 rounded-lg basis-1/3">
                            <div class="flex flex-row justify-between p-2 text-center">
                                <h4 class="text-4xl font-bold">01</h4>
                                <div class="">
                                     <button type="submit" class="flex w-full justify-center text-white rounded-md bg-blue-800 px-4 py-2 text-sm font-medium hover:bg-blue-500" onclick="confirmFollow1()">Pilih Nayla (No.Urut 1)</button>
                                </div>
                            </div> 

                            <!-- img -->
                            <div class="h-72"> <!-- Set a fixed height for the image container -->
                                <img class="w-full h-full object-cover" src="img/nayla.png" alt="profile-picture" />
                            </div>   

                            <!-- Content -->
                            <div class="flex flex-col p-4 rounded-lg flex-grow">
                                <!-- Name -->
                                <h2 class="text-lg font-medium">
                                    Nayla Qur'ainy Regitha Amalia
                                </h2>

                                <!-- Visi -->
                                <div class="flex flex-col gap-y-2">
                                    <h4 class="text-sm font-medium text-gray-300">
                                        VISI
                                    </h4>
                                    <ul class="text-sm list-decimal list-outside text-justify pl-4 mb-2">
                                        <li>Menjadikan Osis yang berprestasi akademik maupun non akademik.. Tapi juga memiliki kinerja yang baik di osis.. mewujudkan pengurus osis yang humble, percaya diri, dan memiliki kemampuan komunikasi yang baik</li>
                                        <li>Menjadikan osis sebagai pengurus yang terpuji dalam budi pekerti dan berbudaya lingkungan</li>
                                        <li>Menjadikan osis sebagai wadah untuk mengembangkan kreativitas, jiwa kepemimpinan dan bakat siswa</li>
                                    </ul>
                                </div>
                                
                                <!-- Misi -->
                                <div class="flex flex-col gap-y-2 flex-grow">
                                    <h4 class="text-sm font-medium text-gray-300">
                                        MISI
                                    </h4>
                                    <ul class="text-sm list-decimal list-outside text-justify pl-4 mb-2">
                                        <li>Meneruskan proker osis terdahulu yang sudah terlaksana maupun yang belum terlaksana</li>
                                        <li>Mempertahan kan gelar adiwiyata mandiri sekolah</li>
                                        <li>Bisa Menjadi Contoh dan teladan bagi seluruh siswa</li>
                                        <li>Menjadikan OSIS sebagai wadah yang menampung segala bakat, potensi, aspirasi, dan keahlian yang dimiliki siswa</li>
                                        <li>Merangkul semua siswa untuk mencapai tujuan bersama</li>
                                    </ul>
                                </div>
                            </div>
                        </div>

                        <!-- Card 2 -->
                        <div class="flex flex-col border border-gray-200 rounded-lg basis-1/3">
                            <div class="flex flex-row justify-between p-2 text-center">
                                <h4 class="text-4xl font-bold">02</h4>
                                <div class="">
                                     <button type="submit" class="flex w-full justify-center text-white rounded-md bg-blue-800 px-4 py-2 text-sm font-medium hover:bg-blue-500" onclick="confirmFollow2()">Pilih Qoirul (No.Urut 2)</button>
                                </div>
                            </div> 

                            <!-- img -->
                            <div class="h-72"> <!-- Set a fixed height for the image container -->
                                <img class="w-full h-full object-cover" src="img/qoirul.png" alt="profile-picture" />
                            </div>   

                            <!-- Content -->
                            <div class="flex flex-col p-4 rounded-lg flex-grow">
                                <!-- Name -->
                                <h2 class="text-lg font-medium">
                                    M. Qoirul Ulum
                                </h2>

                                <!-- Visi -->
                                <div class="flex flex-col gap-y-2">
                                    <h4 class="text-sm font-medium text-gray-300">
                                        VISI
                                    </h4>
                                    <p class="text-sm mb-2">Meningkatkan mutu OSIS sebagai organisasi yang memiliki siswa siswi yangg berkualitas disiplin dan sopan di SMPN 22 Malang
                                    </p>
                                </div>
                                
                                <!-- Misi -->
                                <div class="flex flex-col gap-y-2 flex-grow">
                                    <h4 class="text-sm font-medium text-gray-300">
                                        MISI
                                    </h4>
                                    <ul class="text-sm list-decimal list-outside text-justify pl-4 mb-2">
                                        <li>Meningkatkan prestasi siswa baik akademik maupun non akademik melalui bantuan organisasi</li>
                                        <li>Menjadikan OSIS wadah untuk memperbaiki siswa di dalam kedisiplinan maupun berfikir</li>
                                    </ul>
                                </div>
                            </div>
                        </div>

                        <!-- Card 3 -->
                        <div class="flex flex-col border border-gray-200 rounded-lg basis-1/3">
                            <div class="flex flex-row justify-between p-2 text-center">
                                <h4 class="text-4xl font-bold">03</h4>
                                <div class="">
                                     <button type="submit" class="flex w-full justify-center text-white rounded-md bg-blue-800 px-4 py-2 text-sm font-medium hover:bg-blue-500" onclick="confirmFollow3()">Pilih Mikaela (No.Urut 3)</button>
                                </div>
                            </div> 

                            <!-- img -->
                            <div class="h-72"> <!-- Set a fixed height for the image container -->
                                <img class="w-full h-full object-cover" src="img/mikaela.png" alt="profile-picture" />
                            </div>   

                            <!-- Content -->
                            <div class="flex flex-col p-4 rounded-lg flex-grow">

                                <!-- Name -->
                                <h2 class="text-lg font-medium">
                                    Mikaela Angely Wilson
                                </h2>

                                <!-- Visi -->
                                <div class="flex flex-col gap-y-2">
                                    <h4 class="text-sm font-medium text-gray-300">
                                        VISI
                                    </h4>
                                    <p class="text-sm mb-2">Menjadikan sekolah sebagai lingkungan yang harmonis, berbudaya, berjiwa sosial, religius serta berwawasan luas atas teknologi</p>
                                </div>
                                
                                <!-- Misi -->
                                <div class="flex flex-col gap-y-2 flex-grow">
                                    <h4 class="text-sm font-medium text-gray-300">
                                        MISI
                                    </h4>
                                    <ul class="text-sm list-decimal list-outside text-justify pl-4">
                                        <li>Mendorong budaya hidup bersih dan peduli lingkungan dengan kegiatan penghijauan, daur ulang, serta program “Sekolah Hijau.”Serta kegiatan nya dilakukan pada pagi hari dengan iringan lagu times darling dari produk inovasi</li>
                                        <li>Membentuk tim peduli lingkungan yang bertugas menjaga kebersihan dan merawat tanaman di lingkungan sekolah</li>
                                        <li>Mengadakan seminar seputar cara menggunakan aplikasi canva, atau aplikasi edit lainnya</li>
                                        <li>Mempromosikan nilai-nilai moral dan spiritual di beberapa kegiatan</li>
                                        <li>Mendorong kerja sama antara siswa, guru, dan staf sekolah melalui kegiatan gotong royong, bakti sosial, dan proyek kolaboratif</li>
                                        <li>Menggalang kegiatan sosial seperti bakti sosial, donasi untuk panti asuhan, dan kampanye sosial</li>
                                        <li>Menghidupkan kegiatan ekstrakurikuler yang berbasis seni dan budaya</li>
                                        <li>Memperkenalkan nilai-nilai kearifan lokal dalam setiap kegiatan OSIS contoh nya fashion show baju adat</li>
                                    </ul>
                                    <p class="text-sm mb-2">Dengan visi dan misi ini, OSIS dapat menjadi motor penggerak dalam menciptakan sekolah yang tidak hanya maju secara akademis, tetapi juga memiliki lingkungan yang sehat, religius, bekerja sama, peduli sosial, dan kaya akan budaya.</p>
                                </div>
                            </div>

                            
                        </div>
                       
                    </div>                   
                </div>

                <div class="text-end">
                    <a href="{{route('logout')}}" class="text-gray-400 text-sm font-light">Keluar</a>
                </div>
               
            </div>
        </div>

        <script>
            async function submitVote(candidateId) {
                try {
                    const response = await fetch('{{ route('vote') }}', {
                        method: "POST",
                        headers: {
                            "Content-Type": "application/json",
                            "Accept": "application/json",
                            "X-CSRF-TOKEN": "{{ csrf_token() }}"
                        },
                        body: JSON.stringify({
                            "nis": "{{ \Illuminate\Support\Facades\Auth::user()->nis }}",
                            "candidate_id": candidateId,
                            "_token": "{{ csrf_token() }}"
                        })
                    });

                    if (response.ok) {
                        window.location = "{{ route('thanks') }}";
                    } else {
                        const err = await response.json().catch(() => ({}));
                        Swal.fire({
                            icon: "error",
                            title: "Gagal memilih!",
                            text: err.message || "Terjadi kesalahan saat menyimpan suara. Silakan coba lagi."
                        });
                    }
                } catch (e) {
                    Swal.fire({
                        icon: "error",
                        title: "Koneksi Error",
                        text: "Gagal terhubung ke server. Silakan periksa koneksi internet."
                    });
                }
            }

            // Candidate 1
            function confirmFollow1() {
                Swal.fire({
                    title: "Apakah kamu yakin memilih Nayla Qur'ainy Regitha Amalia",
                    text: "sebagai ketua OSIS SMPN 22 Malang Masa Bhakti 2024/2025?",
                    icon: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#3085d6",
                    cancelButtonColor: "#d33",
                    confirmButtonText: "Yakin!"
                }).then((result) => {
                    if (result.isConfirmed) {
                        submitVote(1);
                    }
                });
            }

            // Candidate 2
            function confirmFollow2() {
                Swal.fire({
                    title: "Apakah kamu yakin memilih M. Qoirul Ulum",
                    text: "sebagai ketua OSIS SMPN 22 Malang Masa Bhakti 2024/2025?",
                    icon: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#3085d6",
                    cancelButtonColor: "#d33",
                    confirmButtonText: "Yakin!"
                }).then((result) => {
                    if (result.isConfirmed) {
                        submitVote(2);
                    }
                });
            }

            // Candidate 3
            function confirmFollow3() {
                Swal.fire({
                    title: "Apakah kamu yakin memilih Mikaela Angely Wilson",
                    text: "sebagai ketua OSIS SMPN 22 Malang Masa Bhakti 2024/2025?",
                    icon: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#3085d6",
                    cancelButtonColor: "#d33",
                    confirmButtonText: "Yakin!"
                }).then((result) => {
                    if (result.isConfirmed) {
                        submitVote(3);
                    }
                });
            }
        </script>
    </div>
@endsection