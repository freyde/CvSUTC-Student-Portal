@extends('layouts.app')

@section('content')
<div class="max-w-md mx-auto">
  <h1 class="text-2xl font-semibold mb-6">Login</h1>

  <a href="{{ route('login.google') }}"
     class="w-full inline-flex items-center justify-center gap-2 px-4 py-2 rounded bg-red-600 text-white hover:bg-red-700">
    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 48 48" class="h-5 w-5"><path fill="#FFC107" d="M43.611,20.083H42V20H24v8h11.303c-1.649,4.657-6.08,8-11.303,8c-6.627,0-12-5.373-12-12 s5.373-12,12-12c3.059,0,5.842,1.154,7.961,3.039l5.657-5.657C33.151,6.053,28. Eight,4,24,4C12.955,4,4,12.955,4,24 s8.955,20,20,20s20-8.955,20-20C44,22.659,43.861,21.35,43.611,20.083z"/><path fill="#FF3D00" d="M6.306,14.691l6.571,4.819C14.411,16.086,18.829,12,24,12c3.059,0,5.842,1.154,7.961,3.039l5.657-5.657C33.151,6.053,28. Eight,4,24,4C16.318,4,9.656,8.337,6.306,14.691z"/><path fill="#4CAF50" d="M24,44c5.166,0,9.86-1.977,13.409-5.192l-6.191-5.238C29.104,35.091,26.715,36,24,36c-5.203,0-9.62-3.343-11.277-7.986 l-6.49,5.002C9.553,39.67,16.227,44,24,44z"/><path fill="#1976D2" d="M43.611,20.083H42V20H24v8h11.303c-0.792,2.238-2.231,4.166-4.094,5.571c0.001-0.001,0.002-0.001,0.003-0.002 l6.191,5.238C36.245,39.685,44,34,44,24C44,22.659,43.861,21.35,43.611,20.083z"/></svg>
    Continue with Google
  </a>

  <div class="mt-6 text-sm text-gray-600">You’ll be asked to choose a Google account.</div>
</div>
@endsection


