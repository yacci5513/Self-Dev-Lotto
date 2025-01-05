<x-app-layout>
    <div class="min-h-screen flex items-center justify-center bg-gray-100 py-3 px-4 sm:px-6 lg:px-8">
        <div class="max-w-md w-full space-y-3">
            <div class="flex items-center justify-center">
                <a href="{{ route('dashboard') }}">
                    <div class="mx-auto h-64 w-64 rounded-md bg-transparent p-1 shadow-lg overflow-hidden">
                        <img class="h-full w-full object-cover rounded-md" src="{{ asset('images/logo.png') }}" alt="로또버스">
                    </div>
                </a>
            </div>
            <div>
                <div class="flex flex-col items-center">
                    <div class="flex mb-1">
                        <span class="inline-flex items-center justify-center h-12 w-12 rounded-full bg-gradient-to-r from-red-400 to-red-600 border-2 border-white text-white text-2xl font-bold shadow-lg">L</span>
                        <span class="inline-flex items-center justify-center h-12 w-12 rounded-full bg-gradient-to-r from-blue-400 to-blue-600 border-2 border-white text-white text-2xl font-bold shadow-lg">O</span>
                        <span class="inline-flex items-center justify-center h-12 w-12 rounded-full bg-gradient-to-r from-orange-400 to-orange-600 border-2 border-white text-white text-2xl font-bold shadow-lg">T</span>
                        <span class="inline-flex items-center justify-center h-12 w-12 rounded-full bg-gradient-to-r from-green-400 to-green-600 border-2 border-white text-white text-2xl font-bold shadow-lg">T</span>
                        <span class="inline-flex items-center justify-center h-12 w-12 rounded-full bg-gradient-to-r from-red-400 to-red-600 border-2 border-white text-white text-2xl font-bold shadow-lg">O</span>
                    </div>

                    <div class="flex space-x-1">
                        <span class="inline-flex items-center justify-center h-11 w-11 rounded-lg bg-yellow-500 border-2 border-white text-white text-2xl font-bold shadow-lg">B</span>
                        <span class="inline-flex items-center justify-center h-11 w-11 rounded-lg bg-yellow-500 border-2 border-white text-white text-2xl font-bold shadow-lg">U</span>
                        <span class="inline-flex items-center justify-center h-11 w-11 rounded-lg bg-yellow-500 border-2 border-white text-white text-2xl font-bold shadow-lg">S</span>
                    </div>
                </div>
                <p class="mt-4 text-center text-sm text-gray-600">
                    행운이 가득한 로또 여행을 시작하세요
                </p>
            </div>

            <form class="mt-8 space-y-5" action="{{ route('register.submit') }}" method="POST">
                @csrf
                <div class="rounded-md shadow-sm -space-y-px">
                    <div>
                        <label for="name" class="sr-only">이름</label>
                        <input id="name" name="name" type="text" required 
                            class="appearance-none rounded-t-md relative block w-full px-3 py-3 border border-gray-300 placeholder-gray-500 text-gray-900 focus:outline-none focus:ring-purple-500 focus:border-purple-500 focus:z-10 sm:text-sm"
                            placeholder="이름">
                    </div>
                    <div>
                        <label for="email" class="sr-only">이메일</label>
                        <div class="flex">
                            <input id="email" 
                                name="email" 
                                type="email" 
                                value="{{ old('email') }}"
                                required 
                                class="appearance-none rounded-none relative block w-full px-3 py-3 border border-gray-300 placeholder-gray-500 text-gray-900 focus:outline-none focus:ring-purple-500 focus:border-purple-500 focus:z-10 sm:text-sm"
                                placeholder="이메일">
                            <button type="button" 
                                onclick="checkEmail(document.getElementById('email').value)"
                                class="ml-1 px-3 h-10 border border-transparent text-xs font-small rounded-md text-white bg-purple-600 hover:bg-purple-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500 self-center flex flex-col items-center justify-center space-y-0.5 whitespace-nowrap">
                                <span>중복</span>
                                <span>확인</span>
                            </button>
                        </div>
                        <span id="email-status" class="text-sm mt-1"></span>
                    </div>
                    <div>
                        <label for="password" class="sr-only">비밀번호</label>
                        <input id="password" name="password" type="password" required 
                            class="appearance-none relative block w-full px-3 py-3 border border-gray-300 placeholder-gray-500 text-gray-900 focus:outline-none focus:ring-purple-500 focus:border-purple-500 focus:z-10 sm:text-sm"
                            placeholder="비밀번호">
                    </div>
                    <div>
                        <label for="password_confirmation" class="sr-only">비밀번호 확인</label>
                        <input id="password_confirmation" name="password_confirmation" type="password" required 
                            class="appearance-none rounded-b-md relative block w-full px-3 py-3 border border-gray-300 placeholder-gray-500 text-gray-900 focus:outline-none focus:ring-purple-500 focus:border-purple-500 focus:z-10 sm:text-sm"
                            placeholder="비밀번호 확인">
                    </div>
                </div>

                @if ($errors->any())
                    <div class="mt-2">
                        @foreach ($errors->all() as $error)
                            <p class="text-red-500 text-xs mt-1">{{ $error }}</p>
                        @endforeach
                    </div>
                @endif

                <div>
                    <button type="submit" 
                        class="group relative w-full flex justify-center py-3 px-4 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition duration-150 ease-in-out">
                        <span class="absolute left-0 inset-y-0 flex items-center pl-3">
                            <svg class="h-5 w-5 text-blue-100 group-hover:text-blue-200" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                <path d="M8 9a3 3 0 100-6 3 3 0 000 6zM8 11a6 6 0 016 6H2a6 6 0 016-6zM16 7a1 1 0 10-2 0v1h-1a1 1 0 100 2h1v1a1 1 0 102 0v-1h1a1 1 0 100-2h-1V7z"/>
                            </svg>
                        </span>
                        회원가입
                    </button>
                </div>
            </form>

            <!-- 로그인 페이지로 이동하는 버튼 추가 -->
            <a href="{{ route('login') }}" 
                class="group relative w-full flex justify-center py-3 px-4 border border-transparent text-sm font-medium rounded-md text-white bg-gray-500 hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-400 transition duration-150 ease-in-out">
                <span class="absolute left-0 inset-y-0 flex items-center pl-3">
                    <svg class="h-5 w-5 text-gray-100 group-hover:text-gray-200" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd" />
                    </svg>
                </span>
                로그인하러 가기
            </a>
        </div>
    </div>
</x-app-layout> 

<script>
function checkEmail(email) {
    if (!email) {
        alert('이메일을 입력해주세요.');
        return;
    }

    // 이메일 형식 검사
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    if (!emailRegex.test(email)) {
        const statusElement = document.getElementById('email-status');
        statusElement.className = 'text-sm mt-1 text-red-600';
        statusElement.textContent = '올바른 이메일 형식이 아닙니다.';
        return;
    }

    fetch('{{ route('check.email') }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({ email: email })
    })
    .then(response => response.json())
    .then(data => {
        const statusElement = document.getElementById('email-status');
        if (data.available) {
            statusElement.className = 'text-sm mt-1 text-green-600';
        } else {
            statusElement.className = 'text-sm mt-1 text-red-600';
        }
        statusElement.textContent = data.message;
    })
    .catch(error => {
        const statusElement = document.getElementById('email-status');
        statusElement.className = 'text-sm mt-1 text-red-600';
        statusElement.textContent = '올바른 이메일 형식이 아닙니다.';
    });
}
</script> 