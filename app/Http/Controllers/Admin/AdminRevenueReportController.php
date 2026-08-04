<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Models\Department;
use App\Models\Invoice;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminRevenueReportController extends Controller
{
    /**
     * Display comprehensive financial revenue report, department breakdown, and invoice ledgers.
     */
    public function index(Request $request): View
    {
        $month = $request->query('month', now()->format('Y-m'));
        $departmentId = $request->query('department_id');

        [$year, $m] = explode('-', $month);

        // Core Financial Metrics
        $stats = [
            'total_revenue' => Appointment::where('payment_status', 'paid')->sum('consultation_fee'),
            'monthly_revenue' => Appointment::where('payment_status', 'paid')
                ->whereYear('appointment_date', $year)
                ->whereMonth('appointment_date', $m)
                ->sum('consultation_fee'),
            'today_revenue' => Appointment::where('payment_status', 'paid')
                ->whereDate('appointment_date', now()->toDateString())
                ->sum('consultation_fee'),
            'paid_invoices_count' => Invoice::where('status', 'paid')->count(),
        ];

        // Revenue Breakdown per Department
        $departments = Department::withCount(['doctors'])
            ->get()
            ->map(function ($dept) use ($year, $m) {
                $appointmentsQuery = Appointment::whereHas('doctor', fn ($dq) => $dq->where('department_id', $dept->id))
                    ->where('payment_status', 'paid');

                $dept->total_revenue = (float) (clone $appointmentsQuery)->sum('consultation_fee');
                $dept->monthly_revenue = (float) (clone $appointmentsQuery)
                    ->whereYear('appointment_date', $year)
                    ->whereMonth('appointment_date', $m)
                    ->sum('consultation_fee');
                $dept->paid_appointments_count = (int) (clone $appointmentsQuery)->count();

                return $dept;
            });

        // Detailed Invoices Ledger
        $invoicesQuery = Invoice::with(['appointment.doctor.user', 'appointment.doctor.department', 'patient.user', 'payment']);

        if (! empty($departmentId)) {
            $invoicesQuery->whereHas('appointment.doctor', function ($dq) use ($departmentId) {
                $dq->where('department_id', $departmentId);
            });
        }

        if (! empty($month)) {
            $invoicesQuery->whereYear('issued_at', $year)->whereMonth('issued_at', $m);
        }

        $invoices = $invoicesQuery->orderBy('issued_at', 'desc')
            ->orderBy('id', 'desc')
            ->paginate(15)
            ->withQueryString();

        return view('admin.reports.revenue', compact('stats', 'departments', 'invoices', 'month', 'departmentId'));
    }
}
