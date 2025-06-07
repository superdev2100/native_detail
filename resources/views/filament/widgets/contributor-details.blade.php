<div class="space-y-4">
    <div class="text-lg font-medium text-gray-900 mb-4">
        Data for {{ $month }}
    </div>

    {{-- Categories Table --}}
    <div class="overflow-x-auto">
        <table class="w-full text-sm text-left text-gray-500">
            <thead class="text-xs text-gray-700 uppercase bg-gray-50">
                <tr>
                    <th class="px-6 py-3">Category</th>
                    <th class="px-6 py-3">Amount</th>
                </tr>
            </thead>
            <tbody>
                @foreach($categories as $category => $amount)
                    <tr class="bg-white border-b">
                        <td class="px-6 py-4">{{ $category }}</td>
                        <td class="px-6 py-4">₹{{ number_format($amount, 2) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    {{-- Tags Table --}}
    <div class="overflow-x-auto mt-4">
        <table class="w-full text-sm text-left text-gray-500">
            <thead class="text-xs text-gray-700 uppercase bg-gray-50">
                <tr>
                    <th class="px-6 py-3">Tags</th>
                    <th class="px-6 py-3">Count</th>
                </tr>
            </thead>
            <tbody>
                @foreach($tags as $tag => $count)
                    <tr class="bg-white border-b">
                        <td class="px-6 py-4">{{ $tag }}</td>
                        <td class="px-6 py-4">{{ $count }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
 