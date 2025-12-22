@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto">
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-2xl font-semibold">Users</h1>
        <div class="flex items-center gap-2">
            <button
                x-data
                @click="$dispatch('open-modal', 'import-users')"
                class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-500 focus:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                Import CSV
            </button>
        </div>
    </div>

    <div class="mb-6 bg-white shadow ring-1 ring-black ring-opacity-5 rounded-lg p-4">
        <form method="POST" action="{{ route('admin.users.import-csv') }}" enctype="multipart/form-data" class="flex items-center gap-3">
            @csrf
            <div>
                <input type="file" name="csv_file" accept=".csv" required class="block w-full text-sm text-gray-900 border border-gray-300 rounded-lg cursor-pointer bg-gray-50 focus:outline-none">
                @error('csv_file')
                    <div class="text-red-600 text-sm mt-1">{{ $message }}</div>
                @enderror
            </div>
            <x-primary-button type="submit">Upload CSV</x-primary-button>
            <div class="text-xs text-gray-500">name,email,role,student_number,program_code</div>
        </form>
    </div>

    @if (session('status'))
        <div class="mb-4 p-3 bg-green-100 text-green-800 rounded">{{ session('status') }}</div>
    @endif

    @if (session('import_errors'))
        <div class="mb-4 p-3 bg-red-100 text-red-800 rounded">
            <div class="font-semibold mb-2">Import errors:</div>
            <ul class="list-disc ml-6 space-y-1">
                @foreach(session('import_errors') as $err)
                    <li>{{ $err }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- Search Bar -->
    <div class="bg-white rounded-lg shadow p-4 mb-6">
        <form method="GET" action="{{ route('admin.users.index') }}" class="flex gap-4">
            <input type="hidden" name="role" value="{{ $role }}">
            <div class="flex-1">
                <label class="block text-sm font-medium text-gray-700 mb-1">Search Users</label>
                <input 
                    type="text" 
                    name="search" 
                    value="{{ request('search') }}" 
                    placeholder="Search by name, email, or student number..." 
                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                >
            </div>
            <div class="flex items-end">
                <button type="submit" class="px-4 py-2 rounded bg-gray-900 text-white hover:bg-black">Search</button>
                @if(request('search'))
                    <a href="{{ route('admin.users.index', ['role' => $role]) }}" class="ml-2 px-4 py-2 rounded border border-gray-300 hover:bg-gray-50">Clear</a>
                @endif
            </div>
        </form>
    </div>

    <div class="border-b border-gray-200 mb-4">
        <nav class="-mb-px flex space-x-8" aria-label="Tabs">
            @php($tabs = ['all' => 'All', 'student' => 'Students', 'teacher' => 'Teachers', 'admin' => 'Admins'])
            @foreach ($tabs as $value => $label)
                <a href="{{ route('admin.users.index', ['role' => $value, 'search' => request('search')]) }}" class="whitespace-nowrap border-b-2 px-1 py-4 text-sm font-medium {{ $role === $value ? 'border-indigo-500 text-indigo-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">{{ $label }}</a>
            @endforeach
        </nav>
    </div>

    <div class="overflow-x-auto bg-white shadow ring-1 ring-black ring-opacity-5 rounded-lg">
        <table class="w-full table-fixed divide-y divide-gray-300">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Role</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Student #</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Program</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Password</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @foreach ($users as $user)
                <tr>
                    <td class="px-4 py-2">{{ $user->name }}</td>
                    <td class="px-4 py-2">{{ $user->email ?? '—' }}</td>
                    <td class="px-4 py-2 capitalize">{{ $user->role }}</td>
                    <td class="px-4 py-2">{{ $user->student_number ?? '—' }}</td>
                    <td class="px-4 py-2">{{ optional($user->program)->code ?? '—' }}</td>
                    <td class="px-4 py-2 text-sm font-medium">
                        @if($user->password)
                            <button
                                type="button"
                                class="text-blue-600 hover:text-blue-900 mr-3"
                                onclick="openPasswordModal({{ $user->id }})"
                            >
                                Re-Generate
                            </button>
                        @else
                            <span class="text-gray-500">-</span>
                        @endif
                    </td>
                    <td class="px-4 py-2 text-sm text-left space-x-2 font-medium">
                        @if(!$user->password)
                        <form method="POST" action="{{ route('admin.users.generate-password', $user) }}" class="inline">
                            @csrf
                            <button type="submit" class="text-blue-600 hover:text-blue-900 mr-3">Generate</button>
                        </form>
                        @endif
                        @if(in_array($user->role, ['student','teacher']))
                        <form method="POST" action="{{ route('admin.users.destroy', $user) }}" class="inline" onsubmit="return confirm('Delete this user? This action cannot be undone.');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-red-600 hover:text-red-900">
                                Delete
                            </button>
                        </form>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $users->links() }}
    </div>

    <x-modal name="import-users" :show="false" focusable>
        <form method="POST" action="{{ route('admin.users.import-csv') }}" enctype="multipart/form-data" class="p-6">
            @csrf
            <h2 class="text-lg font-medium text-gray-900">Import Users from CSV</h2>
            <p class="mt-1 text-sm text-gray-600">Expected columns: name, email(optional for students), role(student|teacher|admin), student_number(optional unless student), program_code(optional)</p>
            <div class="mt-4">
                <input type="file" name="csv_file" accept=".csv" required class="block w-full text-sm text-gray-900 border border-gray-300 rounded-lg cursor-pointer bg-gray-50 focus:outline-none">
                @error('csv_file')
                    <div class="text-red-600 text-sm mt-1">{{ $message }}</div>
                @enderror
            </div>
            <div class="mt-6 flex justify-end gap-2">
                <x-secondary-button x-on:click="$dispatch('close')">
                    Cancel
                </x-secondary-button>
                <x-primary-button type="submit">
                    Upload
                </x-primary-button>
            </div>
            <div class="mt-4 text-xs text-gray-500">
                Example:
                <pre class="bg-gray-100 p-2 rounded">name,email,role,student_number,program_code
Jane Admin,jane.admin@example.com,admin,,
Tom Teacher,tom.teacher@example.com,teacher,,
Ana Student,,student,2023-00001,BSCS</pre>
            </div>
        </form>
    </x-modal>

    <!-- Password Modals (View existing users with password) -->
    @foreach($users as $user)
        @if($user->password)
        <div id="password-modal-{{ $user->id }}" class="fixed inset-0 z-50 hidden items-center justify-center bg-black bg-opacity-40">
            <div class="bg-white rounded-lg shadow-lg w-full max-w-md p-6">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-semibold">View Password</h3>
                    <button type="button" class="text-gray-500 hover:text-gray-800" onclick="closePasswordModal({{ $user->id }})">×</button>
                </div>
                @if(session('generated_password') && session('generated_password.user_id') === $user->id)
                    <div class="mb-4 p-3 bg-yellow-50 border border-yellow-200 text-yellow-800 rounded">
                        <div class="font-semibold mb-1">New Password</div>
                        <div class="text-sm">
                            Password: <span class="font-mono px-2 py-1 bg-white border border-yellow-200 rounded">{{ session('generated_password.value') }}</span>
                        </div>
                        <p class="mt-2 text-xs text-gray-600">Share this password securely with the user. It will not be shown again.</p>
                    </div>
                    <div class="flex justify-end">
                        <button type="button" class="px-4 py-2 rounded bg-indigo-600 text-white hover:bg-indigo-500" onclick="closePasswordModal({{ $user->id }})">Close</button>
                    </div>
                @else
                    <p class="text-sm text-gray-600 mb-4">
                        Enter your admin password to view a new temporary password for <span class="font-medium">{{ $user->name }}</span>.
                    </p>
                    <form method="POST" action="{{ route('admin.users.view-password', $user) }}" class="space-y-4">
                        @csrf
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1" for="admin_password_{{ $user->id }}">Your Admin Password</label>
                            <input type="password" id="admin_password_{{ $user->id }}" name="admin_password" required class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        </div>
                        <div class="flex justify-end gap-2">
                            <button type="button" class="px-4 py-2 rounded border border-gray-300 hover:bg-gray-50" onclick="closePasswordModal({{ $user->id }})">Cancel</button>
                            <button type="submit" class="px-4 py-2 rounded bg-indigo-600 text-white hover:bg-indigo-500">View</button>
                        </div>
                    </form>
                @endif
            </div>
        </div>
        @endif
    @endforeach
</div>
@endsection

@section('scripts')
<script>
    @if(session('generated_password'))
        // Auto-open the modal for the user whose password was just generated/viewed
        document.addEventListener('DOMContentLoaded', function () {
            const userId = {{ session('generated_password.user_id') }};
            openPasswordModal(userId);
        });
    @endif

    function openPasswordModal(id) {
        const modal = document.getElementById(`password-modal-${id}`);
        if (modal) modal.classList.remove('hidden');
    }
    function closePasswordModal(id) {
        const modal = document.getElementById(`password-modal-${id}`);
        if (modal) modal.classList.add('hidden');
    }
</script>
@endsection


