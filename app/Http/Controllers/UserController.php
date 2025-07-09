<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Pagination\LengthAwarePaginator;
use Symfony\Component\HttpFoundation\StreamedResponse;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $page = $request->input('page', 1);
        $filters = $request->except('page');
        $filterKey = http_build_query($filters);
        $cacheKey = "random_users_all_" . md5($filterKey); // One key for full data

        try {
            $fetchedUsers = $this->fetchUsersFromCache($filters, $cacheKey);

            // Paginate
            $perPage = 10;
            $offset = ($page - 1) * $perPage;
            $visibleUsers = $fetchedUsers->slice($offset, $perPage)->values();

            $paginatedUsers = new LengthAwarePaginator(
                $visibleUsers,
                $fetchedUsers->count(),
                $perPage,
                $page,
                ['path' => url()->current(), 'query' => $request->query()]
            );

            return view('users', [
                'users' => $paginatedUsers,
                'error' => null,
            ]);
        } catch (\Exception $e) {
            return view('users', [
                'users' => null,
                'error' => 'Unable to fetch users. Please try again later.',
            ]);
        }
    }

    public function export(Request $request): StreamedResponse
    {
        $page = $request->input('page', 1);
        $filters = $request->except('page');
        $filterKey = http_build_query($filters);
        $cacheKey = "random_users_all_" . md5($filterKey);
        $perPage = 10;

        try {
            $users = $this->fetchUsersFromCache($filters, $cacheKey);

            // Paginate
            $offset = ($page - 1) * $perPage;
            $visibleUsers = $users->slice($offset, $perPage)->values();
        } catch (\Exception $e) {
            return back()->with('error', 'Export failed');
        }

        $headers = [
            "Content-Type" => "text/csv",
            "Content-Disposition" => "attachment; filename=users_page_{$page}.csv",
        ];

        return response()->stream(function () use ($visibleUsers) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['Name', 'Email', 'Gender', 'Nationality']);

            foreach ($visibleUsers as $user) {
                fputcsv($handle, [
                    $user['name'],
                    $user['email'],
                    $user['gender'],
                    $user['nationality']
                ]);
            }

            fclose($handle);
        }, 200, $headers);
    }

//Fetch users from RandomUser API with caching
    private function fetchUsersFromCache(array $filters, string $cacheKey, int $results = 50)
    {
        return Cache::remember($cacheKey, now()->addMinutes(10), function () use ($filters, $results) {
            $url = "https://randomuser.me/api/?results={$results}";

            if (!empty($filters['gender'])) {
                $url .= "&gender=" . $filters['gender'];
            }

            if (!empty($filters['nationality'])) {
                $url .= "&nat=" . $filters['nationality'];
            }

            $res = Http::get($url);

            if ($res->failed() || !isset($res->json()['results'])) {
                throw new \Exception('API not working');
            }

            $rawUsers = $res->json()['results'];
            $formattedUsers = [];

            foreach ($rawUsers as $user) {
                $formattedUsers[] = [
                    'name' => $user['name']['first'] . ' ' . $user['name']['last'],
                    'email' => $user['email'],
                    'gender' => ucfirst($user['gender']),
                    'nationality' => $user['nat'],
                ];
            }

            return collect($formattedUsers);
        });
    }

}
