@props(['url'])
<tr>
    <td class="header">
        <a href="{{ $url }}" style="display: inline-block;">
            @if (trim($slot) === 'SiliconCove')
                <img src="https://i.ibb.co/DgYRvJQY/siliconcovelogo.png" alt="SiliconCove Logo" style="width:120px;">
            @else
                {{ $slot }}
            @endif
        </a>
    </td>
</tr>
{{-- https://ibb.co/dJt5VTDt --}}
{{-- <a href="https://ibb.co/dJt5VTDt"><img src="https://i.ibb.co/DgYRvJQY/siliconcovelogo.png" alt="siliconcovelogo" border="0"></a> --}}