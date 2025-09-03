@props(['title'])

<tr class="border-b">
    <td class="px-6 py-3">
        {{ $slot }}
    </td>
    <td class="px-6 py-3 text-right">
        <a {{ $attributes }} class="btn-view-details text-blue-600 hover:underline">
            View Details
        </a>
    </td>
</tr>
