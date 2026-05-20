<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\RequestException;

class MusicController extends Controller
{
    public function index()
    {
        $hasBookedSession = $this->hasConfirmedTraineeBooking();
        $youtubeConfigured = (bool) config('services.youtube.key');

        return view('music.index', compact('hasBookedSession', 'youtubeConfigured'));
    }

    private function getFallbackTracks(string $query = ''): array
    {
        $allFallbacks = [
            [
                'video_id' => '2S24-y0Ij3Y',
                'title' => 'NEFFEX - Fight Back [Workout Motivation]',
                'channel' => 'NEFFEX Music',
                'thumbnail' => 'https://img.youtube.com/vi/2S24-y0Ij3Y/0.jpg',
            ],
            [
                'video_id' => '83R59AnBY90',
                'title' => 'NEFFEX - Grateful [Clean Gym Motivation]',
                'channel' => 'NEFFEX Music',
                'thumbnail' => 'https://img.youtube.com/vi/83R59AnBY90/0.jpg',
            ],
            [
                'video_id' => 'B3_m9z2p4J0',
                'title' => 'NEFFEX - Cold [Aggressive Training Beat]',
                'channel' => 'NEFFEX Music',
                'thumbnail' => 'https://img.youtube.com/vi/B3_m9z2p4J0/0.jpg',
            ],
            [
                'video_id' => '4bS1W1nE_U0',
                'title' => 'NEFFEX - Crown [Power Gym Beat]',
                'channel' => 'NEFFEX Music',
                'thumbnail' => 'https://img.youtube.com/vi/4bS1W1nE_U0/0.jpg',
            ],
            [
                'video_id' => '24C8r8JupYY',
                'title' => 'NEFFEX - Destiny [High-Energy Workout]',
                'channel' => 'NEFFEX Music',
                'thumbnail' => 'https://img.youtube.com/vi/24C8r8JupYY/0.jpg',
            ],
            [
                'video_id' => 'jfKfPfyJRdk',
                'title' => 'VirtuGym Clean Lo-Fi Workout Background Beats',
                'channel' => 'VirtuGym Premium',
                'thumbnail' => 'https://img.youtube.com/vi/jfKfPfyJRdk/0.jpg',
            ],
            [
                'video_id' => 'K4DyBUG242c',
                'title' => 'NCS: Workout Music Mix [No Copyright]',
                'channel' => 'NoCopyrightSounds',
                'thumbnail' => 'https://img.youtube.com/vi/K4DyBUG242c/0.jpg',
            ],
            [
                'video_id' => 'F82A5yDkiQ4',
                'title' => 'Gym Workout Beats - Till I Collapse (Inst. Edit)',
                'channel' => 'Workout Beats',
                'thumbnail' => 'https://img.youtube.com/vi/F82A5yDkiQ4/0.jpg',
            ],
        ];

        if (empty($query)) {
            return $allFallbacks;
        }

        // Fuzzy match on title or channel
        $filtered = array_filter($allFallbacks, function ($track) use ($query) {
            return mb_stripos($track['title'], $query) !== false || mb_stripos($track['channel'], $query) !== false;
        });

        return !empty($filtered) ? array_values($filtered) : $allFallbacks;
    }

    public function search(Request $request)
    {
        if (!$this->hasConfirmedTraineeBooking()) {
            abort(403, 'Workout music is available after booking a confirmed session.');
        }

        $validated = $request->validate([
            'q' => 'required|string|min:2|max:80',
        ]);

        $apiKey = config('services.youtube.key');

        if (!$apiKey) {
            $songs = $this->getFallbackTracks($validated['q']);
            return response()->json(['songs' => $songs]);
        }

        try {
            $response = $this->youtubeSearch($validated['q'], 8, $apiKey);
            
            if (!$response->successful()) {
                $songs = $this->getFallbackTracks($validated['q']);
                return response()->json(['songs' => $songs]);
            }

            $songs = collect($response->json('items', []))
                ->filter(fn ($item) => !empty($item['id']['videoId']))
                ->map(function ($item) {
                    return [
                        'video_id' => $item['id']['videoId'],
                        'title' => $item['snippet']['title'] ?? 'Untitled video',
                        'channel' => $item['snippet']['channelTitle'] ?? 'YouTube',
                        'thumbnail' => $item['snippet']['thumbnails']['medium']['url']
                            ?? $item['snippet']['thumbnails']['default']['url']
                            ?? null,
                    ];
                })
                ->values();

            return response()->json(['songs' => $songs]);

        } catch (ConnectionException|RequestException $exception) {
            $songs = $this->getFallbackTracks($validated['q']);
            return response()->json(['songs' => $songs]);
        }
    }

    public function defaultTrack()
    {
        if (!$this->hasConfirmedTraineeBooking()) {
            abort(403, 'Workout music is available after booking a confirmed session.');
        }

        $apiKey = config('services.youtube.key');

        if (!$apiKey) {
            $songs = $this->getFallbackTracks();
            return response()->json([
                'song' => $songs[0],
            ]);
        }

        try {
            $response = $this->youtubeSearch('gym workout music motivation clean', 1, $apiKey);
            
            if (!$response->successful()) {
                $songs = $this->getFallbackTracks();
                return response()->json([
                    'song' => $songs[0],
                ]);
            }

            $item = collect($response->json('items', []))->firstWhere('id.videoId');

            if (!$item) {
                $songs = $this->getFallbackTracks();
                return response()->json([
                    'song' => $songs[0],
                ]);
            }

            return response()->json([
                'song' => [
                    'video_id' => $item['id']['videoId'],
                    'title' => $item['snippet']['title'] ?? 'Workout music',
                    'channel' => $item['snippet']['channelTitle'] ?? 'YouTube',
                ],
            ]);

        } catch (ConnectionException|RequestException $exception) {
            $songs = $this->getFallbackTracks();
            return response()->json([
                'song' => $songs[0],
            ]);
        }
    }

    public function backgroundTrack()
    {
        $apiKey = config('services.youtube.key');
        $fallbackVideoId = config('services.youtube.background_video_id');

        if (!$apiKey) {
            $songs = $this->getFallbackTracks();
            return response()->json([
                'song' => $songs[0] ?? [
                    'video_id' => $fallbackVideoId,
                    'title' => 'VirtuGym background workout music',
                    'channel' => 'YouTube',
                ],
            ]);
        }

        try {
            $response = $this->youtubeSearch('gym workout music motivation clean no copyright', 1, $apiKey);
            
            if (!$response->successful()) {
                $songs = $this->getFallbackTracks();
                return response()->json([
                    'song' => $songs[0] ?? [
                        'video_id' => $fallbackVideoId,
                        'title' => 'VirtuGym background workout music',
                        'channel' => 'YouTube',
                    ],
                ]);
            }

            $item = collect($response->json('items', []))->firstWhere('id.videoId');

            return response()->json([
                'song' => [
                    'video_id' => $item['id']['videoId'] ?? $fallbackVideoId,
                    'title' => $item['snippet']['title'] ?? 'VirtuGym background workout music',
                    'channel' => $item['snippet']['channelTitle'] ?? 'YouTube',
                ],
            ]);

        } catch (ConnectionException|RequestException $exception) {
            $songs = $this->getFallbackTracks();
            return response()->json([
                'song' => $songs[0] ?? [
                    'video_id' => $fallbackVideoId,
                    'title' => 'VirtuGym background workout music',
                    'channel' => 'YouTube',
                ],
            ]);
        }
    }

    private function youtubeSearch(string $query, int $maxResults, string $apiKey)
    {
        return Http::connectTimeout(3)
            ->timeout(5)
            ->get('https://www.googleapis.com/youtube/v3/search', [
                'part' => 'snippet',
                'type' => 'video',
                'videoEmbeddable' => 'true',
                'videoSyndicated' => 'true',
                'order' => 'relevance',
                'safeSearch' => 'none',
                'maxResults' => $maxResults,
                'q' => $query,
                'key' => $apiKey,
            ]);
    }

    private function hasConfirmedTraineeBooking(): bool
    {
        $userId = Auth::id();

        if (Auth::user()->role !== 'trainee') {
            return false;
        }

        return Booking::where('status', 'confirmed')
            ->where('trainee_id', $userId)
            ->exists();
    }
}
