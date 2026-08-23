<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', config('app.name'))</title>
    <link rel="icon" type="image/svg+xml" href="{{ asset('emblem-vietnam.svg') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    @vite(['resources/css/app.css', 'resources/js/admin/app.js'])
</head>
<body class="min-h-screen bg-admin-page font-inter text-slate-900">
    <header class="bg-primary text-white shadow-sm">
        <div class="flex w-full flex-wrap items-center gap-x-3 gap-y-2 px-4 py-2 sm:gap-x-4 xl:flex-nowrap">
            <a href="{{ route('admin.dashboard') }}" class="flex shrink-0 items-center gap-3 text-white">
                <img
                    class="h-11 w-11 shrink-0 object-contain"
                    src="{{ asset('emblem-vietnam.svg') }}"
                    alt="Quốc huy Việt Nam"
                >
                <span class="hidden sm:block">
                    <span class="block text-base font-bold leading-tight tracking-tight text-white">Cổng Dịch Vụ Công</span>
                    <span class="mt-0.5 block text-[9px] font-semibold tracking-[0.12em] text-white/60">PHỤC VỤ NGƯỜI DÂN</span>
                </span>
            </a>

            <nav class="order-3 flex w-full min-w-0 flex-wrap items-center justify-center gap-1 md:order-none md:w-auto md:flex-1 xl:flex-nowrap" aria-label="Điều hướng quản trị">
                <a
                    href="{{ route('admin.dashboard') }}"
                    @class([
                        'whitespace-nowrap rounded-lg px-3 py-2 text-sm font-semibold transition-colors',
                        'bg-white/20 text-white' => request()->routeIs('admin.dashboard'),
                        'text-white/65 hover:bg-white/10 hover:text-white' => ! request()->routeIs('admin.dashboard'),
                    ])
                >
                    Tổng quan
                </a>

                @can('viewAny', \App\Models\Department::class)
                    <a
                        href="{{ route('admin.departments.index') }}"
                        @class([
                            'whitespace-nowrap rounded-lg px-3 py-2 text-sm font-semibold transition-colors',
                            'bg-white/20 text-white' => request()->routeIs('admin.departments.*'),
                            'text-white/65 hover:bg-white/10 hover:text-white' => ! request()->routeIs('admin.departments.*'),
                        ])
                    >
                        Phòng ban
                    </a>
                @endcan

                @can('viewAny', \App\Models\ServiceCategory::class)
                    <a
                        href="{{ route('admin.service-categories.index') }}"
                        @class([
                            'whitespace-nowrap rounded-lg px-3 py-2 text-sm font-semibold transition-colors',
                            'bg-white/20 text-white' => request()->routeIs('admin.service-categories.*'),
                            'text-white/65 hover:bg-white/10 hover:text-white' => ! request()->routeIs('admin.service-categories.*'),
                        ])
                    >
                        Danh mục
                    </a>
                @endcan

                @can('viewAny', \App\Models\ServiceType::class)
                    <a
                        href="{{ route('admin.service-types.index') }}"
                        @class([
                            'whitespace-nowrap rounded-lg px-3 py-2 text-sm font-semibold transition-colors',
                            'bg-white/20 text-white' => request()->routeIs('admin.service-types.*'),
                            'text-white/65 hover:bg-white/10 hover:text-white' => ! request()->routeIs('admin.service-types.*'),
                        ])
                    >
                        Dịch vụ
                    </a>
                @endcan

                @can('viewAny', \App\Models\Application::class)
                    <a
                        href="{{ route('admin.applications.index') }}"
                        @class([
                            'whitespace-nowrap rounded-lg px-3 py-2 text-sm font-semibold transition-colors',
                            'bg-white/20 text-white' => request()->routeIs('admin.applications.*'),
                            'text-white/65 hover:bg-white/10 hover:text-white' => ! request()->routeIs('admin.applications.*'),
                        ])
                    >
                        Hồ sơ
                    </a>
                @endcan

                @can('viewAny', \App\Models\User::class)
                    <a
                        href="{{ route('admin.users.index') }}"
                        @class([
                            'whitespace-nowrap rounded-lg px-3 py-2 text-sm font-semibold transition-colors',
                            'bg-white/20 text-white' => request()->routeIs('admin.users.index', 'admin.users.show'),
                            'text-white/65 hover:bg-white/10 hover:text-white' => ! request()->routeIs('admin.users.index', 'admin.users.show'),
                        ])
                    >
                        Người dùng
                    </a>
                @endcan

                @if (auth()->user()?->isSuperAdmin())
                    <a
                        href="{{ route('admin.users.import') }}"
                        @class([
                            'whitespace-nowrap rounded-lg px-3 py-2 text-sm font-semibold transition-colors',
                            'bg-white/20 text-white' => request()->routeIs('admin.users.import*'),
                            'text-white/65 hover:bg-white/10 hover:text-white' => ! request()->routeIs('admin.users.import*'),
                        ])
                    >
                        Nhập CSV
                    </a>
                @endif

                @if (auth()->user()?->isSuperAdmin())
                    <a
                        href="{{ route('admin.activity-logs.index') }}"
                        @class([
                            'whitespace-nowrap rounded-lg px-3 py-2 text-sm font-semibold transition-colors',
                            'bg-white/20 text-white' => request()->routeIs('admin.activity-logs.*'),
                            'text-white/65 hover:bg-white/10 hover:text-white' => ! request()->routeIs('admin.activity-logs.*'),
                        ])
                    >
                        Nhật ký
                    </a>
                @endif
            </nav>

            @php
                $navUser = auth()->user();
                $navRoleLabel = $navUser?->role?->label() ?? '';
                $navRoleBadgeClass = match($navUser?->role?->value) {
                    'super_admin' => 'bg-amber-300 text-amber-900 ring-amber-400/40',
                    'manager' => 'bg-sky-200 text-sky-900 ring-sky-300/50',
                    'staff' => 'bg-white text-primary ring-white/30',
                    default => 'bg-white/20 text-white ring-white/20',
                };
                $navDepartments = collect();
                if ($navUser?->isManager()) {
                    $navDepartments = $navUser->ledDepartments()->get(['name','code']);
                } elseif ($navUser?->isStaff()) {
                    $navDepartments = $navUser->departments()->get(['name','code']);
                }
            @endphp
            <div class="relative flex shrink-0 items-center gap-2 sm:gap-3" x-data="{ open: false }" @click.outside="open = false" @keydown.escape.window="open = false">
                <button type="button" @click="open = !open" class="flex max-w-[220px] items-center gap-2 rounded-full bg-white/10 px-2 py-1.5 pr-2.5 ring-1 ring-white/20 hover:bg-white/15 focus:outline-none focus:ring-2 focus:ring-white/60" :aria-expanded="open.toString()">
                    <span class="hidden max-w-[120px] truncate text-sm font-semibold text-white sm:inline">{{ $navUser?->name }}</span>
                    <span class="sm:hidden flex h-7 w-7 items-center justify-center rounded-full bg-white text-xs font-bold text-primary">{{ mb_substr($navUser?->name ?? 'A', 0, 1, 'UTF-8') }}</span>
                    <span class="inline-flex items-center rounded-full px-2 py-0.5 text-[10px] font-extrabold uppercase tracking-widest ring-1 {{ $navRoleBadgeClass }}">{{ $navRoleLabel }}</span>
                    <svg class="h-3.5 w-3.5 shrink-0 text-white/70 transition-transform" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7"/></svg>
                </button>

                <div x-show="open" x-transition:enter="transition ease-out duration-150" x-transition:enter-start="opacity-0 translate-y-1" x-transition:enter-end="opacity-100 translate-y-0" x-transition:leave="transition ease-in duration-100" x-transition:leave-start="opacity-100 translate-y-0" x-transition:leave-end="opacity-0 translate-y-1" x-cloak class="absolute right-0 top-full z-30 mt-2 w-[320px] overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-2xl">
                    <div class="bg-gradient-to-br from-primary to-[#0a3a7a] px-4 py-4 text-white">
                        <p class="truncate text-sm font-bold">{{ $navUser?->name }}</p>
                        <p class="truncate text-xs text-white/70">{{ $navUser?->email }}</p>
                        <div class="mt-3 inline-flex items-center gap-2 rounded-full bg-white px-2.5 py-1 text-xs font-bold text-primary shadow-sm">
                            <span class="h-2 w-2 rounded-full bg-emerald-500"></span>
                            {{ $navRoleLabel }}
                        </div>
                    </div>
                    <div class="space-y-3 p-4">
                        <div>
                            <p class="text-[11px] font-bold uppercase tracking-widest text-slate-400">Vai trò</p>
                            <p class="mt-1 text-sm font-semibold text-slate-900">{{ $navRoleLabel }}</p>
                            <p class="text-xs text-slate-500">
                                @if($navUser?->isSuperAdmin()) Toàn quyền quản trị hệ thống
                                @elseif($navUser?->isManager()) Quản lý & duyệt hồ sơ phòng ban
                                @elseif($navUser?->isStaff()) Xử lý hồ sơ được phân công
                                @else Vai trò hệ thống @endif
                            </p>
                        </div>
                        <div>
                            <p class="text-[11px] font-bold uppercase tracking-widest text-slate-400">Phòng ban</p>
                            @if($navUser?->isSuperAdmin())
                                <p class="mt-1 inline-flex items-center gap-1.5 rounded-lg bg-amber-50 px-2.5 py-1.5 text-sm font-semibold text-amber-900 ring-1 ring-amber-200">Toàn hệ thống · Tất cả phòng ban</p>
                            @elseif($navDepartments->isNotEmpty())
                                <ul class="mt-2 space-y-1.5">
                                    @foreach($navDepartments as $dept)
                                        <li class="flex items-center gap-2 rounded-xl border border-slate-200 bg-slate-50 px-3 py-2">
                                            <span class="h-2 w-2 shrink-0 rounded-full bg-primary"></span>
                                            <div class="min-w-0">
                                                <p class="truncate text-sm font-semibold text-slate-900">{{ $dept->name }}</p>
                                                <p class="text-xs text-slate-500">{{ $dept->code }}</p>
                                            </div>
                                        </li>
                                    @endforeach
                                </ul>
                            @else
                                <p class="mt-1 text-sm text-slate-500">Chưa thuộc phòng ban nào</p>
                            @endif
                        </div>
                    </div>
                    <div class="flex items-center justify-between border-t border-slate-100 bg-slate-50 px-4 py-2.5">
                        <span class="text-[11px] font-medium text-slate-500">Bấm ra ngoài để đóng</span>
                        <button type="button" @click="open=false" class="text-xs font-semibold text-slate-600 hover:text-slate-900">Đóng</button>
                    </div>
                </div>

                <form method="POST" action="{{ route('admin.logout') }}">
                    @csrf
                    <button class="rounded-lg px-2 py-2 text-sm font-semibold text-white/80 transition hover:bg-white/10 hover:text-white focus:outline-none focus:ring-2 focus:ring-white/70 sm:px-3" type="submit">
                        Đăng xuất
                    </button>
                </form>
            </div>
        </div>
    </header>

    <main class="mx-auto w-full max-w-[1600px] px-4 py-6 sm:px-6 lg:px-8" x-data>
        @if (session('success'))
            <div class="mb-5 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-medium text-emerald-800" role="status">
                {{ session('success') }}
            </div>
        @endif

        @if (session('error'))
            <div class="mb-5 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm font-medium text-red-800" role="alert">
                {{ session('error') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="mb-5 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800" role="alert">
                <p class="font-semibold">Không thể hoàn tất thao tác.</p>
                <p class="mt-1">Vui lòng kiểm tra lại các trường được đánh dấu và thử lại.</p>
            </div>
        @endif

        @yield('content')
    </main>
</body>
</html>
