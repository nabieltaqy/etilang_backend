<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Violation;
use App\Models\Transaction;
use App\Models\Ticket;

class DashboardController extends Controller
{
    public function index()
    {
        $data = [
            'last_3_months' => $this->getDashboardData(3),
            'last_6_months' => $this->getDashboardData(6),
            'last_12_months' => $this->getDashboardData(12),
        ];

        return response()->json([
            'message' => 'Dashboard data retrieved successfully',
            'data' => $data,
        ]);
    }

    private function getDashboardData($months)
    {
        $currentFrom = now()->subMonths($months);
        $previousFrom = now()->subMonths($months * 2);
        $previousTo = now()->subMonths($months);

        // Current Data
        $violations = Violation::where('created_at', '>=', $currentFrom)->count();
        $tickets = Ticket::where('created_at', '>=', $currentFrom)->count();
        $vehicles = Ticket::with('violation')
            ->where('created_at', '>=', $currentFrom)
            ->get()
            ->pluck('violation.number')
            ->unique()
            ->count();
        $amount = Transaction::where('status', 'settlement')
            ->where('created_at', '>=', $currentFrom)
            ->sum('amount');
        $highestViolationType = $this->getHighestViolationType($months);

        // Previous Data
        $prevViolations = Violation::whereBetween('created_at', [$previousFrom, $previousTo])->count();
        $prevTickets = Ticket::whereBetween('created_at', [$previousFrom, $previousTo])->count();
        $prevVehicles = Ticket::with('violation')
            ->whereBetween('created_at', [$previousFrom, $previousTo])
            ->get()
            ->pluck('violation.number')
            ->unique()
            ->count();
        $prevAmount = Transaction::where('status', 'settlements')
            ->whereBetween('created_at', [$previousFrom, $previousTo])
            ->sum('amount');

        return [
            'violations' => [
                'count' => $violations,
                'change' => $this->calculatePercentageChange($violations, $prevViolations),
            ],
            'tickets' => [
                'count' => $tickets,
                'change' => $this->calculatePercentageChange($tickets, $prevTickets),
            ],
            'vehicles' => [
                'count' => $vehicles,
                'change' => $this->calculatePercentageChange($vehicles, $prevVehicles),
            ],
            'amount' => [
                'sum' => $amount,
                'change' => $this->calculatePercentageChange($amount, $prevAmount),
            ],
            'highest_violation_type' => $highestViolationType,
        ];
    }

    private function getHighestViolationType($months)
    {
        return Ticket::with('violation.violationType')
            ->where('created_at', '>=', now()->subMonths($months))
            ->get()
            ->groupBy('violation.violationType.name')
            ->map(fn($group) => $group->count())
            ->sortDesc();
    }

    private function calculatePercentageChange($current, $previous)
    {
        if ($previous == 0) {
            return $current == 0 ? 0 : 100;
        }
        return round((($current - $previous) / $previous) * 100, 2);
    }
}
