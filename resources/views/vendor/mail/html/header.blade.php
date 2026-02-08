@props(['url'])
<tr>
    <td class="header">
        <a href="{{ $url }}" style="display: inline-block;">
            @if (trim($slot) === 'SiliconCove')
                <img
                    src="https://i.ibb.co/qbx0WcP/siliconcovelogo.png"
                    alt="SiliconCove"
                    style="height: 40px; width: auto; border: 0; display: block;"
                >
            @else
                {{ $slot }}
            @endif
        </a>
    </td>
</tr>
{{-- https://ibb.co/dJt5VTDt --}}
{{-- <a href="https://ibb.co/dJt5VTDt"><img src="https://i.ibb.co/DgYRvJQY/siliconcovelogo.png" alt="siliconcovelogo" border="0"></a> --}}
