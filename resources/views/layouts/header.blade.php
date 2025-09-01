<!-- Load Preline JS and Tailwind CSS for live preview -->
<script src="https://cdn.tailwindcss.com"></script>
<script src="https://cdn.jsdelivr.net/npm/preline@2.3.0/dist/preline.js"></script>
<script>
  tailwind.config = {
    theme: {
      extend: {
        fontFamily: {
          sans: ['Poppins', 'sans-serif'],
        },
      }
    }
  }
</script>
<!-- Poppins Font from Google Fonts -->
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">

<style>
  body {
    font-family: 'Poppins', sans-serif;
  }
</style>

<!-- ========== HEADER ========== -->
<header class="flex flex-wrap md:justify-start md:flex-nowrap z-50 w-full bg-gradient-to-r from-blue-700 to-indigo-800 text-white border-b border-indigo-500 shadow-2xl">
  <nav class="relative max-w-[85rem] w-full mx-auto px-4 sm:px-6 lg:px-8 py-4 md:flex md:items-center md:justify-between" aria-label="Global">
    <div class="flex items-center justify-between">
      <!-- Logo -->
      <a class="flex-none text-3xl font-bold rounded-lg transition-all duration-300 transform hover:scale-105" href="{{ route('dashboard') }}" aria-label="Kasir">
        Kasir
      </a>

      <!-- Mobile Toggle -->
      <div class="md:hidden">
        <button id="menu-toggle" type="button" class="p-2 inline-flex justify-center items-center gap-2 rounded-lg border border-indigo-300 font-medium bg-white/20 text-white shadow-sm hover:bg-white/30 transition-all text-sm focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-offset-white focus:ring-indigo-600" aria-label="Toggle navigation">
          <svg id="menu-open" class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <line x1="3" x2="21" y1="6" y2="6"/>
            <line x1="3" x2="21" y1="12" y2="12"/>
            <line x1="3" x2="21" y1="18" y2="18"/>
          </svg>
          <svg id="menu-close" class="hidden w-4 h-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path d="M18 6 6 18"/>
            <path d="m6 6 12 12"/>
          </svg>
        </button>
      </div>
    </div>

    <!-- Menu -->
    <div id="menu" class="hidden w-full md:flex md:w-auto flex-col md:flex-row gap-4 md:gap-7 mt-5 md:mt-0">
      <!-- Links -->
      @if(Auth::user()->role === 'user' || Auth::user()->role === 'admin')
      <a class="font-semibold text-white/80 hover:text-white transition-all duration-300 transform hover:scale-110 hover:bg-white/20 px-3 py-2 rounded-lg {{ request()->routeIs('produk.*') ? 'bg-white/30 text-white' : '' }}" href="{{ route('produk.index') }}">
        Produk
      </a>
      <a class="font-semibold text-white/80 hover:text-white transition-all duration-300 transform hover:scale-110 hover:bg-white/20 px-3 py-2 rounded-lg {{ request()->routeIs(['penjualan.index', 'penjualan.create', 'penjualan.detail.*', 'penjualan.print']) ? 'bg-white/30 text-white' : '' }}" href="{{ route('penjualan.index') }}">
        Penjualan 
      </a>
      <a class="font-semibold text-white/80 hover:text-white transition-all duration-300 transform hover:scale-110 hover:bg-white/20 px-3 py-2 rounded-lg {{ request()->routeIs('penjualan.history') ? 'bg-white/30 text-white' : '' }}" href="{{ route('penjualan.history') }}">
        History
      </a>
      @endif
      <div id="menu" class="hidden w-full md:flex md:w-auto flex-col md:flex-row gap-4 md:gap-7 mt-5 md:mt-0">
        @if(Auth::user()->role === 'admin')
        <a class="font-semibold text-white/80 hover:text-white transition-all duration-300 transform hover:scale-110 hover:bg-white/20 px-3 py-2 rounded-lg relative {{ request()->routeIs('restock.*') ? 'bg-white/30 text-white' : '' }}" href="{{ route('restock.index') }}">
          Restock
          <span class="absolute top-0 right-0 inline-flex items-center justify-center size-5 text-xs font-bold text-white bg-red-500 rounded-full -translate-y-1/2 translate-x-1/2">
            5
          </span>
        </a>
        @endif
      </div>

      <!-- User Dropdown -->
      <div class="flex items-center gap-x-2 md:ms-auto">
        @auth
        <div x-data="{ open: false }" class="relative">
          <!-- Trigger -->
          <button @click="open = !open" class="flex items-center gap-2 py-2.5 px-6 rounded-full font-bold bg-white text-blue-700 shadow-md transition-all duration-300 hover:shadow-lg focus:outline-none">
            <span class="truncate max-w-[10rem]">{{ Auth::user()->name }}</span>
            <svg class="w-4 h-4 transition-transform duration-200" :class="{'rotate-180': open}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
            </svg>
          </button>

          <!-- Dropdown -->
          <div x-show="open" @click.outside="open = false" x-transition.origin.top.right
            class="absolute right-0 mt-2 w-44 bg-white text-gray-800 rounded-lg shadow-lg py-2 z-50">
            @if(Auth::user()->role === 'admin')
            <a href="{{ route('suppliers.create') }}" class="block px-4 py-2 text-sm hover:bg-gray-100">Manajemen Supplier</a>
            <a href="{{ route('admin.index') }}" class="block px-4 py-2 text-sm hover:bg-gray-100">Manajemen Pengguna</a>
            @endif
            <a href="{{ route('user.change-password') }}" class="block px-4 py-2 text-sm hover:bg-gray-100">Ganti Kata Sandi</a>
            <form method="POST" action="{{ route('logout') }}">
              @csrf
              <button type="submit" class="w-full text-left block px-4 py-2 text-sm text-red-600 hover:bg-red-100">Keluar</button>
            </form>
          </div>
        </div>
        @else
        <a class="py-2.5 px-6 inline-flex items-center justify-center text-sm font-bold rounded-full bg-white text-blue-700 shadow-md transition-all duration-300 hover:shadow-lg focus:outline-none" href="{{ route('login') }}">
          Sign in
        </a>
        @endauth
      </div>
      
    </div>
  </nav>
</header>

<!-- Alpine.js -->
<script src="//unpkg.com/alpinejs" defer></script>

<!-- Mobile Menu Toggle -->
<script>
  const btn = document.getElementById('menu-toggle');
  const menu = document.getElementById('menu');
  const openIcon = document.getElementById('menu-open');
  const closeIcon = document.getElementById('menu-close');
  
  btn.addEventListener('click', () => {
    menu.classList.toggle('hidden');
    openIcon.classList.toggle('hidden');
    closeIcon.classList.toggle('hidden');
  });
</script>
<!-- ========== END HEADER ========== -->
