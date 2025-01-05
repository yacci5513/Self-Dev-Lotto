<x-app-layout>
    <div class="min-h-screen flex items-center justify-center bg-gray-100 py-3 px-4 sm:px-6 lg:px-8">
        <div class="max-w-md w-full space-y-3">
            @if (session('success'))
                <div class="rounded-md bg-green-50 p-4">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-green-400" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                            </svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm font-medium text-green-800">
                                {{ session('success') }}
                            </p>
                        </div>
                    </div>
                </div>
            @endif
            
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
            
            <form class="mt-8 space-y-5" action="{{ route('login.submit') }}" method="POST">
                @csrf
                <div class="rounded-md shadow-sm -space-y-px">
                    <div>
                        <label for="email" class="sr-only">이메일</label>
                        <input id="email" 
                            name="email" 
                            type="email" 
                            value="{{ old('email') }}"
                            required 
                            class="appearance-none rounded-t-md relative block w-full px-3 py-3 border border-gray-300 placeholder-gray-500 text-gray-900 focus:outline-none focus:ring-purple-500 focus:border-purple-500 focus:z-10 sm:text-sm"
                            placeholder="이메일">
                    </div>
                    <div>
                        <label for="password" class="sr-only">비밀번호</label>
                        <input id="password" name="password" type="password" required 
                            class="appearance-none rounded-b-md relative block w-full px-3 py-3 border border-gray-300 placeholder-gray-500 text-gray-900 focus:outline-none focus:ring-purple-500 focus:border-purple-500 focus:z-10 sm:text-sm"
                            placeholder="비밀번호">
                    </div>
                </div>

                @error('email')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror

                <div>
                    <button type="submit" 
                        class="group relative w-full flex justify-center py-3 px-4 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition duration-150 ease-in-out">
                        <span class="absolute left-0 inset-y-0 flex items-center pl-3">
                            <svg class="h-5 w-5 text-blue-100 group-hover:text-blue-200" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd" />
                            </svg>
                        </span>
                        로그인
                    </button>
                </div>
            </form>
            
            <a href="{{ route('register') }}" 
                class="group relative w-full flex justify-center py-3 px-4 border border-transparent text-sm font-medium rounded-md text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition duration-150 ease-in-out">
                <span class="absolute left-0 inset-y-0 flex items-center pl-3">
                    <svg class="h-5 w-5 text-gray-100 group-hover:text-green-200" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                        <path d="M8 9a3 3 0 100-6 3 3 0 000 6zM8 11a6 6 0 016 6H2a6 6 0 016-6zM16 7a1 1 0 10-2 0v1h-1a1 1 0 100 2h1v1a1 1 0 102 0v-1h1a1 1 0 100-2h-1V7z"/>
                    </svg>
                </span>
                사용자 계정 생성
            </a>
        </div>
    </div>
</x-app-layout>