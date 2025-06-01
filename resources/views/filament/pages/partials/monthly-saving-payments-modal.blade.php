<div>
    <h3 class="text-lg font-bold mb-4">Payments for {{ $member->name }}</h3>
    <div class="w-full overflow-x-auto">
        <table class="w-full min-w-full divide-y divide-gray-200 border rounded-lg">
            <thead class="bg-gray-100">
                <tr>
                    <th class="px-4 py-2 text-left text-xs font-semibold text-gray-700 uppercase">Date</th>
                    <th class="px-4 py-2 text-left text-xs font-semibold text-gray-700 uppercase">Amount</th>
                    <th class="px-4 py-2 text-left text-xs font-semibold text-gray-700 uppercase">Method</th>
                    <th class="px-4 py-2 text-left text-xs font-semibold text-gray-700 uppercase">Reference</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-100">
                @forelse($payments as $payment)
                    <tr>
                        <td class="px-4 py-2 whitespace-nowrap font-medium text-gray-900">{{ \Carbon\Carbon::parse($payment->date)->format('d M Y') }}</td>
                        <td class="px-4 py-2 whitespace-nowrap text-green-700 font-bold">₹{{ number_format($payment->amount, 2) }}</td>
                        <td class="px-4 py-2 whitespace-nowrap text-blue-700">{{ ucfirst($payment->payment_method) }}</td>
                        <td class="px-4 py-2 whitespace-nowrap text-gray-600">{{ $payment->reference_number ?: '-' }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="px-4 py-4 text-center text-gray-500">No payments found.</td>
                    </tr>
                @endforelse
            </tbody>
            @if($payments->count())
            <tfoot>
                <tr class="bg-gray-50">
                    <td class="px-4 py-2 font-semibold text-right" colspan="1">Total</td>
                    <td class="px-4 py-2 font-bold text-green-900">₹{{ number_format($payments->sum('amount'), 2) }}</td>
                    <td colspan="2"></td>
                </tr>
            </tfoot>
            @endif
        </table>
    </div>
</div>
