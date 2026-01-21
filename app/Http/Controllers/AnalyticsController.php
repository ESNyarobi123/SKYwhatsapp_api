<?php

namespace App\Http\Controllers;

use App\Models\Message;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AnalyticsController extends Controller
{
    /**
     * Display the analytics dashboard.
     */
    public function index(Request $request)
    {
        $user = auth()->user();
        $period = $request->get('period', '7'); // 7, 30, 90 days
        $startDate = now()->subDays((int) $period)->startOfDay();
        $endDate = now()->endOfDay();

        // Messages per day
        $messagesPerDay = $this->getMessagesPerDay($user->id, $startDate, $endDate);
        
        // Success rates
        $successRates = $this->getSuccessRates($user->id, $startDate, $endDate);
        
        // Peak hours
        $peakHours = $this->getPeakHours($user->id, $startDate, $endDate);
        
        // Summary stats
        $stats = $this->getSummaryStats($user->id, $startDate, $endDate);
        
        // Top instances
        $topInstances = $this->getTopInstances($user->id, $startDate, $endDate);
        
        // Message types breakdown
        $messageTypes = $this->getMessageTypes($user->id, $startDate, $endDate);

        return view('dashboard.analytics.index', compact(
            'messagesPerDay',
            'successRates',
            'peakHours',
            'stats',
            'topInstances',
            'messageTypes',
            'period'
        ));
    }

    /**
     * Get messages per day data.
     */
    protected function getMessagesPerDay(int $userId, Carbon $startDate, Carbon $endDate): array
    {
        $messages = Message::where('user_id', $userId)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->selectRaw('DATE(created_at) as date, direction, COUNT(*) as count')
            ->groupBy('date', 'direction')
            ->orderBy('date')
            ->get();

        $labels = [];
        $inbound = [];
        $outbound = [];

        $currentDate = $startDate->copy();
        while ($currentDate <= $endDate) {
            $dateStr = $currentDate->format('Y-m-d');
            $labels[] = $currentDate->format('M d');
            
            $dayData = $messages->where('date', $dateStr);
            $inbound[] = $dayData->where('direction', 'inbound')->first()?->count ?? 0;
            $outbound[] = $dayData->where('direction', 'outbound')->first()?->count ?? 0;
            
            $currentDate->addDay();
        }

        return [
            'labels' => $labels,
            'datasets' => [
                [
                    'label' => 'Received',
                    'data' => $inbound,
                    'borderColor' => '#3B82F6',
                    'backgroundColor' => 'rgba(59, 130, 246, 0.1)',
                    'fill' => true,
                ],
                [
                    'label' => 'Sent',
                    'data' => $outbound,
                    'borderColor' => '#10B981',
                    'backgroundColor' => 'rgba(16, 185, 129, 0.1)',
                    'fill' => true,
                ],
            ],
        ];
    }

    /**
     * Get success rates data.
     */
    protected function getSuccessRates(int $userId, Carbon $startDate, Carbon $endDate): array
    {
        $statusCounts = Message::where('user_id', $userId)
            ->where('direction', 'outbound')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->selectRaw('status, COUNT(*) as count')
            ->groupBy('status')
            ->pluck('count', 'status')
            ->toArray();

        $total = array_sum($statusCounts);
        
        return [
            'delivered' => $statusCounts['delivered'] ?? 0,
            'sent' => $statusCounts['sent'] ?? 0,
            'pending' => $statusCounts['pending'] ?? 0,
            'failed' => $statusCounts['failed'] ?? 0,
            'total' => $total,
            'success_rate' => $total > 0 
                ? round((($statusCounts['delivered'] ?? 0) + ($statusCounts['sent'] ?? 0)) / $total * 100, 1) 
                : 0,
        ];
    }

    /**
     * Get peak hours data.
     */
    protected function getPeakHours(int $userId, Carbon $startDate, Carbon $endDate): array
    {
        $hourlyData = Message::where('user_id', $userId)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->selectRaw('HOUR(created_at) as hour, COUNT(*) as count')
            ->groupBy('hour')
            ->orderBy('hour')
            ->pluck('count', 'hour')
            ->toArray();

        $labels = [];
        $data = [];
        
        for ($i = 0; $i < 24; $i++) {
            $labels[] = sprintf('%02d:00', $i);
            $data[] = $hourlyData[$i] ?? 0;
        }

        // Find peak hours
        $maxCount = max($data) ?: 1;
        $peakHoursList = array_keys(array_filter($data, fn($count) => $count >= $maxCount * 0.8));

        return [
            'labels' => $labels,
            'data' => $data,
            'peak_hours' => $peakHoursList,
            'max_count' => $maxCount,
        ];
    }

    /**
     * Get summary statistics.
     */
    protected function getSummaryStats(int $userId, Carbon $startDate, Carbon $endDate): array
    {
        $currentPeriod = Message::where('user_id', $userId)
            ->whereBetween('created_at', [$startDate, $endDate]);

        $previousStart = $startDate->copy()->subDays($startDate->diffInDays($endDate));
        $previousPeriod = Message::where('user_id', $userId)
            ->whereBetween('created_at', [$previousStart, $startDate]);

        $currentTotal = $currentPeriod->count();
        $previousTotal = $previousPeriod->count();
        
        $currentOutbound = (clone $currentPeriod)->where('direction', 'outbound')->count();
        $currentInbound = (clone $currentPeriod)->where('direction', 'inbound')->count();

        $growth = $previousTotal > 0 
            ? round(($currentTotal - $previousTotal) / $previousTotal * 100, 1) 
            : 0;

        return [
            'total_messages' => $currentTotal,
            'outbound' => $currentOutbound,
            'inbound' => $currentInbound,
            'growth' => $growth,
            'avg_per_day' => round($currentTotal / max(1, $startDate->diffInDays($endDate)), 1),
        ];
    }

    /**
     * Get top performing instances.
     */
    protected function getTopInstances(int $userId, Carbon $startDate, Carbon $endDate): array
    {
        return Message::where('user_id', $userId)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->with('instance:id,name,phone_number')
            ->selectRaw('instance_id, COUNT(*) as count')
            ->groupBy('instance_id')
            ->orderByDesc('count')
            ->limit(5)
            ->get()
            ->map(fn($item) => [
                'name' => $item->instance?->name ?? 'Unknown',
                'phone' => $item->instance?->phone_number ?? 'N/A',
                'count' => $item->count,
            ])
            ->toArray();
    }

    /**
     * Get message types breakdown.
     */
    protected function getMessageTypes(int $userId, Carbon $startDate, Carbon $endDate): array
    {
        $messages = Message::where('user_id', $userId)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->get();

        $types = [
            'text' => 0,
            'image' => 0,
            'video' => 0,
            'audio' => 0,
            'document' => 0,
            'other' => 0,
        ];

        foreach ($messages as $message) {
            if ($message->hasMedia()) {
                $mediaType = $message->getMediaType();
                if (str_contains($mediaType ?? '', 'image')) {
                    $types['image']++;
                } elseif (str_contains($mediaType ?? '', 'video')) {
                    $types['video']++;
                } elseif (str_contains($mediaType ?? '', 'audio')) {
                    $types['audio']++;
                } elseif ($mediaType) {
                    $types['document']++;
                } else {
                    $types['other']++;
                }
            } else {
                $types['text']++;
            }
        }

        return $types;
    }

    /**
     * Export analytics to CSV.
     */
    public function exportCsv(Request $request)
    {
        $user = auth()->user();
        $period = $request->get('period', '30');
        $startDate = now()->subDays((int) $period)->startOfDay();
        $endDate = now()->endOfDay();

        $messages = Message::where('user_id', $user->id)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->with('instance:id,name')
            ->orderBy('created_at', 'desc')
            ->get();

        $filename = 'analytics_export_' . now()->format('Y-m-d') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ];

        $callback = function () use ($messages) {
            $file = fopen('php://output', 'w');
            
            // Header row
            fputcsv($file, ['Date', 'Time', 'Direction', 'Status', 'Instance', 'To/From', 'Message']);

            foreach ($messages as $message) {
                fputcsv($file, [
                    $message->created_at->format('Y-m-d'),
                    $message->created_at->format('H:i:s'),
                    $message->direction,
                    $message->status,
                    $message->instance?->name ?? 'N/A',
                    $message->direction === 'inbound' ? $message->from : $message->to,
                    substr($message->body ?? '', 0, 100),
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
