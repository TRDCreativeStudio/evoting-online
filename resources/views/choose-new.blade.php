@extends('layout')

@section('title', 'Kartu Suara Pemilihan')
@section('content')
    <div class="bg-gray-100 min-h-screen p-3 md:p-4 flex items-center justify-center box-border">
        <div class="bg-white shadow-none rounded-lg p-2 w-[1200px] min-h-[calc(100vh-2rem)] flex flex-col justify-between">
            <div class="flex flex-col flex-1 justify-between min-h-0">
                <div class="flex justify-between pt-3 pb-4 px-2 items-end">
                    <img src="img/logo_smpn22.png" alt="" class="h-[20px]">

                    <h3 class="text-sm font-medium text-center">
                    Surat Suara Pemilihan Ketua OSIS 2026/2027
                    </h3>

                    <h3 class="text-sm font-medium text-center">
                    Hi, {{ Auth::user()->name }}!
                    </h3>
                </div>

                <div class="flex flex-row gap-x-2 flex-1 my-2 min-h-0">

                    <!-- Card 1 -->
                    <div class="flex flex-col border border-gray-200 rounded-lg basis-1/3 justify-between">
                        <div class="flex flex-row justify-between p-2 text-center">
                            <h4 class="text-4xl font-bold">01</h4>
                            <div class="">
                                 <button type="submit" class="flex w-full justify-center text-white rounded-md bg-blue-800 px-4 py-2 text-sm font-medium hover:bg-blue-500" onclick="confirmFollow1()">Pilih Almira (No.Urut 1)</button>
                            </div>
                        </div> 

                        <!-- img -->
                        <div class="flex-1 overflow-hidden min-h-0">
                            <img class="w-full h-full object-cover" src="img/almira-1.jpg" alt="profile-picture" />
                        </div>   

                        <!-- Content -->
                        <div class="flex flex-col p-4 rounded-lg">
                            <!-- Name -->
                            <h2 class="text-lg font-medium">
                                Almira Quenna Febri Cahyani
                            </h2>
                        </div>
                    </div>

                    <!-- Card 2 -->
                    <div class="flex flex-col border border-gray-200 rounded-lg basis-1/3 justify-between">
                        <div class="flex flex-row justify-between p-2 text-center">
                            <h4 class="text-4xl font-bold">02</h4>
                            <div class="">
                                 <button type="submit" class="flex w-full justify-center text-white rounded-md bg-blue-800 px-4 py-2 text-sm font-medium hover:bg-blue-500" onclick="confirmFollow2()">Pilih Afifah (No.Urut 2)</button>
                            </div>
                        </div> 

                        <!-- img -->
                        <div class="flex-1 overflow-hidden min-h-0">
                            <img class="w-full h-full object-cover" src="img/afifah-2.jpg" alt="profile-picture" />
                        </div>   

                        <!-- Content -->
                        <div class="flex flex-col p-4 rounded-lg">
                            <!-- Name -->
                            <h2 class="text-lg font-medium">
                                Afifah Ignacea Putri
                            </h2>
                        </div>
                    </div>

                    <!-- Card 3 -->
                    <div class="flex flex-col border border-gray-200 rounded-lg basis-1/3 justify-between">
                        <div class="flex flex-row justify-between p-2 text-center">
                            <h4 class="text-4xl font-bold">03</h4>
                            <div class="">
                                 <button type="submit" class="flex w-full justify-center text-white rounded-md bg-blue-800 px-4 py-2 text-sm font-medium hover:bg-blue-500" onclick="confirmFollow3()">Pilih Talita (No.Urut 3)</button>
                            </div>
                        </div> 

                        <!-- img -->
                        <div class="flex-1 overflow-hidden min-h-0">
                            <img class="w-full h-full object-cover" src="img/talita-3.jpg" alt="profile-picture" />
                        </div>   

                        <!-- Content -->
                        <div class="flex flex-col p-4 rounded-lg">
                            <!-- Name -->
                            <h2 class="text-lg font-medium">
                                Talita Naila Zahra
                            </h2>
                        </div>
                    </div>
                   
                </div>                   
            </div>

            <div class="text-end pt-1">
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
                    title: "Apakah kamu yakin memilih Almira Quenna Febri Cahyani",
                    text: "sebagai ketua OSIS SMPN 22 Malang Masa Bhakti 2026/2027?",
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
                    title: "Apakah kamu yakin memilih Afifah Ignacea Putri",
                    text: "sebagai ketua OSIS SMPN 22 Malang Masa Bhakti 2026/2027?",
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
                    title: "Apakah kamu yakin memilih Talita Naila Zahra",
                    text: "sebagai ketua OSIS SMPN 22 Malang Masa Bhakti 2026/2027?",
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
@endsection