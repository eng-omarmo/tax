<?php

namespace App\Http\Controllers;

use App\Models\Property;
use App\Models\Notification;
use Illuminate\Http\Request;
use App\Jobs\NotifyPropertyOwnerJob;
use App\Services\TimeService; // Assuming you might need this for current quarter/year

class NotificationController extends Controller
{
    protected $timeService;

    public function __construct(TimeService $timeService)
    {
        $this->timeService = $timeService;
    }

    public function index(Request $request)
    {
        $query = Notification::with('property.landlord.user', 'property.district', 'property.branch')
            ->select('notifications.*') // Ensure we are selecting from notifications table primarily
            ->join('properties', 'notifications.property_id', '=', 'properties.id'); // Join for sorting/filtering by property fields

        // Search functionality
        if ($request->filled('search')) {
            $searchTerm = $request->search;
            $query->where(function ($q) use ($searchTerm) {
                $q->whereHas('property', function ($subQuery) use ($searchTerm) {
                    $subQuery->where('property_name', 'like', '%' . $searchTerm . '%')
                             ->orWhere('house_code', 'like', '%' . $searchTerm . '%');
                })
                ->orWhereHas('property.landlord.user', function ($subQuery) use ($searchTerm) {
                    $subQuery->where('name', 'like', '%' . $searchTerm . '%');
                });
            });
        }

        // Filter by quarter
        if ($request->filled('quarter')) {
            $query->where('notifications.quarter', $request->quarter);
        }

        // Filter by year
        if ($request->filled('year')) {
            $query->where('notifications.year', $request->year);
        }

        // Filter by notification status
        if ($request->filled('is_notified')) {
            $query->where('notifications.is_notified', $request->is_notified);
        }

        // Sorting
        $sortColumn = $request->get('sort_by', 'notifications.created_at');
        $sortDirection = $request->get('sort_direction', 'desc');

        // Validate sortable columns to prevent SQL injection if column names are directly from user input
        $validSortColumns = ['notifications.created_at', 'properties.property_name', 'notifications.quarter', 'notifications.year', 'notifications.is_notified'];
        if (in_array($sortColumn, $validSortColumns)) {
            $query->orderBy($sortColumn, $sortDirection);
        }

        $notifications = $query->paginate(15); // Or any other number you prefer

        // For filter dropdowns
        $quarters = Notification::distinct()->pluck('quarter');
        $years = Notification::distinct()->pluck('year');

        return view('notifications.index', compact('notifications', 'quarters', 'years'));
    }

    public function reNotify(Request $request, $propertyId)
    {
        $property = Property::find($propertyId);

        if (!$property) {
            return redirect()->back()->with('error', 'Property not found.');
        }

        // Option 1: Update existing notification record (if you only want one record per property per quarter/year)
        $notification = Notification::where('property_id', $propertyId)
                                    ->where('quarter', $this->timeService->currentQuarter()) // Or get from request if re-notifying for a specific past period
                                    ->where('year', $this->timeService->currentYear())
                                    ->first();

        if ($notification) {
            $notification->updated_at = now(); // Or a specific notification_date field if you have one
            $notification->is_notified = false; // Set to false so the job can process it
            $notification->save();
        } else {
            // Option 2: Create a new notification record if you want to log each re-notification attempt
            // This might be preferable for a history log.
            // Ensure NotifyPropertyOwnerJob handles potential duplicates or decide on your logic here.
            // For simplicity, let's assume the job creates the notification record upon successful notification.
        }

        // Dispatch the job to re-notify
        // You might need to pass specific quarter/year if not using current
        NotifyPropertyOwnerJob::dispatch($property, $this->timeService->currentQuarter(), $this->timeService->currentYear())->onQueue('notifications');

        return redirect()->route('notifications.index')->with('success', 'Re-notification job for ' . $property->property_name . ' has been dispatched.');
    }

    // Optional: For displaying notification history if you log each attempt
    public function history($propertyId)
    {
        $property = Property::findOrFail($propertyId);
        $notificationHistory = Notification::where('property_id', $propertyId)
                                          ->orderBy('created_at', 'desc')
                                          ->paginate(10);

        return view('notifications.history', compact('property', 'notificationHistory'));
    }
}
