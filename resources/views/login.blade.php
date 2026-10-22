@extends('layout')

@section('title', 'Login')
@section('content')

    <div class="bg-gradient-to-r from-blue-800 to-indigo-900 h-screen">
        <div class="flex flex-col items-center justify-center h-screen">
            <div class="flex items-center justify-center">
                <div class="bg-white shadow-none rounded-lg p-8 w-96">
                    <img src="img/logo_smpn22.png" alt="" class="w-1/2 mb-5">
                    <p class="text-gray-600 text-sm font-base">Selamat Datang di</p>
                    <h2 class="text-xl font-bold mb-4">Pemilihan Ketua OSIS Masa Bhakti 2026/2027</h2>

                    <form class="space-y-6" action="{{route('login')}}" method="POST">
                    @csrf

                        @if (session('status'))
                            <script>
                                    Swal.fire({
                                    icon: "error",
                                    title: "Oops...",
                                    text: "{{ session('status') }}",
                                    footer: '<a href="#">Why do I have this issue?</a>'
                                });
                            </script> 
                        @endif

                        <div class="space-y-2">
                            <div>
                                <label for="nis" class="block mb-2 text-sm font-base text-gray-900">Nomor Induk Siswa</label>
                                <input type="number" name="nis" id="nis" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full py-2.5 px-4" placeholder="Masukkan NIS" required />
                            </div>

                            <style>
                                /* Untuk Chrome, Safari, Edge, dan Opera */
                                input[type=number]::-webkit-outer-spin-button,
                                input[type=number]::-webkit-inner-spin-button {
                                    -webkit-appearance: none;
                                    margin: 0;
                                }

                                /* Untuk Firefox */
                                input[type=number] {
                                    -moz-appearance: textfield;
                                }
                            </style>

                            <div>

                                <label for="token" class="block mb-2 text-sm font-base text-gray-900">Token Pemilihan</label>
                                <input type="text" name="token" id="token" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full py-2.5 px-4" placeholder="Masukkan Token" required />
                            </div>
                        </div>

                        <button type="submit" class="flex w-full justify-center text-white mt-4 rounded-md bg-blue-800 px-4 py-2 text-sm font-medium hover:bg-blue-500">Masuk</button>

                    </form>
                </div>
            </div>
        </div>
        
    </div>

@endsection