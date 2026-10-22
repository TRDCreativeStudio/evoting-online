@extends('layout')

@section('title', 'Terima kasih!')
@section('content')
<div class="bg-gray-100 h-screen">
    <div class="flex flex-col gap-y-4 items-center justify-center h-screen">
        <dotlottie-player src="https://lottie.host/f3fc042c-bedb-458b-b791-4a19ea63ef2c/hP06Yj7LhO.json" background="transparent" speed="1" style="width: 400px; height: 400px" direction="1" playMode="normal" loop autoplay></dotlottie-player>

        <h2 class="text-center text-2xl font-medium">
        Terima kasih, anda telah berpartisipasi dalam <br>
        pemilihan Ketua OSIS 2026/2027
        </h2>

        <a href="{{route('logout')}}" class="border border-gray-200 py-2 px-4 rounded-full text-gray-300 hover:text-gray-400">Kembali ke homepage</a>

        <p class="text-sm text-gray-500">Otomatis kembali dalam <span id="countdown">10</span> detik</p>
    </div>

    <script>
        let cd = document.querySelector("#countdown");
        let interval = setInterval(() => {
            let count = cd.innerText;
            cd.innerText = --count;

            if(count == 0) {
                clearInterval(interval);
                window.location.href = "{{ route('index') }}";
            }
        }, 1000);

    </script>
</div>
@endsection