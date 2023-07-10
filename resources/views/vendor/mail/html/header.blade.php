@props(['url'])
<tr>
<td class="header">
<a href="{{ $url }}" style="display: inline-block;">
@if (trim($slot) === 'Laravel')
<img src="https://www.atmiyausa.org/assets/images/resources/logo-1.png" class="logo" alt="atmiyausa Logo">
@else
{{ $slot }}
@endif
</a>
</td>
</tr>
