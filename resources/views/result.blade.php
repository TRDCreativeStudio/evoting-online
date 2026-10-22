@extends('layout')

@section('title', 'Result')
@section('content')
<div class="">
    <div class="bg-gray-100 h-screen">
            <div class="flex items-center justify-center h-screen py-4">
                <div class="bg-white shadow-none rounded-lg p-2 w-[1000px]">
                    <div class="flex flex-col gap-y-2">
                        <div class="flex justify-between pt-3 pb-4 px-2 items-end">
                            <img src="img/logo_smpn22.png" alt="" class="h-[20px]">

                            <h3 class="text-sm font-medium text-center">
                            Hasil Pemilihan Ketua OSIS 2024/2025
                            </h3>

                            <div class="flex items-center gap-3">
                                <a href="{{ route('admin.students') }}" class="px-3 py-1.5 bg-blue-600 hover:bg-blue-700 text-white rounded-lg text-xs font-medium transition flex items-center gap-1 shadow-sm">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                                    Kelola Siswa & Token
                                </a>
                                <a href="{{route('logout')}}" class="text-gray-400 text-sm font-light hover:text-red-500">Keluar</a>
                            </div>
                        </div>

                        <div class="flex flex-row gap-x-2">
                            <div class="flex flex-col border border-gray-200 rounded-lg p-3 w-full">
                                <h4 class="text-sm">
                                Progress Pemilihan
                                </h4>
                                <p class="text-right text-xl"> {{ number_format($overallPercentage, 2) }}% </p>
                            </div>
                            <div class="flex flex-col border border-gray-200 rounded-lg p-3 w-full">
                                <h4 class="text-sm">
                                Jumlah Telah Memilih
                                </h4>
                                <p class="text-right text-xl">{{ $alreadyVote }}/ {{ $userCount }} </p>
                            </div>
                            <div class="flex flex-col border border-gray-200 rounded-lg p-3 w-full">
                                <h4 class="text-sm">
                                Waktu Terakhir Diperbarui 
                                </h4>

                                @php
                                    // Get the current time using Carbon

                                    $currentTime = \Carbon\Carbon::now()->format('d/m/Y, H:i:s');

                                @endphp
                                <p class="text-right text-xl">{{ $currentTime }}</p>
                            </div>
                            
                        </div>

                        <div class="border border-gray-200 rounded-lg">
                            
                            <!-- Bar Chart -->
                            <div class="pt-6 px-2 pb-0">
                                <div id="bar-chart">
                                </div>
                            </div>
                        
                        </div>                   
                    </div>

                    <!-- <div class="relative mx-4 mt-4 flex flex-col gap-4 overflow-hidden rounded-none bg-transparent bg-clip-border text-gray-700 shadow-none md:flex-row md:items-center">
                        <img src="img/logo_smpn22.png" alt="" class="w-1/5 mb-5">
                        <p class="text-gray-600 text-sm font-base">Selamat Datang di</p>
                        <h2 class="text-xl font-bold mb-4">Pemilihan Ketua OSIS Masa Bhakti 2024/2025</h2>
                        <div class="w-max rounded-lg bg-gray-900 p-5 text-white">
                            <svg
                                xmlns="http://www.w3.org/2000/svg"
                                fill="none"
                                viewBox="0 0 24 24"
                                stroke-width="1.5"
                                stroke="currentColor"
                                aria-hidden="true"
                                class="h-6 w-6"
                            >
                                <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                d="M6.429 9.75L2.25 12l4.179 2.25m0-4.5l5.571 3 5.571-3m-11.142 0L2.25 7.5 12 2.25l9.75 5.25-4.179 2.25m0 0L21.75 12l-4.179 2.25m0 0l4.179 2.25L12 21.75 2.25 16.5l4.179-2.25m11.142 0l-5.571 3-5.571-3"
                                ></path>
                            </svg>
                            </div>
                            <div>
                            <h6 class="block font-sans text-base font-semibold leading-relaxed tracking-normal text-blue-gray-900 antialiased">
                                Line Chart
                            </h6>
                            <p class="block max-w-sm font-sans text-sm font-normal leading-normal text-gray-700 antialiased">
                                Visualize your data in a simple way using the
                                @material-tailwind/html chart plugin.
                            </p>
                        </div>
                    </div> -->
                </div>
            </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
    <script>

        var voteData = @json($results);

        // var voteNumbers = voteData.map(function(vote) {
        //     return 'Candidate ' + vote.vote_number; // Map vote_number to candidate names or IDs
        // });

        // var data = voteData.map(function(vote) {
        //     return vote.total;
        // });

        var totalVotes = voteData.map(function(vote) {
            return vote.total; // Map total votes per candidate
        });
        
        const chartConfig = {
        series: [
            {
            name: "Hasil Voting",
            // data: [50, 40, 100],
            data: totalVotes,
            },
        ],
        chart: {
            type: "bar",
            height: 390,
            toolbar: {
            show: false,
            },
        },
        title: {
            show: "",
        },
        dataLabels: {
            enabled: true,
        },
        colors: ["#020617"],
        plotOptions: {
            bar: {
            columnWidth: "60%",
            borderRadius: 2,
            },
        },
        xaxis: {
            axisTicks: {
            show: false,
            },
            axisBorder: {
            show: false,
            },
            labels: {
            style: {
                colors: "#616161",
                fontSize: "12px",
                fontFamily: "inherit",
                fontWeight: 400,
            },
            },
            categories: voteData.map(function(vote) {
                return vote.name; // Nama kandidat diurutkan sesuai total suaranya
            }),
        },
        yaxis: {
            labels: {
            style: {
                colors: "#616161",
                fontSize: "12px",
                fontFamily: "inherit",
                fontWeight: 400,
            },
            },
        },
        grid: {
            show: true,
            borderColor: "#dddddd",
            strokeDashArray: 5,
            xaxis: {
            lines: {
                show: true,
            },
            },
            padding: {
            top: 5,
            right: 5,
            },
        },
        fill: {
            opacity: 0.8,
        },
        tooltip: {
            theme: "dark",
        },
        };
        
        const chart = new ApexCharts(document.querySelector("#bar-chart"), chartConfig);
        
        chart.render();
    </script>
</div>
@endsection