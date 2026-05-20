@extends('layouts.app')

@section('title', 'Manage Bookings')

@section('content')
<div class="max-w-6xl mx-auto px-4 py-8">
    <h1 class="text-3xl font-bold mb-6 bg-gradient-to-r from-purple-400 to-pink-400 bg-clip-text text-transparent">📅 Manage Bookings</h1>

    @if(session('success'))
        <div class="mb-4 rounded-lg border border-green-500/30 bg-green-500/10 px-4 py-3 text-sm font-semibold text-green-300">
            {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div class="mb-4 rounded-lg border border-red-500/30 bg-red-500/10 px-4 py-3 text-sm font-semibold text-red-300">
            {{ session('error') }}
        </div>
    @endif
    
    <div class="bg-gray-800/50 backdrop-blur-sm rounded-xl overflow-hidden border border-gray-700 shadow-lg">
        <div class="px-6 py-4 border-b border-gray-700">
            <h2 class="text-xl font-bold text-white">All Bookings</h2>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-700/50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase">Trainee</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase">Trainer</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase">Date & Time</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase">Duration</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase">Amount</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase">Refund</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-700">
                    @foreach($bookings as $booking)
                    @php
                        $refundUpiId = $booking->trainee->upi_id ?? null;
                    @endphp
                    <tr class="hover:bg-gray-700/30 transition">
                        <td class="px-6 py-3 font-medium text-gray-200">{{ $booking->trainee->name ?? 'N/A' }}</td>
                        <td class="px-6 py-3 text-gray-400">{{ $booking->trainer->name ?? 'N/A' }}</td>
                        <td class="px-6 py-3 text-gray-400">{{ \Carbon\Carbon::parse($booking->session_date)->format('M d, Y h:i A') }}</td>
                        <td class="px-6 py-3 text-gray-400">{{ $booking->duration_minutes ?? 60 }} min</td>
                        <td class="px-6 py-3 font-semibold text-purple-400">₹{{ number_format($booking->amount ?? 0) }}</td>
                        <td class="px-6 py-3">
                            <span class="px-2 py-1 rounded-full text-xs font-semibold 
                                @if($booking->status == 'confirmed') bg-green-900/50 text-green-300
                                @elseif($booking->status == 'completed') bg-blue-900/50 text-blue-300
                                @elseif($booking->status == 'cancelled') bg-red-900/50 text-red-300
                                @elseif($booking->status == 'no_show') bg-orange-900/50 text-orange-300
                                @else bg-gray-700 text-gray-300 @endif">
                            {{ $booking->status == 'no_show' ? 'No Show' : ucfirst($booking->status ?? 'pending') }}
                        </span>
                    </td>
                    <td class="px-6 py-3 text-sm">
                        @if(($booking->refund_status ?? null) === 'pending_admin')
                            <div class="space-y-2">
                                <div>
                                    <span class="inline-flex rounded-full bg-yellow-500/10 px-2 py-1 text-xs font-semibold text-yellow-300 ring-1 ring-yellow-500/30">Admin refund pending</span>
                                </div>
                                <div class="text-xs text-gray-400">
                                    <div>Refund: <span class="font-semibold text-gray-200">₹{{ number_format($booking->refund_amount ?? $booking->amount ?? 0) }}</span></div>
                                    <div>Trainee: {{ $booking->trainee->name ?? 'N/A' }}</div>
                                    <div>Trainer: {{ $booking->trainer->name ?? 'N/A' }}</div>
                                    @if($refundUpiId)
                                        <div>UPI: <span class="font-semibold text-gray-200">{{ $refundUpiId }}</span></div>
                                        <div class="text-yellow-300">From trainee profile</div>
                                    @else
                                        <div class="text-gray-500">UPI: Not added yet</div>
                                    @endif
                                    @if($booking->cancellation_reason)
                                        <div>Reason: {{ $booking->cancellation_reason }}</div>
                                    @endif
                                    @if($booking->refund_error)
                                        <div class="text-red-300">{{ $booking->refund_error }}</div>
                                    @endif
                                </div>
                                <form method="POST" action="{{ route('admin.bookings.refund', $booking->id) }}" onsubmit="return confirm('Process refund for this trainee?')">
                                    @csrf
                                    <button type="submit" class="rounded-md bg-green-500/15 px-3 py-1.5 text-xs font-semibold text-green-300 ring-1 ring-green-500/30 transition hover:bg-green-500/25">Process Refund</button>
                                </form>
                            </div>
                        @elseif(($booking->refund_status ?? null) === 'processed')
                            <div class="text-xs text-green-300">
                                Refunded ₹{{ number_format($booking->refund_amount ?? 0) }}
                                @if($refundUpiId)
                                    <div class="text-gray-400">UPI: {{ $refundUpiId }}</div>
                                @endif
                                @if($booking->refund_reference)
                                    <div class="text-gray-400">Ref: {{ $booking->refund_reference }}</div>
                                @endif
                            </div>
                        @elseif(($booking->refund_status ?? null) === 'failed')
                            <div class="text-xs text-red-300">
                                Refund failed
                                @if($booking->refund_error)
                                    <div class="text-gray-400">{{ $booking->refund_error }}</div>
                                @endif
                            </div>
                        @elseif(($booking->cancellation_policy ?? null) === 'trainee_no_refund')
                            <span class="text-xs text-gray-400">No refund</span>
                        @else
                            <span class="text-xs text-gray-500">-</span>
                        @endif
                    </td>
                    <td class="px-6 py-3">
                        <form method="POST" action="{{ route('admin.bookings.delete', $booking->id) }}" onsubmit="return confirm('Delete this booking?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-red-400 hover:text-red-300 text-sm transition">Delete</button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <div class="px-6 py-4 border-t border-gray-700">
        {{ $bookings->links() }}
    </div>
</div>
@endsection
