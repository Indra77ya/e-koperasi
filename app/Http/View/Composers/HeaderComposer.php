<?php

namespace App\Http\View\Composers;

use Illuminate\View\View;
use App\Models\Loan;
use App\Models\LoanInstallment;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Auth;

class HeaderComposer
{
    /**
     * Bind data to the view.
     *
     * @param  View  $view
     * @return void
     */
    public function compose(View $view)
    {
        // System Alerts
        $pendingLoansCount = Loan::where('status', 'diajukan')->count();

        $dueTodayCount = LoanInstallment::where('tanggal_jatuh_tempo', date('Y-m-d'))
            ->where('status', 'belum_lunas')
            ->count();

        $notifications = collect([]);
        $unreadNotificationCount = 0;

        // Try to fetch notifications if table exists
        if (Schema::hasTable('notifications') && Auth::check()) {
            $user = Auth::user();
            $notifications = $user->unreadNotifications()->take(5)->get();
            $unreadNotificationCount = $user->unreadNotifications()->count();
        }

        $view->with('headerAlerts', [
            'pending_loans' => $pendingLoansCount,
            'due_today' => $dueTodayCount,
            'notifications' => $notifications,
            'unread_notifications_count' => $unreadNotificationCount,
            'total_alerts' => $pendingLoansCount + $dueTodayCount + $unreadNotificationCount
        ]);
    }
}
