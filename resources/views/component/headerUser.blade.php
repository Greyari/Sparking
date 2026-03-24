<!-- Navbar -->
<nav class="sticky top-0 z-50 shadow-lg bg-blue-900 font-poppins">
    <div class="flex items-center justify-between px-4 py-4 mx-auto max-w-7xl">
        <!--logo dan nama-->
        <div class="flex items-center text-white">
            <img src="{{ asset('images/icon.png') }}" alt="Logo" class="w-12 h-12">
            <h1 class="pt-1 text-2xl font-bold ">PARKING</h1>
        </div>

        <div x-data="{ open: false }" class="relative">
            <button @click="open = !open" @click.outside="open = false"
                class="text-blue-200 transition-colors hover:text-white focus:outline-none">
                <i class="text-4xl fas fa-user-circle"></i>
            </button>

            <!-- Dropdown Menu -->
            <div x-show="open" x-transition:enter="transition ease-out duration-100"
                x-transition:enter-start="transform opacity-0 scale-95"
                x-transition:enter-end="transform opacity-100 scale-100" x-transition:leave="transition ease-in duration-75"
                x-transition:leave-start="transform opacity-100 scale-100"
                x-transition:leave-end="transform opacity-0 scale-95"
                class="absolute right-0 z-50 w-48 py-1 mt-2 bg-white border border-gray-200 rounded-md shadow-lg">
                <a href="{{ route('settings') }}"
                    class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-blue-50 hover:text-blue-600">
                    <i class="mr-2 text-gray-500 fas fa-cog"></i> Setting
                </a>

                <!-- Logout dengan form POST -->
                <form method="POST" action="{{ route('logout') }}" class="w-full">
                    @csrf
                    <button type="submit"
                        class="flex items-center w-full px-4 py-2 text-sm text-left text-gray-700 hover:bg-blue-50 hover:text-blue-600">
                        <i class="mr-2 text-gray-500 fas fa-sign-out-alt"></i> Logout
                    </button>
                </form>
            </div>
        </div>
    </div>
</nav>
