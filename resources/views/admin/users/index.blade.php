@extends('admin.layouts.app')

@section('title', 'Manajemen Pengguna')

@section('content')
<div class="flex justify-between items-center mb-6">
    <h2 class="text-lg font-semibold">Semua Pengguna</h2>
    <span class="bg-gray-700 px-3 py-1 rounded-full text-sm">{{ $users->total() }} total</span>
</div>

<div class="bg-gray-800 rounded-xl border border-gray-700 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead>
                <tr class="text-gray-400 text-sm border-b border-gray-700">
                    <th class="text-left px-4 py-3">Nama</th>
                    <th class="text-left px-4 py-3">Email</th>
                    <th class="text-center px-4 py-3">Admin</th>
                    <th class="text-center px-4 py-3">Bergabung</th>
                    <th class="text-center px-4 py-3">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($users as $user)
                <tr class="border-b border-gray-700 hover:bg-gray-700/50">
                    <td class="px-4 py-3">{{ $user->name }}</td>
                    <td class="px-4 py-3 text-sm text-gray-400">{{ $user->email }}</td>
                    <td class="px-4 py-3 text-center">
                        @if($user->is_admin)
                            <span class="bg-purple-600 px-3 py-1 rounded-full text-xs font-semibold">
                                <i class="fas fa-crown"></i> Admin
                            </span>
                        @else
                            <span class="text-gray-500 text-sm">User</span>
                        @endif
                    </td>
                    <td class="px-4 py-3 text-center text-sm text-gray-400">{{ $user->created_at->format('d/m/Y') }}</td>
                    <td class="px-4 py-3 text-center">
                        <a href="{{ route('admin.users.edit', $user) }}" class="bg-blue-600 px-3 py-1.5 rounded text-xs hover:bg-blue-700 transition inline-flex items-center space-x-1">
                            <i class="fas fa-edit"></i>
                            <span>Edit</span>
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="px-4 py-8 text-center text-gray-400">Belum ada pengguna</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="mt-6">
    {{ $users->links() }}
</div>
@endsection
