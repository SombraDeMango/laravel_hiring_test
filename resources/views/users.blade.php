@extends('layouts.app')

@section('content')
    <div class="container py-4">
        <h2 class="mb-4">User List</h2>

        @if (!empty($error))
            <div class="alert alert-danger">
                {{ $error }}
            </div>
        @endif

        @if ($users)
            <div class="mb-3 text-end">
                <a href="{{ route('users.export', request()->query()) }}" class="btn btn-success btn-sm">
                    Export CSV
                </a>
            </div>

            <div class="table-responsive">
                <table class="table table-bordered table-striped align-middle">
                    <thead class="table-dark">
                    <tr>
                        <th>#</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Gender</th>
                        <th>Nationality</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach ($users as $index => $user)
                        <tr>
                            <td>{{ ($users->currentPage() - 1) * $users->perPage() + $index + 1 }}</td>
                            <td>{{ $user['name'] }}</td>
                            <td>{{ $user['email'] }}</td>
                            <td>{{ $user['gender'] }}</td>
                            <td>{{ $user['nationality'] }}</td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>

            {{-- Bootstrap pagination --}}
            <div class="mt-3">
                {{ $users->links('pagination::bootstrap-5') }}
            </div>
        @endif
    </div>
@endsection
