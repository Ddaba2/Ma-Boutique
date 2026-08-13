<table class="stats-row" width="100%">
    <tr>
        @foreach($stats as $stat)
            <td class="stat-box" width="{{ (int) (100 / count($stats)) }}%">
                <div class="stat-value">{{ $stat['value'] }}</div>
                <div class="stat-label">{{ $stat['label'] }}</div>
            </td>
            @if(!$loop->last)
                <td width="8"></td>
            @endif
        @endforeach
    </tr>
</table>
