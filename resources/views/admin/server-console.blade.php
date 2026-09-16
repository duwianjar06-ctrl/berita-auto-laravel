@extends('layouts.app')

@section('content')
    <div class="space-y-6">
        <h1 class="text-2xl font-black">REMOTE SERVER CONSOLE</h1>

        <p class="rounded border border-amber-200 bg-amber-50 p-4">
            Console ini hanya menjalankan command Laravel/Artisan yang telah diizinkan.
            Command sistem/OS tidak didukung.
        </p>

        @if (session('status'))
            <div class="rounded bg-emerald-50 p-3">
                {{ session('status') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="rounded bg-red-50 p-3">
                {{ $errors->first() }}
            </div>
        @endif

        <form class="space-y-3 rounded border bg-white p-5" method="POST" action="{{ route('admin.console.run') }}">
            @csrf

            <label class="font-bold" for="command">CUSTOM COMMAND</label>
            <input
                id="command"
                class="w-full border p-3 font-mono"
                name="command"
                list="commands"
                placeholder="berita:debug-rejected --limit=20"
                required
            >

            <datalist id="commands">
                @foreach ($registry as $key => $value)
                    <option value="{{ $key }}">{{ $value['label'] }}</option>
                @endforeach
            </datalist>

            <label class="block">
                <input type="checkbox" name="confirmed" value="1">
                Confirm mutating command
            </label>

            <button class="rounded bg-slate-900 px-4 py-2 font-bold text-white" type="submit">
                Run
            </button>
        </form>

        <div class="overflow-auto rounded border bg-white">
            <table class="w-full text-left text-sm">
                <thead>
                    <tr>
                        <th class="p-3">Time</th>
                        <th class="p-3">Command</th>
                        <th class="p-3">Status</th>
                        <th class="p-3">Duration</th>
                        <th class="p-3">Requested by</th>
                        <th class="p-3">Result</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($runs as $run)
                        <tr class="border-t">
                            <td class="p-3">{{ $run->created_at }}</td>
                            <td class="p-3 font-mono">{{ $run->command_input }}</td>
                            <td class="p-3">{{ $run->status }}</td>
                            <td class="p-3">{{ $run->duration_ms }} ms</td>
                            <td class="p-3">{{ $run->requested_by }}</td>
                            <td class="p-3">
                                <details>
                                    <summary>View Output</summary>
                                    <pre class="max-w-xl whitespace-pre-wrap">{{ $run->stdout ?? '' }}{{ $run->stderr ?? '' }}</pre>
                                </details>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td class="p-3" colspan="6">No command history yet.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
