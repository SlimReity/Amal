<header class="banner">
  <div class="container mx-auto px-4 py-3 flex flex-wrap items-center justify-between">
    <a class="brand text-xl font-bold text-gray-900 hover:text-blue-600 transition-colors" href="{{ home_url('/') }}">
      {!! $siteName !!}
    </a>

    <!-- Mobile menu button -->
    <button class="mobile-menu-toggle md:hidden p-2 rounded-md hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-blue-500" 
            aria-label="{{ __('Toggle navigation menu', 'sage') }}" 
            aria-expanded="false" 
            type="button">
      <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
      </svg>
    </button>

    @if (has_nav_menu('primary_navigation'))
      <nav class="nav-primary hidden md:flex md:items-center md:space-x-6" aria-label="{{ wp_get_nav_menu_name('primary_navigation') }}">
        {!! wp_nav_menu(['theme_location' => 'primary_navigation', 'menu_class' => 'nav flex space-x-6', 'echo' => false]) !!}
      </nav>
    @else
      <nav class="nav-primary hidden md:flex md:items-center md:space-x-6" aria-label="{{ __('Main navigation', 'sage') }}">
        <ul class="nav flex space-x-6">
          @foreach ($navigationItems as $item)
            <li>
              <a href="{{ $item['url'] }}" 
                 class="nav-link px-3 py-2 text-gray-700 hover:text-blue-600 transition-colors rounded-md hover:bg-gray-50 {{ $item['current'] ? 'text-blue-600 font-medium' : '' }}"
                 @if($item['current']) aria-current="page" @endif>
                {{ $item['title'] }}
              </a>
            </li>
          @endforeach
        </ul>
      </nav>
    @endif

    <!-- Mobile menu dropdown -->
    <div class="mobile-menu w-full md:hidden hidden mt-4">
      @if (has_nav_menu('primary_navigation'))
        {!! wp_nav_menu(['theme_location' => 'primary_navigation', 'menu_class' => 'nav-mobile flex flex-col space-y-2', 'echo' => false]) !!}
      @else
        <ul class="nav-mobile flex flex-col space-y-2">
          @foreach ($navigationItems as $item)
            <li>
              <a href="{{ $item['url'] }}" 
                 class="nav-link block px-3 py-2 text-gray-700 hover:text-blue-600 transition-colors rounded-md hover:bg-gray-50 {{ $item['current'] ? 'text-blue-600 font-medium' : '' }}"
                 @if($item['current']) aria-current="page" @endif>
                {{ $item['title'] }}
              </a>
            </li>
          @endforeach
        </ul>
      @endif
    </div>
  </div>
</header>
