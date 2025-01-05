<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>로또 번호 생성기</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body>
    <div class="min-h-screen bg-gray-100">
        @if (!Request::is('login') && !Request::is('register'))
        <div class="bg-white shadow">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex items-center py-4">
                    <!-- 로고 이미지 (왼쪽) -->

                    <a href="{{ route('dashboard') }}">
                        <div class="h-16 w-16 sm:h-20 sm:w-20 overflow-hidden">
                            <img class="h-full w-full object-cover rounded-2xl shadow-lg" src="{{ asset('images/logo.png') }}" alt="로또버스">
                        </div>
                    </a>
                    <!-- LOTTO BUS 텍스트 (중앙) -->
                    <div class="flex-1 flex justify-center">
                        <div class="flex flex-col items-center">
                            <div class="flex mb-2">
                                <span class="inline-flex items-center justify-center h-8 w-8 sm:h-10 sm:w-10 rounded-full bg-gradient-to-r from-red-400 to-red-600 border-2 border-white text-white text-base sm:text-xl font-bold shadow-lg">L</span>
                                <span class="inline-flex items-center justify-center h-8 w-8 sm:h-10 sm:w-10 rounded-full bg-gradient-to-r from-blue-400 to-blue-600 border-2 border-white text-white text-base sm:text-xl font-bold shadow-lg">O</span>
                                <span class="inline-flex items-center justify-center h-8 w-8 sm:h-10 sm:w-10 rounded-full bg-gradient-to-r from-orange-400 to-orange-600 border-2 border-white text-white text-base sm:text-xl font-bold shadow-lg">T</span>
                                <span class="inline-flex items-center justify-center h-8 w-8 sm:h-10 sm:w-10 rounded-full bg-gradient-to-r from-green-400 to-green-600 border-2 border-white text-white text-base sm:text-xl font-bold shadow-lg">T</span>
                                <span class="inline-flex items-center justify-center h-8 w-8 sm:h-10 sm:w-10 rounded-full bg-gradient-to-r from-red-400 to-red-600 border-2 border-white text-white text-base sm:text-xl font-bold shadow-lg">O</span>
                            </div>
                            <div class="flex justify-center space-x-1">
                                <span class="inline-flex items-center justify-center h-7 w-7 sm:h-9 sm:w-9 rounded-lg bg-yellow-500 border-2 border-white text-white text-base sm:text-xl font-bold shadow-lg">B</span>
                                <span class="inline-flex items-center justify-center h-7 w-7 sm:h-9 sm:w-9 rounded-lg bg-yellow-500 border-2 border-white text-white text-base sm:text-xl font-bold shadow-lg">U</span>
                                <span class="inline-flex items-center justify-center h-7 w-7 sm:h-9 sm:w-9 rounded-lg bg-yellow-500 border-2 border-white text-white text-base sm:text-xl font-bold shadow-lg">S</span>
                            </div>
                        </div>
                    </div>

                    <!-- 오른쪽 로그인/회원가입 버튼 -->
                    <div class="flex items-center">
                        @auth
                            <span class="hidden sm:inline text-gray-700 mr-4">{{ Auth::user()->name }}님</span>
                            <form method="POST" action="{{ route('logout') }}" class="inline">
                                @csrf
                                <button type="submit" class="text-sm sm:text-base text-gray-600 hover:text-gray-900 px-3 sm:px-4 py-1.5 sm:py-2 rounded-md bg-gray-100 hover:bg-gray-200">로그아웃</button>
                            </form>
                        @else
                            <a href="{{ route('login') }}" class="text-sm sm:text-base text-gray-600 hover:text-gray-900 px-3 sm:px-4 py-1.5 sm:py-2 rounded-md bg-gray-100 hover:bg-gray-200 mr-2">로그인</a>
                        @endauth
                    </div>
                </div>
            </div>
        </div>
        @endif

        <main>
            {{ $slot }}
        </main>
    </div>
</body>
</html> 